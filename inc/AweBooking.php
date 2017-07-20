<?php
namespace AweBooking;

use Skeleton\WP_Option;
use Skeleton\Container\Container as SkeletonContainer;

use AweBooking\BAT\Availability;
use AweBooking\Admin\Admin_Hooks;

class AweBooking extends SkeletonContainer {
	const VERSION = '3.0-beta';

	/* Constants */
	const DATE_FORMAT    = 'Y-m-d';
	const JS_DATE_FORMAT = 'YYYY-MM-DD';

	const BOOKING        = 'awebooking';
	const ROOM_TYPE      = 'room_type';
	const HOTEL_LOCATION = 'hotel_location';
	const HOTEL_AMENITY  = 'hotel_amenity';
	const HOTEL_SERVICE  = 'hotel_extra_service';

	const SETTING_KEY    = 'awebooking_settings';

	/**
	 * //
	 *
	 * @var array
	 */
	protected $ajax = [];

	/**
	 * The current globally available container (if any).
	 *
	 * @var static
	 */
	public static $instance;

	/**
	 * Set the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * So, let we create great booking plugin for you!
	 */
	public function __construct() {
		parent::__construct();

		$this->setup();

		$this->trigger( new WP_Core_Hooks );
		$this->trigger( new WP_Query_Hooks );
		$this->trigger( new Logic_Hooks );

		$this->trigger( new Admin_Hooks );
		$this->trigger( new Template_Hooks );
		$this->trigger( new Request_Handler );
		$this->trigger( new Ajax_Controller_Hooks );

		new Service_Tax;

		static::$instance = $this;
		do_action( 'awebooking/booting', $this );
	}

	/**
	 * Setup the core AweBooking.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['url'] = $this->plugin_url();
		$this['path'] = $this->plugin_path();

		$this['multilingual'] = function () {
			return new Support\Multilingual;
		};

		$this->bind( 'option_key', function( $a ) {
			$setting_key = AweBooking::SETTING_KEY;

			if ( $a->is_multi_language() ) {
				$active_language = $a['multilingual']->get_active_language();

				// If active language is "en", or all.
				if ( ! in_array( $active_language , [ '', 'en', 'all' ] ) ) {
					$setting_key .= '_' . $active_language;
				}
			}

			return $setting_key;
		});

		// We use WP_Option for fetch/modify WP options.
		$this['wp_option'] = function ( $a ) {
			return new WP_Option( $a['option_key'] );
		};

		$this['config'] = function ( $awebooking ) {
			return new Config( $awebooking['wp_option'] );
		};

		$this['factory'] = function ( $awebooking ) {
			return new Factory( $awebooking );
		};

		$this['currency_manager'] = function ( $awebooking ) {
			return new Currency\Currency_Manager( $awebooking['config'] );
		};

		$this['currency'] = function ( $awebooking ) {
			$code = $awebooking['currency_manager']->get_current_currency();

			return new Currency\Currency( $code,
				$awebooking['currency_manager']->get_currency( $code )
			);
		};

		$this['request'] = function () {
			return new Support\Http_Request;
		};

		$this['flash_message'] = function () {
			return new Support\Flash_Message;
		};

		// Binding stores.
		$this->bind( 'store.booking', function() {
			return new BAT\Store( 'awebooking_booking', 'room_id' );
		});

		$this->bind( 'store.availability', function() {
			return new BAT\Store( 'awebooking_availability', 'room_id' );
		});

		$this->bind( 'store.pricing', function() {
			return new BAT\Store( 'awebooking_pricing', 'rate_id' );
		});

		$this->bind( 'store.room', function() {
			return new Stores\Room_Store;
		});

		$this->bind( 'store.room_type', function( $awebooking ) {
			return new Stores\Room_Type_Store( $awebooking['store.room'] );
		});

		$this->bind( 'concierge', function( $awebooking ) {
			return new BAT\Concierge( $awebooking );
		});

		add_action( 'widgets_init', [ $this, 'register_widgets' ] );

	}

	/**
	 * Register Awebooking_Check_Availability_Widget
	 */
	public function register_widgets() {
		register_widget( 'AweBooking\\Widgets\\Check_Availability_Widget' );
	}

