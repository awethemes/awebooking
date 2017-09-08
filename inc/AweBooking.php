<?php
namespace AweBooking;

use WP_Session;
use AweBooking\Support\Collection;
use AweBooking\Booking\Store as Booking_Store;
use Skeleton\Container\Container as Skeleton_Container;

class AweBooking extends Skeleton_Container {
	/* Constants */
	const VERSION        = '3.0.0-beta5';
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

	/**
	 * A list add-ons was attached in to AweBooking.
	 *
	 * @var Collection
	 */
	protected $addons;

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
	 * AweBooking constructor.
	 */
	public function __construct() {
		parent::__construct();

		/**
		 * Binding static $instance
		 *
		 * @var $this
		 */
		static::$instance = $this;
		$this->addons = new Collection;

		$this->setup();
		$this->setup_plugin();
	}

	/**
	 * Trigger booting when `plugins_loaded`.
	 *
	 * @return void
	 */
	public function booting() {
		// WP_Session::get_instance();

		$this->trigger( new WP_Core_Hooks );
		$this->trigger( new WP_Query_Hooks );
		$this->trigger( new Logic_Hooks );
		$this->trigger( new Multilingual_Hooks );
		$this->trigger( new Admin\Admin_Hooks );
		$this->trigger( new Template_Hooks );
		$this->trigger( new Request_Handler );
		$this->trigger( new Ajax_Hooks );
		$this->trigger( new Widgets\Widget_Hooks );

		// Skeleton Support.
		skeleton()->trigger( new Skeleton_Hooks );

		do_action( 'awebooking/booting', $this );
	}

	/**
	 * Fire registerd service hooks.
	 */
	public function boot() {
		do_action( 'awebooking/init', $this );

		Shortcodes\Shortcodes::init();
		$this['flash_message']->setup_message();

		// Boot the service hooks.
		parent::boot();

		do_action( 'awebooking/booted', $this );
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

		$this['currency_manager'] = function ( $awebooking ) {
			return new Currency\Currency_Manager;
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
	 * Setup plugin in to WordPress.
	 *
	 * @return void
	 */
	protected function setup_plugin() {
		add_action( 'plugins_loaded', [ $this, '_load_textdomain' ] );
		add_filter( 'plugin_row_meta', [ $this, '_plugin_row_meta' ], 10, 2 );
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
	 * Returns the plugin url.
	 *
	 * @param  string $path Optional, extra url path.
	 * @return string
	 */
	public function plugin_url( $path = null ) {
		return trailingslashit( plugin_dir_url( AWEBOOKING_PLUGIN_FILE_PATH ) ) . $path;
	}

	/**
	 * Returns the plugin path.
	 *
	 * @param  string $path Optional, extra directory/file path.
	 * @return string
	 */
	public function plugin_path( $path = null ) {
		return trailingslashit( plugin_dir_path( AWEBOOKING_PLUGIN_FILE_PATH ) ) . $path;
	}

	/**
	 * Returns the plugin slug.
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( AWEBOOKING_PLUGIN_FILE_PATH );
	}

	/**
	 * Returns the relative template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'awebooking/template_path', 'awebooking/' );
	}

	/**
	 * Load localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/awebooking/awebooking-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/awebooking-LOCALE.mo
	 */
	public function _load_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'awebooking' );

		unload_textdomain( 'awebooking' );

		load_textdomain( 'awebooking', WP_LANG_DIR . '/awebooking/awebooking-' . $locale . '.mo' );
		load_plugin_textdomain( 'awebooking', false, $this->plugin_basename() . '/languages' );
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
