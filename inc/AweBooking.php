<?php
namespace AweBooking;

use WP_Session;
use AweBooking\Booking\Concierge;
use AweBooking\Booking\Store as Booking_Store;
use Skeleton\Container\Container as Skeleton_Container;

class AweBooking extends Skeleton_Container {
	/* Constants */
	const VERSION        = '3.0.0-beta4';
	const SETTING_KEY    = 'awebooking_settings';

	const DATE_FORMAT    = 'Y-m-d';
	const JS_DATE_FORMAT = 'yy-mm-dd';

	const BOOKING        = 'awebooking';

	const ROOM_TYPE      = 'room_type';
	const HOTEL_LOCATION = 'hotel_location';
	const HOTEL_AMENITY  = 'hotel_amenity';
	const HOTEL_SERVICE  = 'hotel_extra_service';

	const STATE_AVAILABLE   = 0;
	const STATE_UNAVAILABLE = 1;
	const STATE_PENDING     = 2;
	const STATE_BOOKED      = 3;

	// Weekday.
	const SUNDAY    = 0;
	const MONDAY    = 1;
	const TUESDAY   = 2;
	const WEDNESDAY = 3;
	const THURSDAY  = 4;
	const FRIDAY    = 5;
	const SATURDAY  = 6;

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

		static::$instance = $this;

		$this->setup();
		$this->trigger( new WP_Core_Hooks );
		$this->trigger( new WP_Query_Hooks );
		$this->trigger( new Logic_Hooks );
		$this->trigger( new Multilingual_Hooks );

		$this->trigger( new Admin\Admin_Hooks );
		$this->trigger( new Template_Hooks );
		$this->trigger( new Request_Handler );
		$this->trigger( new Ajax_Hooks );
		$this->trigger( new Widgets\Widget_Hooks );

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

		$this->bind( 'session', function () {
			return WP_Session::get_instance();
		});

		$this->bind( 'option_key', function( $a ) {
			return AweBooking::SETTING_KEY;
		});

		$this->bind( 'setting', function ( $a ) {
			return new Setting( $a['option_key'] );
		});

		// TODO: Remove this!!!
		$this['config'] = function ( $awebooking ) {
			return $awebooking['setting'];
		};

		$this['factory'] = function ( $awebooking ) {
			return new Factory( $awebooking );
		};

		$this['currency_manager'] = function ( $awebooking ) {
			return new Currency\Currency_Manager( $awebooking['config'] );
		};

		$this['currency'] = function ( $a ) {
			return new Currency\Currency( $a['setting']->get( 'currency' ) );
		};

		$this['flash_message'] = function () {
			return new Support\Flash_Message;
		};

		// Binding stores.
		$this->bind( 'store.booking', function() {
			return new Booking_Store( 'awebooking_booking', 'room_id' );
		});

		$this->bind( 'store.availability', function() {
			return new Booking_Store( 'awebooking_availability', 'room_id' );
		});

		$this->bind( 'store.pricing', function() {
			return new Booking_Store( 'awebooking_pricing', 'rate_id' );
		});
	}

	/**
	 * Fire registerd service hooks.
	 */
	public function boot() {
		do_action( 'awebooking/init', $this );

		parent::boot();

		Shortcodes\Shortcodes::init();
		$this['flash_message']->setup_message();

		add_filter( 'plugin_row_meta', [ $this, '_plugin_row_meta' ], 10, 2 );

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
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin row meta.
	 * @param	mixed $file  Plugin base file.
	 * @return	array
	 */
	public function _plugin_row_meta( $links, $file ) {
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
