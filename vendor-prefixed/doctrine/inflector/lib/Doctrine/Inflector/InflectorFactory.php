<?php

declare (strict_types=1);
namespace Doctrine\Inflector;

use Doctrine\Inflector\Rules\English;
use Doctrine\Inflector\Rules\French;
use Doctrine\Inflector\Rules\NorwegianBokmal;
use Doctrine\Inflector\Rules\Portuguese;
use Doctrine\Inflector\Rules\Spanish;
use Doctrine\Inflector\Rules\Turkish;
use InvalidArgumentException;
use function sprintf;
final class InflectorFactory
{
    public static function create() : \Doctrine\Inflector\LanguageInflectorFactory
    {
        return self::createForLanguage(\Doctrine\Inflector\Language::ENGLISH);
    }
    public static function createForLanguage(string $language) : \Doctrine\Inflector\LanguageInflectorFactory
    {
        switch ($language) {
            case \Doctrine\Inflector\Language::ENGLISH:
                return new English\InflectorFactory();
            case \Doctrine\Inflector\Language::FRENCH:
                return new French\InflectorFactory();
            case \Doctrine\Inflector\Language::NORWEGIAN_BOKMAL:
                return new NorwegianBokmal\InflectorFactory();
            case \Doctrine\Inflector\Language::PORTUGUESE:
                return new Portuguese\InflectorFactory();
            case \Doctrine\Inflector\Language::SPANISH:
                return new Spanish\InflectorFactory();
            case \Doctrine\Inflector\Language::TURKISH:
                return new Turkish\InflectorFactory();
            default:
                throw new InvalidArgumentException(sprintf('Language "%s" is not supported.', $language));
        }
    }
}
