<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier;

use AweBooking\Vendor\Symfony\Component\EventDispatcher\Event;
use AweBooking\Vendor\Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use AweBooking\Vendor\Symfony\Component\Messenger\MessageBusInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Event\MessageEvent;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\MessageInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\SentMessage;
use AweBooking\Vendor\Symfony\Component\Notifier\Transport\TransportInterface;
use AweBooking\Vendor\Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class Chatter implements ChatterInterface
{
    private $transport;
    private $bus;
    private $dispatcher;
    public function __construct(TransportInterface $transport, MessageBusInterface $bus = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->transport = $transport;
        $this->bus = $bus;
        $this->dispatcher = \class_exists(Event::class) ? LegacyEventDispatcherProxy::decorate($dispatcher) : $dispatcher;
    }
    public function __toString() : string
    {
        return 'chat';
    }
    public function supports(MessageInterface $message) : bool
    {
        return $this->transport->supports($message);
    }
    public function send(MessageInterface $message) : ?SentMessage
    {
        if (null === $this->bus) {
            return $this->transport->send($message);
        }
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(new MessageEvent($message, \true));
        }
        $this->bus->dispatch($message);
        return null;
    }
}
