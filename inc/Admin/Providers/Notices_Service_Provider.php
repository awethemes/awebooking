<?php

namespace AweBooking\Admin\Providers;

use AweBooking\Admin\Notices;
use AweBooking\Support\Service_Provider;
use WPLibs\Session\Flash\Flash_Notifier;
use WPLibs\Session\Flash\Session_Store;

class Notices_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		$this->plugin->singleton( 'flash_notices', function() {
			return new Flash_Notifier( $this->plugin->make( Session_Store::class ), '_admin_notices' );
		});

		$this->plugin->alias( 'flash_notices', 'admin_notices' );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		// Init the core notices.
		Notices::init();

		add_action( 'admin_notices', [ $this, 'display_flash_notices' ] );
		// add_action( 'admin_notices', [ $this, 'notice_objects_with_no_lang' ] );
	}

	/**
	 * Setup and display admin notices.
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 *
	 * @access private
	 */
	public function display_flash_notices() {
		$messages = $this->plugin['flash_notices']->all();

		if ( $messages && $messages->isNotEmpty() ) {
			include dirname( __DIR__ ) . '/views/notices/html-flash-notices.php';
		}
	}

	/**
	 * Displays a notice when there are objects with no language assigned
	 *
	 * @access private
	 */
	public function notice_objects_with_no_lang() {
		if ( abrs_multiple_hotels() && ( ! abrs_get_page_id( 'primary_hotel' ) || abrs_get_orphan_room_types( 1 ) ) ) {
			printf(
				'<div class="notice error"><p>%s <a href="%s">%s</a></p></div>',
				esc_html__( 'There are room types without hotel.', 'awebooking' ),
				'#',
				esc_html__( 'You can set them all to the default language.', 'awebooking' )
			);
		}
	}
}
