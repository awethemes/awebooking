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

use AweBooking\Vendor\Symfony\Component\Notifier\Exception\IncompleteDsnException;
use AweBooking\Vendor\Symfony\Component\Notifier\Exception\MissingRequiredOptionException;
use AweBooking\Vendor\Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
interface TransportFactoryInterface
{
    /**
     * @throws UnsupportedSchemeException
     * @throws IncompleteDsnException
     * @throws MissingRequiredOptionException
     */
    public function create(Dsn $dsn) : TransportInterface;
    public function supports(Dsn $dsn) : bool;
}
