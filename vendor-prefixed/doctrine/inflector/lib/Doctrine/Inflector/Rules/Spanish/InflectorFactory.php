<?php

declare (strict_types=1);
namespace Doctrine\Inflector\Rules\Spanish;

use Doctrine\Inflector\GenericLanguageInflectorFactory;
use Doctrine\Inflector\Rules\Ruleset;
final class InflectorFactory extends GenericLanguageInflectorFactory
{
    protected function getSingularRuleset() : Ruleset
    {
        return \Doctrine\Inflector\Rules\Spanish\Rules::getSingularRuleset();
    }
    protected function getPluralRuleset() : Ruleset
    {
        return \Doctrine\Inflector\Rules\Spanish\Rules::getPluralRuleset();
    }
}
