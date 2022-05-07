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

use AweBooking\Vendor\Symfony\Component\Notifier\Notification\Notification;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class ChatMessage implements MessageInterface
{
    private $transport;
    private $subject;
    private $options;
    private $notification;
    public function __construct(string $subject, MessageOptionsInterface $options = null)
    {
        $this->subject = $subject;
        $this->options = $options;
    }
    public static function fromNotification(Notification $notification) : self
    {
        $message = new self($notification->getSubject());
        $message->notification = $notification;
        return $message;
    }
    /**
     * @return $this
     */
    public function subject(string $subject) : self
    {
        $this->subject = $subject;
        return $this;
    }
    public function getSubject() : string
    {
        return $this->subject;
    }
    public function getRecipientId() : ?string
    {
        return $this->options ? $this->options->getRecipientId() : null;
    }
    /**
     * @return $this
     */
    public function options(MessageOptionsInterface $options) : self
    {
        $this->options = $options;
        return $this;
    }
    public function getOptions() : ?MessageOptionsInterface
    {
        return $this->options;
    }
    /**
     * @return $this
     */
    public function transport(?string $transport) : self
    {
        $this->transport = $transport;
        return $this;
    }
    public function getTransport() : ?string
    {
        return $this->transport;
    }
    public function getNotification() : ?Notification
    {
        return $this->notification;
    }
}
