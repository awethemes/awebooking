<?php
namespace AweBooking\Providers;

use Skeleton\Skeleton;
use Valitron\Validator as V;
use AweBooking\Support\Service_Provider;

class Skeleton_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the AweBooking.
	 */
	public function register() {
		$this->register_skeleton_binding();

		$this->register_skeleton_fields();

		$this->register_validator_rules();
	}

	/**
	 * Binding the Skeleton.
	 *
	 * @return void
	 */
	protected function register_skeleton_binding() {
		$this->awebooking->instance( 'skeleton', Skeleton::get_instance() );
		$this->awebooking->alias( 'skeleton', Skeleton::class );
	}

	/**
	 * Register the Skeleton field.
	 *
	 * @return void
	 */
	protected function register_skeleton_fields() {
		$field_manager = $this->awebooking['skeleton']->get_fields();

		foreach ( [
			'note'                => \AweBooking\Admin\Fields\Note_Field::class,
			'date_range'          => \AweBooking\Admin\Fields\Date_Range_Field::class,
			'awebooking_services' => \AweBooking\Admin\Fields\Service_List_Field::class,
			'per_person_pricing'  => \AweBooking\Admin\Fields\Per_Person_Pricing_Field::class,
		] as $key => $value ) {
			$field_manager->register_field( $key, $value );
		}
	}

	/**
	 * Register the validator rules.
	 *
	 * @return void
	 */
	protected function register_validator_rules() {
		V::addRule( 'datePeriod', function( $field, $value, array $params ) {
			$strict = isset( $params[0] ) && $params[0];

			$sanitized = awebooking_sanitize_period( $value, $strict );

			return ! empty( $sanitized );
		}, esc_html_x( 'period is invalid', 'validate message', 'awebooking' ) );

		V::addRule( 'price', function( $field, $value, array $params ) {
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
