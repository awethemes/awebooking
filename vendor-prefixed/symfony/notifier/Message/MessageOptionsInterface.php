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

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface MessageOptionsInterface
{
    public function toArray() : array;
    public function getRecipientId() : ?string;
}
