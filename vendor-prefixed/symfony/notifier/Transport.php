<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier;

use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\AllMySms\AllMySmsTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\AmazonSns\AmazonSnsTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Clickatell\ClickatellTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Discord\DiscordTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Esendex\EsendexTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Expo\ExpoTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Firebase\FirebaseTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\FreeMobile\FreeMobileTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\GatewayApi\GatewayApiTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Gitter\GitterTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Infobip\InfobipTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Iqsms\IqsmsTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\LightSms\LightSmsTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Mailjet\MailjetTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Mattermost\MattermostTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\MessageBird\MessageBirdTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\MessageMedia\MessageMediaTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\MicrosoftTeams\MicrosoftTeamsTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Mobyt\MobytTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Nexmo\NexmoTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Octopush\OctopushTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\OvhCloud\OvhCloudTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\RocketChat\RocketChatTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Sendinblue\SendinblueTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Sinch\SinchTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Slack\SlackTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Sms77\Sms77TransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Smsapi\SmsapiTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\SmsBiuras\SmsBiurasTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Smsc\SmscTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Telegram\TelegramTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Telnyx\TelnyxTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\TurboSms\TurboSmsTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Twilio\TwilioTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Vonage\VonageTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Yunpian\YunpianTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Bridge\Zulip\ZulipTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use AweBooking\Vendor\Symfony\Component\Notifier\Transport\Dsn;
use AweBooking\Vendor\Symfony\Component\Notifier\Transport\FailoverTransport;
use AweBooking\Vendor\Symfony\Component\Notifier\Transport\NullTransportFactory;
use AweBooking\Vendor\Symfony\Component\Notifier\Transport\RoundRobinTransport;
use AweBooking\Vendor\Symfony\Component\Notifier\Transport\TransportFactoryInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Transport\TransportInterface;
use AweBooking\Vendor\Symfony\Component\Notifier\Transport\Transports;
use AweBooking\Vendor\Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use AweBooking\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final since Symfony 5.4
 */
class Transport
{
    private const FACTORY_CLASSES = [AllMySmsTransportFactory::class, AmazonSnsTransportFactory::class, ClickatellTransportFactory::class, DiscordTransportFactory::class, EsendexTransportFactory::class, ExpoTransportFactory::class, FirebaseTransportFactory::class, FreeMobileTransportFactory::class, GatewayApiTransportFactory::class, GitterTransportFactory::class, InfobipTransportFactory::class, IqsmsTransportFactory::class, LightSmsTransportFactory::class, MailjetTransportFactory::class, MattermostTransportFactory::class, MessageBirdTransportFactory::class, MessageMediaTransportFactory::class, MicrosoftTeamsTransportFactory::class, MobytTransportFactory::class, NexmoTransportFactory::class, OctopushTransportFactory::class, OvhCloudTransportFactory::class, RocketChatTransportFactory::class, SendinblueTransportFactory::class, SinchTransportFactory::class, SlackTransportFactory::class, Sms77TransportFactory::class, SmsapiTransportFactory::class, SmsBiurasTransportFactory::class, SmscTransportFactory::class, TelegramTransportFactory::class, TelnyxTransportFactory::class, TurboSmsTransportFactory::class, TwilioTransportFactory::class, VonageTransportFactory::class, YunpianTransportFactory::class, ZulipTransportFactory::class];
    private $factories;
    public static function fromDsn(string $dsn, EventDispatcherInterface $dispatcher = null, HttpClientInterface $client = null) : TransportInterface
    {
        $factory = new self(self::getDefaultFactories($dispatcher, $client));
        return $factory->fromString($dsn);
    }
    public static function fromDsns(array $dsns, EventDispatcherInterface $dispatcher = null, HttpClientInterface $client = null) : TransportInterface
    {
        $factory = new self(\iterator_to_array(self::getDefaultFactories($dispatcher, $client)));
        return $factory->fromStrings($dsns);
    }
    /**
     * @param TransportFactoryInterface[] $factories
     */
    public function __construct(iterable $factories)
    {
        $this->factories = $factories;
    }
    public function fromStrings(array $dsns) : Transports
    {
        $transports = [];
        foreach ($dsns as $name => $dsn) {
            $transports[$name] = $this->fromString($dsn);
        }
        return new Transports($transports);
    }
    public function fromString(string $dsn) : TransportInterface
    {
        $dsns = \preg_split('/\\s++\\|\\|\\s++/', $dsn);
        if (\count($dsns) > 1) {
            return new FailoverTransport($this->createFromDsns($dsns));
        }
        $dsns = \preg_split('/\\s++&&\\s++/', $dsn);
        if (\count($dsns) > 1) {
            return new RoundRobinTransport($this->createFromDsns($dsns));
        }
        return $this->fromDsnObject(new Dsn($dsn));
    }
    public function fromDsnObject(Dsn $dsn) : TransportInterface
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($dsn)) {
                return $factory->create($dsn);
            }
        }
        throw new UnsupportedSchemeException($dsn);
    }
    /**
     * @return TransportInterface[]
     */
    private function createFromDsns(array $dsns) : array
    {
        $transports = [];
        foreach ($dsns as $dsn) {
            $transports[] = $this->fromDsnObject(new Dsn($dsn));
        }
        return $transports;
    }
    /**
     * @return TransportFactoryInterface[]
     */
    private static function getDefaultFactories(EventDispatcherInterface $dispatcher = null, HttpClientInterface $client = null) : iterable
    {
        foreach (self::FACTORY_CLASSES as $factoryClass) {
            if (\class_exists($factoryClass)) {
                (yield new $factoryClass($dispatcher, $client));
            }
        }
        (yield new NullTransportFactory($dispatcher, $client));
    }
}
