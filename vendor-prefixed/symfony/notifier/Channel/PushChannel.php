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

use AweBooking\Vendor\Symfony\Component\Notifier\Message\PushMessage;
use AweBooking\Vendor\Symfony\Component\Notifier\Notification\Notification;
use AweBooking\Vendor\Symfony\Component\Notifier\Notification\PushNotificationInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Recipient\RecipientInterface;
/**
 * @author Tomas Norkūnas <norkunas.tom@gmail.com>
 */
class PushChannel extends AbstractChannel
{
    public function notify(Notification $notification, RecipientInterface $recipient, string $transportName = null) : void
    {
        $message = null;
        if ($notification instanceof PushNotificationInterface) {
            $message = $notification->asPushMessage($recipient, $transportName);
        }
        if (null === $message) {
            $message = PushMessage::fromNotification($notification);
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
        return \true;
    }
}
