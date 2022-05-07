<?php

declare (strict_types=1);
namespace Doctrine\Inflector;

use Doctrine\Inflector\Rules\Ruleset;
use function array_unshift;
abstract class GenericLanguageInflectorFactory implements \Doctrine\Inflector\LanguageInflectorFactory
{
    /** @var Ruleset[] */
    private $singularRulesets = [];
    /** @var Ruleset[] */
    private $pluralRulesets = [];
    public final function __construct()
    {
        $this->singularRulesets[] = $this->getSingularRuleset();
        $this->pluralRulesets[] = $this->getPluralRuleset();
    }
    public final function build() : \Doctrine\Inflector\Inflector
    {
        return new \Doctrine\Inflector\Inflector(new \Doctrine\Inflector\CachedWordInflector(new \Doctrine\Inflector\RulesetInflector(...$this->singularRulesets)), new \Doctrine\Inflector\CachedWordInflector(new \Doctrine\Inflector\RulesetInflector(...$this->pluralRulesets)));
    }
    public final function withSingularRules(?Ruleset $singularRules, bool $reset = \false) : \Doctrine\Inflector\LanguageInflectorFactory
    {
        if ($reset) {
            $this->singularRulesets = [];
        }
        if ($singularRules instanceof Ruleset) {
            array_unshift($this->singularRulesets, $singularRules);
        }
        return $this;
    }
    public final function withPluralRules(?Ruleset $pluralRules, bool $reset = \false) : \Doctrine\Inflector\LanguageInflectorFactory
    {
        if ($reset) {
            $this->pluralRulesets = [];
        }
        if ($pluralRules instanceof Ruleset) {
            array_unshift($this->pluralRulesets, $pluralRules);
        }
        return $this;
    }
    protected abstract function getSingularRuleset() : Ruleset;
    protected abstract function getPluralRuleset() : Ruleset;
}
