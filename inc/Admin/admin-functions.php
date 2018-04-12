<?php

/**
 * List all screens by awebooking.
 *
 * @return array
 */
function abrs_admin_screens() {
	return apply_filters( 'awebooking/admin_screens', [
		'room_type',
		'awebooking',
		'edit-room_type',
		'edit-awebooking',
	]);
}

/**
 * Determine if a given string matches current route.
 *
 * @param  string|array $pattern The string route.
 * @return bool
 */
function abrs_admin_route_is( $pattern ) {
	$screen = get_current_screen();

	// Check the screen isset first.
	if ( is_null( $screen ) || 'awebooking_route' !== $screen->base ) {
		return false;
	}

	return abrs_str_is( $pattern, abrs_request()->route_path() );
}

/**
 * Add a admin notice.
 *
 * @param  string $message The notice message.
 * @param  string $level   The notice level.
 * @return \AweBooking\Component\Flash\Flash_Notifier
 */
function abrs_admin_notices( $message = null, $level = 'info' ) {
	$notices = awebooking()->make( 'admin_notices' );

	if ( is_null( $message ) ) {
		return $notices;
	}

	return $notices->add_message( $message, $level );
}

/**
 * Load a admin template.
 *
 * @param  string $template The template relative path.
 * @param  array  $vars     The data inject to template.
 * @return \AweBooking\Admin\Admin_Template|string
 */
function abrs_admin_template( $template = null, array $vars = [] ) {
	$admin_template = awebooking()->make( 'admin_template' );

	if ( is_null( $template ) ) {
		return $admin_template;
	}

	return $admin_template->get( $template, $vars );
}

/**
 * Print a admin template.
 *
 * @param  string $template The template relative path.
 * @param  array  $vars     The data inject to template.
 * @return \AweBooking\Admin\Admin_Template|string
 */
function abrs_admin_template_part( $template = null, array $vars = [] ) {
	return abrs_admin_template()->partial( $template, $vars );
}
