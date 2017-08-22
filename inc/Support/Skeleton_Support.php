<?php
namespace AweBooking\Support;

use Valitron\Validator;
use Skeleton\Container\Service_Hooks;

class Skeleton_Support extends Service_Hooks {
	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 *
	 * @param Container $container Container instance.
	 */
	public function register( $container ) {
		$this->register_validator_rules();

		$container->extend( 'cmb2_manager', function( $manager ) {
			$manager->register_field( 'date_range', 'AweBooking\\Support\\Fields\\Date_Range_Field' );

			return $manager;
		});
	}

	/**
	 * Register the validator rules.
	 *
	 * @return void
	 */
	protected function register_validator_rules() {
		Validator::addRule( 'datePeriod', function( $field, $value, array $params ) {
			$strict = ( isset( $params[0] ) &&  $params[0] );

			$sanitized = awebooking_sanitize_period( $value, $strict );

			return ! empty( $sanitized );
		});

		Validator::addRule( 'price', function( $field, $value, array $params ) {
			if ( 0 == $value ) {
				return true;
			}

			$allow_negative = ( isset( $params[0] ) &&  $params[0] );
			$sanitized = awebooking_sanitize_price( $value );

			if ( ! $allow_negative ) {
				return $sanitized > 0;
			}

			return 0 != $sanitized;
		});
	}
}
