<?php

namespace AweBooking\System\Inertia;

use AweBooking\System\Container;
use AweBooking\Vendor\Illuminate\Contracts\Support\Arrayable;
use AweBooking\Vendor\Illuminate\Support\Arr;
use Closure;

class Response
{
	/**
	 * @var InertiaRequest
	 */
	protected $request;

	/**
	 * @var string
	 */
	protected $component;

	/**
	 * @var array<string, mixed>
	 */
	protected $props;

	/**
	 * @var string|null
	 */
	protected $version;

	/**
	 * @var array
	 */
	protected $viewData = [];

	/**
	 * @var callable
	 */
	protected $viewCallback;

	/**
	 * @param string $component
	 * @param array $props
	 * @param string $version
	 * @param callable|null $viewCallback
	 */
	public function __construct(
		InertiaRequest $request,
		string $component,
		array $props,
		string $version,
		callable $viewCallback
	) {
		$this->request = $request;
		$this->component = $component;
		$this->props = $props;
		$this->version = $version;
		$this->viewCallback = $viewCallback;
	}

	/**
	 * @param string|array $key
	 * @param mixed|null $value
	 * @return $this
	 */
	public function with($key, $value = null): self
	{
		if (is_array($key)) {
			$this->props = array_merge($this->props, $key);
		} else {
			$this->props[$key] = $value;
		}

		return $this;
	}

	/**
	 * @param string|array $key
	 * @param mixed|null $value
	 * @return $this
	 */
	public function withViewData($key, $value = null): self
	{
		if (is_array($key)) {
			$this->viewData = array_merge($this->viewData, $key);
		} else {
			$this->viewData[$key] = $value;
		}

		return $this;
	}

	/**
	 * Response inertia component.
	 *
	 * @return void
	 */
	public function output(): void
	{
		$onlyKeys = $this->request->getPartialData();

		$props = ($onlyKeys && $this->request->getPartialComponent() === $this->component)
			? Arr::only($this->props, $onlyKeys)
			: array_filter($this->props, static function ($prop) {
				return !($prop instanceof LazyProp);
			});

		$props = $this->resolvePropertyInstances($props);

		$page = [
			'component' => $this->component,
			'props' => $props,
			'url' => $_SERVER['REQUEST_URI'],
			// 'url' => $request->getBaseUrl() . $request->getRequestUri(),
			'version' => $this->version,
		];

		if ($this->request->isInertiaRequest()) {
			header('Cache-Control: no-cache');
			header('Content-Type: application/json');
			header('Vary: Accept');
			header('X-Inertia: true');

            /*if ($request->method() === 'GET' && $request->header('X-Inertia-Version', '') !== Inertia::getVersion()) {
                $response = $this->onVersionChange($request, $response);
            }

            if ($response->isOk() && empty($response->getContent())) {
                $response = $this->onEmptyResponse($request, $response);
            }

            if ($response->getStatusCode() === 302 && in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
                $response->setStatusCode(303);
            }*/

            wp_send_json($page);
			exit;
		}

		call_user_func($this->viewCallback, $this->viewData + ['page' => $page]);
	}

	/**
	 * Resolve all necessary class instances in the given props.
	 *
	 * @param array $props
	 * @param bool $unpackDotProps
	 * @return array
	 */
	public function resolvePropertyInstances(array $props, bool $unpackDotProps = true): array
	{
		foreach ($props as $key => $value) {
			if ($value instanceof Closure) {
				$value = Container::getInstance()->call($value);
			}

			if ($value instanceof LazyProp) {
				$value = Container::getInstance()->call($value);
			}

			if ($value instanceof ResourceResponse || $value instanceof JsonResource) {
				$value = $value->toResponse($request)->getData(true);
			}

			if ($value instanceof Arrayable) {
				$value = $value->toArray();
			}

			if (is_array($value)) {
				$value = $this->resolvePropertyInstances($value, false);
			}

			if ($unpackDotProps && str_contains($key, '.')) {
				Arr::set($props, $key, $value);
				unset($props[$key]);
			} else {
				$props[$key] = $value;
			}
		}

		return $props;
	}
}
