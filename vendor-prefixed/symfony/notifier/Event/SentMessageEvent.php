<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Event;

use AweBooking\Vendor\Symfony\Component\Notifier\Message\SentMessage;
use AweBooking\Vendor\Symfony\Contracts\EventDispatcher\Event;
/**
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
final class SentMessageEvent extends Event
{
    private $message;
    public function __construct(SentMessage $message)
    {
        $this->message = $message;
    }
    public function getMessage() : SentMessage
    {
        return $this->message;
    }
}
