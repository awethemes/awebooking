<?php

declare (strict_types=1);
namespace Doctrine\Inflector;

class NoopWordInflector implements \Doctrine\Inflector\WordInflector
{
    public function inflect(string $word) : string
    {
        return $word;
    }
}
