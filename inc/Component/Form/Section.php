<?php
namespace AweBooking\Component\Form;

class Section implements \ArrayAccess {
	/**
	 * The CMB2 instance.
	 *
	 * @var CMB2
	 */
	protected $cmb2;

	/**
	 * Unique identifier.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Title of the section to show in UI.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Priority of the section which informs load order of sections.
	 *
	 * @var integer
	 */
	public $priority = 10;

	/**
	 * CMB2 fields for this section.
	 *
	 * @var array
	 */
	public $fields = [];

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @param CMB    $cmb2 The CMB2 instance.
	 * @param string $id   An specific ID of the section.
	 * @param array  $args Section arguments.
	 */
	public function __construct( $cmb2, $id, $args = [] ) {
		$this->id = $id;
		$this->cmb2 = $cmb2;

		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		// Users cannot customize the $controls array.
		$this->fields = [];
	}

	/**
	 * Add a field to this section of the CMB2.
	 *
	 * @param  array $field    The field config array.
	 * @param  int   $position Position of field.
	 * @return string|false
	 */
	public function add_field( array $field, $position = 0 ) {
		$field['section'] = $this->id;

		return $this->cmb2->add_field( $field );
	}

	/**
	 * Whether the given offset exists.
	 *
	 * @param  string $offset The offset name.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->$offset );
	}

	/**
	 * Fetch the offset.
	 *
	 * @param  string $offset The offset name.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->__get( $offset );
	}

	/**
	 * Assign the offset.
	 *
	 * @param  string $offset The offset name.
	 * @param  mixed  $value  The offset value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		if ( 'cmb2' !== $offset ) {
			$this->$offset = $value;
		}
	}

	/**
	 * Unset the offset.
	 *
	 * @param  mixed $offset The offset name.
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		// ...
	}

	/**
	 * Magic getter method.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		switch ( $property ) {
			case 'uid':
				return $this->cmb2->prop( 'id' ) . '-' . $this->id;
			default:
				return $this->{$property};
		}
	}
}
