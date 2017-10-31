<?php
namespace AweBooking;

use AweBooking\Support\Addon;
use AweBooking\Booking\Store as Booking_Store;
use Awethemes\WP_Session\WP_Session;

use Illuminate\Support\Arr;
use Illuminate\Container\Container;

final class AweBooking extends Container {
	/* Constants */
	const VERSION        = '3.0.0-beta10';
	const SETTING_KEY    = 'awebooking_settings';

	const DATE_FORMAT    = 'Y-m-d';
	const JS_DATE_FORMAT = 'yy-mm-dd';

	const BOOKING        = 'awebooking';
	const ROOM_TYPE      = 'room_type';
	const PRICING_RATE   = 'pricing_rate';
	const HOTEL_LOCATION = 'hotel_location';
	const HOTEL_AMENITY  = 'hotel_amenity';
	const HOTEL_SERVICE  = 'hotel_extra_service';

	const STATE_AVAILABLE   = 0;
	const STATE_UNAVAILABLE = 1;
	const STATE_PENDING     = 2;
	const STATE_BOOKED      = 3;

	/**
	 * Indicates if the application has "booted".
	 *
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * All of the registered service providers.
	 *
	 * @var array
	 */
	protected $service_providers = [];

	/**
	 * The names of the loaded service providers.
	 *
	 * @var array
	 */
	protected $loaded_providers = [];

	/**
	 * The deferred services and their providers.
	 *
	 * @var array
	 */
	protected $deferred_services = [];

	/**
	 * A list add-ons was attached in to AweBooking.
	 *
	 * @var array
	 */
	protected $addons = [];

	/**
	 * Set the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function get_instance() {
		return static::getInstance();
	}

	/**
	 * AweBooking constructor.
	 */
	public function __construct() {
		new Skeleton_Hooks;

		// Register base bindings.
		$this->register_base_bindings();
		$this->register_base_service_providers();

		/**
		 * When `plugins_loaded`, initialize the core.
		 */
		add_action( 'plugins_loaded', [ $this, 'initialize' ] );

		/**
		 * Boottrap core after the `skeleton/init` fired.
		 */
		add_action( 'skeleton/init', [ $this, 'terminate' ] );
	}

	/**
	 * Get the version number of the AweBooking.
	 *
	 * @return string
	 */
	public function version() {
		return static::VERSION;
	}

	/**
	 * Register the basic bindings into the container.
	 *
	 * @return void
	 */
	protected function register_base_bindings() {
		static::setInstance( $this );
		$this->instance( 'awebooking', $this );
		$this->instance( AweBooking::class, $this );

		$this->register( Providers\Monolog_Service_Provider::class );

		$this->singleton( Background_Updater::class );

		$this->singleton(
			'session', function() {
				return new WP_Session( 'awebooking_session', [ 'lifetime' => 120 ] );
			}
		);

		$this->alias( 'session', WP_Session::class );

		$this['session']->hooks();

		$this->singleton( 'cart', Cart\Cart::class );

		$this['url'] = $this->plugin_url();
		$this['path'] = $this->plugin_path();

		$this->singleton(
			'option_key', function( $a ) {
				return AweBooking::SETTING_KEY;
			}
		);

		$this->singleton(
			'setting', function ( $a ) {
				return new Setting( $a['option_key'] );
			}
		);

		// TODO: Remove this!!!
		$this['config'] = function ( $awebooking ) {
			return $awebooking['setting'];
		};

		$this->singleton(
			'currency_manager', function ( $awebooking ) {
				return new Currency\Currency_Manager();
			}
		);

		$this->singleton(
			'currency', function ( $a ) {
				return new Currency\Currency( $a['setting']->get( 'currency' ) );
			}
		);

		$this->singleton(
			'flash_message', function () {
				return new Support\Flash_Message();
			}
		);

		// Binding stores.
		$this->singleton(
			'store.booking', function() {
				return new Booking_Store( 'awebooking_booking', 'room_id' );
			}
		);

		$this->singleton(
			'store.availability', function() {
				return new Booking_Store( 'awebooking_availability', 'room_id' );
			}
		);

		$this->singleton(
			'store.pricing', function() {
				return new Booking_Store( 'awebooking_pricing', 'rate_id' );
			}
		);
	}

