<?php
namespace Skeleton\Iconfonts;

use Skeleton\WP_Option;
use Skeleton\Container\Service_Hooks;

class Iconfonts_Hooks extends Service_Hooks {
	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 *
	 * @param Skeleton $skeleton Skeleton instance.
	 */
	public function register( $skeleton ) {
		$upload_dir = wp_upload_dir();

		$skeleton['iconfonts_upload_tmp_dir']   = $upload_dir['basedir'] . '/skeleton-tmp/';
		$skeleton['iconfonts_upload_icons_dir'] = $upload_dir['basedir'] . '/skeleton-iconfonts/';
		$skeleton['iconfonts_upload_icons_url'] = $upload_dir['baseurl'] . '/skeleton-iconfonts/';

		$skeleton->bind( 'iconfonts_manager', function ( $skeleton ) {
			return new Manager( $skeleton );
		});

		$skeleton->bind( 'iconfonts_options', function () {
			return new WP_Option( '_skeleton_icons' );
		});

		$skeleton->bind( 'iconfonts_installer', function ( $skeleton ) {
			return new Installer( $skeleton, $skeleton['iconfonts_manager'], $skeleton['iconfonts_options'] );
		});

		$skeleton->bind( 'iconfonts_admin_uploader', function ( $skeleton ) {
			return new Admin_Uploader( $skeleton, $skeleton['iconfonts_manager'], $skeleton['iconfonts_installer'] );
		});
	}

	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param Skeleton $skeleton Skeleton instance.
	 */
	public function init( $skeleton ) {
		add_filter( 'upload_mimes', array( $this, 'svg_mime_support' ) );
		// add_action( 'admin_menu', array( $this, 'add_submenu' ) );

		add_filter( 'media_view_strings', array( $this, '_media_view_strings' ) );
		add_action( 'print_media_templates', array( $this, '_media_templates' ) );
	}

	/**
	 * Media templates.
	 *
	 * @return void
	 */
	public function _media_templates() {
		include trailingslashit( __DIR__ ) . 'views/media_templates.php';
	}

	/**
	 * Filter media view strings
	 *
	 * @param  array  $strings Media view strings.
	 * @return array
	 */
	public function _media_view_strings( $strings ) {
		$strings['iconPicker'] = array(
			'frameTitle' => esc_html__( 'Icon Picker', 'skeleton' ),
			'allFilter'  => esc_html__( 'All', 'skeleton' ),
			'selectIcon' => esc_html__( 'Select Icon', 'skeleton' ),
		);

		return $strings;
	}

	public function add_submenu() {
		add_options_page(
			esc_html__( 'Font Icons', 'awebooking' ),
			esc_html__( 'Font Icons', 'awebooking' ),
			'manage_options',
			'skeleton-iconfonts',
			array( $this->container['iconfonts_admin_uploader'], 'output' )
		);
	}

	/**
	 * Add SVG support.
	 *
	 * @param array $mimes Array mimes type.
	 * @return array
	 */
	public function svg_mime_support( array $mimes ) {
		if ( ! isset( $mimes['svg'] ) ) {
			$mimes['svg'] = 'image/svg+xml';
		}

		return $mimes;
	}
}
