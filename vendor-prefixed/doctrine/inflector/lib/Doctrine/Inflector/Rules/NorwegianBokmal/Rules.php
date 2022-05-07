<?php

declare (strict_types=1);
namespace Doctrine\Inflector\Rules\NorwegianBokmal;

use Doctrine\Inflector\Rules\Patterns;
use Doctrine\Inflector\Rules\Ruleset;
use Doctrine\Inflector\Rules\Substitutions;
use Doctrine\Inflector\Rules\Transformations;
final class Rules
{
    public static function getSingularRuleset() : Ruleset
    {
        return new Ruleset(new Transformations(...\Doctrine\Inflector\Rules\NorwegianBokmal\Inflectible::getSingular()), new Patterns(...\Doctrine\Inflector\Rules\NorwegianBokmal\Uninflected::getSingular()), (new Substitutions(...\Doctrine\Inflector\Rules\NorwegianBokmal\Inflectible::getIrregular()))->getFlippedSubstitutions());
    }
    public static function getPluralRuleset() : Ruleset
    {
        return new Ruleset(new Transformations(...\Doctrine\Inflector\Rules\NorwegianBokmal\Inflectible::getPlural()), new Patterns(...\Doctrine\Inflector\Rules\NorwegianBokmal\Uninflected::getPlural()), new Substitutions(...\Doctrine\Inflector\Rules\NorwegianBokmal\Inflectible::getIrregular()));
    }
}
