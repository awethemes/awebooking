<?php

namespace AweBooking\System\Bridge;

use AweBooking\System\EventDispatcher;
use AweBooking\Vendor\Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SymfonyEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(object $event, string $eventName = null): object
    {
        $eventName = $eventName ?? get_class($event);

        $this->eventDispatcher->dispatch($eventName, $event);

        return $event;
    }
}
