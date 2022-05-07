<?php

namespace Awepointment\BusinessTime;

use SplObjectStorage;

class DateObjectStorage extends SplObjectStorage
{
    /**
     * @var string
     */
    private $hashFormat;

    /**
     * @param string $hashFormat
     */
    public function __construct(string $hashFormat)
    {
        $this->hashFormat = $hashFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash($object)
    {
        return $object->format($this->hashFormat);
    }
}
