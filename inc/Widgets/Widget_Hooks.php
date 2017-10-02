<?php
namespace AweBooking\Widgets;

use AweBooking\Support\Service_Hooks;
use AweBooking\Widgets\Check_Availability_Widget;

class Widget_Hooks extends Service_Hooks {
	/**
	 * Registers services on the given container.
	 *
	 * @param AweBooking $awebooking AweBooking instance.
	 */
	public function register( $awebooking ) {
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
	}

	/**
	 * Register AweBooking widgets.
	 */
	public function register_widgets() {
		register_widget( Check_Availability_Widget::class );
		register_widget( Booking_Cart_Widget::class );
	}
}
