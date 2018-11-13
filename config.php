<?php

return [
	'view' => [
		'paths' => [
			__DIR__ . '/templates/',
		],
	],

	'bootstrappers' => [
		\AweBooking\Core\Bootstrap\Load_Textdomain::class,
		\AweBooking\Core\Bootstrap\Setup_Environment::class,
		\AweBooking\Core\Bootstrap\Load_Configuration::class,
		\AweBooking\Core\Bootstrap\Include_Functions::class,
		\AweBooking\Core\Bootstrap\Start_Session::class,
		\AweBooking\Core\Bootstrap\Boot_Providers::class,
	],

	'service_providers' => [
		\AweBooking\Core\Providers\Intl_Service_Provider::class,
		\AweBooking\Core\Providers\Http_Service_Provider::class,
		\AweBooking\Core\Providers\Form_Service_Provider::class,
		// \AweBooking\Component\View\View_Service_Provider::class,
		\AweBooking\Core\Providers\Query_Service_Provider::class,
		\AweBooking\Core\Providers\Logic_Service_Provider::class,
		\AweBooking\Core\Providers\Payment_Service_Provider::class,
		\AweBooking\Core\Providers\Email_Service_Provider::class,
		\AweBooking\Core\Providers\Shortcode_Service_Provider::class,
		\AweBooking\Core\Providers\Widget_Service_Provider::class,
		\AweBooking\Core\Providers\Addons_Service_Provider::class,
		\AweBooking\Schedules\Schedule_Service_Provider::class,

		\AweBooking\Frontend\Providers\Frontend_Service_Provider::class,
		\AweBooking\Frontend\Providers\Template_Loader_Service_Provider::class,
		\AweBooking\Frontend\Providers\Scripts_Service_Provider::class,
		\AweBooking\Frontend\Providers\Reservation_Service_Provider::class,

		\AweBooking\Admin\Providers\Admin_Service_Provider::class,
		\AweBooking\Admin\Providers\Menu_Service_Provider::class,
		\AweBooking\Admin\Providers\Permalink_Service_Provider::class,
		\AweBooking\Admin\Providers\Scripts_Service_Provider::class,
		\AweBooking\Admin\Providers\Metaboxes_Service_Provider::class,
		\AweBooking\Admin\Providers\Post_Types_Service_Provider::class,
		\AweBooking\Admin\Providers\Taxonomies_Service_Provider::class,
		\AweBooking\Admin\Providers\Notices_Service_Provider::class,
	],
];
