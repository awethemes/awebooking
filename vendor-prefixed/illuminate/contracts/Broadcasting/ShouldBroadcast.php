<?php

namespace AweBooking\Vendor\Illuminate\Contracts\Broadcasting;

interface ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \AweBooking\Vendor\Illuminate\Broadcasting\Channel|\AweBooking\Vendor\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn();
}
