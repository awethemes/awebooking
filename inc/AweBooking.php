<?php
namespace AweBooking;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use AweBooking\Support\Utils as U;
use AweBooking\Support\Addon;
use Illuminate\Container\Container;

final class AweBooking extends Container {
	use Deprecated\AweBooking_Deprecated;

	/**
	 * The AweBooking version.
	 *
	 * @var string
	 */
	const VERSION = '3.0.5';

	/* Deprecated constants */
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
	 * The plugin file path.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * Indicates if the awebooking has "booted".
	 *
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * Indicates if the awebooking has "bootstrapped".
	 *
	 * @var bool
	 */
	protected $bootstrapped = false;

	/**
	 * The loaded service providers.
	 *
	 * @var array
	 */
	protected $loaded_providers = [];

	/**
	 * The loaded addons.
	 *
	 * @var array
	 */
	protected $loaded_addons = [];

	/**
	 * The list of addons has been failed the validation.
	 *
	 * @var array
	 */
	protected $failed_addons = [];

	/**
	 * The catched exceptions during running the AweBooking.
	 *
	 * @var array
	 */
	protected $exceptions = [];

	/**
	 * The bootstrap classes.
	 *
	 * @var array
	 */
	protected $bootstrappers = [
		\AweBooking\Bootstrap\Load_Textdomain::class,
		\AweBooking\Bootstrap\Load_Configuration::class,
		\AweBooking\Bootstrap\Start_Session::class,
		\AweBooking\Bootstrap\Setup_Environment::class,
	];

	/**
	 * The core service providers.
	 *
	 * @var array
	 */
	protected $service_providers = [
		\AweBooking\Providers\Skeleton_Service_Provider::class,
		\AweBooking\Providers\Core_Service_Provider::class,
		\AweBooking\Providers\WP_Query_Service_Provider::class,
		\AweBooking\Providers\Logic_Service_Provider::class,
		\AweBooking\Providers\Payment_Service_Provider::class,
		\AweBooking\Providers\Reservation_Service_Provider::class,
		\AweBooking\Providers\Admin_Service_Provider::class,
		\AweBooking\Providers\Frontend_Service_Provider::class,
		\AweBooking\Providers\Route_Service_Provider::class,
	];

	/**
	 * Get the instance of the AweBooking.
	 *
	 * @return static
	 */
	public static function get_instance() {
		return static::getInstance();
	}

	/**
	 * Create AweBooking plugin instance.
	 *
	 * @param string $plugin_file The plugin file path.
	 */
	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;

		$this->binding_paths();

		$this->register_base_bindings();
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
	 * Catch an exception during running the AweBooking.
	 *
	 * @param  mixed  $e    The Exception or Throwable.
	 * @param  string $type The exception type, default: 'awebooking'.
	 * @throws \Exception
	 */
	public function catch_exception( $e, $type = 'awebooking' ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			throw $e;
		}

		// Log the exception.
		$this->exceptions[ $type ] = $e;
		$this->get_logger()->error( $e->getMessage(), [ 'exception' => $e ] );

