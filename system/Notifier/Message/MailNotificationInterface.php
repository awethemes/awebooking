<?php

namespace AweBooking\System\Notifier\Message;

use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

interface MailNotificationInterface
{
    /**
     * @param EmailRecipientInterface $recipient
     * @param string|null $transport
     * @return mixed
     */
    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null);
}
