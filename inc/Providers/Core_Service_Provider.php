<?php
namespace AweBooking\Providers;

use AweBooking\Cart\Cart;
use AweBooking\Booking\Store;
use AweBooking\Currency\Currency;
use AweBooking\Currency\Currency_Manager;
use AweBooking\Shortcodes\Shortcodes;
use AweBooking\Support\Service_Provider;

class Core_Service_Provider extends Service_Provider {
	/**
	 * The AweBooking core widgets.
	 *
	 * @var array
	 */
	protected $widgets = [
		\AweBooking\Widgets\Booking_Cart_Widget::class,
		\AweBooking\Widgets\Check_Availability_Widget::class,
	];

	/**
	 * Registers services on the AweBooking.
	 */
	public function register() {
		$this->awebooking->singleton( 'currency_manager', function() {
			return new Currency_Manager;
		});

		$this->awebooking->singleton( 'currency', function( $a ) {
			return new Currency( $a['setting']->get( 'currency' ) );
		});

		$this->awebooking->singleton( 'cart', function( $a ) {
			return new Cart( $a['session'] );
		});

		$this->awebooking->alias( 'cart', Cart::class );

		// Binding the stores.
		$this->awebooking->singleton( 'store.booking', function() {
			return new Store( 'awebooking_booking', 'room_id' );
		});

		$this->awebooking->singleton( 'store.availability', function() {
			return new Store( 'awebooking_availability', 'room_id' );
		});

		$this->awebooking->singleton( 'store.pricing', function() {
			return new Store( 'awebooking_pricing', 'rate_id' );
		});

		Shortcodes::init();
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
	}

	/**
	 * Register AweBooking widgets.
	 */
	public function register_widgets() {
		foreach ( $this->widgets as $widget ) {
			register_widget( $widget );
		}
	}
}
