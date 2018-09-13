<?php
namespace AweBooking;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;

final class Plugin extends Container {
	use Support\Traits\Plugin_Provider,
		Support\Traits\Plugin_Options,
		Deprecated\AweBooking;

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	const VERSION = '3.1.8';

	/**
	 * The plugin file path.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * Indicates if the plugin has "booted".
	 *
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * The bootstrap classes.
	 *
	 * @var array
	 */
	protected static $bootstrappers = [
		\AweBooking\Core\Bootstrap\Load_Textdomain::class,
		\AweBooking\Core\Bootstrap\Load_Configuration::class,
		\AweBooking\Core\Bootstrap\Setup_Environment::class,
		\AweBooking\Core\Bootstrap\Start_Session::class,
		\AweBooking\Core\Bootstrap\Boot_Providers::class,
		\AweBooking\Core\Bootstrap\Include_Functions::class,
	];

	/**
	 * The core service providers.
	 *
	 * @var array
	 */
	protected static $service_providers = [
		'core' => [
			\AweBooking\Core\Providers\Intl_Service_Provider::class,
			\AweBooking\Core\Providers\Form_Service_Provider::class,
			\AweBooking\Core\Providers\Http_Service_Provider::class,
			\AweBooking\Core\Providers\Query_Service_Provider::class,
			\AweBooking\Core\Providers\Logic_Service_Provider::class,
			\AweBooking\Core\Providers\Payment_Service_Provider::class,
			\AweBooking\Core\Providers\Email_Service_Provider::class,
			\AweBooking\Core\Providers\Shortcode_Service_Provider::class,
			\AweBooking\Core\Providers\Widget_Service_Provider::class,
			\AweBooking\Core\Providers\Addons_Service_Provider::class,
		],
		'admin' => [
			\AweBooking\Admin\Providers\Admin_Service_Provider::class,
			\AweBooking\Admin\Providers\Menu_Service_Provider::class,
			\AweBooking\Admin\Providers\Permalink_Service_Provider::class,
			\AweBooking\Admin\Providers\Scripts_Service_Provider::class,
			\AweBooking\Admin\Providers\Metaboxes_Service_Provider::class,
			\AweBooking\Admin\Providers\Post_Types_Service_Provider::class,
			\AweBooking\Admin\Providers\Taxonomies_Service_Provider::class,
			\AweBooking\Admin\Providers\Notices_Service_Provider::class,
		],
		'frontend' => [
			\AweBooking\Frontend\Providers\Frontend_Service_Provider::class,
			\AweBooking\Frontend\Providers\Template_Loader_Service_Provider::class,
			\AweBooking\Frontend\Providers\Scripts_Service_Provider::class,
			\AweBooking\Frontend\Providers\Reservation_Service_Provider::class,
		],
	];

	/**
	 * Get the instance of the plugin.
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

		Constants::defines();
	}

	/**
	 * Get the version.
	 *
	 * @return string
	 */
	public function version() {
		return static::VERSION;
	}

	/**
	 * Bind all of paths in the container.
	 *
	 * @return void
	 */
	protected function binding_paths() {
		$this->instance( 'plugin_url', $this->plugin_url() );
		$this->instance( 'plugin_path', $this->plugin_path() );
		$this->instance( 'plugin_basename', $this->plugin_basename() );
	}

	/**
	 * Register the basic bindings into the container.
	 *
	 * @return void
	 */
	protected function register_base_bindings() {
		static::setInstance( $this );
		$this->instance( static::class, $this );

		$this->bind( 'installer', function() {
			return new Installer( $this );
		});

		$this->singleton( 'logger', function () {
			return new Logger( 'awebooking', [ $this->get_monolog_handler() ] );
		});

		$this->singleton( 'multilingual', function() {
			return new Multilingual;
		});

		$this->alias( 'logger', Logger::class );
		$this->alias( 'logger', LoggerInterface::class );
		$this->alias( 'multilingual', Multilingual::class );
	}

