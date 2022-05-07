<?php

namespace AweBooking\System\Notifier\Channel;

use AweBooking\System\Notifier\Message\MailMessage;
use AweBooking\System\Notifier\Message\MailNotificationInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Channel\ChannelInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Exception\LogicException;
use AweBooking\Vendor\Symfony\Component\Notifier\Notification\Notification;
use AweBooking\Vendor\Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Recipient\RecipientInterface;
use WC_Email;

class WPMailChannel implements ChannelInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(Notification $notification, RecipientInterface $recipient): bool
    {
        return $recipient instanceof EmailRecipientInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(
        Notification $notification,
        RecipientInterface $recipient,
        string $transportName = null
    ): void {
        $message = null;

        if ($notification instanceof MailNotificationInterface) {
            // Load the WC-Mailer to ensure the WC_Mail exists before call asEmailMessage().
            if (class_exists('WooCommerce')) {
                WC()->mailer();
            }

            $message = $notification->asEmailMessage($recipient, $transportName);

            // Trigger send WC_Email.
            if ($message instanceof WC_Email) {
                if (!$message->recipient) {
                    $message->recipient = $recipient->getEmail();
                }

                $this->triggerSendWCEmail($message);

                return;
            }

            if (!$message instanceof MailMessage) {
                throw new LogicException(
                    get_class($notification) . '::asEmailMessage() must return a MailMessage or WC_Email instance.'
                );
            }
        }

        if ($message === null) {
            $message = MailMessage::fromNotification($notification);
        }

        wp_mail(
            $recipient->getEmail(),
            $message->getSubject(),
            $message->getContent(),
            $message->getHeaders(),
            $message->getAttachments()
        );
    }

    /**
     * Trigger send WC_Email message.
     *
     * @param WC_Email $message
     */
    private function triggerSendWCEmail(WC_Email $message)
    {
        if (!$message->is_enabled()) {
            return;
        }

        $message->setup_locale();

        $message->send(
            $message->get_recipient(),
            $message->get_subject(),
            $message->get_content(),
            $message->get_headers(),
            $message->get_attachments()
        );

        $message->restore_locale();
    }
}
