<?php
namespace Skeleton;

use CMB2_Boxes;
use LogicException;
use Skeleton\CMB2\Builder\Display_Builder;
use Skeleton\CMB2\Builder\Term_Display_Builder;

/**
 * Metabox based on top CMB2.
 *
 * @see https://github.com/WebDevStudios/CMB2/wiki
 * @see https://developer.wordpress.org/reference/functions/add_meta_box
 */
class Metabox extends CMB2\CMB2 {
	/**
	 * Make a new metabox.
	 *
	 * @param string $cmb_id  Metabox ID.
	 * @param array  $args    Optional. Metabox config array.
	 */
	public function __construct( $cmb_id, $args = array() ) {
		$args['id'] = $cmb_id;

		if ( CMB2_Boxes::get( $args['id'] ) ) {
			throw new LogicException( "A metabox with id `{$args['id']}` has been registered." );
		}

		parent::__construct( $args );
	}

	/**
	 * Make a new metabox using static method.
	 *
	 * @param string $cmb_id  Metabox ID.
	 * @param array  $args    Optional. Metabox config array.
	 */
	public static function make( $cmb_id, $args = array() ) {
		return new static( $cmb_id, $args );
	}

	/**
	 * Set metabox properties.
	 *
	 * @param  array $properties Metabox properties.
	 * @return $this
	 */
	public function set( array $properties ) {
		if ( empty( $properties ) ) {
			return $this;
		}

		foreach ( $properties as $key => $value ) {
			// Ignore locked properties.
			if ( in_array( $key, array( 'id' ) ) ) {
				continue;
			}

			$this->set_prop( $key, $value );
		}

		// Because `mb_object_type` method called in constructor,
		// we need force re-call this method to rebuld `mb_object_type` property.
		if ( array_key_exists( 'object_types', $properties ) ) {
			$this->mb_object_type = null;
			$this->mb_object_type();
		}

		return $this;
	}

	/**
	 * Set metabox title.
	 *
	 * @param  string $title Metabox title.
	 * @return $this
	 */
	public function set_title( $title ) {
		$this->set_prop( 'title', $title );
		return $this;
	}

	/**
	 * Set metabox context.
	 *
	 * This setting only working with object_types use `add_meta_box()` method.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_meta_box
	 *
	 * @param  string $context Metabox context, default: normal.
	 * @return $this
	 */
	public function set_context( $context ) {
		$this->set_prop( 'context', $context );
		return $this;
	}

	/**
	 * Set metabox priority.
	 *
	 * @param  string|int $priority Metabox priority, default: high.
	 * @return $this
	 */
	public function set_priority( $priority ) {
		$this->set_prop( 'priority', $priority );
		return $this;
	}

	/**
	 * Set metabox extra classes.
	 *
	 * @param  mixed $classes A string, array, callback classes.
	 * @return $this
	 */
	public function set_classes( $classes ) {
		if ( is_callable( $classes ) ) {
			$this->set_prop( 'classes_cb', $classes );
		} else {
			$this->set_prop( 'classes', $classes );
		}

		return $this;
	}

	/**
	 * Closed metabox by default?.
	 *
	 * @param  boolean $closed //.
	 * @return $this
	 */
	public function closed( $closed = true ) {
		$this->set_prop( 'closed', $closed );
		return $this;
	}

	/**
	 * Include CMB2 stylesheet?.
	 *
	 * @param  boolean $include //.
	 * @return $this
	 */
	public function include_styles( $include = true ) {
		$this->set_prop( 'cmb_styles', $include );
		return $this;
	}

	/**
	 * Determine whether should display field names.
	 *
	 * @param  boolean $show Should display field names.
	 * @return $this
	 */
	public function show_field_names( $show = true ) {
		$this->set_prop( 'show_names', $show );
		return $this;
	}

	/**
	 * Display metabox as vertical tabs styled.
	 *
	 * @param  boolean $vertical On/off vertical styled.
	 * @return $this
	 */
	public function vertical_tabs( $vertical = true ) {
		$this->set_prop( 'vertical_tabs', $vertical );
		return $this;
	}

	/**
	 * Setting meta box should show where (object_types).
	 *
	 * CMB2 valid object types: user, comment, post, page and {custom-post-types}
	 *
	 * @param  string $object_types Object types.
	 * @return Display_Builder
	 */
	public function show_on( $object_types ) {
		// Set object_types exclude `term` object type.
		// Use method $this->show_on_term( $taxonomies ) instead.
		$object_types = $this->parse_object_types( $object_types, array( 'term' ) );

		$this->set_object_types( $object_types );

		return new Display_Builder( $this );
	}

	/**
	 * Setting metabox display in term.
	 *
	 * @param  string|array $taxonomies A list of taxonomies will be show.
	 * @return Term_Display_Builder
	 */
	public function show_on_term( $taxonomies ) {
		if ( is_string( $taxonomies ) ) {
			$taxonomies = array_map( 'trim', explode( ',', $taxonomies ) );
		}

		$this->add_object_types( 'term' );
		$this->set_prop( 'taxonomies', $taxonomies );

		return new Term_Display_Builder( $this );
	}

	/**
	 * Add new one or mode object types to metabox.
	 *
	 * @param string|array $object_types New object types.
	 */
	public function add_object_types( $object_types ) {
		$object_types = $this->parse_object_types( $object_types );
		$current_object_types = $this->prop( 'object_types' );

		foreach ( $object_types as $name ) {
			if ( ! in_array( $name, $current_object_types ) ) {
				$current_object_types[] = $name;
			}
		}

		$this->set_object_types( $current_object_types );
	}

	/**
	 * Remove one or more object types.
	 *
	 * @param string|array $object_types Remove object types.
	 */
	public function remove_object_types( $object_types ) {
		$remove_object_types = $this->parse_object_types( $object_types );
		$current_object_types = $this->prop( 'object_types' );

		foreach ( $current_object_types as $i => $name ) {
			if ( in_array( $name, $remove_object_types ) ) {
				unset( $current_object_types[ $i ] );
			}
		}

		$this->set_object_types( $current_object_types );
	}

	/**
	 * Set metabox object types.
	 *
	 * @param string|array $object_types Object types.
	 */
	public function set_object_types( $object_types ) {
		$this->set_prop( 'object_types', $object_types );

		// Because `mb_object_type` method called in constructor,
		// we need force re-call this method to rebuld `mb_object_type` property.
		$this->mb_object_type = null;
		$this->mb_object_type();
	}

	/**
	 * Return a parsed object type from a string.
	 *
	 * @param  array|string $object_types Raw object types.
	 * @param  array        $excludes     Object type(s) will be ignore.
	 * @return array
	 */
	protected function parse_object_types( $object_types, $excludes = array() ) {
		if ( is_string( $object_types ) ) {
			$object_types = array_map( 'trim', explode( ',', $object_types ) );
		}

		// Return of don't need excludes anything.
		if ( empty( $excludes ) ) {
			return $object_types;
		}

		// Remove excludes keys.
		foreach ( $object_types as $i => $name ) {
			if ( in_array( $name, $excludes ) ) {
				unset( $object_types[ $i ] );
			}
		}

		return $object_types;
	}

	/**
	 * Hook to custom render display.
	 *
	 * @return void
	 */
	public function before_display_form() {
		$this->render->navigation_class = 'wp-clearfix cmb2-nav-default';

		if ( $this->prop( 'vertical_tabs' ) && ! in_array( $this->mb_object_type(), array( 'user', 'term' ) ) ) {
			$this->render->navigation_class = 'cmb2-nav-vertical';
			$this->render->after_sections = '<div class="wp-clearfix">';
		}
	}
}
