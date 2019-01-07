<?php

namespace AweBooking\Core\Bootstrap;

use AweBooking\Plugin;
use AweBooking\Constants;

class Setup_Environment {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * Setup environment bootstrapper.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Bootstrap the plugin.
	 *
	 * @return void
	 */
	public function bootstrap() {
		// Register WP core.
		add_action( 'init', [ $this, 'register_endpoints' ], 1 );
		add_action( 'init', [ $this, 'register_taxonomies' ], 5 );
		add_action( 'init', [ $this, 'register_post_types' ], 5 );
		add_action( 'init', [ $this, 'register_post_status' ], 10 );
		add_action( 'after_setup_theme', [ $this, 'add_image_sizes' ] );
	}

	/**
	 * Add Awebooking Image sizes to WP.
	 *
	 * @access private
	 */
	public function add_image_sizes() {
		$thumbnail = abrs_get_image_size( 'thumbnail' );
		$archive   = abrs_get_image_size( 'archive' );
		$single    = abrs_get_image_size( 'single' );

		add_image_size( 'awebooking_thumbnail', $thumbnail['width'], $thumbnail['height'], $thumbnail['crop'] );
		add_image_size( 'awebooking_archive', $archive['width'], $archive['height'], $archive['crop'] );
		add_image_size( 'awebooking_single', $single['width'], $single['height'], $single['crop'] );
	}

	/**
	 * Register the endpoints and the rewrite rules.
	 *
	 * @access private
	 */
	public function register_endpoints() {
		global $wp, $wp_rewrite;

		// Register "awebooking_route" in the query var.
		$wp->add_query_var( 'awebooking_route' );

		// Gets the endpoint name.
		$endpoint_name = $this->plugin->endpoint_name();

		add_rewrite_rule( '^' . $endpoint_name . '/?$', 'index.php?awebooking_route=/', 'top' );
		add_rewrite_rule( '^' . $endpoint_name . '/(.*)?', 'index.php?awebooking_route=/$matches[1]', 'top' );
		add_rewrite_rule( '^' . $wp_rewrite->index . '/' . $endpoint_name . '/?$', 'index.php?awebooking_route=/', 'top' );
		add_rewrite_rule( '^' . $wp_rewrite->index . '/' . $endpoint_name . '/(.*)?', 'index.php?awebooking_route=/$matches[1]', 'top' );
	}

	/**
	 * Register core taxonomies.
	 *
	 * @access private
	 */
	public function register_taxonomies() {
		if ( ! is_blog_installed() || taxonomy_exists( Constants::HOTEL_AMENITY ) ) {
			return;
		}

		do_action( 'abrs_register_taxonomy' );

		$capabilities = [
			'manage_terms' => 'manage_room_type_terms',
			'edit_terms'   => 'edit_room_type_terms',
			'delete_terms' => 'delete_room_type_terms',
			'assign_terms' => 'assign_room_type_terms',
		];

		register_taxonomy( Constants::HOTEL_AMENITY, Constants::ROOM_TYPE, apply_filters( 'abrs_register_amenity_args', [
			'labels'              => [
				'name'                  => esc_html_x( 'Amenities', 'Amenity plural name', 'awebooking' ),
				'singular_name'         => esc_html_x( 'Amenity', 'Amenity singular name', 'awebooking' ),
				'menu_name'             => esc_html_x( 'Amenities', 'Admin menu name', 'awebooking' ),
				'search_items'          => esc_html__( 'Query amenities', 'awebooking' ),
				'popular_items'         => esc_html__( 'Popular amenities', 'awebooking' ),
				'all_items'             => esc_html__( 'All amenities', 'awebooking' ),
				'parent_item'           => esc_html__( 'Parent amenity', 'awebooking' ),
				'parent_item_colon'     => esc_html__( 'Parent amenity', 'awebooking' ),
				'edit_item'             => esc_html__( 'Edit amenity', 'awebooking' ),
				'update_item'           => esc_html__( 'Update amenity', 'awebooking' ),
				'add_new_item'          => esc_html__( 'Add New Amenity', 'awebooking' ),
				'new_item_name'         => esc_html__( 'New Amenity Name', 'awebooking' ),
				'add_or_remove_items'   => esc_html__( 'Add or remove amenities', 'awebooking' ),
				'choose_from_most_used' => esc_html__( 'Choose from most used amenities', 'awebooking' ),
			],
			'hierarchical'        => true,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_quick_edit'  => false,
			'show_admin_column'   => false,
			'rewrite'             => false,
			'query_var'           => false,
			'capabilities'        => $capabilities,
		]));

		do_action( 'abrs_after_register_taxonomy' );
	}

