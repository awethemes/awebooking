<?php
namespace AweBooking;

class Roles {
	/**
	 * Create roles and capabilities.
	 *
	 * @access private
	 */
	public function create() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) || is_null( $wp_roles ) ) {
			return;
		}

		// Hotel customer role: somebody who can only manage their profile, view and request some actions their booking.
		add_role( 'awebooking_customer', esc_html__( 'Hotel Customer', 'awebooking' ), [
			'read' => true,
		] );

		// Hotel receptionist: somebody who can view room types, services, amenities, manage to price, manage availability, assists guests making hotel reservations.
		add_role( 'awebooking_receptionist', esc_html__( 'Hotel Receptionist', 'awebooking' ), [
			'level_9'                => true,
			'level_8'                => true,
			'level_7'                => true,
			'level_6'                => true,
			'level_5'                => true,
			'level_4'                => true,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'read'                   => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true,
			'edit_posts'             => true,
			'edit_pages'             => true,
			'publish_posts'          => true,
			'publish_pages'          => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
		] );

		foreach ( $this->get_core_receptionist_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'awebooking_receptionist', $cap );
			}
		}

		// Hotel manager role: somebody who has access to all the AweBooking's features.
		add_role( 'awebooking_manager', esc_html__( 'Hotel Manager', 'awebooking' ), [
			'level_9'                => true,
			'level_8'                => true,
			'level_7'                => true,
			'level_6'                => true,
			'level_5'                => true,
			'level_4'                => true,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'read'                   => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true,
			'edit_users'             => true,
			'edit_posts'             => true,
			'edit_pages'             => true,
			'edit_published_posts'   => true,
			'edit_published_pages'   => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_others_posts'      => true,
			'edit_others_pages'      => true,
			'publish_posts'          => true,
			'publish_pages'          => true,
			'delete_posts'           => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'delete_others_posts'    => true,
			'delete_others_pages'    => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'upload_files'           => true,
			'export'                 => true,
			'import'                 => true,
			'list_users'             => true,
		] );

		foreach ( $this->get_core_manager_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'awebooking_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Remove roles and capabilities.
	 *
	 * @access private
	 */
	public function remove() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) || is_null( $wp_roles ) ) {
			return;
		}

		foreach ( $this->get_core_receptionist_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'awebooking_receptionist', $cap );
			}
		}

		foreach ( $this->get_core_manager_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'awebooking_manager', $cap );
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}

		remove_role( 'awebooking_customer' );
		remove_role( 'awebooking_receptionist' );
		remove_role( 'awebooking_manager' );
	}

	/**
	 * Get manager capabilities for awebooking.
	 *
	 * @return array
	 */
	protected function get_core_manager_capabilities() {
		$capabilities = [];

		$capabilities['core'] = [
			'manage_awebooking',
			'manage_awebooking_settings',
		];

		$capability_types = [
			Constants::BOOKING,
			Constants::ROOM_TYPE,
			Constants::HOTEL_SERVICE,
			Constants::HOTEL_LOCATION,
		];

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = [
				// Post type.
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms.
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms",
			];
		}

		return $capabilities;
	}

	/**
	 * Get receptionist capabilities for awebooking.
	 *
	 * @return array
	 */
	protected function get_core_receptionist_capabilities() {
		$capabilities = [];

		$capabilities['core'] = [
			'manage_awebooking',
		];

		// Room type.
		$room_type = Constants::ROOM_TYPE;

		$capabilities[ $room_type ] = [
			"read_{$room_type}",
			"edit_{$room_type}s",
			"edit_others_{$room_type}s",
			"read_private_{$room_type}s",

			"manage_{$room_type}_terms",
			// "assign_{$room_type}_terms",
		];

		// Booking.
		$booking = Constants::BOOKING;

		$capabilities[ $booking ] = [
			// Post type.
			"edit_{$booking}",
			"read_{$booking}",
			"delete_{$booking}",
			"edit_{$booking}s",
			"edit_others_{$booking}s",
			"publish_{$booking}s",
			"read_private_{$booking}s",
			"delete_{$booking}s",
			"delete_private_{$booking}s",
			"delete_published_{$booking}s",
			"delete_others_{$booking}s",
			"edit_private_{$booking}s",
			"edit_published_{$booking}s",

			// Terms.
			"manage_{$booking}_terms",
			"edit_{$booking}_terms",
			"delete_{$booking}_terms",
			"assign_{$booking}_terms",
		];

		return $capabilities;
	}
}
