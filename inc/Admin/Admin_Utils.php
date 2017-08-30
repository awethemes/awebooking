<?php
namespace AweBooking\Admin;

class Admin_Utils {

	public static function prints_weekday_checkbox( array $args ) {
		global $wp_locale;

		$args = wp_parse_args( $args, [
			'id'     => 'day_options',
			'label'  => 'abbrev',
			'before' => '',
			'after'  => '',
		]);

		$output = '';
		$week_begins = (int) get_option( 'start_of_week' );

		for ( $i = 0; $i <= 6; $i++ ) {
			$wd = (int) ( $i + $week_begins ) % 7;

			$wd_name = $wp_locale->get_weekday( $wd );
			switch ( $args['label'] ) {
				case 'initial':
					$wd_label = $wp_locale->get_weekday_initial( $wd_name );
					break;
				case 'abbrev':
					$wd_label = $wp_locale->get_weekday_abbrev( $wd_name );
					break;
				default:
					$wd_label = $wd_name;
					break;
			}

			$output .= sprintf(
				'<label title="%4$s"><input type="checkbox" name="%2$s[]" value="%1$s" checked="checked"><span>%3$s</span></label>',
				esc_attr( $wd ),
				esc_attr( $args['id'] ),
				esc_html( $wd_label ),
				esc_attr( $wd_name )
			);
		}

		print $output;
	}

	/**
	 * Create a page and store the ID in an option.
	 *
	 * @param mixed  $slug Slug for the new page.
	 * @param string $option Option name to store the page's ID.
	 * @param string $page_title (default: '') Title for the new page.
	 * @param string $page_content (default: '') Content for the new page.
	 * @param int    $post_parent (default: 0) Parent for the new page.
	 * @return int page ID
	 */
	public static function create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;

		$option_value     = awebooking_option( $option );

		if ( $option_value > 0 ) {
			$page_object = get_post( $option_value );

			if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
				// Valid page is already in place.
				return $page_object->ID;
			}
		}

		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode).
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug.
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
		}

		$valid_page_found = apply_filters( 'awebooking/create_page_id', $valid_page_found, $slug, $page_content );

		if ( $valid_page_found ) {
			if ( $option ) {
				awebooking( 'setting' )->set( $option, $valid_page_found );
			}
			return $valid_page_found;
		}

		// Search for a matching valid trashed page.
		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode).
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug.
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
		}

		if ( $trashed_page_found ) {
			$page_id   = $trashed_page_found;
			$page_data = array(
				'ID'             => $page_id,
				'post_status'    => 'publish',
			);
		 	wp_update_post( $page_data );
		} else {
			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_title'     => $page_title,
				'post_content'   => $page_content,
				'post_parent'    => $post_parent,
				'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $page_data );
		}

		if ( $option ) {
			awebooking( 'setting' )->set( $option, $page_id );
		}

		return $page_id;
	}
}
