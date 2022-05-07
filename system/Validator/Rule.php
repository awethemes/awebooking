<?php

namespace AweBooking\System\Validator;

use ReflectionClass;

class Rule
{
    public const REQUIRED = 'required';
    public const REQUIRED_WITH = 'requiredWith';
    public const REQUIRED_WITHOUT = 'requiredWithout';
    public const EQUALS = 'equals';
    public const DIFFERENT = 'different';
    public const ACCEPTED = 'accepted';
    public const NUMERIC = 'numeric';
    public const INTEGER = 'integer';
    public const BOOLEAN = 'boolean';
    public const ARRAY = 'array';
    public const LENGTH = 'length';
    public const LENGTH_BETWEEN = 'lengthBetween';
    public const LENGTH_MIN = 'lengthMin';
    public const LENGTH_MAX = 'lengthMax';
    public const MIN = 'min';
    public const MAX = 'max';
    public const LIST_CONTAINS = 'listContains';
    public const IN = 'in';
    public const NOTIN = 'notIn';
    public const IP = 'ip';
    public const IPV4 = 'ipv4';
    public const IPV6 = 'ipv6';
    public const EMAIL = 'email';
    public const EMAIL_DNS = 'emailDNS';
    public const URL = 'url';
    public const URL_ACTIVE = 'urlActive';
    public const ALPHA = 'alpha';
    public const ALPHA_NUM = 'alphaNum';
    public const ASCII = 'ascii';
    public const SLUG = 'slug';
    public const REGEX = 'regex';
    public const DATE = 'date';
    public const DATEFORMAT = 'dateFormat';
    public const DATE_BEFORE = 'dateBefore';
    public const DATE_AFTER = 'dateAfter';
    public const CONTAINS = 'contains';
    public const SUBSET = 'subset';
    public const CONTAINS_UNIQUE = 'containsUnique';
    public const CREDIT_CARD = 'creditCard';
    public const INSTANCEOF = 'instanceOf';
    public const OPTIONAL = 'optional';
    public const ARRAY_HAS_KEYS = 'arrayHasKeys';

    /**
     * @var string[]
     */
    private static $rules;

    public static function getRules(): array
    {
        if (static::$rules === null) {
            $constants = (new ReflectionClass(__CLASS__))->getConstants();

            static::$rules = array_values($constants);
        }

        return static::$rules;
    }

    public static function __callStatic(string $method, array $parameters)
    {
    }
}
