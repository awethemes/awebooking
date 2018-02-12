<?php
namespace AweBooking\Model;

use Awethemes\WP_Object\WP_Object as Base_WP_Object;

abstract class WP_Object extends Base_WP_Object {
	/**
	 * Prefix for hooks.
	 *
	 * @var string
	 */
	protected $prefix = 'awebooking';

	/**
	 * {@inheritdoc}
	 */
	protected function before_save() {
		$call_method = $this->exists() ? 'updating' : 'inserting';

		if ( method_exists( $this, $call_method ) ) {
			call_user_func( [ $this, $call_method ] );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function cast_attribute( $key, $value ) {
		if ( is_null( $value ) ) {
			return $value;
		}

		switch ( $this->get_cast_type( $key ) ) {
			case 'decimal':
				return Decimal::create( $value );
			default:
				return parent::cast_attribute( $key, $value );
		}
	}
}
