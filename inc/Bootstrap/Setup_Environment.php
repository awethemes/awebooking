<?php
namespace AweBooking\Bootstrap;

use Skeleton\Taxonomy;
use Skeleton\Post_Type;
use AweBooking\Constants;
use AweBooking\AweBooking;

class Setup_Environment {
	/**
	 * Bootstrap the AweBooking.
	 *
	 * @param  AweBooking $awebooking The AweBooking instance.
	 * @return void
	 */
	public function bootstrap( AweBooking $awebooking ) {
		// The core things.
		add_action( 'init', [ $this, 'register_taxonomies' ], 5 );
		add_action( 'init', [ $this, 'register_post_types' ], 5 );
		add_action( 'init', [ $this, 'register_post_status' ], 10 );
		add_action( 'init', [ $this, 'register_endpoints' ], 10 );

		// In frontend.
		add_action( 'after_setup_theme', [ $this, 'register_sidebars' ] );
		add_action( 'after_setup_theme', [ $this, 'add_image_sizes' ] );
		add_action( 'after_setup_theme', [ $this, 'include_template_functions' ], 11 );
	}

	/**
	 * Add AweBooking Image sizes to WP.
	 *
	 * TODO: Clean this!!!
	 */
	public function add_image_sizes() {
		$awebooking_thumbnail = awebooking_get_image_size( 'awebooking_thumbnail' );
		$awebooking_catalog   = awebooking_get_image_size( 'awebooking_catalog' );
		$awebooking_single    = awebooking_get_image_size( 'awebooking_single' );

		add_image_size( 'awebooking_thumbnail', $awebooking_thumbnail['width'], $awebooking_thumbnail['height'], $awebooking_thumbnail['crop'] );
		add_image_size( 'awebooking_catalog', $awebooking_catalog['width'], $awebooking_catalog['height'], $awebooking_catalog['crop'] );
		add_image_size( 'awebooking_single', $awebooking_single['width'], $awebooking_single['height'], $awebooking_single['crop'] );
	}

