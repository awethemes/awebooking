<?php
namespace Skeleton\Iconfonts\Icons;

class Iconpack implements Iconpack_Interface {
	/**
	 * Iconpack unique ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Iconpack display name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Iconpack version.
	 *
	 * @var string
	 */
	public $version = '';

	/**
	 * Stylesheet ID.
	 *
	 * @var string
	 */
	public $stylesheet_id = '';

	/**
	 * Stylesheet URI.
	 *
	 * @var string
	 */
	public $stylesheet_uri = '';

	/**
	 * Iconpack icons.
	 *
	 * @var string
	 */
	public $icons = array();

	/**
	 * Iconpack groups.
	 *
	 * @var string
	 */
	public $groups = array();

	/**
	 * Supplied $args override class property defaults.
	 *
	 * @param array $args Optional. Arguments to override class property defaults.
	 */
	public function __construct( array $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );

		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->{$key} = $args[ $key ];
			}
		}
	}

	public function register_styles() {
	}

	public function enqueue_styles() {
	}

	/**
	 * Get properties
	 *
	 * @since  0.1.0
	 * @return array
	 */
	public function get_props() {
		$props = array(
			'id'         => $this->id,
			'name'       => $this->name,
			'controller' => 'Font', // Never change this.
			'templateId' => 'font',
			'data'       => array(
				'groups' => $this->groups(),
				'items'  => $this->icons(),
			),
		);

		/**
		 * Filter icon type properties
		 *
		 * @since 0.1.0
		 * @param array            $props Icon type properties.
		 * @param string           $id    Icon type ID.
		 * @param Icon_Picker_Type $type  Icon_Picker_Type object.
		 */
		$props = apply_filters( 'skeleton/iconfonts/iconpack/props', $props, $this->id, $this );

		/**
		 * Filter icon type properties
		 *
		 * @since 0.1.0
		 * @param array            $props Icon type properties.
		 * @param Icon_Picker_Type $type  Icon_Picker_Type object.
		 */
		$props = apply_filters( "skeleton/iconfonts/iconpack/props_{$this->id}", $props, $this );

		return $props;
	}

	/**
	 * Return an array icon groups.
	 *
	 * @return array
	 */
	public function groups() {
		return apply_filters( 'skeleton/iconfonts/' . $this->id . '/groups', $this->groups );
	}

	/**
	 * Return an array of icons.
	 *
	 * @return array
	 */
	public function icons() {
		return apply_filters( 'skeleton/iconfonts/' . $this->id . '/icons', $this->icons );
	}
}
