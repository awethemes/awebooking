<?php
namespace Skeleton\CMB2;

class Section extends Tabable {
	/**
	 * Panel in which to show the section, making it a sub-section.
	 *
	 * @var string
	 */
	public $panel = '';

	/**
	 * CMB2 fields for this section.
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * Set panel where this section will belong to.
	 *
	 * @param  Panel|string $panel Panel ID or a Panel object.
	 * @return $this
	 */
	public function as_child_of( $panel ) {
		if ( $panel instanceof Panel ) {
			$panel = $panel->id;
		}

		$this->panel = $panel;
		return $this;
	}

	/**
	 * Add a field to this section of the CMB2.
	 *
	 * @param  array $field CMB2 field config array.
	 * @return string|false
	 */
	public function add_field( array $field ) {
		$field['section'] = $this->id;
		return $this->cmb2->add_field( $field );
	}

	/**
	 * Add a group field to the metabox in this tab.
	 *
	 * @param  array    $id       Group ID.
	 * @param  callable $callback Group builder callback.
	 * @param  int      $position Optional, position of group.
	 * @return \Skeleton\CMB2_Builder\Group_Builder
	 */
	public function add_group( $id, $callback = null, $position = 0 ) {
		$group = $this->cmb2->add_group( $id, $callback, $position );
		$group->set_property( 'section', $this->id );

		return $group;
	}
}
