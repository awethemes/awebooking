<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Messenger;

use AweBooking\Vendor\Symfony\Component\Notifier\Message\MessageInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\SentMessage;
use AweBooking\Vendor\Symfony\Component\Notifier\Transport\TransportInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class MessageHandler
{
    private $transport;
    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }
    public function __invoke(MessageInterface $message) : ?SentMessage
    {
        return $this->transport->send($message);
    }
}
