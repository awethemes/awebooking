<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Channel;

use AweBooking\Vendor\Symfony\Component\Notifier\Message\SmsMessage;
use AweBooking\Vendor\Symfony\Component\Notifier\Notification\Notification;
use AweBooking\Vendor\Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Recipient\RecipientInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Recipient\SmsRecipientInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SmsChannel extends AbstractChannel
{
    public function notify(Notification $notification, RecipientInterface $recipient, string $transportName = null) : void
    {
        $message = null;
        if ($notification instanceof SmsNotificationInterface) {
            $message = $notification->asSmsMessage($recipient, $transportName);
        }
        if (null === $message) {
            $message = SmsMessage::fromNotification($notification, $recipient);
        }
        if (null !== $transportName) {
            $message->transport($transportName);
        }
        if (null === $this->bus) {
            $this->transport->send($message);
        } else {
            $this->bus->dispatch($message);
        }
    }
    public function supports(Notification $notification, RecipientInterface $recipient) : bool
    {
        return $recipient instanceof SmsRecipientInterface;
    }
}