	/**
	 * Register all of the base service providers.
	 *
	 * @return void
	 */
	protected function register_base_service_providers() {
	}

	/**
	 * Trigger booting when `plugins_loaded`.
	 *
	 * @return void
	 */
	public function initialize() {
		$this->load_textdomain();

		// Skeleton Support.
		// Register core service providers.
		$this->trigger( new WP_Core_Hooks() );
		$this->trigger( new WP_Query_Hooks() );
		$this->trigger( new Logic_Hooks() );

		$this->trigger( new Ajax_Hooks() );
		$this->trigger( new Request_Handler() );
		$this->trigger( new Template_Hooks() );

		$this->trigger( new Widgets\Widget_Hooks() );
		$this->trigger( new Multilingual_Hooks() );
		$this->trigger( new Admin\Admin_Hooks() );

		$this->make( Background_Updater::class );
		add_action( 'init', [ Installer::class, 'check_version' ] );

		do_action( 'awebooking/init', $this );
	}

	public function terminate() {
		$this->boottrap();

		Shortcodes\Shortcodes::init();

		add_filter( 'plugin_row_meta', [ $this, '_plugin_row_meta' ], 10, 2 );
		add_action( 'after_plugin_row', [ $this, '_plugin_addon_notices' ], 10, 3 );
	}

