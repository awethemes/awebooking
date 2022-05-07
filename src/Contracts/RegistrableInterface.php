<?php

namespace AweBooking\PMS\Contracts;

use AweBooking\System\Container;

interface RegistrableInterface
{
    /**
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void;
}
