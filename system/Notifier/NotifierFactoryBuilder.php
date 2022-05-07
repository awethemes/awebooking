<?php

namespace AweBooking\System\Notifier;

use AweBooking\System\Bridge\SymfonyEventDispatcher;
use AweBooking\System\Container;
use AweBooking\Vendor\Symfony\Component\HttpClient\HttpClient;
use AweBooking\Vendor\Symfony\Component\Notifier\Channel\ChannelPolicy;
use AweBooking\Vendor\Symfony\Component\Notifier\Channel\ChatChannel;
use AweBooking\Vendor\Symfony\Component\Notifier\Channel\SmsChannel;
use AweBooking\Vendor\Symfony\Component\Notifier\Chatter;
use AweBooking\Vendor\Symfony\Component\Notifier\Notifier;
use AweBooking\Vendor\Symfony\Component\Notifier\Notification\Notification;
use AweBooking\Vendor\Symfony\Component\Notifier\Texter;
use AweBooking\Vendor\Symfony\Component\Notifier\Transport;

class NotifierFactoryBuilder
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    private $channelPolicy = [];

    /**
     * @var array
     */
    private $chatterTransports = [];

    /**
     * @var array
     */
    private $texterTransports = [];

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->registerBinding();
    }

    /**
     * Register binding into the container.
     *
     * @return void
     */
    private function registerBinding()
    {
        $this->container->bind('notifier.http_client', function () {
            return HttpClient::create();
        });

        $this->container->singleton('chatter.transports', function () {
            return Transport::fromDsns(
                $this->chatterTransports,
                $this->container->get(SymfonyEventDispatcher::class),
                $this->container->get('notifier.http_client')
            );
        });

        $this->container->singleton('texter.transports', function () {
            return Transport::fromDsns(
                $this->texterTransports,
                $this->container->get(SymfonyEventDispatcher::class),
                $this->container->get('notifier.http_client')
            );
        });
    }

    /**
     * Configuring channel policies.
     *
     * @param string $name
     * @param array $channels
     * @return $this
     *
     * @example
     * ```php
     * $builder->channelPolicy(Notification::IMPORTANCE_URGENT', ['sms', 'chat/slack', 'mail'])
     * ```
     *
     * @see Notification::IMPORTANCE_LOW
     * @see Notification::IMPORTANCE_URGENT
     * @see Notification::IMPORTANCE_HIGH
     * @see Notification::IMPORTANCE_MEDIUM
     */
    public function channelPolicy(string $name, array $channels)
    {
        $this->channelPolicy[$name] = $channels;

        return $this;
    }

    /**
     * Add a chat transport.
     *
     * @param string $name
     * @param string $dsnString
     * @return $this
     */
    public function chatTransport(string $name, string $dsnString)
    {
        $this->chatterTransports[$name] = $dsnString;

        return $this;
    }

    /**
     * Add a texter transport.
     *
     * @param string $name
     * @param string $dsnString
     * @return $this
     */
    public function texterTransport(string $name, string $dsnString)
    {
        $this->texterTransports[$name] = $dsnString;

        return $this;
    }

    /**
     * Create the Notifier.
     *
     * @param bool $enableChatChannel
     * @param bool $enableSmsChannel
     * @return Notifier
     */
    public function createNotifier($enableChatChannel = true, $enableSmsChannel = true): Notifier
    {
        $channels = ['mail' => new Channel\WPMailChannel()];

        if ($enableChatChannel) {
            $channels['chat'] = new ChatChannel($this->container->get('chatter.transports'));
        }

        if ($enableSmsChannel) {
            $channels['sms'] = new SmsChannel($this->container->get('texter.transports'));
        }

        return new Notifier(
            $channels,
            new ChannelPolicy($this->channelPolicy)
        );
    }

    /**
     * Create the Chatter.
     *
     * @return Chatter
     */
    public function createChatter()
    {
        return new Chatter(
            $this->container->get('chatter.transports'),
            null,
            $this->container->get(SymfonyEventDispatcher::class)
        );
    }

    /**
     * Create the Texter.
     *
     * @return Texter
     */
    public function createTexter()
    {
        return new Texter(
            $this->container->get('texter.transports'),
            null,
            $this->container->get(SymfonyEventDispatcher::class)
        );
    }
}
