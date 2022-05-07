<?php

namespace AweBooking\System;

trait ContainerAwareTrait
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }
}
