<?php
namespace AweBooking\Component\Form;

trait Form_Sections {
	/**
	 * Registered instances of Section.
	 *
	 * @var array
	 */
	public $sections = [];

	/**
	 * Get the registered sections.
	 *
	 * @return array
	 */
	public function sections() {
		return $this->sections;
	}

	/**
	 * Add a form section.
	 *
	 * @param  Section|string $id   CMB2 Section object, or section ID.
	 * @param  array|callable $args Optional. Section arguments or section callback.
	 * @return Section              The instance of the section that was added.
	 */
	public function add_section( $id, $args = [] ) {
		$section = ( $id instanceof Section ) ? $id : new Section( $this, $id, $args );

		return $this->sections[ $section->id ] = $section;
	}

	/**
	 * Retrieve a section.
	 *
	 * @param  string $id   Section ID.
	 * @return Section|null The section, if set.
	 */
	public function get_section( $id ) {
		if ( array_key_exists( $id, $this->sections ) ) {
			return $this->sections[ $id ];
		}
	}

	/**
	 * Mapping that fields belong to their section.
	 *
	 * This should be call before about the form.
	 *
	 * @return void
	 */
	public function prepare_fields() {
		$fields = $this->prop( 'fields' );

		// If empty the sections just leave.
		if ( empty( $this->sections ) ) {
			return;
		}

		// Group fields by section, then map them to their section.
		abrs_collect( $fields )->groupBy( 'section' )
			->each( function( $fields, $section ) {
				if ( array_key_exists( $section, $this->sections ) ) {
					$this->sections[ $section ]['fields'] = $fields->keyBy( 'id' );
				}
			});

		// Sort the section based on priority.
		$this->sections = abrs_collect( $this->sections )
			->sortBy( 'priority' )
			->all();
	}
}
