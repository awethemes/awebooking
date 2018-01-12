<?php
namespace AweBooking\Providers;

use AweBooking\Constants;
use AweBooking\Support\Flash_Message;
use AweBooking\Support\Service_Provider;

use AweBooking\Http\Controllers\Ajax_Handler;
use AweBooking\Http\Controllers\Request_Handler;

class Frontend_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the AweBooking.
	 */
	public function register() {
		$this->awebooking->singleton( 'flash_message', function( $a ) {
			return new Flash_Message( $a['session'] );
		});
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$this->awebooking->make( Ajax_Handler::class );
		$this->awebooking->make( Request_Handler::class );

		add_filter( 'body_class', [ $this, 'modify_body_class' ] );
		add_filter( 'template_include', [ $this, 'overwrite_template' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * @access private
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'awebooking-template', awebooking()->plugin_url() . '/assets/css/awebooking.css', array(), AWEBOOKING_VERSION );
		wp_register_style( 'magnific-popup', awebooking()->plugin_url() . '/assets/css/magnific-popup.css', array(), '1.1.0' );

		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		wp_enqueue_script( 'awebooking', awebooking()->plugin_url() . '/assets/js/front-end/awebooking.js', array( 'jquery' ), AWEBOOKING_VERSION, true );
		wp_register_script( 'magnific-popup', awebooking()->plugin_url() . '/assets/js/magnific-popup/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );

		wp_enqueue_script( 'booking-ajax', awebooking()->plugin_url() . '/assets/js/front-end/booking-handler.js', array( 'jquery' ), AWEBOOKING_VERSION, true );
		wp_localize_script( 'booking-ajax', 'booking_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		));

		global $wp_locale;

		wp_localize_script( 'awebooking', '_awebookingDateSetting', array(
			'i10n' => [
				'locale'        => get_locale(),
				'months'        => array_values( $wp_locale->month ),
				'monthsShort'   => array_values( $wp_locale->month_abbrev ),
				'weekdays'      => array_values( $wp_locale->weekday ),
				'weekdaysMin'   => array_values( $wp_locale->weekday_initial ),
				'weekdaysShort' => array_values( $wp_locale->weekday_abbrev ),
			],
		));
	}

	/**
	 * Modify body classes in awebooking pages.
	 *
	 * @param  array $classes Body classes.
	 * @return array
	 *
	 * @access private
	 */
	public function modify_body_class( $classes ) {
		switch ( true ) {
			case is_awebooking():
				$classes[] = 'awebooking';
				$classes[] = 'awebooking-page';
				break;

			case is_room_type_archive():
				$classes[] = 'awebooking-room-type-archive';
				break;

			case is_room_type():
				$classes[] = 'awebooking-room-type';
				break;

			case is_check_availability_page():
				$classes[] = 'awebooking-check-availability-page';
				break;

			case is_booking_info_page():
				$classes[] = 'awebooking-booking-info-page';
				break;

			case is_booking_checkout_page():
				$classes[] = 'awebooking-checkout-page';
				break;
		}

		return $classes;
	}

	/**
	 * Overwrite awebooking template in some case.
	 *
	 * @param  string $template The template file-path.
	 * @return string
	 *
	 * @access private
	 */
	public function overwrite_template( $template ) {
		if ( is_embed() ) {
			return $template;
		}

		if ( $overwrite_template = $this->find_overwrite_template() ) {
			$template = awebooking_locate_template( $overwrite_template );
		}

		return $template;
	}

	/**
	 * Find the overwrite template by guest current context.
	 *
	 * @return string
	 */
	protected function find_overwrite_template() {
		$template = '';

		switch ( true ) {
			case is_singular( Constants::ROOM_TYPE ):
				$template = 'single-room-type.php';
				break;

			case is_post_type_archive( Constants::ROOM_TYPE ):
				$template = 'archive-room-type.php';
				break;
		}

		return $template;
	}
}
