<?php

namespace AweBooking\System;

use AweBooking\Vendor\Illuminate\Support\Collection;
use AweBooking\Vendor\Illuminate\Support\Str;
use Exception;
use ReflectionClass;
use ReflectionFunction;
use WP_User;

class Access
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $policies = [];

    /**
     * @var array
     */
    protected $beforeCallbacks = [];

    /**
     * @var callable
     */
    protected $userResolver;

    /**
     * @var array
     */
    protected static $resolved = [];

    /**
     * @param array $policies
     * @param array $beforeCallbacks
     * @param callable|null $userResolver
     */
    public function __construct(
        array $policies = [],
        array $beforeCallbacks = [],
        callable $userResolver = null
    ) {
        $this->policies = $policies;
        $this->beforeCallbacks = $beforeCallbacks;
        $this->userResolver = $userResolver ?? static function () {
            return wp_get_current_user();
        };
    }

    /**
     * Get a gate instance for the given user.
     *
     * @param WP_User $user
     * @return static
     */
    public function forUser(WP_User $user)
    {
        return new static(
            $this->policies,
            $this->beforeCallbacks,
            function () use ($user) {
                return $user;
            }
        );
    }

    /**
     * Define a policy class for a given class type.
     *
     * @param string $class
     * @param string $policy
     * @return $this
     */
    public function policy(string $class, string $policy)
    {
        $this->policies[$class] = $policy;

        return $this;
    }

    /**
     * Register a callback to run before all Gate checks.
     *
     * @param callable $callback
     * @return $this
     */
    public function before(callable $callback)
    {
        $this->beforeCallbacks[] = $callback;

        return $this;
    }

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param string $ability
     * @param array|mixed $arguments
     * @return bool
     */
    public function allows($ability, $arguments = [])
    {
        return $this->check($ability, $arguments);
    }

    /**
     * Determine if the given ability should be denied for the current user.
     *
     * @param string $ability
     * @param array|mixed $arguments
     * @return bool
     */
    public function denies($ability, $arguments = [])
    {
        return !$this->allows($ability, $arguments);
    }

    /**
     * Determine if any one of the given abilities should be granted for the current user.
     *
     * @param iterable|string $abilities
     * @param array|mixed $arguments
     * @return bool
     */
    public function any($abilities, $arguments = [])
    {
        // $access->any([Policy::class, ['view', 'create']], $post)...
        if (isset($abilities[1]) && is_array($abilities[1])) {
            $abilities = Collection::make($abilities[1])->map(function ($ability) use ($abilities) {
                return [$abilities[0], $ability];
            })->all();
        }

        return Collection::make($abilities)->contains(function ($ability) use ($arguments) {
            return $this->check($ability, $arguments);
        });
    }

    /**
     * Determine if all of the given abilities should be denied for the current user.
     *
     * @param iterable|string $abilities
     * @param array|mixed $arguments
     * @return bool
     */
    public function none($abilities, $arguments = [])
    {
        return !$this->any($abilities, $arguments);
    }

    /**
     * Determine if all of the given abilities should be granted for the current user.
     *
     * @param iterable|string $abilities
     * @param array|mixed $arguments
     * @return bool
     */
    public function check($abilities, $arguments = [])
    {
        if (is_array($abilities) && class_exists($abilities[0])) {
            $abilities = [$abilities];
        }

        return (new Collection($abilities))->every(function ($ability) use ($arguments) {
            return (bool) $this->call($ability, $arguments);
        });
    }

    /**
     * Get the raw result from the authorization callback.
     *
     * @param string $ability
     * @param array|mixed $arguments
     * @return mixed
     */
    public function call($ability, $arguments = [])
    {
        $arguments = is_array($arguments) ? $arguments : [$arguments];

        $user = $this->resolveUser();

        // First we will call the "before" callbacks for the Access. If any of these give
        // back a non-null response, we will immediately return that result in order
        // to let the developers override all checks for some authorization cases.
        $result = $this->callBeforeCallbacks($user, $ability, $arguments);

        if (is_null($result)) {
            $callback = $this->resolveAuthCallback($user, $ability, ...$arguments);

            $result = $callback($user, ...$arguments);
        }

        return $result;
    }

    /**
     * Call all of the before callbacks and return if a result is given.
     *
     * @param WP_User $user
     * @param string $ability
     * @param array $arguments
     * @return bool|null
     */
    protected function callBeforeCallbacks($user, $ability, array $arguments)
    {
        foreach ($this->beforeCallbacks as $beforeCallback) {
            if (!$this->canBeCalledWithUser($user, $beforeCallback)) {
                continue;
            }

            if (!is_null($result = $beforeCallback($user, $ability, $arguments))) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Resolve the callable for the given ability and arguments.
     *
     * @param WP_User|null $user
     * @param string $ability
     * @param mixed ...$arguments
     * @return callable
     */
    protected function resolveAuthCallback($user, string $ability, ...$arguments)
    {
        if (
            isset($arguments[0])
            && !is_null($policy = $this->resolvePolicyForModel($arguments[0]))
            && $callback = $this->makePolicyCallback($user, $ability, $arguments, $policy)
        ) {
            return $callback;
        }

        return static function () use ($arguments, $ability, $user) {
            return user_can($user, $ability, ...$arguments);
        };
    }

    /**
     * Create the callback for a policy check.
     *
     * @param WP_User|null $user
     * @param string $ability
     * @param array $arguments
     * @param mixed $policy
     * @return bool|callable
     */
    protected function makePolicyCallback($user, $ability, array $arguments, $policy)
    {
        if (!is_callable([$policy, $this->formatAbilityToMethod($ability)])) {
            return false;
        }

        return function () use ($user, $ability, $arguments, $policy) {
            // This callback will be responsible for calling the policy's before method and
            // running this policy method if necessary. This is used to when objects are
            // mapped to policy objects in the user's configurations or on this class.
            $result = $this->callPolicyBefore(
                $policy,
                $user,
                $ability,
                $arguments
            );

            // When we receive a non-null result from this before method, we will return it
            // as the "final" results. This will allow developers to override the checks
            // in this policy to return the result for all rules defined in the class.
            if (!is_null($result)) {
                return $result;
            }

            $method = $this->formatAbilityToMethod($ability);

            return $this->callPolicyMethod($policy, $method, $user, $arguments);
        };
    }

    /**
     * Call the "before" method on the given policy, if applicable.
     *
     * @param mixed $policy
     * @param WP_User $user
     * @param string $ability
     * @param array $arguments
     * @return mixed
     */
    protected function callPolicyBefore($policy, $user, $ability, $arguments)
    {
        if (!method_exists($policy, 'before')) {
            return null;
        }

        if ($this->canBeCalledWithUser($user, $policy, 'before')) {
            return $policy->before($user, $ability, ...$arguments);
        }

        return null;
    }

    /**
     * Call the appropriate method on the given policy.
     *
     * @param mixed $policy
     * @param string $method
     * @param WP_User|null $user
     * @param array $arguments
     * @return mixed
     */
    protected function callPolicyMethod($policy, $method, $user, array $arguments)
    {
        // If this first argument is a string, that means they are passing a class name
        // to the policy. We will remove the first argument from this argument array
        // because this policy already knows what type of models it can authorize.
        if (isset($arguments[0]) && is_string($arguments[0])) {
            array_shift($arguments);
        }

        if (!is_callable([$policy, $method])) {
            return null;
        }

        if ($this->canBeCalledWithUser($user, $policy, $method)) {
            return $policy->{$method}($user, ...$arguments);
        }

        return null;
    }

    /**
     * Get a policy instance for a given model class.
     *
     * @param object|string $class
     * @return string|null
     */
    public function resolvePolicyForModel($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!is_string($class) || !class_exists($class)) {
            return null;
        }

        if (isset($this->policies[$class])) {
            return $this->resolvePolicy($this->policies[$class]);
        }

        if (method_exists($class, 'getPolicyClass') && ($policy = $class::getPolicyClass())) {
            $this->policies[$class] = $policy;

            return $this->resolvePolicy($policy);
        }

        foreach ($this->policies as $expected => $policy) {
            if (is_subclass_of($class, $expected)) {
                return $this->resolvePolicy($policy);
            }
        }

        return null;
    }

    /**
     * Build a policy class instance of the given type.
     *
     * @param object|string $class
     * @return mixed
     */
    protected function resolvePolicy($class)
    {
        $name = is_object($class) ? get_class($class) : $class;

        if (isset(static::$resolved[$name])) {
            return static::$resolved[$name];
        }

        return static::$resolved[$name] = $this->container->get($class);
    }

    /**
     * Resolve the user from the user resolver.
     *
     * @return WP_User|null
     */
    protected function resolveUser(): WP_User
    {
        return call_user_func($this->userResolver);
    }

    /**
     * Determine whether the callback/method can be called with the given user.
     *
     * @param WP_User|null $user
     * @param \Closure|string|array $class
     * @param string|null $method
     * @return bool
     */
    protected function canBeCalledWithUser($user, $class, $method = null)
    {
        if (!is_null($user)) {
            return true;
        }

        if (!is_null($method)) {
            return $this->methodAllowsGuests($class, $method);
        }

        if (is_array($class)) {
            $className = is_string($class[0]) ? $class[0] : get_class($class[0]);

            return $this->methodAllowsGuests($className, $class[1]);
        }

        return $this->callbackAllowsGuests($class);
    }

    /**
     * Determine if the given class method allows guests.
     *
     * @param string $class
     * @param string $method
     * @return bool
     */
    protected function methodAllowsGuests($class, $method)
    {
        try {
            $reflection = new ReflectionClass($class);

            $method = $reflection->getMethod($method);
        } catch (Exception $e) {
            return false;
        }

        if ($method) {
            $parameters = $method->getParameters();

            return isset($parameters[0]) && $this->parameterAllowsGuests($parameters[0]);
        }

        return false;
    }

    /**
     * Determine if the callback allows guests.
     *
     * @param callable $callback
     * @return bool
     */
    protected function callbackAllowsGuests($callback)
    {
        $parameters = (new ReflectionFunction($callback))->getParameters();

        return isset($parameters[0]) && $this->parameterAllowsGuests($parameters[0]);
    }

    /**
     * Determine if the given parameter allows guests.
     *
     * @param \ReflectionParameter $parameter
     * @return bool
     */
    protected function parameterAllowsGuests($parameter)
    {
        return ($parameter->hasType() && $parameter->allowsNull())
               || ($parameter->isDefaultValueAvailable() && is_null($parameter->getDefaultValue()));
    }

    /**
     * Format the policy ability into a method name.
     *
     * @param string $ability
     * @return string
     */
    protected function formatAbilityToMethod($ability)
    {
        return strpos($ability, '-') !== false ? Str::camel($ability) : $ability;
    }
}
