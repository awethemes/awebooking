<?php

namespace AweBooking\PMS\Jobs;

use AweBooking\System\Queue\Job;

class TestJob extends Job
{
    /**
     * {@inheritdoc}
     */
    public function perform()
    {
        // TODO: Implement perform() method.
        dump('asdasd', $this);
    }
}
