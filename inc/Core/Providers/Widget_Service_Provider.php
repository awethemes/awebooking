<?php

namespace AweBooking\Core\Providers;

use AweBooking\Support\Service_Provider;

class Widget_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @access private
	 */
	public function register() {
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
	}

	/**
	 * Register core widgets.
	 *
	 * @access private
	 */
	public function register_widgets() {
		foreach ( [
			\AweBooking\Core\Widget\Search_Form_Widget::class,
		] as $widget ) {
			register_widget( $widget );
		}
	}
}
