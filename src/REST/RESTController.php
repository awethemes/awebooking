<?php

namespace AweBooking\PMS\REST;

use AweBooking\System\ContainerAwareTrait;
use WP_REST_Controller;

abstract class RESTController extends WP_REST_Controller
{
    use ContainerAwareTrait;

    /**
     * The namespace of this controller's route.
     *
     * @var string
     */
    protected $namespace = 'store';

    /**
     * Check manager permissions on REST API.
     *
     * @param string $capability
     * @param mixed ...$args
     * @return \Closure
     */
    protected function currentArtistCan($capability, ...$args)
    {
        return function () use ($capability, $args) {
            if (!Artist::isArtist(wp_get_current_user())) {
                return false;
            }

            return current_user_can($capability, ...$args);
        };
    }

    /**
     * Check manager permissions on REST API.
     *
     * @param string $object Object.
     * @param string $context Request context.
     * @return \Closure
     */
    protected function currentUserCanManage($object, $context = 'read')
    {
        return function () use ($context, $object) {
            $objects = [
                'settings' => 'manage_options',
                'art_submissions' => 'manage_options',
                'artists_requests' => 'manage_options',
                'sizing_templates' => 'manage_options',
            ];

            $permission = current_user_can($objects[$object]);

            return apply_filters('artisus_rest_check_permissions', $permission, $context, $object);
        };
    }
}
