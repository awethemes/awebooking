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

use AweBooking\Vendor\Symfony\Component\Notifier\Exception\LogicException;
use AweBooking\Vendor\Symfony\Component\Notifier\Exception\RuntimeException;
use AweBooking\Vendor\Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\MessageInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\SentMessage;
/**
 * Uses several Transports using a round robin algorithm.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RoundRobinTransport implements TransportInterface
{
    /**
     * @var \SplObjectStorage<TransportInterface, float>
     */
    private $deadTransports;
    private $transports = [];
    private $retryPeriod;
    private $cursor = -1;
    /**
     * @param TransportInterface[] $transports
     */
    public function __construct(array $transports, int $retryPeriod = 60)
    {
        if (!$transports) {
            throw new LogicException(\sprintf('"%s" must have at least one transport configured.', static::class));
        }
        $this->transports = $transports;
        $this->deadTransports = new \SplObjectStorage();
        $this->retryPeriod = $retryPeriod;
    }
    public function __toString() : string
    {
        return \implode(' ' . $this->getNameSymbol() . ' ', \array_map('strval', $this->transports));
    }
    public function supports(MessageInterface $message) : bool
    {
        foreach ($this->transports as $transport) {
            if ($transport->supports($message)) {
                return \true;
            }
        }
        return \false;
    }
    public function send(MessageInterface $message) : SentMessage
    {
        if (!$this->supports($message)) {
            throw new LogicException(\sprintf('None of the configured Transports of "%s" supports the given message.', static::class));
        }
        while ($transport = $this->getNextTransport($message)) {
            try {
                return $transport->send($message);
            } catch (TransportExceptionInterface $e) {
                $this->deadTransports[$transport] = \microtime(\true);
            }
        }
        throw new RuntimeException('All transports failed.');
    }
    /**
     * Rotates the transport list around and returns the first instance.
     */
    protected function getNextTransport(MessageInterface $message) : ?TransportInterface
    {
        if (-1 === $this->cursor) {
            $this->cursor = $this->getInitialCursor();
        }
        $cursor = $this->cursor;
        while (\true) {
            $transport = $this->transports[$cursor];
            if (!$transport->supports($message)) {
                $cursor = $this->moveCursor($cursor);
                continue;
            }
            if (!$this->isTransportDead($transport)) {
                break;
            }
            if (\microtime(\true) - $this->deadTransports[$transport] > $this->retryPeriod) {
                $this->deadTransports->detach($transport);
                break;
            }
            if ($this->cursor === ($cursor = $this->moveCursor($cursor))) {
                return null;
            }
        }
        $this->cursor = $this->moveCursor($cursor);
        return $transport;
    }
    protected function isTransportDead(TransportInterface $transport) : bool
    {
        return $this->deadTransports->contains($transport);
    }
    protected function getInitialCursor() : int
    {
        // the cursor initial value is randomized so that
        // when are not in a daemon, we are still rotating the transports
        return \mt_rand(0, \count($this->transports) - 1);
    }
    protected function getNameSymbol() : string
    {
        return '&&';
    }
    private function moveCursor(int $cursor) : int
    {
        return ++$cursor >= \count($this->transports) ? 0 : $cursor;
    }
}
