<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\DataCollector;

use AweBooking\Vendor\Symfony\Component\HttpFoundation\Request;
use AweBooking\Vendor\Symfony\Component\HttpFoundation\Response;
use AweBooking\Vendor\Symfony\Component\HttpKernel\DataCollector\DataCollector;
use AweBooking\Vendor\Symfony\Component\Notifier\Event\NotificationEvents;
use AweBooking\Vendor\Symfony\Component\Notifier\EventListener\NotificationLoggerListener;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class NotificationDataCollector extends DataCollector
{
    private $logger;
    public function __construct(NotificationLoggerListener $logger)
    {
        $this->logger = $logger;
    }
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->data['events'] = $this->logger->getEvents();
    }
    public function getEvents() : NotificationEvents
    {
        return $this->data['events'];
    }
    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return 'notifier';
    }
}
