<?php
namespace AweBooking\Admin;

use AweBooking\Setting;
use Skeleton\CMB2\CMB2;
use Skeleton\CMB2\Field_Proxy;
use Skeleton\Support\Multidimensional;

class Admin_Settings extends CMB2 {
	/**
	 * The Setting instance.
	 *
	 * @var \AweBooking\Setting
	 */
	protected $setting;

	/**
	 * Constructor.
	 *
	 * @param Setting $setting The Setting instance.
	 */
	public function __construct( Setting $setting ) {
		$this->setting = $setting;

		parent::__construct([
			'id'         => 'awebooking-settings',
			'hookup'     => false,
			'cmb_styles' => false,
		]);

		$this->object_id( $this->setting->get_setting_key() );
		$this->object_type( 'options-page' );
	}

	/**
	 * Register settings by callback or setting-class.
	 *
	 * @param  callable|string $settings The settings callback or class to register.
	 * @return void
	 */
	public function register( $settings ) {
		awebooking()->call( $settings, [ $this ], 'registers' );
	}

	/**
	 * Store a raw values into the database.
	 *
	 * @param  array $values The store values.
	 * @return bool
	 */
	public function store( array $values ) {
		$options = (array) get_option( $this->setting->get_setting_key(), [] );

		foreach ( $values as $key => $value ) {
			Multidimensional::replace( $options, $key, $value );
		}

		return update_option( $this->setting->get_setting_key(), $options );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_field( $field, $group = null, $reset_cached = false ) {
		$field = parent::get_field( $field, $group, $reset_cached );

		return $field ? new Field_Proxy( $this, $field ) : null;
	}

	/**
	 * Prepare fields validation errors.
	 *
	 * @return void
	 */
	public function prepare_validation_errors() {
		$this->prepare_validate();
	}

	/**
	 * Determine if an field exists.
	 *
	 * @param  mixed $key The field key ID.
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return ! is_null( $this->offsetGet( $key ) );
	}

	/**
	 * Get an field at a given offset.
	 *
	 * @param  mixed $key The field key ID.
	 * @return mixed
	 */
	public function offsetGet( $key ) {
		return $this->get_field( $key );
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param  mixed $key  Field ID.
	 * @param  mixed $args Field args.
	 * @return void
	 */
	public function offsetSet( $key, $args ) {
		$this->add_field( array_merge( (array) $args, [ 'id' => $key ] ) );
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string $key Field key ID.
	 * @return void
	 */
	public function offsetUnset( $key ) {
		$this->remove_field( $key );
	}
}
