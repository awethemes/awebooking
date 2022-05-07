<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Channel;

use AweBooking\Vendor\Symfony\Component\HttpFoundation\RequestStack;
use AweBooking\Vendor\Symfony\Component\Notifier\Notification\Notification;
use AweBooking\Vendor\Symfony\Component\Notifier\Recipient\RecipientInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class BrowserChannel implements ChannelInterface
{
    private $stack;
    public function __construct(RequestStack $stack)
    {
        $this->stack = $stack;
    }
    public function notify(Notification $notification, RecipientInterface $recipient, string $transportName = null) : void
    {
        if (null === ($request = $this->stack->getCurrentRequest())) {
            return;
        }
        $message = $notification->getSubject();
        if ($notification->getEmoji()) {
            $message = $notification->getEmoji() . ' ' . $message;
        }
        $request->getSession()->getFlashBag()->add('notification', $message);
    }
    public function supports(Notification $notification, RecipientInterface $recipient) : bool
    {
        return \true;
    }
}
