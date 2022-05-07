<?php

namespace AweBooking\PMS\REST\Controllers\Admin;

class MenuItemsController extends \WP_REST_Menu_Items_Controller
{
    public function __construct()
    {
        parent::__construct('nav_menu_item');

        $this->namespace = 'pod/v1';
        $this->rest_base = 'menu-items';
    }

    public function prepare_item_for_response($item, $request)
    {
        $response = parent::prepare_item_for_response($item, $request);

        $response->data['content']['raw'] = $item->post_content;

        return $response;
    }
}
