<?php

namespace Skeleton\Support;

class WP_Data {
	/**
	 * Get Wordpress specific data from the DB and return in a usable array.
	 *
	 * @param  string $type Data type.
	 * @param  mixed  $args Optional, data query args or something else.
	 * @return array
	 */
	public static function get( $type, $args = array() ) {
		$data = array();

		switch ( $type ) {
			case 'post':
			case 'posts':
				$posts = get_posts( $args );
				if ( ! empty( $posts ) ) {
					$data = wp_list_pluck( $posts, 'post_title', 'ID' );
				}
				break;

			case 'page':
			case 'pages':
				$pages = get_pages( $args );
				if ( ! empty( $pages ) ) {
					$data = wp_list_pluck( $pages, 'post_title', 'ID' );
				}
				break;

			case 'category':
			case 'categories':
				$categories = get_categories( $args );
				if ( ! empty( $categories ) ) {
					$data = wp_list_pluck( $categories, 'name', 'term_id' );
				}
				break;

			case 'tag':
			case 'tags':
				$tags = get_tags( $args );
				if ( ! empty( $tags ) ) {
					$data = wp_list_pluck( $tags, 'name', 'term_id' );
				}
				break;

			case 'menu':
			case 'menus':
				$menus = wp_get_nav_menus( $args );
				if ( ! empty( $menus ) ) {
					$data = wp_list_pluck( $menus, 'name', 'term_id' );
				}
				break;

			case 'user':
			case 'users':
				$users = get_users( $args );
				if ( ! empty( $users ) ) {
					$data = wp_list_pluck( $users, 'display_name', 'ID' );
				}
				break;

			case 'taxonomy':
			case 'taxonomies':
				$data = get_taxonomies( $args, 'names', 'and' );
				break;

			case 'term':
			case 'terms':
				$terms = get_terms( $args );
				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
					$data = wp_list_pluck( $terms, 'name', 'term_id' );
				}
				break;

			case 'sidebar':
			case 'sidebars':
				global $wp_registered_sidebars;
				$data = wp_list_pluck( $wp_registered_sidebars, 'name' );
				break;

			case 'post_type':
			case 'post_types':
				global $wp_post_types;

				$args = wp_parse_args( $args, array(
					'public' => true,
					'exclude_from_search' => false,
				) );

				$post_types = get_post_types( $args, 'names', 'and' );
				ksort( $post_types );

				foreach ( $post_types as $post_type ) {
					if ( isset( $wp_post_types[ $post_type ]->labels->menu_name ) ) {
						$data[ $post_type ] = $wp_post_types[ $post_type ]->labels->menu_name;
					} else {
						$data[ $post_type ] = ucfirst( $post_type );
					}
				}
				break;

			case 'menu_location':
			case 'menu_locations':
				global $_wp_registered_nav_menus;
				$data = $_wp_registered_nav_menus;
				break;

			case 'image_size':
			case 'image_sizes':
				global $_wp_additional_image_sizes;

				foreach ( $_wp_additional_image_sizes as $size_name => $size_attrs ) {
					$data[ $size_name ] = $size_name . ' - ' . $size_attrs['width'] . ' x ' . $size_attrs['height'];
				}
				break;

			case 'role':
			case 'roles':
				global $wp_roles; // WP_Roles instance.
				$data = $wp_roles->get_names();
				break;

			case 'capability':
			case 'capabilities':
				global $wp_roles;

				foreach ( $wp_roles->roles as $role ) {
					foreach ( $role['capabilities'] as $key => $cap ) {
						$data[ $key ] = ucwords( str_replace( '_', ' ', $key ) );
					}
				}
				break;

			case 'custom':
			case 'callback':
				if ( is_callable( $args ) ) {
					$data = call_user_func( $args );
				} elseif ( is_array( $args ) && isset( $args['call'] ) ) {
					$pass_args = isset( $args['args'] ) ? $args['args'] : array();
					$data = call_user_func_array( $args['call'], (array) $pass_args );
				}
				break;

			default:
				return null;
				break;
		} // End switch().

		return $data;
	}
}