	/**
	 * Register core post-types.
	 *
	 * @access private
	 */
	public function register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( Constants::ROOM_TYPE ) ) {
			return;
		}

		do_action( 'abrs_register_post_types' );

		// Get the room type slug.
		$room_type_slug = apply_filters( 'abrs_room_type_slug',
			get_option( 'awebooking_room_type_permalink', 'room_type' )
		);

		register_post_type( Constants::ROOM_TYPE, apply_filters( 'abrs_register_room_type_args', [
			'labels'              => [
				'name'                  => esc_html_x( 'Room Types', 'Room type plural name', 'awebooking' ),
				'singular_name'         => esc_html_x( 'Room type', 'Room type singular name', 'awebooking' ),
				'menu_name'             => esc_html_x( 'Hotel', 'Admin menu name', 'awebooking' ), /* TODO: Change this label depend by context. */
				'all_items'             => esc_html__( 'Room Types', 'awebooking' ),
				'add_new'               => esc_html__( 'Add New', 'awebooking' ),
				'add_new_item'          => esc_html__( 'Add new room type', 'awebooking' ),
				'edit'                  => esc_html__( 'Edit', 'awebooking' ),
				'edit_item'             => esc_html__( 'Edit room type', 'awebooking' ),
				'new_item'              => esc_html__( 'New room type', 'awebooking' ),
				'view_item'             => esc_html__( 'View room type', 'awebooking' ),
				'view_items'            => esc_html__( 'View room types', 'awebooking' ),
				'search_items'          => esc_html__( 'Query room types', 'awebooking' ),
				'not_found'             => esc_html__( 'No room types found', 'awebooking' ),
				'not_found_in_trash'    => esc_html__( 'No room types found in trash', 'awebooking' ),
				'parent'                => esc_html__( 'Parent room type', 'awebooking' ),
				'featured_image'        => esc_html__( 'Room type image', 'awebooking' ),
				'set_featured_image'    => esc_html__( 'Set room type image', 'awebooking' ),
				'remove_featured_image' => esc_html__( 'Remove room type image', 'awebooking' ),
				'use_featured_image'    => esc_html__( 'Use as room type image', 'awebooking' ),
				'insert_into_item'      => esc_html__( 'Insert into room type', 'awebooking' ),
				'uploaded_to_this_item' => esc_html__( 'Uploaded to this room type', 'awebooking' ),
				'filter_items_list'     => esc_html__( 'Filter room types', 'awebooking' ),
				'items_list_navigation' => esc_html__( 'Room Types navigation', 'awebooking' ),
				'items_list'            => esc_html__( 'Room Types list', 'awebooking' ),
			],
			'description'         => esc_html__( 'This is where you can add new room type to your hotel.', 'awebooking' ),
			'public'              => true,
			'hierarchical'        => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'menu_position'       => 53,
			'menu_icon'           => 'dashicons-building',
			'map_meta_cap'        => true,
			'capability_type'     => Constants::ROOM_TYPE,
			'supports'            => [ 'title', 'editor', 'thumbnail' ],
			'has_archive'         => true,
			'rewrite'             => [
				'slug'       => $room_type_slug,
				'with_front' => false,
			],
		]));

		register_post_type( Constants::BOOKING, apply_filters( 'abrs_register_booking_args', [
			'labels'              => [
				'name'                  => esc_html_x( 'Bookings', 'Booking plural name', 'awebooking' ),
				'singular_name'         => esc_html_x( 'Booking', 'Booking singular name', 'awebooking' ),
				'menu_name'             => esc_html_x( 'Bookings', 'Admin menu name', 'awebooking' ),
				'add_new'               => esc_html__( 'Add booking', 'awebooking' ),
				'add_new_item'          => esc_html__( 'Add new booking', 'awebooking' ),
				'edit'                  => esc_html__( 'Edit', 'awebooking' ),
				'edit_item'             => esc_html__( 'Edit booking', 'awebooking' ),
				'new_item'              => esc_html__( 'New booking', 'awebooking' ),
				'view_item'             => esc_html__( 'View booking', 'awebooking' ),
				'search_items'          => esc_html__( 'Query bookings', 'awebooking' ),
				'not_found'             => esc_html__( 'No bookings found', 'awebooking' ),
				'not_found_in_trash'    => esc_html__( 'No bookings found in trash', 'awebooking' ),
				'parent'                => esc_html__( 'Parent bookings', 'awebooking' ),
				'filter_items_list'     => esc_html__( 'Filter bookings', 'awebooking' ),
				'items_list_navigation' => esc_html__( 'Bookings navigation', 'awebooking' ),
				'items_list'            => esc_html__( 'Bookings List', 'awebooking' ),
			],
			'description'         => esc_html__( 'This is where store bookings are stored.', 'awebooking' ),
			'public'              => false,
			'hierarchical'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => 'awebooking',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'show_in_rest'        => true,
			'map_meta_cap'        => true,
			'capability_type'     => Constants::BOOKING,
			'supports'            => [ 'comments' ],
			'rewrite'             => false,
			'has_archive'         => false,
		]));

		register_post_type( Constants::HOTEL_SERVICE, apply_filters( 'abrs_register_service_args', [
			'labels'              => [
				'name'                  => esc_html_x( 'Services', 'Service plural name', 'awebooking' ),
				'singular_name'         => esc_html_x( 'Service', 'Service singular name', 'awebooking' ),
				'menu_name'             => esc_html_x( 'Services', 'Admin menu name', 'awebooking' ),
				'add_new'               => esc_html__( 'Add service', 'awebooking' ),
				'add_new_item'          => esc_html__( 'Add new service', 'awebooking' ),
				'edit'                  => esc_html__( 'Edit', 'awebooking' ),
				'edit_item'             => esc_html__( 'Edit service', 'awebooking' ),
				'new_item'              => esc_html__( 'New service', 'awebooking' ),
				'view_item'             => esc_html__( 'View service', 'awebooking' ),
				'search_items'          => esc_html__( 'Query services', 'awebooking' ),
				'not_found'             => esc_html__( 'No services found', 'awebooking' ),
				'not_found_in_trash'    => esc_html__( 'No services found in trash', 'awebooking' ),
				'parent'                => esc_html__( 'Parent services', 'awebooking' ),
				'filter_items_list'     => esc_html__( 'Filter services', 'awebooking' ),
				'items_list_navigation' => esc_html__( 'Services navigation', 'awebooking' ),
				'items_list'            => esc_html__( 'Services List', 'awebooking' ),
			],
			'description'         => esc_html__( 'This is where store services are stored.', 'awebooking' ),
			'public'              => false,
			'hierarchical'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=room_type',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'show_in_rest'        => true,
			'map_meta_cap'        => true,
			'capability_type'     => Constants::HOTEL_SERVICE,
			'supports'            => [ 'title', 'thumbnail' ],
			'rewrite'             => false,
			'has_archive'         => false,
		]));

		// Enable multiple_hotels.
		if ( abrs_multiple_hotels() ) {
			$hotel_slug = apply_filters( 'abrs_hotel_slug',
				get_option( 'awebooking_hotel_permalink', 'hotel_location' )
			);

			register_post_type( Constants::HOTEL_LOCATION, apply_filters( 'abrs_register_location_args', [
				'labels'              => [
					'name'                  => esc_html_x( 'Hotels', 'Hotel plural name', 'awebooking' ),
					'singular_name'         => esc_html_x( 'Hotel', 'Hotel singular name', 'awebooking' ),
					'menu_name'             => esc_html_x( 'Hotels', 'Admin menu name', 'awebooking' ),
					'add_new'               => esc_html__( 'Add hotel', 'awebooking' ),
					'add_new_item'          => esc_html__( 'Add new hotel', 'awebooking' ),
					'edit'                  => esc_html__( 'Edit', 'awebooking' ),
					'edit_item'             => esc_html__( 'Edit hotel', 'awebooking' ),
					'new_item'              => esc_html__( 'New hotel', 'awebooking' ),
					'view_item'             => esc_html__( 'View hotel', 'awebooking' ),
					'search_items'          => esc_html__( 'Query hotel', 'awebooking' ),
					'not_found'             => esc_html__( 'No hotel found', 'awebooking' ),
					'not_found_in_trash'    => esc_html__( 'No hotel found in trash', 'awebooking' ),
					'parent'                => esc_html__( 'Parent hotel', 'awebooking' ),
					'filter_items_list'     => esc_html__( 'Filter hotel', 'awebooking' ),
					'items_list_navigation' => esc_html__( 'Hotels navigation', 'awebooking' ),
					'items_list'            => esc_html__( 'Hotels List', 'awebooking' ),
				],
				'public'              => true,
				'hierarchical'        => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=room_type',
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'show_in_rest'        => true,
				'map_meta_cap'        => true,
				'capability_type'     => Constants::HOTEL_LOCATION,
				'supports'            => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
				'has_archive'         => true,
				'rewrite'             => [
					'slug'       => $hotel_slug,
					'with_front' => false,
				],
			]));
		}

		do_action( 'abrs_after_register_post_types' );
	}

	/**
	 * Register custom post statuses, used for booking status.
	 *
	 * @access private
	 */
	public function register_post_status() {
		register_post_status( 'awebooking-pending', [
			'label'                     => _x( 'Pending', 'Booking status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'awebooking' ), // @codingStandardsIgnoreLine
		]);

		register_post_status( 'awebooking-inprocess', [
			'label'                     => _x( 'Processing', 'Booking status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>', 'awebooking' ), // @codingStandardsIgnoreLine
		]);

		register_post_status( 'awebooking-on-hold', [
			'label'                     => _x( 'Reserved', 'Booking status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Reserved <span class="count">(%s)</span>', 'Reserved <span class="count">(%s)</span>', 'awebooking' ), // @codingStandardsIgnoreLine
		]);

		register_post_status( 'awebooking-deposit', [
			'label'                     => _x( 'Deposit', 'Booking status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Deposit <span class="count">(%s)</span>', 'Deposit <span class="count">(%s)</span>', 'awebooking' ), // @codingStandardsIgnoreLine
		]);

		register_post_status( 'awebooking-completed', [
			'label'                     => _x( 'Paid', 'Booking status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Paid <span class="count">(%s)</span>', 'Paid <span class="count">(%s)</span>', 'awebooking' ), // @codingStandardsIgnoreLine
		]);

		register_post_status( 'checked-in', [
			'label'                     => _x( 'Checked In', 'Booking status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Checked In <span class="count">(%s)</span>', 'Checked In <span class="count">(%s)</span>', 'awebooking' ), // @codingStandardsIgnoreLine
		]);

		register_post_status( 'checked-out', [
			'label'                     => _x( 'Checked Out', 'Booking status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Checked Out <span class="count">(%s)</span>', 'Checked Out <span class="count">(%s)</span>', 'awebooking' ), // @codingStandardsIgnoreLine
		]);

		register_post_status( 'awebooking-cancelled', [
			'label'                     => _x( 'Cancelled', 'Booking status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'awebooking' ), // @codingStandardsIgnoreLine
		]);
	}
}
