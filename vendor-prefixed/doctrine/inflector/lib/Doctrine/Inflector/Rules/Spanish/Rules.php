<?php

declare (strict_types=1);
namespace Doctrine\Inflector\Rules\Spanish;

use Doctrine\Inflector\Rules\Patterns;
use Doctrine\Inflector\Rules\Ruleset;
use Doctrine\Inflector\Rules\Substitutions;
use Doctrine\Inflector\Rules\Transformations;
final class Rules
{
    public static function getSingularRuleset() : Ruleset
    {
        return new Ruleset(new Transformations(...\Doctrine\Inflector\Rules\Spanish\Inflectible::getSingular()), new Patterns(...\Doctrine\Inflector\Rules\Spanish\Uninflected::getSingular()), (new Substitutions(...\Doctrine\Inflector\Rules\Spanish\Inflectible::getIrregular()))->getFlippedSubstitutions());
    }
    public static function getPluralRuleset() : Ruleset
    {
        return new Ruleset(new Transformations(...\Doctrine\Inflector\Rules\Spanish\Inflectible::getPlural()), new Patterns(...\Doctrine\Inflector\Rules\Spanish\Uninflected::getPlural()), new Substitutions(...\Doctrine\Inflector\Rules\Spanish\Inflectible::getIrregular()));
    }
}
