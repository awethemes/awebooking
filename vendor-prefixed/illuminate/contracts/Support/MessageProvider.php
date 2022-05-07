<?php

namespace AweBooking\Vendor\Illuminate\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \AweBooking\Vendor\Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}
