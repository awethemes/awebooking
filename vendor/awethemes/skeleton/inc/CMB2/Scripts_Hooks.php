<?php

namespace Skeleton\CMB2;

use Skeleton\Container\Service_Hooks;

class Scripts_Hooks extends Service_Hooks {
	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param Skeleton $skeleton Skeleton instance.
	 */
	public function init( $skeleton ) {
		add_action( 'admin_head', array( $this, 'admin_enqueue_styles' ), 40 );
		add_action( 'admin_footer', array( $this, 'admin_enqueue_scripts' ), 40 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_register_scripts' ), 20 );
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_register_scripts() {
		$skeleton_url = $this->container['url'];

		/**
		 * Should we load minified files?
		 */
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ) ? '' : '.min';

		/**
	 	* If we are debugging the site,
	 	* use a unique version every page load so as to ensure no cache issues.
		 */
		$version = SKELETON_VERSION;

		// Register vendor, plugins styles & scripts.
		wp_register_script( 'ace-editor', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.6/ace.js', array(), '1.2.6', true );
		wp_register_script( 'ace-ext-language_tools', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.6/ext-language_tools.js', array( 'ace-editor' ), '1.2.6', true );

		wp_register_style( 'jquery-ui-slider-pips', $skeleton_url . 'css/jquery-ui-slider-pips.css', array(), '1.11.4' );
		wp_register_script( 'jquery-ui-slider-pips', $skeleton_url . 'js/plugins/jquery-ui-slider-pips.min.js', array( 'jquery-ui-slider' ), '1.11.4', true );
		wp_register_script( 'wp-color-picker-alpha', $skeleton_url . 'js/plugins/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), '1.2.2', true );

		wp_register_style( 'skeleton', $skeleton_url . 'css/skeleton' . $suffix . '.css', array(), $version );
		wp_register_script( 'skeleton', $skeleton_url . 'js/skeleton' . $suffix . '.js', array( 'wp-util', 'jquery-effects-highlight' ), $version, true );

		wp_register_style( 'skeleton-iconpack', $skeleton_url . 'css/skeleton-iconpack' . $suffix . '.css', array( 'skeleton' ), $version );
		wp_register_script( 'skeleton-iconpack', $skeleton_url . 'js/skeleton-iconpack' . $suffix . '.js', array( 'skeleton' ), $version, true );
		wp_register_script( 'icon-picker', $skeleton_url . 'js/vendor/icon-picker' . $suffix . '.js', array( 'media-views' ), '0.5.0', true );

		// Enqueue Skeleton.
		wp_enqueue_script( 'skeleton' );

		wp_localize_script( 'skeleton', 'Skeleton', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'strings' => array(
				'warning' => esc_html__( 'Are you sure you want to do this?', 'skeleton' ),
			),
		) );

		$icon_manager = skeleton( 'iconfonts_manager');
		foreach ( $icon_manager->all() as $iconpack ) {
			$is_registerd = wp_style_is( $iconpack->stylesheet_id, 'registered' );

			if ( ! $is_registerd && $iconpack->stylesheet_uri ) {
				wp_register_style( $iconpack->stylesheet_id, $iconpack->stylesheet_uri, array(), $iconpack->version );
			}

			wp_enqueue_style( $iconpack->stylesheet_id );
		}

		wp_localize_script( 'icon-picker', 'iconPicker', array(
			'types' => $icon_manager->get_for_iconpicker_js(),
		) );

		do_action( 'skeleton/register_admin_scripts' );
	}

	/**
	 * Automatic enqueue framework style after cmb2-styles.
	 *
	 * @access private
	 */
	public function admin_enqueue_styles() {
		if ( wp_style_is( 'cmb2-styles' ) ) {
			wp_enqueue_style( 'skeleton' );
		}

		do_action( 'skeleton/admin_enqueue_styles' );
	}

	/**
	 * Automatic enqueue framework script after cmb2-scripts.
	 *
	 * @access private
	 */
	public function admin_enqueue_scripts() {
		if ( wp_script_is( 'cmb2-scripts' ) ) {
			wp_enqueue_script( 'skeleton' );
		}

		do_action( 'skeleton/admin_enqueue_scripts' );
	}
}
