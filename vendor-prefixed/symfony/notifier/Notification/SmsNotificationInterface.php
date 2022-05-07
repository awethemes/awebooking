<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Notification;

use AweBooking\Vendor\Symfony\Component\Notifier\Message\SmsMessage;
use AweBooking\Vendor\Symfony\Component\Notifier\Recipient\SmsRecipientInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface SmsNotificationInterface
{
    public function asSmsMessage(SmsRecipientInterface $recipient, string $transport = null) : ?SmsMessage;
}
