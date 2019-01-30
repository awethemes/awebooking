<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package awebooking
 */

/**
 * Adds a AweBooking category to the block inserter.
 *
 * @param array $categories Array of categories.
 *
 * @return array Array of block categories.
 */
function awebooking_add_block_category( $categories ) {
	return array_merge(
		$categories,
		[
			[
				'slug'  => 'awebooking',
				'title' => __( 'AweBooking', 'awebooking' ),
			],
		]
	);
}

add_filter( 'block_categories', 'awebooking_add_block_category' );

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
 */
function awebooking_rooms_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$dir = __DIR__;

	$block_dependencies = [
		'wp-api-fetch',
		'wp-blocks',
		'wp-components',
		'wp-compose',
		'wp-data',
		'wp-element',
		'wp-editor',
		'wp-i18n',
		'wp-url',
		'lodash',
	];

	$assets_url = trailingslashit( plugin_dir_url( __FILE__ ) );
	if ( file_exists( __DIR__ . '/../hot' ) ) {
		$assets_url = '//localhost:8089/blocks/';
	}

	$index_js = 'awebooking-rooms/index.js';
	wp_register_script(
		'awebooking-rooms-block-editor',
		$assets_url . $index_js,
		$block_dependencies,
		null
	);

	$editor_css = 'awebooking-rooms/editor.css';
	wp_register_style(
		'awebooking-rooms-block-editor',
		$assets_url . $editor_css,
		[],
		null
	);

	$style_css = 'awebooking-rooms/style.css';
	wp_register_style(
		'awebooking-rooms-block',
		$assets_url . $style_css,
		[],
		null
	);

	register_block_type( 'awebooking/awebooking-rooms', [
		'editor_script' => 'awebooking-rooms-block-editor',
		'editor_style'  => 'awebooking-rooms-block-editor',
		'style'         => 'awebooking-rooms-block',
	] );
}

add_action( 'init', 'awebooking_rooms_block_init' );

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
 */
function awebooking_search_form_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$dir = __DIR__;

	$block_dependencies = [
		'wp-api-fetch',
		'wp-blocks',
		'wp-components',
		'wp-compose',
		'wp-data',
		'wp-element',
		'wp-editor',
		'wp-i18n',
		'wp-url',
		'lodash',
	];

	$assets_url = trailingslashit( plugin_dir_url( __FILE__ ) );
	if ( file_exists( __DIR__ . '/../hot' ) ) {
		$assets_url = '//localhost:8089/blocks/';
	}

	$index_js = 'awebooking-search-form/index.js';
	wp_register_script(
		'awebooking-search-form-block-editor',
		$assets_url . $index_js,
		$block_dependencies,
		null
	);

	$editor_css = 'awebooking-search-form/editor.css';
	wp_register_style(
		'awebooking-search-form-block-editor',
		$assets_url . $editor_css,
		[],
		null
	);

	$style_css = 'awebooking-search-form/style.css';
	wp_register_style(
		'awebooking-search-form-block',
		$assets_url . $style_css,
		[],
		null
	);

	register_block_type( 'awebooking/awebooking-search-form', [
		'editor_script' => 'awebooking-search-form-block-editor',
		'editor_style'  => 'awebooking-search-form-block-editor',
		'style'         => 'awebooking-search-form-block',
	] );
}

add_action( 'init', 'awebooking_search_form_block_init' );

function awebooking_add_fields_to_api() {
	register_rest_field( 'room_type',
		'thumbnail_url',
		[
			'get_callback' => 'awebooking_api_get_thumbnail_url',
		]
	);

	register_rest_field( 'room_type',
		'room_name',
		[
			'get_callback' => 'awebooking_api_get_room_name',
		]
	);

	register_rest_field( 'room_type',
		'price_html',
		[
			'get_callback' => 'awebooking_api_get_price_html',
		]
	);

	register_rest_field( 'room_type',
		'short_description',
		[
			'get_callback' => 'awebooking_api_get_short_description',
		]
	);
}

// add_action( 'rest_api_init', 'awebooking_add_fields_to_api' );

/**
 * Get the value of the "thumbnail_url" field
 *
 * @param array           $object     Details of current post.
 * @param string          $field_name Name of field.
 * @param WP_REST_Request $request    Current request
 *
 * @return mixed
 */
function awebooking_api_get_thumbnail_url( $object, $field_name, $request ) {
	return get_the_post_thumbnail_url( $object['id'] );
}

/**
 * Get the value of the "room_name" field
 *
 * @param array           $object     Details of current post.
 * @param string          $field_name Name of field.
 * @param WP_REST_Request $request    Current request
 *
 * @return mixed
 */
function awebooking_api_get_room_name( $object, $field_name, $request ) {
	return abrs_get_room_type( $object['id'] )->get_title();
}

/**
 * Get the value of the "price_html" field
 *
 * @param array           $object     Details of current post.
 * @param string          $field_name Name of field.
 * @param WP_REST_Request $request    Current request
 *
 * @return mixed
 */
function awebooking_api_get_price_html( $object, $field_name, $request ) {
	ob_start();
	$room_type = abrs_get_room_type( $object['id'] );

	/* translators: %s room price */
	printf( esc_html__( 'Start from %s/night', 'awebooking' ), '<span>' . abrs_format_price( $room_type->get( 'rack_rate' ) ) . '</span>' ); // WPCS: xss ok.

	return ob_get_clean();
}

/**
 * Get the value of the "short_description" field
 *
 * @param array           $object     Details of current post.
 * @param string          $field_name Name of field.
 * @param WP_REST_Request $request    Current request
 *
 * @return mixed
 */
function awebooking_api_get_short_description( $object, $field_name, $request ) {
	return abrs_get_room_type( $object['id'] )->get( 'short_description' );
}
