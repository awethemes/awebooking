<?php

namespace AweBooking\PMS\Contracts;

use AweBooking\System\Container;

interface InitializableInterface
{
    /**
     * @param Container $container
     * @return void
     */
    public function initialize(Container $container): void;
}
