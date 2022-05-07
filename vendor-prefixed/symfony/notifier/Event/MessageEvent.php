<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Event;

use AweBooking\Vendor\Symfony\Component\Notifier\Message\MessageInterface;
use AweBooking\Vendor\Symfony\Contracts\EventDispatcher\Event;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class MessageEvent extends Event
{
    private $message;
    private $queued;
    public function __construct(MessageInterface $message, bool $queued = \false)
    {
        $this->message = $message;
        $this->queued = $queued;
    }
    public function getMessage() : MessageInterface
    {
        return $this->message;
    }
    public function isQueued() : bool
    {
        return $this->queued;
    }
}
