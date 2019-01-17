<?php
namespace AweBooking\Component\Form;

use WPLibs\Http\Request;
use AweBooking\Model\Model;
use AweBooking\Support\Fluent;

class Form extends \CMB2 implements \ArrayAccess, \IteratorAggregate {
	use Form_Sections;

	/**
	 * The unique form ID.
	 *
	 * @var string
	 */
	protected $form_id;

	/**
	 * The render row callback.
	 *
	 * @var mixed
	 */
	protected $render_callback;

	/**
	 * Constructor.
	 *
	 * @param string $form_id     The form ID.
	 * @param mixed  $object_id   The object ID (post ID, option-key or a object).
	 * @param string $object_type The object type (post type slug, or 'user', 'term', 'comment', or 'options-page').
	 */
	public function __construct( $form_id, $object_id = 0, $object_type = '' ) {
		// Sets the form ID.
		$this->form_id = $form_id;
		$this->cmb_id  =& $this->form_id;

		// Set the default box args.
		$this->meta_box = array_merge( $this->mb_defaults, [
			'id'           => $this->form_id,
			'object_types' => ( $object_id instanceof Model ) ? 'model' : $object_type,
			'save_fields'  => true,
			'show_in_rest' => false,
			'hookup'       => false,
			'fields'       => [], // Empty the fields.
		]);

		// Set the object ID.
		$this->object_id( $object_id );

		// Ensures object_types is an array.
		$this->set_prop( 'object_types', $this->box_types() );

		if ( $this->is_options_page_mb() ) {
			$this->init_options_mb();
		} elseif ( $this->is_static_mb() ) {
			$this->init_static_mb();
		}

		// Try to guest the mb_object_type.
		if ( ! $this->mb_object_type ) {
			$this->mb_object_type();
		}

		// Call the register controls.
		$this->setup_fields();
	}

	/**
	 * Custom the render row callback.
	 *
	 * @param callable $callback The render callback.
	 */
	public function render_callback( callable $callback ) {
		$this->render_callback = $callback;
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	protected function setup_fields() {}

	/**
	 * Get sanitized values from an HTTP request.
	 *
	 * @param  \WPLibs\Http\Request $request The request instance.
	 * @return \AweBooking\Support\Fluent
	 */
	public function handle( Request $request ) {
		return new Fluent(
			$this->get_sanitized_values( $request->all() )
		);
	}

	/**
	 * Display a field.
	 *
	 * @param  string|array $field The field name or field args.
	 * @return void
	 */
	public function show_field( $field ) {
		// If given an field ID and already registered, just display it.
		if ( is_string( $field ) && isset( $this[ $field ] ) ) {
			$this[ $field ]->display();
			return;
		}

		// Try add field before render.
		if ( is_array( $field ) && $this->add_field( $field ) ) {
			$this->render_field( $field );
			return;
		}

		// @codingStandardsIgnoreLine
		_doing_it_wrong( static::class . '::' . __FUNCTION__, 'The field must be an array or a registered field ID.', '3.1' );
	}

	/**
	 * Add a field at position after a another field.
	 *
	 * @param string $key  The "append" field name.
	 * @param array  $args The field args.
	 *
	 * @return false|string
	 */
	public function add_field_after( $key, array $args ) {
		$fields = $this->prop( 'fields' );

		$posstion = count( $fields ) > 0
			? array_search( $key, array_keys( $fields ) )
			: null;

		return $this->add_field( $args, null !== $posstion ? $posstion + 2 : 0 );
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
	public function fill_values( array $data ) {
		foreach ( $data as $key => $value ) {
			if ( isset( $this[ $key ] ) ) {
				$this->get_field( $key )->set_value( $value );
			}
		}
	}

	/**
	 *  Determines if this is a static form.
	 *
	 * @return boolean
	 */
	public function is_static_mb() {
		return ( in_array( 'static', $this->box_types() )
			|| $this->object_id instanceof Fluent
			|| $this->object_id instanceof Request
			|| $this->object_id instanceof Model );
	}

	/**
	 * Init the static metabox.
	 *
	 * @return void
	 */
	protected function init_static_mb() {
		$this->mb_object_type = 'model';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _add_field_to_array( $field, &$fields, $position = 0 ) {
		parent::_add_field_to_array( $this->_modify_field_args( $field ), $fields, $position );
	}

	/**
	 * Modify the field before add to list.
	 *
	 * @param  array $field The field args.
	 * @return array
	 */
	protected function _modify_field_args( $field ) {
		// Transform checkbox & toggle field type.
		if ( 'checkbox' === $field['type'] ) {
			$field['type'] = 'abrs_checkbox';
		} elseif ( 'toggle' === $field['type'] ) {
			$field['type'] = 'abrs_toggle';
		}

		// Render field callback.
		if ( $this->render_callback && 'group' !== $field['type'] && empty( $field['render_row_cb'] ) ) {
			$field['render_row_cb'] = $this->render_callback;
		}

		// Field label callback.
		if ( ! isset( $field['label_cb'] ) && 'title' !== $field['type'] ) {
			$field['label_cb'] = [ $this, 'render_label_callback' ];
		}

		// Modify the field attributes.
		$field['attributes'] = isset( $field['attributes'] ) ? $field['attributes'] : [];

		if ( isset( $field['required'] ) && $field['required'] ) {
			$field['attributes'] = array_merge( $field['attributes'], [ 'required' => true ] );
		}

		return $field;
	}

	/**
	 * Custom field label with tooltip.
	 *
	 * @param  array       $field_args The field args.
	 * @param  \CMB2_Field $field The CMB2_Field instance.
	 * @return string
	 */
	public function render_label_callback( $field_args, $field ) {
		return include( trailingslashit( __DIR__ ) . 'views/html-field-label.php' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {
		foreach ( $this->prop( 'fields' ) as $field_args ) {
			yield $this->get_field( $field_args['id'] );
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

	/**
	 * Magic getter object property.
	 *
	 * @param  string $property Object property.
	 * @return mixed
	 *
	 * @throws \Exception
	 */
	public function __get( $property ) {
		switch ( $property ) {
			case 'form_id':
			case 'sections':
				return $this->{$property};
			default:
				return parent::__get( $property );
		}
	}
}
