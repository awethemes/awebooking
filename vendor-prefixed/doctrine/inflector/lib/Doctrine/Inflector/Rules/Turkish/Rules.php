<?php

declare (strict_types=1);
namespace Doctrine\Inflector\Rules\Turkish;

use Doctrine\Inflector\Rules\Patterns;
use Doctrine\Inflector\Rules\Ruleset;
use Doctrine\Inflector\Rules\Substitutions;
use Doctrine\Inflector\Rules\Transformations;
final class Rules
{
    public static function getSingularRuleset() : Ruleset
    {
        return new Ruleset(new Transformations(...\Doctrine\Inflector\Rules\Turkish\Inflectible::getSingular()), new Patterns(...\Doctrine\Inflector\Rules\Turkish\Uninflected::getSingular()), (new Substitutions(...\Doctrine\Inflector\Rules\Turkish\Inflectible::getIrregular()))->getFlippedSubstitutions());
    }
    public static function getPluralRuleset() : Ruleset
    {
        return new Ruleset(new Transformations(...\Doctrine\Inflector\Rules\Turkish\Inflectible::getPlural()), new Patterns(...\Doctrine\Inflector\Rules\Turkish\Uninflected::getPlural()), new Substitutions(...\Doctrine\Inflector\Rules\Turkish\Inflectible::getIrregular()));
    }
}
