<?php

namespace AweBooking\Core\Providers;

use WPLibs\Http\Request;
use AweBooking\Model\Model;
use AweBooking\Support\Fluent;
use AweBooking\Support\Service_Provider;
use AweBooking\Component\Form\Custom_Fields;

class Form_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'cmb2_init', function () {
			$cf = new Custom_Fields;

			$cf->register( 'abrs_dates' );
			$cf->register( 'abrs_amount', 'abrs_sanitize_decimal' );
			$cf->register( 'abrs_toggle', 'abrs_sanitize_checkbox' );
			$cf->register( 'abrs_checkbox', 'abrs_sanitize_checkbox' );
			$cf->register( 'abrs_image_size', 'abrs_sanitize_image_size' );
			$cf->register( 'include', 'abrs_clean', true );
		});
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'cmb2_override_meta_value', function( $check, $object, $args ) {
			return ( $object instanceof Model || $object instanceof Request || $object instanceof Fluent )
				? @$object->get( $args['field_id'] )
				: $check;
		}, PHP_INT_MAX, 3 );
	}
}
