<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Message;

use AweBooking\Vendor\Symfony\Bridge\Twig\Mime\NotificationEmail;
use AweBooking\Vendor\Symfony\Component\Mailer\Envelope;
use AweBooking\Vendor\Symfony\Component\Mime\Email;
use AweBooking\Vendor\Symfony\Component\Mime\RawMessage;
use AweBooking\Vendor\Symfony\Component\Notifier\Exception\InvalidArgumentException;
use AweBooking\Vendor\Symfony\Component\Notifier\Exception\LogicException;
use AweBooking\Vendor\Symfony\Component\Notifier\Notification\Notification;
use AweBooking\Vendor\Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class EmailMessage implements MessageInterface
{
    private $message;
    private $envelope;
    public function __construct(RawMessage $message, Envelope $envelope = null)
    {
        $this->message = $message;
        $this->envelope = $envelope;
    }
    public static function fromNotification(Notification $notification, EmailRecipientInterface $recipient) : self
    {
        if ('' === $recipient->getEmail()) {
            throw new InvalidArgumentException(\sprintf('"%s" needs an email, it cannot be empty.', __CLASS__));
        }
        if (!\class_exists(NotificationEmail::class)) {
            $email = (new Email())->to($recipient->getEmail())->subject($notification->getSubject())->text($notification->getContent() ?: $notification->getSubject());
        } else {
            $email = (new NotificationEmail())->to($recipient->getEmail())->subject($notification->getSubject())->content($notification->getContent() ?: $notification->getSubject())->importance($notification->getImportance());
            if ($exception = $notification->getException()) {
                $email->exception($exception);
            }
        }
        return new self($email);
    }
    public function getMessage() : RawMessage
    {
        return $this->message;
    }
    public function getEnvelope() : ?Envelope
    {
        return $this->envelope;
    }
    /**
     * @return $this
     */
    public function envelope(Envelope $envelope) : self
    {
        $this->envelope = $envelope;
        return $this;
    }
    public function getSubject() : string
    {
        return '';
    }
    public function getRecipientId() : ?string
    {
        return null;
    }
    public function getOptions() : ?MessageOptionsInterface
    {
        return null;
    }
    /**
     * @return $this
     */
    public function transport(?string $transport) : self
    {
        if (!$this->message instanceof Email) {
            throw new LogicException('Cannot set a Transport on a RawMessage instance.');
        }
        if (null === $transport) {
            return $this;
        }
        $this->message->getHeaders()->addTextHeader('X-Transport', $transport);
        return $this;
    }
    public function getTransport() : ?string
    {
        return $this->message instanceof Email ? $this->message->getHeaders()->getHeaderBody('X-Transport') : null;
    }
}