	/**
	 * Get the Monolog handler for the application.
	 *
	 * @return \Monolog\Handler\HandlerInterface
	 *
	 * @throws \Exception
	 */
	protected function get_monolog_handler() {
		return ( new StreamHandler( WP_CONTENT_DIR . '/awebooking.log', Logger::DEBUG ) )
					->setFormatter( new LineFormatter( null, null, true, true ) );
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
	 * Initialize the plugin when `plugins_loaded`.
	 *
	 * @access private
	 *
	 * @throws \Exception
	 */
	public function initialize() {
		try {
			$this->bootstrap();
		} catch ( \Exception $e ) {
			$this->catch_exception( $e );
		}
	}

	/**
	 * Register a bootstrapper.
	 *
	 * @param string $bootstrap The bootstrap class.
	 */
	public function bootstrapper( $bootstrap ) {
		static::$bootstrappers[] = $bootstrap;
	}

	/**
	 * Register a provider into the plugin.
	 *
	 * @param string $provider The provider class name.
	 * @param string $area     The area (core, admin, frontend).
	 * @param bool   $prepend  Prepend or append.
	 */
	public function provider( $provider, $area = 'core', $prepend = true ) {
		if ( ! array_key_exists( $area, static::$service_providers ) ) {
			throw new \OutOfRangeException( 'The area must be one of: core, admin or frontend.' );
		}

		if ( $prepend ) {
			static::$service_providers[ $area ] = Arr::prepend( static::$service_providers[ $area ], $provider );
		} else {
			static::$service_providers[ $area ][] = $provider;
		}
	}

	/**
	 * Bootstrap the plugin.
	 *
	 * @access private
	 */
	public function bootstrap() {
		/**
		 * Fire the bootstrap action.
		 *
		 * @param \AweBooking\Plugin $awebooking The awebooking class instance.
		 */
		do_action( 'awebooking_bootstrap', $this );

		// Run bootstrap classes.
		array_walk( static::$bootstrappers, function( $bootstrapper ) {
			$this->make( $bootstrapper )->bootstrap( $this );
		});

		/**
		 * Fire the init action.
		 *
		 * @param \AweBooking\Plugin $awebooking The awebooking class instance.
		 */
		do_action( 'awebooking_init', $this );

		// Build the providers.
		$providers = static::$service_providers['core'];

		if ( is_admin() ) {
			$providers = array_merge( $providers, static::$service_providers['admin'] );
		} elseif ( ! defined( 'DOING_CRON' ) && ( ! is_admin() || defined( 'DOING_AJAX' ) ) ) {
			$providers = array_merge( $providers, static::$service_providers['frontend'] );
		}

		// Filter the service_providers.
		$providers = apply_filters( 'abrs_service_providers', $providers, $this );

		// Loop each provider then register them.
		foreach ( $providers as $provider ) {
			$provider = new $provider( $this );

			if ( method_exists( $provider, 'when' ) && $when = $provider->when() ) {
				$this->register_when( $provider, $when );
			} else {
				$this->register( $provider );
			}
		}

		/**
		 * Fire the loaded action.
		 *
		 * @param \AweBooking\Plugin $awebooking The awebooking class instance.
		 */
		do_action( 'awebooking_loaded', $this );
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
	 * Boot the loaded service providers.
	 *
	 * @access private
	 */
	public function boot() {
		// Leave if plugin has been booted.
		if ( $this->is_booted() ) {
			return;
		}

		/**
		 * Fire the booting action.
		 *
		 * @param \AweBooking\Plugin $awebooking The awebooking class instance.
		 */
		do_action( 'awebooking_booting', $this );

		// Perform boot the loaded providers.
		array_walk( $this->loaded_providers, function( $provider ) {
			$this->boot_provider( $provider );
		});

		// Mark the plugin has been booted.
		$this->booted = true;

		/**
		 * Fire the booted action.
		 *
		 * @param \AweBooking\Plugin $awebooking The awebooking class instance.
		 */
		do_action( 'awebooking_booted', $this );
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
		return plugin_dir_path( $this->plugin_file ) . ( $path ? ltrim( $path, '/' ) : '' );
	}

	/**
	 * Returns the plugin url.
	 *
	 * @param  string $path Optional, extra url path.
	 * @return string
	 */
	public function plugin_url( $path = null ) {
		return plugin_dir_url( $this->plugin_file ) . ( $path ? ltrim( $path, '/' ) : '' );
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
		return apply_filters( 'abrs_template_path', 'awebooking/' );
	}

	/**
	 * Returns the default endpoint name.
	 *
	 * @return string
	 */
	public function endpoint_name() {
		return apply_filters( 'abrs_endpoint_name', 'awebooking-route' );
	}

	/**
	 * Catch an exception during running the plugin.
	 *
	 * @param  mixed $e The Exception or Throwable.
	 * @throws \Exception
	 */
	public function catch_exception( $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			throw $e;
		}

		// Log the exception.
		$this->get_logger()->error(
			$e->getMessage(), [ 'exception' => $e ]
		);

		add_action( 'admin_notices', function() use ( $e ) {
			awebooking_print_fatal_error( $e );
		});
	}

	/**
	 * Handle output buffering exception.
	 *
	 * @see http://php.net/manual/en/function.ob-get-level.php#117325
	 *
	 * @param  \Exception $e        The exception.
	 * @param  int        $ob_level The ob_get_level().
	 * @param  callable   $callback Optional, run callback after.
	 * @return void
	 *
	 * @throws \Exception
	 */
	public function handle_buffering_exception( $e, $ob_level, $callback = null ) {
		// In PHP7+, throw a FatalThrowableError when we catch an Error.
		if ( $e instanceof \Error && class_exists( FatalThrowableError::class ) ) {
			$e = new FatalThrowableError( $e );
		}

		while ( ob_get_level() > $ob_level ) {
			ob_end_clean();
		}

		// When current site in DEBUG mode, just throw that exception.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			throw $e;
		}

		// Call the callback.
		if ( is_callable( $callback ) ) {
			$callback( $e );
		}
	}
}
