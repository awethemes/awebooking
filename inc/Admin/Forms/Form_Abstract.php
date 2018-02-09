<?php
namespace AweBooking\Admin\Forms;

use CMB2_hookup;
use Skeleton\CMB2\CMB2;
use Skeleton\CMB2\Field_Proxy;
use AweBooking\Admin\Forms\Exceptions\ValidationException;
use AweBooking\Admin\Forms\Exceptions\NonceMismatchException;
use Awethemes\Http\Request;

abstract class Form_Abstract extends CMB2 implements \ArrayAccess {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id;

	/**
	 * The request instance.
	 *
	 * @var \Awethemes\Http\Request
	 */
	protected $request;

	/**
	 * Form constructor.
	 */
	public function __construct() {
		parent::__construct([
			'id'         => $this->form_id,
			'hookup'     => false,
			'cmb_styles' => false,
		]);

		// Don't change this!
		// We need prevent CMB2 get field data from database.
		$this->object_id( '_' );
		$this->object_type( 'options-page' );

		$this->fields();
	}

	/**
	 * Set the http request.
	 *
	 * @param \Awethemes\Http\Request $request The request instance.
	 */
	public function set_request( Request $request ) {
		$this->request = $request;
	}

	/**
	 * Get the http request.
	 *
	 * @return \Awethemes\Http\Request
	 */
	public function get_request() {
		return $this->request ?: awebooking()->make( Request::class );
	}

	/**
	 * Register fields in to the CMB2.
	 *
	 * @return void
	 */
	abstract protected function fields();

	/**
	 * Handle process the form.
	 *
	 * @param  array|null $data        An array input data, if null $_POST will be use.
	 * @param  boolean    $check_nonce Run verity nonce from request.
	 * @return array|mixed
	 *
	 * @throws NonceMismatchException
	 * @throws ValidationException
	 */
	public function handle( array $data = null, $check_nonce = true ) {
		return $this->get_sanitized( $data, $check_nonce );
	}

	/**
	 * Get sanitized values of the form.
	 *
	 * @param  array|null $data        An array input data, if null $_POST will be use.
	 * @param  boolean    $check_nonce Run verity nonce from request.
	 * @return array|mixed
	 *
	 * @throws NonceMismatchException
	 * @throws ValidationException
	 */
	public function get_sanitized( array $data = null, $check_nonce = true ) {
		$data  = is_null( $data ) ? $_POST : $data;
		$nonce = $this->nonce();

		if ( $check_nonce && ( ! isset( $data[ $nonce ] ) || ! wp_verify_nonce( $data[ $nonce ], $nonce ) ) ) {
			throw new NonceMismatchException( esc_html__( 'Nonce is invalid, please try again.', 'awebooking' ) );
		}

		// Get sanitized values from input data.
		$sanitized = $this->get_sanitized_values( $data );

		if ( $this->fails() ) {
			throw new ValidationException( esc_html__( 'Input data has failed validation, please check again.', 'awebooking' ) );
		}

		return $sanitized;
	}

	/**
	 * Output the form.
	 *
	 * @return void
	 */
	public function output() {
		$this->setup_fields();

		$this->show_form();

		$this->after_form();
	}

	/**
	 * Returns contents of form.
	 *
	 * @return string
	 */
	public function contents() {
		ob_start();
		$this->output();
		return ob_get_clean();
	}

	/**
	 * Setup the fields value, attributes, etc...
	 *
	 * @return void
	 */
	public function setup_fields() {}

	/**
	 * Display some HTML or hidden input after form.
	 *
	 * @return void
	 */
	public function after_form() {}

	/**
	 * Enqueue CMB2 and our styles, scripts.
	 *
	 * @access private
	 */
	public function enqueue_scripts() {
		CMB2_hookup::enqueue_cmb_js();
		CMB2_hookup::enqueue_cmb_css();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_field( $field, $group = null, $reset_cached = false ) {
		$field = parent::get_field( $field, $group, $reset_cached );

		return $field ? new Field_Proxy( $this, $field ) : null;
	}

	/**
	 * Fill the fields value by given an array data.
	 *
	 * @param  array $data An array of fill data.
	 * @return void
	 */
	public function fill( array $data ) {
		foreach ( $data as $key => $value ) {
			if ( is_null( $this[ $key ] ) ) {
				continue;
			}

			$this[ $key ]->set_value( $value );
		}
	}

	/**
	 * Determine if an field exists.
	 *
	 * @param  mixed $key The field key ID.
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return array_key_exists( $key, $this->prop( 'fields' ) );
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
		$this->add_field(
			array_merge( (array) $args, [ 'id' => $key ] )
		);
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
