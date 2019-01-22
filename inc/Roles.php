<?php

namespace AweBooking;

class Roles {
	/**
	 * Create roles and capabilities.
	 *
	 * @return void
	 */
	public function create() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( null === $wp_roles ) {
			$wp_roles = new \WP_Roles(); // @codingStandardsIgnoreLine
		}

		// Hotel customer role: somebody who can only manage their profile, view and request some actions their booking.
		add_role( 'awebooking_customer', esc_html__( 'Hotel Customer', 'awebooking' ), [
			'read' => true,
		] );

		// Hotel receptionist: somebody who can view room types, bookings, services, amenities, manage to price,
		// manage availability, assists guests making hotel reservations.
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
	 * @return void
	 */
	public function remove() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( null === $wp_roles ) {
			$wp_roles = new \WP_Roles(); // @codingStandardsIgnoreLine
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

		$capability_types = apply_filters( 'abrs_manager_capability_types', [
			Constants::BOOKING,
			Constants::ROOM_TYPE,
			Constants::HOTEL_SERVICE,
			Constants::HOTEL_LOCATION,
		]);

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

		return apply_filters( 'abrs_manager_capabilities', $capabilities, $capability_types );
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

		$capability_types = apply_filters( 'abrs_receptionist_capability_types', [
			Constants::BOOKING,
			Constants::ROOM_TYPE,
			Constants::HOTEL_SERVICE,
			Constants::HOTEL_LOCATION,
		]);

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

		return apply_filters( 'abrs_receptionist_capabilities', $capabilities, $capability_types );
	}
}
