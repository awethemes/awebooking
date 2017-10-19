<?php
namespace AweBooking;

use Skeleton\Taxonomy;
use Skeleton\Post_Type;
use Taxonomy_Single_Term;
use AweBooking\Support\Service_Hooks;

class WP_Core_Hooks extends Service_Hooks {
	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 *
	 * @param Container $container Container instance.
	 */
	public function register( $container ) {
		// Don't push into $this->init(), this hook only active in register method.
		add_action( 'after_setup_theme', array( $this, 'register_sidebar' ) );

		add_action( 'init', array( $this, 'wpdb_table' ), 0 );
	}

	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param AweBooking $awebooking AweBooking Container instance.
	 */
	public function init( $awebooking ) {
		// Don't push into $this->register, this contructors only active in init method.
		$this->register_taxonomies();
		$this->register_post_types();
		$this->register_post_status();

		// Enable single term for location taxonomy.
		if ( $awebooking->is_multi_location() ) {
			$location_tax = new Taxonomy_Single_Term( AweBooking::HOTEL_LOCATION, [], 'select', absint( awebooking_option( 'location_default' ) ) );
			$location_tax->set( 'force_selection', true );
		}
	}

	/**
	 * Booking item meta.
	 */
	public function wpdb_table() {
		global $wpdb;

		$wpdb->tables[] = 'awebooking_booking_itemmeta';
		$wpdb->booking_itemmeta = $wpdb->prefix . 'awebooking_booking_itemmeta';
	}

	/**
	 * Register core taxonomies.
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		// Do nothing if blog is not installed.
		if ( ! is_blog_installed() ) {
			return;
		}

		/**
		 * Fire action before register.
		 *
		 * @hook awebooking/before_register_taxonomy
		 */
		do_action( 'awebooking/before_register_taxonomy' );

		// Register 'hotel_amenity' taxonomy.
		Taxonomy::make(
			AweBooking::HOTEL_AMENITY,
			apply_filters( 'awebooking/taxonomy_objects/hotel_amenity', AweBooking::ROOM_TYPE ),
			esc_html__( 'Amenity', 'awebooking' ),
			esc_html__( 'Amenities', 'awebooking' )
		)
		->set( apply_filters( 'awebooking/taxonomy_args/hotel_amenity', [
			'public'             => false,
			'hierarchical'       => true,
			'show_admin_column'  => false,
			'show_in_quick_edit' => false,
		]))
		->register();

		// Register 'hotel_service' taxonomy.
		Taxonomy::make(
			AweBooking::HOTEL_SERVICE,
			apply_filters( 'awebooking/taxonomy_objects/hotel_service', AweBooking::ROOM_TYPE ),
			esc_html__( 'Service', 'awebooking' ),
			esc_html__( 'Services', 'awebooking' )
		)
		->set( apply_filters( 'awebooking/taxonomy_args/hotel_service', [
			'public'             => false,
			'hierarchical'       => true,
			'show_admin_column'  => false,
			'show_in_quick_edit' => false,
		]))
		->register();

		// Register 'hotel_location' taxonomy.
		if ( awebooking()->is_multi_location() ) {
			Taxonomy::make(
				AweBooking::HOTEL_LOCATION,
				apply_filters( 'awebooking/taxonomy_objects/hotel_location', AweBooking::ROOM_TYPE ),
				esc_html__( 'Location', 'awebooking' ),
				esc_html__( 'Locations', 'awebooking' )
			)
			->set( apply_filters( 'awebooking/taxonomy_args/hotel_location', [
				'public'             => true,
				'hierarchical'       => false,
				'show_admin_column'  => false,
				'show_in_quick_edit' => false,
			]))
			->register();
		}

		/**
		 * Fire a action after registered taxonomies.
		 *
		 * @hook awebooking/after_register_taxonomy
		 */
		do_action( 'awebooking/after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 *
	 * @return void
	 */
	public function register_post_types() {
		// Do nothing if blog is not installed.
		if ( ! is_blog_installed() ) {
			return;
		}

		/**
		 * Fire action before register.
		 *
		 * @hook awebooking/before_register_post_type
		 */
		do_action( 'awebooking/before_register_post_type' );

		// Register 'room_type' post type.
		Post_Type::make(
			'room_type',
			esc_html__( 'Room Type', 'awebooking' ),
			esc_html__( 'Room Types', 'awebooking' )
		)
		->set( apply_filters( 'awebooking/post_type_args/room_type', [
			'menu_icon'       => 'dashicons-calendar',
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'menu_position'   => 53,
			'rewrite'   => [
				'slug'       => get_option( 'awebooking_room_type_permalink', 'room_type' ),
				'feeds'      => true,
				'with_front' => false,
			],
			'labels'    => [
				'add_new'               => esc_html__( 'New Room Type', 'awebooking' ),
				'featured_image'        => esc_html__( 'Room Type Image', 'awebooking' ),
				'set_featured_image'    => esc_html__( 'Set room type image', 'awebooking' ),
				'use_featured_image'    => esc_html__( 'Use as room type image', 'awebooking' ),
				'remove_featured_image' => esc_html__( 'Remove room type image', 'awebooking' ),
			],
		]))
		->register();

		// Register 'awebooking' post type.
		Post_Type::make(
			'awebooking',
			esc_html__( 'Booking', 'awebooking' ),
			esc_html__( 'Bookings', 'awebooking' )
		)
		->set( apply_filters( 'awebooking/post_type_args/awebooking', [
			'public'              => false,
			'rewrite'             => false,
			'query_var'           => false,
			'has_archive'         => false,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'supports'            => array( 'comments' ),
			'labels'              => array(
				'all_items' => esc_html__( 'Bookings', 'awebooking' ),
			),
		]))
		->register();

		// Pricing rates.
		Post_Type::make(
			AweBooking::PRICING_RATE,
			esc_html__( 'Rate', 'awebooking' ),
			esc_html__( 'Rates', 'awebooking' )
		)
		->set( apply_filters( 'awebooking/post_type_args/pricing_rate', [
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
			'supports'            => array( 'title', 'page-attributes' ),
		]))
		->register();

		/**
		 * Fire action after register.
		 *
		 * @hook awebooking/register_post_type
		 */
		do_action( 'awebooking/register_post_type' );
	}

	/**
	 * Register our custom post statuses, used for order status.
	 *
	 * @return void
	 */
	public function register_post_status() {

		$booking_statuses = apply_filters( 'awebooking/register_booking_statuses',
			array(
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
			)
		);

		foreach ( $booking_statuses as $status => $args ) {
			register_post_status( $status, $args );
		}
	}

	/**
	 * Register sidebar.
	 */
	public function register_sidebar() {
		register_sidebar( array(
			'name'          => esc_html__( 'AweBooking', 'awebooking' ),
			'id'            => 'awebooking-sidebar',
			'before_widget' => '<section id="%1$s" class="awebooking-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
}
