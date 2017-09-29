<?php
namespace AweBooking;

use Valitron\Validator;
use AweBooking\Support\Service_Hooks;
use AweBooking\Admin\Fields\Date_Range_Field;
use AweBooking\Admin\Fields\Service_List_Field;

class Skeleton_Hooks extends Service_Hooks {

	public function __construct() {
		add_action( 'skeleton/init', [ $this, 'register' ] );
	}

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 *
	 * @param Container $container Container instance.
	 */
	public function register( $container ) {
		$this->register_validator_rules();

		skeleton()->get_fields()->register_field( 'date_range', Date_Range_Field::class );
		skeleton()->get_fields()->register_field( 'awebooking_services', Service_List_Field::class );
	}

	/**
	 * Register the validator rules.
	 *
	 * @return void
	 */
	protected function register_validator_rules() {
		Validator::addRule( 'datePeriod', function( $field, $value, array $params ) {
			$strict = isset( $params[0] ) && $params[0];

			$sanitized = awebooking_sanitize_period( $value, $strict );

			return ! empty( $sanitized );
		});

		Validator::addRule( 'price', function( $field, $value, array $params ) {
			if ( 0 == $value ) {
				return true;
			}

			$allow_negative = ( isset( $params[0] ) && $params[0] );
			$sanitized = awebooking_sanitize_price( $value );

			if ( ! $allow_negative ) {
				return $sanitized > 0;
			}

			return 0 != $sanitized;
		});
	}
}
