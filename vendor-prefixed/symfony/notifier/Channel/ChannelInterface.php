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

use AweBooking\Vendor\Symfony\Component\Notifier\Notification\Notification;
use AweBooking\Vendor\Symfony\Component\Notifier\Recipient\RecipientInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface ChannelInterface
{
    public function notify(Notification $notification, RecipientInterface $recipient, string $transportName = null) : void;
    public function supports(Notification $notification, RecipientInterface $recipient) : bool;
}
