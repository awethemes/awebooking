<?php

use AweBooking\Component\Form\Form;

if ( ! defined( 'ABRS_ADMIN_PATH' ) ) {
	define( 'ABRS_ADMIN_PATH', awebooking()->plugin_path( 'inc/Admin/' ) );
}

/**
 * List all screens by awebooking.
 *
 * @return array
 */
function abrs_admin_screens() {
	return apply_filters( 'abrs_admin_screens', [
		'room_type',
		'awebooking',
		'hotel_location',
		'edit-room_type',
		'edit-awebooking',
		'hotel_service',
		'edit-hotel_service',
		'edit-hotel_amenity',
	]);
}

/**
 * Determines if current screen is awebooking screen.
 *
 * @return bool
 */
function abrs_is_awebooking_screen() {
	if ( ! $screen = get_current_screen() ) {
		return false;
	}

	if ( 'awebooking_route' === $screen->base ) {
		return true;
	}

	foreach ( abrs_admin_screens() as $_screen_id ) {
		if ( abrs_str_is( $_screen_id, $screen->id ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Determine if a given string matches current route.
 *
 * @param  string|array $pattern The string route.
 * @return bool
 */
function abrs_admin_route_is( $pattern ) {
	if ( ! $screen = get_current_screen() ) {
		return false;
	}

	// Check the screen isset first.
	if ( 'awebooking_route' !== $screen->base ) {
		return false;
	}

	return abrs_str_is( $pattern, abrs_http_request()->route_path() );
}

/**
 * Add a admin notice.
 *
 * @param  string $message The notice message.
 * @param  string $level   The notice level.
 * @return \WPLibs\Session\Flash\Flash_Notifier
 */
function abrs_flash_notices( $message = null, $level = 'info' ) {
	$notices = awebooking()->make( 'flash_notices' );

	if ( is_null( $message ) ) {
		return $notices;
	}

	return $notices->add_message( $message, $level );
}

/**
 * Alias of abrs_flash_notices function.
 *
 * @param  string $message The notice message.
 * @param  string $level   The notice level.
 * @return \WPLibs\Session\Flash\Flash_Notifier
 */
function abrs_admin_notices( $message = null, $level = 'info' ) {
	return abrs_flash_notices( $message, $level );
}

/**
 * Load a admin template.
 *
 * @param  string $template The template relative path.
 * @param  array  $vars     The data inject to template.
 * @return \AweBooking\Admin\Template|string
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
 * @return void
 */
function abrs_admin_template_part( $template = null, array $vars = [] ) {
	abrs_admin_template()->partial( $template, $vars );
}

/**
 * Prints the form controls.
 *
 * @param Form        $controls The form controls.
 * @param string|null $section  Optional, print a section only.
 *
 * @return void
 */
function abrs_admin_print_form( Form $controls, $section = null ) {
	if ( ! is_null( $section ) ) {
		$fields = abrs_optional( $controls->get_section( $section ) )->fields;
	} else {
		$fields = $controls->prop( 'fields' );
	}

	// Empty fields, leave.
	if ( empty( $fields ) ) {
		return;
	}

	$new_row = false;

	// Output the row.
	echo '<div class="abrow">';

	foreach ( $fields as $field ) {
		// Close current row, then open new one.
		if ( $new_row || ( isset( $field['grid_row'] ) && $field['grid_row'] ) ) {
			echo '</div><div class="abrow">';
		}

		$column = ! empty( $field['grid_column'] ) ? $field['grid_column'] : null;

		if ( in_array( $column, [ '0', '', 'auto' ] ) ) {
			$column_class = 'abcol';
		} else {
			$column_class = 'abcol-sm-12 abcol-' . $column;
		}

		echo '<div class="' . esc_attr( $column_class ) . '">';

		switch ( $field['type'] ) {
			case 'title':
				$new_row = true;
				echo '<div class="abrs-postbox-title"><h3>' . abrs_esc_text( $field['name'] ) . '</h3></div>'; // WPCS: XSS OK.
				break;

			case 'note':
				echo '<div class="abrs-note">';
				echo isset( $field['heading'] ) ? '<h4>' . abrs_esc_text( $field['heading'] ) . '</h4>' : ''; // WPCS: XSS OK.
				echo wp_kses_post( wpautop( $field['note'] ) );
				echo '</div>';
				break;

			default:
				$controls->show_field( $field );
				break;
		}

		echo '</div>';
	}

	// Close the row.
	echo '</div>';
}

/**
 * Create a page and store the ID in an option.
 *
 * @param  mixed  $slug         Slug for the new page.
 * @param  string $option       Option name to store the page's ID.
 * @param  string $page_title   Title for the new page.
 * @param  string $page_content Content for the new page.
 * @param  int    $post_parent  Parent for the new page.
 * @return int
 */
function abrs_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;

	$option_value = get_option( $option );

	if ( $option_value > 0
		&& ( $page_object = get_post( $option_value ) )
		&& 'page' === $page_object->post_type
		&& ! in_array( $page_object->post_status, [ 'pending', 'trash', 'future', 'auto-draft' ] ) ) {
		return $page_object->ID;
	}

	if ( '' !== $page_content ) {
		// Query for an existing page with the specified page content (typically a shortcode).
		$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		// Query for an existing page with the specified page slug.
		$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
	}

	$valid_page_found = apply_filters( 'abrs_create_page_id', $valid_page_found, $slug, $page_content );

	if ( $valid_page_found ) {
		if ( $option ) {
			update_option( $option, $valid_page_found );
		}
		return $valid_page_found;
	}

	// Query for a matching valid trashed page.
	if ( '' !== $page_content ) {
		// Query for an existing page with the specified page content (typically a shortcode).
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		// Query for an existing page with the specified page slug.
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
	}

	if ( $trashed_page_found ) {
		$page_id = $trashed_page_found;

		wp_update_post([
			'ID'          => $page_id,
			'post_status' => 'publish',
		]);
	} else {
		$page_id = wp_insert_post([
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $page_title,
			'post_content'   => $page_content,
			'post_parent'    => $post_parent,
			'comment_status' => 'closed',
		]);
	}

	if ( $option ) {
		update_option( $option, $page_id );
	}

	return $page_id;
}
