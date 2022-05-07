<?php

declare (strict_types=1);
namespace Doctrine\Inflector\Rules;

final class Substitution
{
    /** @var Word */
    private $from;
    /** @var Word */
    private $to;
    public function __construct(\Doctrine\Inflector\Rules\Word $from, \Doctrine\Inflector\Rules\Word $to)
    {
        $this->from = $from;
        $this->to = $to;
    }
    public function getFrom() : \Doctrine\Inflector\Rules\Word
    {
        return $this->from;
    }
    public function getTo() : \Doctrine\Inflector\Rules\Word
    {
        return $this->to;
    }
}
