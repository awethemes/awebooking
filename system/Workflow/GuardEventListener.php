<?php

namespace AweBooking\System\Workflow;

use AweBooking\System\Access;
use AweBooking\Vendor\Symfony\Component\Workflow\Event\GuardEvent;

class GuardEventListener
{
    /**
     * @var Access
     */
    private $access;

    public function __construct(Access $access)
    {
        $this->access = $access;
    }

    public function handle(GuardEvent $event)
    {
        $name = $event->getTransition()->getName();

        if (!$this->access->allows($name, $event->getSubject())) {
            $event->setBlocked(true);
        }
    }
}
