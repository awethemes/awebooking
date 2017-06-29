<?php
namespace Skeleton\CMB2;

class Group {
	/**
	 * Group field ID.
	 *
	 * @var string
	 */
	public $group_id;

	/**
	 * CMB2 instance.
	 *
	 * @var string
	 */
	protected $cmb2;

	/**
	 * Constructor of class.
	 *
	 * @param CMB2   $cmb2  CMB2 instance object.
	 * @param string $group_id Group field ID.
	 */
	public function __construct( CMB2 $cmb2, $group_id ) {
		$this->cmb2 = $cmb2;
		$this->group_id = $group_id;
	}

	/**
	 * Add a field to this group field of cmb2.
	 *
	 * @param  array $field    CMB2 field config array.
	 * @param  int   $position Optional, position of field.
	 * @return int|false
	 */
	public function add_field( array $field, $position = 0 ) {
		return $this->cmb2->add_group_field( $this->group_id, $field, $position );
	}

	/**
	 * Set group field args.
	 *
	 * @param  array $args Group field args.
	 * @return $this
	 */
	public function set( array $args ) {
		foreach ( $args as $key => $value ) {
			$this->set_property( $key, $value );
		}

		return $this;
	}

	/**
	 * Set group field property.
	 *
	 * @param  string $key   Field property key name.
	 * @param  mixed  $value Field property value.
	 * @return $this
	 */
	public function set_property( $key, $value ) {
		// Prevent set field ID or field type.
		if ( ! in_array( $key, array( 'id', 'type' ) ) ) {
			$this->cmb2->update_field_property( $this->group_id, $key, $value );
		}

		return $this;
	}
}
