<?php

declare (strict_types=1);
namespace Doctrine\Inflector\Rules\French;

use Doctrine\Inflector\GenericLanguageInflectorFactory;
use Doctrine\Inflector\Rules\Ruleset;
final class InflectorFactory extends GenericLanguageInflectorFactory
{
    protected function getSingularRuleset() : Ruleset
    {
        return \Doctrine\Inflector\Rules\French\Rules::getSingularRuleset();
    }
    protected function getPluralRuleset() : Ruleset
    {
        return \Doctrine\Inflector\Rules\French\Rules::getPluralRuleset();
    }
}
