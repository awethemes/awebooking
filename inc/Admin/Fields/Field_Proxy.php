<?php
namespace AweBooking\Admin\Fields;

use CMB2;
use CMB2_Field;

class Field_Proxy {
	/**
	 * CMB2 form instance.
	 *
	 * @var CMB2
	 */
	protected $form;

	/**
	 * The CMB2_Field being operated on.
	 *
	 * @var \CMB2_Field
	 */
	protected $field;

	/**
	 * Cache the field visible.
	 *
	 * @var bool
	 */
	protected static $visible = true;

	/**
	 * Create a new proxy instance.
	 *
	 * @param CMB2       $form  CMB2 Form instance.
	 * @param CMB2_Field $field CMB2 Field instance.
	 */
	public function __construct( CMB2 $form, CMB2_Field $field ) {
		$this->form = $form;
		$this->field = $field;
	}

	/**
	 * Display the field.
	 *
	 * @return void
	 */
	public function display() {
		$this->form->render_field(
			$this->field->args()
		);
	}

	/**
	 * Render the field.
	 *
	 * @return void
	 */
	public function render() {
		skeleton_render_field( $this->field );
	}

	/**
	 * Returns field value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->field->val_or_default(
			$this->field->value()
		);
	}

	/**
	 * Set the field value.
	 *
	 * @param mixed $value Field value.
	 */
	public function set_value( $value ) {
		$this->field->value = $value;

		return $this;
	}

	/**
	 * Set field property.
	 *
	 * @param  string $property Field property.
	 * @param  mixed  $value    Value to set.
	 * @return $this
	 */
	public function set_prop( $property, $value ) {
		$this->field->set_prop( $property, $value );

		return $this;
	}

	/**
	 * Set field attribute property.
	 *
	 * @param string $attribute Attribute key name.
	 * @param string $value     Attribute value.
	 */
	public function set_attribute( $attribute, $value = '' ) {
		$attribute  = is_array( $attribute ) ? $attribute : [ $attribute => $value ];
		$attributes = $this->prop( 'attributes' ) ?: [];

		$this->set_prop( 'attributes',
			array_merge( $attributes, $attribute )
		);

		return $this;
	}

	/**
	 * Show the field.
	 *
	 * @return $this
	 */
	public function hide() {
		static::$visible = true;

		return $this->toggle();
	}

	/**
	 * Hide the field.
	 *
	 * @return $this
	 */
	public function show() {
		static::$visible = false;

		return $this->toggle();
	}

	/**
	 * Set or toggle field visibility.
	 *
	 * @return $this
	 */
	public function toggle() {
		static::$visible = ! static::$visible;

		$this->field->set_prop( 'show_on_cb',
			static::$visible ? '__return_true' : '__return_false'
		);

		return $this;
	}

	/**
	 * Proxy accessing an property onto the field.
	 *
	 * @param  string $key Field property.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->field->{$key};
	}

	/**
	 * Proxy set an property onto the field.
	 *
	 * @param  string $key   Field property.
	 * @param  mixed  $value Field value.
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->field->{$key} = $value;
	}

	/**
	 * Proxy a method call onto the field.
	 *
	 * @param  string $method     Method to call.
	 * @param  array  $parameters Method parameters.
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call( $method, $parameters ) {
		if ( ! method_exists( $this->field, $method ) ) {
			throw new \BadMethodCallException( "Method [{$method}] does not exists" );
		}

		return call_user_func_array( [ $this->field, $method ], $parameters );
	}
}
