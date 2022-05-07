<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AweBooking\Vendor\Symfony\Component\Notifier\Exception;

use AweBooking\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TransportException extends RuntimeException implements TransportExceptionInterface
{
    private $response;
    private $debug = '';
    public function __construct(string $message, ResponseInterface $response, int $code = 0, \Throwable $previous = null)
    {
        $this->response = $response;
        $this->debug .= $response->getInfo('debug') ?? '';
        parent::__construct($message, $code, $previous);
    }
    public function getResponse() : ResponseInterface
    {
        return $this->response;
    }
    public function getDebug() : string
    {
        return $this->debug;
    }
}
