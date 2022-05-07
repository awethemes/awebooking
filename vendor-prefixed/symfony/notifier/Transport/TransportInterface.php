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

use AweBooking\Vendor\Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\MessageInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Message\SentMessage;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface TransportInterface
{
    /**
     * @throws TransportExceptionInterface
     */
    public function send(MessageInterface $message) : ?SentMessage;
    public function supports(MessageInterface $message) : bool;
    public function __toString() : string;
}