	/**
	 * Load the AweBooking template functions.
	 *
	 * This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		include_once trailingslashit( __DIR__ ) . '/../template-functions.php';
	}

	/**
	 * Register WordPress sidebars.
	 *
	 * @return void
	 */
	public function register_sidebars() {
		register_sidebar([
			'name'          => esc_html__( 'AweBooking', 'awebooking' ),
			'id'            => 'awebooking-sidebar',
			'before_widget' => '<section id="%1$s" class="awebooking-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		]);
	}

	/**
	 * Register the endpoints and the rewrite rules.
	 *
	 * @return void
	 */
	public function register_endpoints() {
		global $wp, $wp_rewrite;

		// Register "awebooking_route" in the query var.
		$wp->add_query_var( 'awebooking_route' );

		// Gets the endpoint name.
		$endpoint_name = awebooking()->endpoint_name();

		add_rewrite_rule( '^' . $endpoint_name . '/?$', 'index.php?awebooking_route=/', 'top' );
		add_rewrite_rule( '^' . $endpoint_name . '/(.*)?', 'index.php?awebooking_route=/$matches[1]', 'top' );
		add_rewrite_rule( '^' . $wp_rewrite->index . '/' . $endpoint_name . '/?$', 'index.php?awebooking_route=/', 'top' );
		add_rewrite_rule( '^' . $wp_rewrite->index . '/' . $endpoint_name . '/(.*)?', 'index.php?awebooking_route=/$matches[1]', 'top' );
	}

	/**
	 * Register core taxonomies.
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		if ( ! is_blog_installed() || taxonomy_exists( Constants::HOTEL_AMENITY ) ) {
			return;
		}

		// Register 'hotel_amenity' taxonomy.
		Taxonomy::make( Constants::HOTEL_AMENITY,
			apply_filters( 'awebooking/taxonomy_objects/hotel_amenity', Constants::ROOM_TYPE ),
			esc_html__( 'Amenity', 'awebooking' ),
			esc_html__( 'Amenities', 'awebooking' )
		)->set( apply_filters( 'awebooking/taxonomy_args/hotel_amenity', [
			'public'             => false,
			'hierarchical'       => true,
			'show_admin_column'  => false,
			'show_in_quick_edit' => false,
			'capabilities'          => [
				'manage_terms' => 'manage_room_type_terms',
				'edit_terms'   => 'edit_room_type_terms',
				'delete_terms' => 'delete_room_type_terms',
				'assign_terms' => 'assign_room_type_terms',
			],
		]))->register();

		// Register 'hotel_service' taxonomy.
		Taxonomy::make( Constants::HOTEL_SERVICE,
			apply_filters( 'awebooking/taxonomy_objects/hotel_service', Constants::ROOM_TYPE ),
			esc_html__( 'Service', 'awebooking' ),
			esc_html__( 'Services', 'awebooking' )
		)->set( apply_filters( 'awebooking/taxonomy_args/hotel_service', [
			'public'             => false,
			'hierarchical'       => true,
			'show_admin_column'  => false,
			'show_in_quick_edit' => false,
			'capabilities'          => [
				'manage_terms' => 'manage_room_type_terms',
				'edit_terms'   => 'edit_room_type_terms',
				'delete_terms' => 'delete_room_type_terms',
				'assign_terms' => 'assign_room_type_terms',
			],
		]))->register();

		if ( awebooking()->bound( 'setting' ) && awebooking( 'setting' )->is_multi_location() ) {
			Taxonomy::make( Constants::HOTEL_LOCATION,
				apply_filters( 'awebooking/taxonomy_objects/hotel_location', Constants::ROOM_TYPE ),
				esc_html__( 'Location', 'awebooking' ),
				esc_html__( 'Locations', 'awebooking' )
			)->set( apply_filters( 'awebooking/taxonomy_args/hotel_location', [
				'public'             => true,
				'hierarchical'       => false,
				'show_admin_column'  => false,
				'show_in_quick_edit' => false,
				'capabilities'          => [
					'manage_terms' => 'manage_room_type_terms',
					'edit_terms'   => 'edit_room_type_terms',
					'delete_terms' => 'delete_room_type_terms',
					'assign_terms' => 'assign_room_type_terms',
				],
			]))->register();

			$location_tax = new \Taxonomy_Single_Term( Constants::HOTEL_LOCATION, [], 'select', absint( awebooking_option( 'location_default' ) ) );
			$location_tax->set( 'force_selection', true );
		}

		do_action( 'awebooking/register_taxonomy' );
	}

	/**
	 * Register core post-types.
	 *
	 * @return void
	 */
	public function register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( Constants::ROOM_TYPE ) ) {
			return;
		}

		Post_Type::make( Constants::ROOM_TYPE,
			esc_html__( 'Room Type', 'awebooking' ),
			esc_html__( 'Room Types', 'awebooking' )
		)->set( apply_filters( 'awebooking/post_type/args_room_type', [
			'menu_icon'       => 'dashicons-building',
			'menu_position'   => 53,
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'map_meta_cap'    => true,
			'capability_type' => 'room_type',
			'rewrite'         => [
				'slug' => get_option( 'awebooking_room_type_permalink', 'room_type' ),
				'feeds' => true,
				'with_front' => false,
			],
			'labels'    => [
				'menu_name'             => esc_html_x( 'Hotel', 'dashboard menu', 'awebooking' ),
				'all_items'             => esc_html__( 'Room Types', 'awebooking' ),
				'add_new'               => esc_html__( 'New Room Type', 'awebooking' ),
				'featured_image'        => esc_html__( 'Room Type Image', 'awebooking' ),
				'set_featured_image'    => esc_html__( 'Set room type image', 'awebooking' ),
				'use_featured_image'    => esc_html__( 'Use as room type image', 'awebooking' ),
				'remove_featured_image' => esc_html__( 'Remove room type image', 'awebooking' ),
			],
		]))->register();

		Post_Type::make( Constants::BOOKING,
			esc_html__( 'Booking', 'awebooking' ),
			esc_html__( 'Bookings', 'awebooking' )
		)->set( apply_filters( 'awebooking/post_type/args_awebooking', [
			'public'              => false,
			'rewrite'             => false,
			'query_var'           => false,
			'has_archive'         => false,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => 'awebooking',
			'supports'            => [ 'comments' ],
			'capability_type'     => 'awebooking',
			'map_meta_cap'        => true,
			'labels'              => [
				'all_items' => esc_html__( 'Bookings', 'awebooking' ),
			],
		]))->register();

		Post_Type::make( Constants::PRICING_RATE,
			esc_html__( 'Rate', 'awebooking' ),
			esc_html__( 'Rates', 'awebooking' )
		)->set( apply_filters( 'awebooking/post_type/args_pricing_rate', [
			'public'              => false,
			'rewrite'             => false,
			'query_var'           => false,
			'has_archive'         => false,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'exclude_from_search' => true,
			'hierarchical'        => true,
			'show_ui'             => false,
			'show_in_menu'        => 'edit.php?post_type=room_type',
			'supports'            => array( 'title', 'page-attributes' ),
			'capability_type'     => 'pricing_rate',
			'map_meta_cap'        => true,
			'labels'              => [
				'all_items' => esc_html__( 'Rates', 'awebooking' ),
			],
		]))->register();

		do_action( 'awebooking/register_post_type' );
	}

	/**
	 * Register our custom post statuses, used for order status.
	 *
	 * @return void
	 */
	public function register_post_status() {
		$booking_statuses = apply_filters( 'awebooking/register_booking_statuses', [
			'awebooking-pending' => array(
				'label'                     => _x( 'Pending', 'Booking status', 'awebooking' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'awebooking' ),
			),
			'awebooking-inprocess' => array(
				'label'                     => _x( 'Processing', 'Booking status', 'awebooking' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>', 'awebooking' ),
			),
			'awebooking-completed' => array(
				'label'                     => _x( 'Completed', 'Booking status', 'awebooking' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'awebooking' ),
			),
			'awebooking-cancelled' => array(
				'label'                     => _x( 'Cancelled', 'Booking status', 'awebooking' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'awebooking' ),
			),
		]);

		foreach ( $booking_statuses as $status => $args ) {
			register_post_status( $status, $args );
		}
	}
}