	/**
	 * Fire registerd service hooks.
	 */
	public function boot() {
		do_action( 'awebooking/init', $this );

		parent::boot();

		Shortcodes::init();

		new Migrations\AweBooking2_Migration;

		// Make sure the options are copied if needed.
		if ( $this->is_multi_language() && static::SETTING_KEY !== $this['option_key'] ) {
			$current_options = $this['wp_option']->all();
			$original_options = (array) get_option( static::SETTING_KEY, [] );

			if ( ! empty( $original_options ) && empty( $current_options ) ) {
				update_option( $this['option_key'], $original_options );
			}
		}

		$this['flash_message']->setup_message();

		add_filter( 'plugin_row_meta', [ $this, 'awebooking_plugin_row_meta' ], 10, 2 );

		do_action( 'awebooking/booted', $this );
	}

	/**
	 * Is running in multi language system?
	 *
	 * @return bool
	 */
	public function is_multi_language() {
		return $this['multilingual']->is_polylang() || $this['multilingual']->is_wpml();
	}

	/**
	 * Is the system running with multi location?
	 *
	 * @return bool
	 */
	public function is_multi_location() {
		return (bool) $this['config']->get( 'enable_location' );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __DIR__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __DIR__ ) );
	}

	/**
	 * Get the plugin slug.
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( $this->plugin_path() );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'awebooking/template_path', 'awebooking/' );
	}

	/**
	 * Return list room states.
	 *
	 * @return array
	 */
	public function get_room_states() {
		return [
			Room_State::AVAILABLE   => esc_html__( 'Available', 'awebooking' ),
			Room_State::UNAVAILABLE => esc_html__( 'Unavailable', 'awebooking' ),
			Room_State::PENDING     => esc_html__( 'Pending', 'awebooking' ),
			Room_State::BOOKED      => esc_html__( 'Booked', 'awebooking' ),
		];
	}

	/**
	 * Get all order statuses.
	 *
	 * @return array
	 */
	public function get_booking_statuses() {
		return apply_filters( 'awebooking/order_statuses', [
			Booking::PENDING    => _x( 'Pending',    'Booking status', 'awebooking' ),
			Booking::PROCESSING => _x( 'Processing', 'Booking status', 'awebooking' ),
			Booking::COMPLETED  => _x( 'Completed',  'Booking status', 'awebooking' ),
			Booking::CANCELLED  => _x( 'Cancelled',  'Booking status', 'awebooking' ),
		]);
	}

	/**
	 * Get all service operations.
	 *
	 * @return array
	 */
	public function get_service_operations() {
		return apply_filters( 'awebooking/service_operations', [
			Service::OP_ADD               => esc_html__( 'Add to price', 'awebooking' ),
			Service::OP_ADD_DAILY         => esc_html__( 'Add to price per night', 'awebooking' ),
			Service::OP_ADD_PERSON        => esc_html__( 'Add to price per person', 'awebooking' ),
			Service::OP_ADD_PERSON_DAILY  => esc_html__( 'Add to price per person per night', 'awebooking' ),
			Service::OP_SUB               => esc_html__( 'Subtract from price', 'awebooking' ),
			Service::OP_SUB_DAILY         => esc_html__( 'Subtract from price per night', 'awebooking' ),
			Service::OP_INCREASE          => esc_html__( 'Increase price by % amount', 'awebooking' ),
			Service::OP_DECREASE          => esc_html__( 'Decrease price by % amount', 'awebooking' ),
		]);
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin row meta.
	 * @param	mixed $file  Plugin base file.
	 * @return	array
	 */
	public function awebooking_plugin_row_meta( $links, $file ) {
		if ( awebooking()->plugin_basename() . '/awebooking.php' == $file ) {
			$row_meta = array(
				'docs' => '<a href="' . esc_url( 'http://docs.awethemes.com/awebooking' ) . '" aria-label="' . esc_attr__( 'View AweBooking documentation', 'awebooking' ) . '">' . esc_html__( 'Docs', 'awebooking' ) . '</a>',
				'demo' => '<a href="' . esc_url( 'http://demo.awethemes.com/awebooking' ) . '" aria-label="' . esc_attr__( 'Visit demo', 'awebooking' ) . '">' . esc_html__( 'View Demo', 'awebooking' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}
