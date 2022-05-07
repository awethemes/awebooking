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
use AweBooking\Vendor\Symfony\Component\HttpClient\HttpClient;
use AweBooking\Vendor\Symfony\Component\Notifier\Event\FailedMessageEvent;
use AweBooking\Vendor\Symfony\Component\Notifier\Event\MessageEvent;
use AweBooking\Vendor\Symfony\Component\Notifier\Event\SentMessageEvent;
use AweBooking\Vendor\Symfony\Component\Notifier\Exception\LogicException;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\MessageInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\SentMessage;
use AweBooking\Vendor\Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use AweBooking\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class AbstractTransport implements TransportInterface
{
    protected const HOST = 'localhost';
    private $dispatcher;
    protected $client;
    protected $host;
    protected $port;
    public function __construct(HttpClientInterface $client = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->client = $client;
        if (null === $client) {
            if (!\class_exists(HttpClient::class)) {
                throw new LogicException(\sprintf('You cannot use "%s" as the HttpClient component is not installed. Try running "composer require symfony/http-client".', __CLASS__));
            }
            $this->client = HttpClient::create();
        }
        $this->dispatcher = \class_exists(Event::class) ? LegacyEventDispatcherProxy::decorate($dispatcher) : $dispatcher;
    }
    /**
     * @return $this
     */
    public function setHost(?string $host) : self
    {
        $this->host = $host;
        return $this;
    }
    /**
     * @return $this
     */
    public function setPort(?int $port) : self
    {
        $this->port = $port;
        return $this;
    }
    public function send(MessageInterface $message) : SentMessage
    {
        if (null === $this->dispatcher) {
            return $this->doSend($message);
        }
        $this->dispatcher->dispatch(new MessageEvent($message));
        try {
            $sentMessage = $this->doSend($message);
        } catch (\Throwable $error) {
            $this->dispatcher->dispatch(new FailedMessageEvent($message, $error));
            throw $error;
        }
        $this->dispatcher->dispatch(new SentMessageEvent($sentMessage));
        return $sentMessage;
    }
    protected abstract function doSend(MessageInterface $message) : SentMessage;
    protected function getEndpoint() : string
    {
        return ($this->host ?: $this->getDefaultHost()) . ($this->port ? ':' . $this->port : '');
    }
    protected function getDefaultHost() : string
    {
        return static::HOST;
    }
}