		add_action( 'admin_notices', function() use ( $e ) {
			awebooking_print_fatal_error( $e );
		});
	}

	/**
	 * Initialization the AweBooking.
	 *
	 * @return void
	 */
	public function initialize() {
		// First, let create and init the installer.
		$installer = $this->make( Installer::class );
		$installer->init();

		// Register the activation and deactivatio hooks.
		register_activation_hook( $this->plugin_file(), [ $installer, 'activation' ] );
		register_deactivation_hook( $this->plugin_file(), [ $installer, 'deactivation' ] );

		// Hooks the AweBooking into WordPress.
		add_action( 'skeleton/loaded', function() {
			try {
				$this->bootstrap();
			} catch ( \Exception $e ) {
				return $this->catch_exception( $e );
			} catch ( \Throwable $e ) {
				return $this->catch_exception( $e );
			}
		});

		add_action( 'skeleton/init', function() {
			try {
				$this->boot();
			} catch ( \Exception $e ) {
				return $this->catch_exception( $e );
			} catch ( \Throwable $e ) {
				return $this->catch_exception( $e );
			}
		});
	}

	/**
	 * Bind all of paths in the container.
	 *
	 * @return void
	 */
	protected function binding_paths() {
		$this->instance( 'url', $this->plugin_url() );
		$this->instance( 'path', $this->plugin_path() );
		$this->instance( 'basename', $this->plugin_basename() );
	}

	/**
	 * Register the basic bindings into the container.
	 *
	 * @return void
	 */
	protected function register_base_bindings() {
		static::setInstance( $this );
		$this->instance( static::class, $this );

		$this->register_logger_bindings();

		$this->singleton( Installer::class );
		$this->singleton( 'multilingual', Multilingual::class );
	}

	/**
	 * Register container bindings for the application.
	 *
	 * @return void
	 */
	protected function register_logger_bindings() {
		$this->singleton( 'logger', function () {
			return new Logger( 'awebooking', [ $this->get_monolog_handler() ] );
		});

		$this->alias( 'logger', Logger::class );
		$this->alias( 'logger', LoggerInterface::class );
	}

	/**
	 * Get the Monolog handler for the application.
	 *
	 * @return \Monolog\Handler\HandlerInterface
	 */
	protected function get_monolog_handler() {
		return ( new StreamHandler( WP_CONTENT_DIR . '/awebooking.log', Logger::DEBUG ) )
					->setFormatter( new LineFormatter( null, null, true, true ) );
	}

	/**
	 * Determine if the awebooking has bootstrapped.
	 *
	 * @return bool
	 */
	public function is_bootstrapped() {
		return $this->bootstrapped;
	}

	/**
	 * Bootstrap the AweBooking.
	 *
	 * @access private
	 */
	public function bootstrap() {
		// Initialize core bindings.
		$this->make( Multilingual::class );

		// Doing the bootstrap the awebooking.
		do_action( 'awebooking/bootstrapping', $this );

		// Run the list of bootstrap classes.
		array_walk( $this->bootstrappers, function( $bootstrapper ) {
			$this->make( $bootstrapper )->bootstrap( $this );
		});

		$this->bootstrapped = true;

		// Initializing core providers.
		do_action( 'awebooking/before_init', $this );

		foreach ( $this->service_providers as $provider ) {
			$provider = new $provider( $this );

			if ( method_exists( $provider, 'when' ) && $provider->has_when() ) {
				$this->register_when( $provider, $provider->when() );
			} else {
				$this->register( $provider );
			}
		}

		do_action( 'awebooking/init', $this );
	}

	/**
	 * Determine if the awebooking has booted.
	 *
	 * @return bool
	 */
	public function is_booted() {
		return $this->booted;
	}

	/**
	 * Boot the awebooking service providers.
	 *
	 * @access private
	 */
	public function boot() {
		if ( ! $this->is_bootstrapped() || $this->is_booted() ) {
			return;
		}

		do_action( 'awebooking/booting', $this );

		// Filter the addons then boot them first.
		$addons = U::collect( $this->loaded_providers )
			->filter( function( $provider ) {
				return $provider instanceof Addon;
			})->each(function( $provider ) {
				$this->boot_provider( $provider );
			});

		// Boot the normal providers late.
		U::collect( $this->loaded_providers )
			->except( $addons->keys()->all() )
			->each(function( $provider ) {
				$this->boot_provider( $provider );
			});

		$this->booted = true;

		do_action( 'awebooking/booted', $this );
	}

	/**
	 * Register a service provider with the awebooking.
	 *
	 * @param  Service_Provider|string $provider The provider class instance or class name.
	 * @param  bool                    $force    If true, force register this provider.
	 * @return Service_Provider
	 */
	public function register( $provider, $force = false ) {
		if ( ( $registered = $this->get_provider( $provider ) ) && ! $force ) {
			return $registered;
		}

		// If the given "provider" is a string, we will resolve it.
		if ( is_string( $provider ) ) {
			$provider = new $provider( $this );
		}

		// Mark the given provider as registered.
		$this->loaded_providers[ get_class( $provider ) ] = $provider;

		// Call the register on the provider.
		if ( method_exists( $provider, 'register' ) ) {
			$provider->register( $this );
		}

		// If the awebooking has already booted, we will call
		// this boot method on the provider class.
		if ( $this->is_booted() ) {
			$this->boot_provider( $provider );
		}

		return $provider;
	}

	/**
	 * Get the registered service provider instance if it exists.
	 *
	 * @param  Service_Provider|string $provider The service provider.
	 * @return Service_Provider|null
	 */
	public function get_provider( $provider ) {
		$name = is_string( $provider ) ? $provider : get_class( $provider );

		if ( array_key_exists( $name, $this->loaded_providers ) ) {
			return $this->loaded_providers[ $name ];
		}
	}

	/**
	 * Boot the given service provider.
	 *
	 * @param  Service_Provider $provider The service provider.
	 * @return mixed
	 */
	protected function boot_provider( $provider ) {
		if ( method_exists( $provider, 'boot' ) ) {
			$this->call( [ $provider, 'boot' ], [ $this ] );
		} elseif ( method_exists( $provider, 'init' ) ) {
			$this->call( [ $provider, 'init' ], [ $this ] );
		}
	}

	/**
	 * Alias of $this->register() method.
	 *
	 * @param  Service_Provider|string $provider The provider class instance or class name.
	 * @param  bool                    $force    If true, force register this provider.
	 * @return Service_Provider
	 */
	public function trigger( $provider, $force = false ) {
		return $this->register( $provider, $force );
	}

	/**
	 * Register the given provider when an action fired.
	 *
	 * @param  Service_Provider|string $provider The provider class instance or class name.
	 * @param  string|array            $hook     The hook name or an array hook: [ $tag, $priority, $accepted_args ].
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	public function register_when( $provider, $hook ) {
		if ( is_string( $hook ) ) {
			$hook = [ $hook, 10, 1 ];
		}

		// Given an invalid hook? let throw a exception.
		if ( ! is_array( $hook ) || 3 !== count( $hook ) ) {
			throw new \InvalidArgumentException( 'The $hook must be an array and contains three elements' );
		}

		list( $tag, $priority, $accepted_args ) = $hook;

		// If the action has been fired, just register the provider
		// and return.
		if ( did_action( $tag ) ) {
			$this->register( $provider );
			return;
		}

		add_action( $tag, function() use ( $provider ) {
			$this->register( $provider );
		}, $priority, $accepted_args );
	}

	/**
	 * Register an addon with the AweBooking.
	 *
	 * @param  Addon $addon The addon instance.
	 * @return Addon|false
	 */
	public function register_addon( Addon $addon ) {
		// The unique addon ID, normally same as plugin name.
		$addon_id = $addon->get_id();

		// If already registered, just leave.
		if ( isset( $this->loaded_addons[ $addon_id ] ) ) {
			return $addon;
		}

		// Binding the awebooking into the addon.
		$addon->set_awebooking( $this );

		// Validate the addon before register.
		$addon->validate();

		if ( $addon->has_errors() ) {
			$this->failed_addons[ $addon_id ] = $addon;
			return false;
		}

		try {
			$this->register( $addon );
		} catch ( \Exception $e ) {
			$addon->log_error( $e->getMessage() );
		} catch ( \Throwable $e ) {
			$addon->log_error( $e->getMessage() );
		}

		$this->loaded_addons[ $addon_id ] = get_class( $addon );

		return $addon;
	}

	/**
	 * Get addon instance by addon ID.
	 *
	 * @param  string $addon_id The addon ID.
	 * @return Addon
	 */
	public function get_addon( $addon_id ) {
		if ( isset( $this->loaded_addons[ $addon_id ] ) ) {
			return $this->get_provider( $this->loaded_addons[ $addon_id ] );
		}
	}

	/**
	 * Get the loaded addons.
	 *
	 * @return array
	 */
	public function get_addons() {
		return array_map( function( $addon_class ) {
			return $this->get_provider( $addon_class );
		}, $this->loaded_addons );
	}

	/**
	 * Get the list failed addons.
	 *
	 * @return array
	 */
	public function get_failed_addons() {
		return $this->failed_addons;
	}

	/**
	 * Returns the plugin file path.
	 *
	 * @return string
	 */
	public function plugin_file() {
		return $this->plugin_file;
	}

	/**
	 * Returns the plugin path.
	 *
	 * @param  string $path Optional, extra directory/file path.
	 * @return string
	 */
	public function plugin_path( $path = null ) {
		return trailingslashit( plugin_dir_path( $this->plugin_file ) ) . $path;
	}

	/**
	 * Returns the plugin url.
	 *
	 * @param  string $path Optional, extra url path.
	 * @return string
	 */
	public function plugin_url( $path = null ) {
		return trailingslashit( plugin_dir_url( $this->plugin_file ) ) . $path;
	}

	/**
	 * Returns the plugin basename (awebooking/awebooking.php).
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( $this->plugin_file );
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
	 * Returns the default endpoint name.
	 *
	 * @return string
	 */
	public function endpoint_name() {
		return apply_filters( 'awebooking/endpoint_name', 'awebooking-route' );
	}

	/**
	 * Get the logger implementation.
	 *
	 * @return \Psr\Log\LoggerInterface
	 */
	public function get_logger() {
		return $this->make( LoggerInterface::class );
	}

	/**
	 * Get the Installer instance.
	 *
	 * @return \AweBooking\Installer
	 */
	public function get_installer() {
		return $this->make( Installer::class );
	}

	/**
	 * Get the Multilingual instance.
	 *
	 * @return \AweBooking\Multilingual
	 */
	public function get_multilingual() {
		return $this->make( Multilingual::class );
	}

	/**
	 * Is current WordPress is running in multi-languages.
	 *
	 * @return bool
	 */
	public function is_running_multilanguage() {
		$multilingual = $this->get_multilingual();

		$is_multilanguage = $multilingual->is_polylang() || $multilingual->is_wpml();

		return apply_filters( 'awebooking/is_running_multilanguage', $is_multilanguage, $multilingual );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	public function is_wp_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
}
