<?php

namespace AweBooking\System\Queue;

use AweBooking\System\Container;

final class PendingJobDispatch extends QueueJobDispatch
{
    /**
     * Determine if the job should be dispatched.
     *
     * @return bool
     */
    protected function shouldDispatch(): bool
    {
        return true;
    }

    /**
     * Handle the object's destruction.
     *
     * @return void
     */
    public function __destruct()
    {
        if (!$this->shouldDispatch()) {
            return;
        }

        Container::getInstance()->get(Dispatcher::class)
            ->dispatch($this);
    }
}
