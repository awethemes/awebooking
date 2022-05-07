<?php

namespace AweBooking\Vendor\Illuminate\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string|null  $name
     * @return \AweBooking\Vendor\Illuminate\Contracts\Queue\Queue
     */
    public function connection($name = null);
}
