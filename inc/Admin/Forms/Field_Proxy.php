<?php
namespace AweBooking\Admin\Forms;

use CMB2_Field;

class Field_Proxy {
	/**
	 * The form instance.
	 *
	 * @var Form_Abstract
	 */
	protected $form;

	/**
	 * The CMB2_Field being operated on.
	 *
	 * @var \CMB2_Field
	 */
	protected $field;

	/**
	 * Create a new proxy instance.
	 *
	 * @param Form_Abstract $form  The Form instance.
	 * @param CMB2_Field    $field CMB2 Field instance.
	 */
	public function __construct( Form_Abstract $form, CMB2_Field $field ) {
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
			throw new \BadMethodCallException;
		}

		return call_user_func_array( [ $this->field, $method ], $parameters );
	}
}