	/**
	 * Fire registerd service hooks.
	 */
	public function boottrap() {
		if ( $this->booted ) {
			return;
		}

		do_action( 'awebooking/booting', $this );

		array_walk( $this->addons, function ( $a ) {
			$this->init_addon( $a );
		});

		array_walk( $this->service_providers, function ( $p ) {
			$this->boot_provider( $p );
		});

		$this->booted = true;

		do_action( 'awebooking/booted', $this );
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
	 * Is running in multi language system?
	 *
	 * @return bool
	 */
	public function is_multi_language() {
		return $this['multilingual']->is_polylang() || $this['multilingual']->is_wpml();
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
	 * Alias of register method.
	 *
	 * @param  [type]  $provider [description]
	 * @param  boolean $force    [description]
	 * @return [type]            [description]
	 */
	public function trigger( $provider, $force = false ) {
		return $this->register( $provider, $force );
	}

	/**
	 * Register a service provider with the application.
	 *
	 * @param  Service_Provider|string $provider
	 * @param  array                                      $options
	 * @param  bool                                       $force
	 * @return Service_Provider
	 */
	public function register( $provider, $force = false ) {
		if ( ($registered = $this->get_provider( $provider )) && ! $force ) {
			return $registered;
		}

		// If the given "provider" is a string, we will resolve it, passing in the
		// application instance automatically for the developer. This is simply
		// a more convenient way of specifying your service provider classes.
		if ( is_string( $provider ) ) {
			$provider = $this->resolveProvider( $provider );
		}

		if ( method_exists( $provider, 'register' ) ) {
			$provider->register( $this );
		}

		$this->mark_as_registered( $provider );

		// If the application has already booted, we will call this boot method on
		// the provider class so it has an opportunity to do its boot logic and
		// will be ready for any usage by this developer's application logic.
		if ( $this->booted ) {
			$this->boot_provider( $provider );
		}

		return $provider;
	}

	/**
	 * Get the registered service provider instance if it exists.
	 *
	 * @param  Service_Provider|string $provider
	 * @return Service_Provider|null
	 */
	public function get_provider( $provider ) {
		$name = is_string( $provider ) ? $provider : get_class( $provider );

		return Arr::first(
			$this->service_providers, function ( $value ) use ( $name ) {
				return $value instanceof $name;
			}
		);
	}

	/**
	 * Resolve a service provider instance from the class name.
	 *
	 * @param  string $provider
	 * @return Service_Provider
	 */
	public function resolveProvider( $provider ) {
		return new $provider($this);
	}

	/**
	 * Mark the given provider as registered.
	 *
	 * @param  Service_Provider $provider
	 * @return void
	 */
	protected function mark_as_registered( $provider ) {
		$this->service_providers[] = $provider;

		$this->loaded_providers[ get_class( $provider ) ] = true;
	}

	/**
	 * Boot the given service provider.
	 *
	 * @param  Service_Provider $provider
	 * @return mixed
	 */
	protected function boot_provider( $provider ) {
		$provider->init( $this );

		if ( method_exists( $provider, 'boot' ) ) {
			return $this->call( [ $provider, 'boot' ] );
		}
	}

	/**
	 * Load and boot all of the remaining deferred providers.
	 *
	 * @return void
	 */
	public function load_deferred_providers() {
		// We will simply spin through each of the deferred providers and register each
		// one and boot them if the application has booted. This should make each of
		// the remaining services available to this application for immediate use.
		foreach ( $this->deferred_services as $service => $provider ) {
			$this->load_deferred_provider( $service );
		}

		$this->deferred_services = [];
	}

	/**
	 * Load the provider for a deferred service.
	 *
	 * @param  string $service
	 * @return void
	 */
	public function load_deferred_provider( $service ) {
		if ( ! isset( $this->deferred_services[ $service ] ) ) {
			return;
		}

		$provider = $this->deferred_services[ $service ];

		// If the service provider has not already been loaded and registered we can
		// register it with the application and remove the service from this list
		// of deferred services, since it will already be loaded on subsequent.
		if ( ! isset( $this->loaded_providers[ $provider ] ) ) {
			$this->register_deferred_provider( $provider, $service );
		}
	}

	/**
	 * Register a deferred provider and service.
	 *
	 * @param  string      $provider
	 * @param  string|null $service
	 * @return void
	 */
	public function register_deferred_provider( $provider, $service = null ) {
		// Once the provider that provides the deferred service has been registered we
		// will remove it from our local list of the deferred services with related
		// providers so that this container does not try to resolve it out again.
		if ( $service ) {
			unset( $this->deferred_services[ $service ] );
		}

		$this->register( $instance = new $provider($this) );

		if ( ! $this->booted ) {
			$this->booting(
				function () use ( $instance ) {
					$this->boot_provider( $instance );
				}
			);
		}
	}

	/**
	 * Register addon for AweBooking.
	 *
	 * @param  Addon $addon The addon object instance.
	 * @return $this
	 */
	public function register_addon( Addon $addon ) {
		// Unique addon ID, normally same as plugin name.
		$addon_id = $addon->get_id();

		// If already registerd addon, just leave.
		if ( isset( $this->addons[ $addon_id ] ) ) {
			return $this;
		}

		// Binding this container into the addon.
		if ( is_null( $addon->awebooking ) ) {
			$addon->awebooking = $this;
		}

		$addon->register();

		if ( $this->booted ) {
			$this->init_addon( $addon );
		}

		$this->addons[ $addon_id ] = $addon;

		return $this;
	}

	/**
	 * Gets addons instance by registed ID.
	 *
	 * @param  string $addon_id Register addon ID.
	 * @return Addon
	 */
	public function get_addon( $addon_id ) {
		return isset( $this->addons[ $addon_id ] ) ? $this->addons[ $addon_id ] : null;
	}

	/**
	 * Init the addon.
	 *
	 * @param  Addon $addon Addon object.
	 * @return void
	 */
	protected function init_addon( Addon $addon ) {
		$require_version = $addon->requires();
		$require_version = ( ! $require_version || 'latest' === $require_version ) ? static::VERSION : $require_version;

		if ( ! version_compare( static::VERSION, $require_version, '>=' ) ) {
			$addon->log_error(
				sprintf(
					esc_html__( 'Addon requires at least AweBooking version %1$s to work, you have running on AweBooking %2$s', 'awebooking' ),
					esc_html( $require_version ),
					esc_html( static::VERSION )
				)
			);

			return;
		}

		// Prevent booting addon if got any errors.
		if ( $addon->has_errors() ) {
			return;
		}

		// Init the addon.
		$addon->init();

		if ( $addon->is_notify_update() ) {
			$addon->setup_addon_updater();
		}

		/**
		 * Fire event after init addon.
		 *
		 * @param mixed      $addon      The addon instance object.
		 * @param AweBooking $awebooking AweBooking instance object.
		 */
		do_action( 'awebooking/addons/init_' . $addon->get_id(), $addon, $this );
	}

	/**
	 * Register a new boot listener.
	 *
	 * @param  mixed $callback
	 * @return void
	 */
	public function booting( $callback ) {
		add_action( 'awebooking/booting', $callbacks, 0 );
	}

	/**
	 * Register a new "booted" listener.
	 *
	 * @param  mixed $callback
	 * @return void
	 */
	public function booted( $callback ) {
		add_action( 'awebooking/booted', $callbacks, 0 );
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
	protected function load_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'awebooking' );

		unload_textdomain( 'awebooking' );

		load_textdomain( 'awebooking', WP_LANG_DIR . '/awebooking/awebooking-' . $locale . '.mo' );
		load_plugin_textdomain( 'awebooking', false, dirname( $this->plugin_basename() ) . '/languages' );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param  mixed $links Plugin row meta.
	 * @param  mixed $file  Plugin base file.
	 * @return array
	 */
	public function _plugin_row_meta( $links, $file ) {
		if ( $this->plugin_basename() == $file ) {
			$row_meta = array(
				'docs' => '<a href="' . esc_url( 'http://docs.awethemes.com/awebooking' ) . '" aria-label="' . esc_attr__( 'View AweBooking documentation', 'awebooking' ) . '">' . esc_html__( 'Docs', 'awebooking' ) . '</a>',
				'demo' => '<a href="' . esc_url( 'http://demo.awethemes.com/awebooking' ) . '" aria-label="' . esc_attr__( 'Visit demo', 'awebooking' ) . '">' . esc_html__( 'View Demo', 'awebooking' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	/**
	 * Display error messages of add-ons.
	 *
	 * @param  string $plugin_file Path to the plugin file, relative to the plugins directory.
	 * @param  array  $plugin_data An array of plugin data.
	 * @param  string $status      Status of the plugin.
	 * @return void
	 */
	public function _plugin_addon_notices( $plugin_file, $plugin_data, $status ) {
		static $plugin_addons;

		// Cache this list addons for use less memory.
		if ( is_null( $plugin_addons ) ) {
			$plugin_addons = array_filter(
				array_map( function( $addon ) {
					return $addon->is_wp_plugin() ? $addon->get_basename() : null;
				}, $this->addons )
			);
		}

		// Ignore outside scope of AweBooking addons.
		if ( ! in_array( $plugin_file, array_values( $plugin_addons ) ) ) {
			return;
		}

		$addon = $this->get_addon( Arr::get( array_flip( $plugin_addons ), $plugin_file ) );
		if ( ! $addon->has_errors() ) {
			return;
		}

		printf(
			'<tr class="awebooking-addon-notice-tr plugin-update-tr active"><td colspan="3" class="awebooking-addon-notice plugin-update colspanchange"><div class="notice inline notice-warning notice-alt"><strong>%1$s</strong><ul>%2$s</ul></div></td></tr>',
			esc_html__( 'This plugin has been activated but cannot be loaded by AweBooking by reason(s):', 'awebooking' ),
			'<li>' . implode( '</li><li>', $addon->get_errors() ) . '</li>'
		);
	}
}
