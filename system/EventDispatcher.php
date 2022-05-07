<?php

namespace AweBooking\System;

use AweBooking\Vendor\Illuminate\Events\Dispatcher;
use LogicException;

class EventDispatcher extends Dispatcher
{
    /**
     * {@inheritdoc}
     */
    protected function broadcastEvent($event)
    {
        throw new LogicException('Broadcast event is not supported yet');
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveQueue()
    {
        throw new LogicException('Queue event is not supported yet');
    }

    /**
     * {@inheritdoc}
     */
    protected function handlerShouldBeQueued($class)
    {
        return false;
    }
}
