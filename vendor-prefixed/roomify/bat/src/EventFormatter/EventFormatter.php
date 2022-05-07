<?php

/**
 * @file
 * Interface EventFormatter
 */
namespace AweBooking\Vendor\Roomify\Bat\EventFormatter;

use AweBooking\Vendor\Roomify\Bat\Event\EventInterface;
interface EventFormatter
{
    /**
     * @param \Roomify\Bat\Event\EventInterface $event
     */
    public function format(EventInterface $event);
}
