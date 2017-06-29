<?php
namespace Skeleton\Iconfonts;

use Skeleton\Skeleton;
use Skeleton\Support\Utils;
use Skeleton\Iconfonts\Icons\Iconpack_Plugable;
use Skeleton\Iconfonts\Icons\Iconpack_Interface;

class Manager {
	/**
	 * Skeleton container instance.
	 *
	 * @var Skeleton
	 */
	protected $skeleton;

	/**
	 * An array registerd icon packs.
	 *
	 * @var array
	 */
	protected $iconpacks = array();

	/**
	 * Constructor Manager.
	 *
	 * @param Skeleton $skeleton
	 */
	public function __construct( Skeleton $skeleton ) {
		$this->skeleton = $skeleton;

		$this->register( new Icons\Dashicons );
		// $this->register( new Icons\FontAwesome );

		foreach ( $skeleton['iconfonts_options']->all() as $id => $relative_path ) {
			$this->register( Iconpack_Plugable::import( $id ) );
		}

		do_action( 'skeleton/register_icon_fonts', $this );
	}

	/**
	 * Get all iconpacks.
	 *
	 * @return array
	 */
	public function all() {
		return $this->iconpacks;
	}

	/**
	 * Register a iconpack.
	 *
	 * @param  Iconpack_Interface $iconpack Iconpack.
	 * @return void
	 */
	public function register( Iconpack_Interface $iconpack ) {
		if ( ! $this->is_valid_iconpack( $iconpack ) ) {
			return;
		}

		$iconpack->register_styles();

		$this->iconpacks[ $iconpack->id ] = $iconpack;
	}

	/**
	 * Remove a icon.
	 *
	 * @param  string $iconpack Iconpack name.
	 * @return void
	 */
	public function unregister( $iconpack ) {
		unset( $this->iconpack[ $iconpack ] );
	}

	/**
	 * Get iconpack by key.
	 *
	 * @param  string $id Iconpack ID.
	 * @return mixed
	 */
	public function get( $id ) {
		if ( isset( $this->iconpack[ $id ] ) ) {
			return $this->iconpack[ $id ];
		}

		return null;
	}

	/**
	 * Check if icon type is valid
	 *
	 * @since  0.1.0
	 * @param  Icon_Picker_Type $type Icon type.
	 * @return bool
	 */
	protected function is_valid_iconpack( Iconpack_Interface $type ) {
		foreach ( array( 'id' ) as $var ) {
			$value = $type->$var;

			if ( empty( $value ) ) {
				trigger_error( esc_html( sprintf( 'Icon Picker: "%s" cannot be empty.', $var ) ) );
				return false;
			}
		}

		if ( isset( $this->types[ $type->id ] ) ) {
			trigger_error( esc_html( sprintf( 'Icon Picker: Icon type %s is already registered. Please use a different ID.', $type->id ) ) );
			return false;
		}

		return true;
	}

	/**
	 * Get all icons for JS.
	 *
	 * @return array
	 */
	public function get_for_js() {
		$types = array();
		$names = array();

		foreach ( $this->iconpacks as $type ) {
			$types[ $type->id ] = $type->get_props();
			$names[ $type->id ] = $type->name;
		}

		array_multisort( $names, SORT_ASC, $types );

		return $types;
	}

	/**
	 * Get all icons for JS Iconpicker.
	 *
	 * The Skeleton use wp.media.view.MediaFrame.IconPicker from
	 * kucrut/wp-icon-picker (GPLv2 license) for picker icon. So
	 * this return same format of kucrut/wp-icon-picker.
	 *
	 * @link https://github.com/kucrut/wp-icon-picker
	 *
	 * @return array
	 */
	public function get_for_iconpicker_js() {
		$types = $this->get_for_js();

		$types['image'] = array(
			'id'         => 'image',
			'name'       => 'Image',
			'controller' => 'Img',
			'templateId' => 'image',
			'data'       => array( 'mimeTypes' => Utils::image_mime_types() ),
		);

		$types['svg'] = array(
			'id'         => 'svg',
			'name'       => 'SVG',
			'controller' => 'Img',
			'templateId' => 'svg',
			'data'       => array( 'mimeTypes' => 'image/svg+xml' ),
		);

		return $types;
	}
}
