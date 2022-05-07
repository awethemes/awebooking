<?php

namespace AweBooking\PMS\REST\Controllers;

use AweBooking\REST\RESTController;
use AweBooking\Vendor\Payum\Core\Payum;
use AweBooking\Vendor\Payum\Core\Reply\ReplyInterface;
use AweBooking\Vendor\Payum\Core\Request\Authorize;
use AweBooking\Vendor\Payum\Core\Request\Capture;
use AweBooking\Vendor\Symfony\Component\HttpFoundation\Request;
use WP_REST_Server;

class PaymentController extends RESTController
{
    /**
     * @var Payum
     */
    protected $payum;

    /**
     * @param Payum $payum
     */
    public function __construct(Payum $payum)
    {
        $this->payum = $payum;
    }

    /**
     * Register controller routes.
     *
     * @return void
     */
    public function register()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/payment/authorize/(?P<token>>[\w-]+)',
            [
                [
                    'methods' => WP_REST_Server::ALLMETHODS,
                    'callback' => [$this, 'authorize'],
                    'permission_callback' => '__return_true',
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/payment/capture/(?P<token>>[\w-]+)',
            [
                [
                    'methods' => WP_REST_Server::ALLMETHODS,
                    'callback' => [$this, 'capture'],
                    'permission_callback' => '__return_true',
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );
    }

    public function authorize(Request $request, $token)
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);

        $gateway = $this->payum->getGateway($token->getGatewayName());

        try {
            $gateway->execute(new Authorize($token));
        } catch (ReplyInterface $reply) {
            return $this->convertReply($reply);
        }

        $this->payum->getHttpRequestVerifier()->invalidate($token);

        wp_redirect($token->getAfterUrl());
        exit;
    }

    public function capture(Request $request, $token)
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);

        $gateway = $this->payum->getGateway($token->getGatewayName());

        try {
            $gateway->execute(new Capture($token));
        } catch (ReplyInterface $reply) {
            return $this->convertReply($reply);
        }

        $this->payum->getHttpRequestVerifier()->invalidate($token);

        wp_redirect($token->getAfterUrl());
        exit;
    }

    /**
     * @param ReplyInterface $reply
     * @return Response
     */
    private function convertReply(ReplyInterface $reply)
    {
        /** @var ReplyToSymfonyResponseConverter $converter */
        $converter = \App::make('payum.converter.reply_to_http_response');

        return $converter->convert($reply);
    }
}
