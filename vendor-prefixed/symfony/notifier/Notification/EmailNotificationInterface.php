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

use AweBooking\Vendor\Symfony\Component\Notifier\Message\EmailMessage;
use AweBooking\Vendor\Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface EmailNotificationInterface
{
    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null) : ?EmailMessage;
}
