<?php

use Skeleton\Skeleton;
use Skeleton\Support\WP_Data;
use Skeleton\Support\Autoload;

if ( ! function_exists( 'dd' ) ) {
	/**
	 * Dump the passed variables and end the script.
	 */
	function dd() {
		// @codingStandardsIgnoreLine
		array_map( function( $x ) { var_dump( $x ); }, func_get_args() );
		die;
	}
}

if ( ! function_exists( 'skeleton' ) ) {
	/**
	 * Get Skeleton instance.
	 *
	 * @param  string $key Optional parameter in the container.
	 * @return Skeleton|mixed
	 */
	function skeleton( $key = null ) {
		if ( ! is_null( $key ) ) {
			return Skeleton::get_instance()->make( $key );
		}

		return Skeleton::get_instance();
	}
}

if ( ! function_exists( 'wp_data' ) ) {
	/**
	 * Get Wordpress specific data from the DB and return in a usable array.
	 *
	 * @param  string $type Data type.
	 * @param  mixed  $args Optional, data query args or something else.
	 * @return array
	 */
	function wp_data( $type, $args = array() ) {
		return WP_Data::get( $type, $args );
	}
}

if ( ! function_exists( 'skeleton_psr4_autoloader' ) ) :
	/**
	 * //
	 *
	 * @param  [type] $namespace [description]
	 * @param  [type] $base_dir  [description]
	 * @return [type]            [description]
	 */
	function skeleton_psr4_autoloader( $namespace, $base_dir = null ) {
		$loader = new Autoload;

		if ( is_string( $namespace ) && $base_dir ) {
			$loader->add_namespace( $namespace, $base_dir );
		} elseif ( is_array( $namespace ) ) {
			foreach ( $namespace as $prefix => $dir ) {
				$loader->add_namespace( $prefix, $dir );
			}
		}

		$loader->register();
	}
endif;

if ( ! function_exists( 'skeleton_display_field_errors' ) ) :
	/**
	 * //
	 *
	 * @param  CMB2_Field $field CMB2 Field instance.
	 * @return void
	 */
	function skeleton_display_field_errors( CMB2_Field $field ) {
		$cmb2 = $field->get_cmb();

		// Bail if not see a CMB2 instance.
		if ( ! $cmb2 || is_wp_error( $cmb2 ) ) {
			return;
		}

		$id = $field->id( true );
		$errors = $cmb2->get_errors();

		if ( isset( $errors[ $id ] ) ) {
			$error_message = is_string( $errors[ $id ] ) ? $errors[ $id ] : $errors[ $id ][0];
			printf( '<p class="cmb2-validate-error">%s</p>', $error_message ); // WPCS: XSS OK.
		}
	}
endif;

if ( ! function_exists( 'skeleton_render_field' ) ) :
	/**
	 * //
	 *
	 * @param  CMB2_Field $field CMB2 Field instance.
	 * @return void
	 */
	function skeleton_render_field( CMB2_Field $field ) {
		$field_type = new CMB2_Types( $field );

		$field_type->render();
	}
endif;
