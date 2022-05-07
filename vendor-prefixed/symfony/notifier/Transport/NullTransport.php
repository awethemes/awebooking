<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Transport;

use AweBooking\Vendor\Symfony\Component\EventDispatcher\Event;
use AweBooking\Vendor\Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use AweBooking\Vendor\Symfony\Component\Notifier\Event\MessageEvent;
use AweBooking\Vendor\Symfony\Component\Notifier\Event\SentMessageEvent;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\MessageInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\NullMessage;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\SentMessage;
use AweBooking\Vendor\Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NullTransport implements TransportInterface
{
    private $dispatcher;
    public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = \class_exists(Event::class) ? LegacyEventDispatcherProxy::decorate($dispatcher) : $dispatcher;
    }
    public function send(MessageInterface $message) : SentMessage
    {
        $message = new NullMessage($message);
        $sentMessage = new SentMessage($message, (string) $this);
        if (null === $this->dispatcher) {
            return $sentMessage;
        }
        $this->dispatcher->dispatch(new MessageEvent($message));
        $this->dispatcher->dispatch(new SentMessageEvent($sentMessage));
        return $sentMessage;
    }
    public function __toString() : string
    {
        return 'null';
    }
    public function supports(MessageInterface $message) : bool
    {
        return \true;
    }
}
