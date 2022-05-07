<?php

namespace AweBooking\System\Notifier\Message;

use AweBooking\Vendor\Illuminate\Contracts\Support\Arrayable;
use AweBooking\Vendor\Symfony\Component\Notifier\Notification\Notification;

use function AweBooking\System\collect;

class MailMessage extends Notification
{
    /**
     * The "from" information for the message.
     *
     * @var array
     */
    public $from = [];

    /**
     * The "reply to" information for the message.
     *
     * @var array
     */
    public $replyTo = [];

    /**
     * The "cc" information for the message.
     *
     * @var array
     */
    public $cc = [];

    /**
     * The "bcc" information for the message.
     *
     * @var array
     */
    public $bcc = [];

    /**
     * The attachments for the message.
     *
     * @var array
     */
    public $attachments = [];

    /**
     * The "intro" lines of the notification.
     *
     * @var array
     */
    public $introLines = [];

    /**
     * The "outro" lines of the notification.
     *
     * @var array
     */
    public $outroLines = [];

    /**
     * @param Notification $notification
     * @return static
     */
    public static function fromNotification(Notification $notification)
    {
        if ($notification instanceof self) {
            return $notification;
        }

        return (new self())
            ->subject($notification->getSubject())
            ->content($notification->getContent());
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        $headers = [];

        if ($this->from) {
            $headers[] = $this->formatHeaderAddress('From', $this->from);
        }

        foreach ($this->replyTo as $address) {
            $headers[] = $this->formatHeaderAddress('Reply-To', $address);
        }

        foreach ($this->cc as $address) {
            $headers[] = $this->formatHeaderAddress('Cc', $address);
        }

        foreach ($this->bcc as $address) {
            $headers[] = $this->formatHeaderAddress('Bcc', $address);
        }

        // ['Content-Type' => stripos($content, '<html') === false ? 'text/plan' : 'text/html']

        return $headers;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return [];
    }

    /**
     * Set the from address for the mail message.
     *
     * @param string $address
     * @param string|null $name
     * @return $this
     */
    public function from($address, $name = null)
    {
        $this->from = [$address, $name];

        return $this;
    }

    /**
     * Set the "reply to" address of the message.
     *
     * @param array|string $address
     * @param string|null $name
     * @return $this
     */
    public function replyTo($address, $name = null)
    {
        if ($this->arrayOfAddresses($address)) {
            $this->replyTo += $this->parseAddresses($address);
        } else {
            $this->replyTo[] = [$address, $name];
        }

        return $this;
    }

    /**
     * Set the cc address for the mail message.
     *
     * @param array|string $address
     * @param string|null $name
     * @return $this
     */
    public function cc($address, $name = null)
    {
        if ($this->arrayOfAddresses($address)) {
            $this->cc += $this->parseAddresses($address);
        } else {
            $this->cc[] = [$address, $name];
        }

        return $this;
    }

    /**
     * Set the bcc address for the mail message.
     *
     * @param array|string $address
     * @param string|null $name
     * @return $this
     */
    public function bcc($address, $name = null)
    {
        if ($this->arrayOfAddresses($address)) {
            $this->bcc += $this->parseAddresses($address);
        } else {
            $this->bcc[] = [$address, $name];
        }

        return $this;
    }

    /**
     * Attach a file to the message.
     *
     * @param string $file
     * @param array $options
     * @return $this
     */
    public function attach($file, array $options = [])
    {
        $this->attachments[] = compact('file', 'options');

        return $this;
    }

    /**
     * Add a line of text to the notification.
     *
     * @param mixed $line
     * @return $this
     */
    public function line($line)
    {
        return $this->with($line);
    }

    /**
     * Add a line of text to the notification.
     *
     * @param mixed $line
     * @return $this
     */
    public function with($line)
    {
        if ($line instanceof Action) {
            $this->action($line->text, $line->url);
        } elseif (!$this->actionText) {
            $this->introLines[] = $this->formatLine($line);
        } else {
            $this->outroLines[] = $this->formatLine($line);
        }

        return $this;
    }

    /**
     * Format the given line of text.
     *
     * @param \Illuminate\Contracts\Support\Htmlable|string|array $line
     * @return \Illuminate\Contracts\Support\Htmlable|string
     */
    protected function formatLine($line)
    {
        if ($line instanceof Htmlable) {
            return $line;
        }

        if (is_array($line)) {
            return implode(' ', array_map('trim', $line));
        }

        return trim(implode(' ', array_map('trim', preg_split('/\\r\\n|\\r|\\n/', $line ?? ''))));
    }

    /**
     * @param string $type
     * @param array $address
     * @return string
     */
    protected function formatHeaderAddress($type, array $address)
    {
        [$email, $name] = $address;

        return $name
            ? sprintf('%s: %s <%s>', $type, $name, $email)
            : sprintf('%s: %s', $type, $email);
    }

    /**
     * Parse the multi-address array into the necessary format.
     *
     * @param array $value
     * @return array
     */
    protected function parseAddresses($value)
    {
        return collect($value)->map(function ($address, $name) {
            return [$address, is_numeric($name) ? null : $name];
        })->values()->all();
    }

    /**
     * Determine if the given "address" is actually an array of addresses.
     *
     * @param mixed $address
     * @return bool
     */
    protected function arrayOfAddresses($address)
    {
        return is_iterable($address) || $address instanceof Arrayable;
    }
}
