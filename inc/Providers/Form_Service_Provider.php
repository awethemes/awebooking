<?php
namespace AweBooking\Providers;

use Awethemes\Http\Request;
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
		add_action( 'cmb2_init', [ new Custom_Fields, 'init' ] );
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
		}, 10, 3 );
	}
}
