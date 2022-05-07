<?php

namespace AweBooking\System\Validator;

use AweBooking\System\Valitron\Validator;
use Closure;

class ValidatorFactory
{
    /**
     * @var Closure
     */
    public static $dataResolver;

    /**
     * @param Closure|array $rules
     * @param array|null $data
     * @return Validator
     */
    public static function createValidation($rules, array $data = null): Validator
    {
        if ($data === null) {
            $data = static::resolveValidationData();
        }

        $langDir   = AWEPOINTMENT_PLUGIN_DIR_PATH . '/resources/lang';
        $validator = new Validator($data, [], 'validation', $langDir);

        if (is_array($rules)) {
            $validator->rules($rules);
        } elseif (is_callable($rules)) {
            $rules($validator);
        }

        return $validator;
    }

    /**
     * @return array
     */
    public static function resolveValidationData()
    {
        if (self::$dataResolver) {
            return call_user_func(self::$dataResolver);
        }

        return $_POST;
    }

    /**
     * @param Closure $dataResolver
     */
    public static function setDataResolver(Closure $dataResolver)
    {
        static::$dataResolver = $dataResolver;
    }
}
