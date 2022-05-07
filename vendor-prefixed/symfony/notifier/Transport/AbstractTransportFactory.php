<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Transport;

use AweBooking\Vendor\Symfony\Component\EventDispatcher\Event;
use AweBooking\Vendor\Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use AweBooking\Vendor\Symfony\Component\Notifier\Exception\IncompleteDsnException;
use AweBooking\Vendor\Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use AweBooking\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class AbstractTransportFactory implements TransportFactoryInterface
{
    protected $dispatcher;
    protected $client;
    public function __construct(EventDispatcherInterface $dispatcher = null, HttpClientInterface $client = null)
    {
        $this->dispatcher = \class_exists(Event::class) ? LegacyEventDispatcherProxy::decorate($dispatcher) : $dispatcher;
        $this->client = $client;
    }
    public function supports(Dsn $dsn) : bool
    {
        return \in_array($dsn->getScheme(), $this->getSupportedSchemes());
    }
    /**
     * @return string[]
     */
    protected abstract function getSupportedSchemes() : array;
    protected function getUser(Dsn $dsn) : string
    {
        $user = $dsn->getUser();
        if (null === $user) {
            throw new IncompleteDsnException('User is not set.', $dsn->getOriginalDsn());
        }
        return $user;
    }
    protected function getPassword(Dsn $dsn) : string
    {
        $password = $dsn->getPassword();
        if (null === $password) {
            throw new IncompleteDsnException('Password is not set.', $dsn->getOriginalDsn());
        }
        return $password;
    }
}
