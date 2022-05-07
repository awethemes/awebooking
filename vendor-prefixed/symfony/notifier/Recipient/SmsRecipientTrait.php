<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Recipient;

/**
 * @author Jan Schädlich <jan.schaedlich@sensiolabs.de>
 */
trait SmsRecipientTrait
{
    private $phone;
    public function getPhone() : string
    {
        return $this->phone;
    }
}
