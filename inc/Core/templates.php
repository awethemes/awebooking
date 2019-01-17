<?php

use AweBooking\Frontend\Search\Search_Form;

/**
 * Returns the search form default attributes.
 *
 * @return array
 */
function abrs_search_form_default_atts() {
	return apply_filters( 'abrs_search_form_default_atts', [
		'template'        => '',
		'layout'          => 'horizontal',
		'alignment'       => '',
		'hotel_location'  => true,
		'occupancy'       => true,
		'only_room'       => null,
		'container_class' => '',
	]);
}

/**
 * Display the search rooms form.
 *
 * @param  array   $atts The search form attributes.
 * @param  boolean $echo Is echo or not (return the form).
 * @return string
 */
function abrs_get_search_form( $atts = [], $echo = true ) {
	static $instance = 1;

	$abrs_query = isset( $GLOBALS['abrs_query'] ) ? $GLOBALS['abrs_query'] : null;

	// Pairs the input atts.
	$atts = wp_parse_args( $atts, abrs_search_form_default_atts() );
	$atts['res_request'] = isset( $atts['res_request'] ) ? $atts['res_request'] : null;

	// TODO: Consider improve this!
	if ( ! empty( $_GET['only'] ) && empty( $atts['only_room'] ) && abrs_is_search_page() ) {
		$atts['only_room'] = sanitize_text_field( wp_unslash( $_GET['only'] ) );
	}

	/**
	 * Fires before the search form is retrieved.
	 *
	 * @param array $atts The form attributes.
	 */
	do_action( 'abrs_pre_get_search_form', $atts );

	if ( ! $atts['res_request'] && ( $abrs_query && $abrs_query->res_request ) ) {
		$res_request = $abrs_query->res_request;
	} else {
		$res_request = abrs_create_res_request([
			'check_in'  => 'today',
			'check_out' => 'tomorrow',
		]);
	}

	if ( is_null( $res_request ) || is_wp_error( $res_request ) ) {
		$res_request = null;
	}

	$search_form = new Search_Form( $atts );

	$result = $res_request->display( $search_form );

	/**
	 * Filters the HTML output of the search form.
	 *
	 * @param string $form The search form HTML output.
	 * @param array  $atts The form attributes.
	 */
	$result = apply_filters( 'abrs_get_search_form', $result, $atts );

	if ( $echo ) {
		echo $result; // WPCS: XSS OK.
	} else {
		return $result;
	}
}

/**
 * Display the search form hidden fields on the search form.
 *
 * @param array $atts //.
 */
function abrs_search_form_hidden_fields( $atts ) {
	?>
	<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
		<input type="hidden" name="p" value="<?php echo esc_attr( abrs_get_page_id( 'check_availability' ) ); ?>">
	<?php endif; ?>

	<?php if ( abrs_running_on_multilanguage() ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( abrs_multilingual()->get_current_language() ); ?>">
	<?php endif; ?>

	<?php if ( abrs_is_room_type() ) : ?>
		<input type="hidden" name="hotel" value="<?php echo esc_attr( abrs_get_room_type( get_the_ID() )->get( 'hotel_id' ) ); ?>">
	<?php endif; ?>

	<?php if ( ! empty( $atts['only_room'] ) ) : ?>
		<input type="hidden" name="only" value="<?php echo esc_attr( implode( ',', wp_parse_id_list( $atts['only_room'] ) ) ); ?>">
	<?php endif; ?>
	<?php
}

/**
 * Display the "book now" button.
 *
 * @param  array   $args The args.
 * @param  boolean $echo Is echo or not.
 * @return string
 */
function abrs_book_room_button( $args, $echo = true ) {
	global $abrs_query;

	$args = wp_parse_args( $args, [
		'room_type'   => 0,
		'rate_plan'   => 0,
		'show_button' => true,
		'button_text' => esc_html__( 'Book Now', 'awebooking' ),
		'button_atts' => [],
	]);

	$res_request = ( $abrs_query && $abrs_query->res_request )
		? $abrs_query->res_request
		: null;

	$button = abrs_get_template_content( 'book-button.php', compact( 'args', 'res_request' ) );

	/**
	 * Filters the HTML output of the search form.
	 *
	 * @param string $form The search form HTML output.
	 * @param array  $atts The form attributes.
	 */
	$button = apply_filters( 'abrs_book_room_button', $button, $args );

	if ( $echo ) {
		echo $button; // WPCS: XSS OK.
	} else {
		return $button;
	}
}
