<?php
namespace AweBooking;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Illuminate\Container\Container;
use AweBooking\Support\Fluent;

final class Plugin extends Container {
	use Support\Traits\Plugin_Provider;

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	const VERSION = '3.1.0-dev';

	/**
	 * The plugin file path.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * The plugin options.
	 *
	 * @var \AweBooking\Support\Fluent
	 */
	protected $options;

	/**
	 * The plugin option key name.
	 *
	 * @var string
	 */
	protected $option_key;

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
	protected $bootstrappers = [
		\AweBooking\Bootstrap\Load_Textdomain::class,
		\AweBooking\Bootstrap\Load_Configuration::class,
		\AweBooking\Bootstrap\Setup_Environment::class,
		\AweBooking\Bootstrap\Start_Session::class,
		\AweBooking\Bootstrap\Boot_Providers::class,
	];

	/**
	 * The core service providers.
	 *
	 * @var array
	 */
	protected $service_providers = [
		'core' => [
			\AweBooking\Providers\Intl_Service_Provider::class,
			\AweBooking\Providers\Http_Service_Provider::class,
			\AweBooking\Providers\Payment_Service_Provider::class,
			\AweBooking\Providers\Query_Service_Provider::class,
		],
		'admin' => [
			\AweBooking\Admin\Providers\Admin_Service_Provider::class,
			\AweBooking\Admin\Providers\Notices_Service_Provider::class,
			\AweBooking\Admin\Providers\Menu_Service_Provider::class,
			\AweBooking\Admin\Providers\Scripts_Service_Provider::class,
			\AweBooking\Admin\Providers\Metaboxes_Service_Provider::class,
			\AweBooking\Admin\Providers\Post_Types_Service_Provider::class,
			\AweBooking\Admin\Providers\Taxonomies_Service_Provider::class,
		],
		'frontend' => [
			// ...
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
	 * Initialize the plugin when `plugins_loaded`.
	 *
	 * @access private
	 */
	public function initialize() {
		try {
			$this->bootstrap();
		} catch ( \Exception $e ) {
			$this->catch_exception( $e );
		}
	}

	/**
	 * Bootstrap the plugin.
	 *
	 * @access private
	 */
	protected function bootstrap() {
		/**
		 * Fire the action before bootstrap.
		 *
		 * @param \AweBooking\Plugin $awebooking The awebooking class instance.
		 */
		do_action( 'awebooking/bootstrapping', $this );

		// Run bootstrap classes.
		array_walk( $this->bootstrappers, function( $bootstrapper ) {
			$this->make( $bootstrapper )->bootstrap( $this );
		});

		/**
		 * Fire the bootstrapped action.
		 *
		 * @param \AweBooking\Plugin $awebooking The awebooking class instance.
		 */
		do_action( 'awebooking/bootstrapped', $this );

		// Build the providers.
		$providers = $this->service_providers['core'];

		if ( abrs_request_is( 'frontend' ) ) {
			$providers = array_merge( $providers, $this->service_providers['frontend'] );
		}

		// In admin, we will merge admin providers into the list.
		if ( abrs_request_is( 'admin' ) ) {
			$providers = array_merge( $providers, $this->service_providers['admin'] );
		}

		// Filter the service_providers.
		$providers = apply_filters( 'awebooking/service_providers', $providers, $this );

		// Require the core functions before registered providers.
		require trailingslashit( __DIR__ ) . 'core-functions.php';

		/**
		 * Fire the init action.
		 *
		 * @param \AweBooking\Plugin $awebooking The awebooking class instance.
		 */
		do_action( 'awebooking/init', $this );

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
		 * Fire the after_init action.
		 *
		 * @param \AweBooking\Plugin $awebooking The awebooking class instance.
		 */
		do_action( 'awebooking/after_init', $this );
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
		do_action( 'awebooking/booting', $this );

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
		do_action( 'awebooking/booted', $this );
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
	 * Get name for option key storing setting
	 *
	 * @return string
	 */
	public function get_option_key() {
		return $this->option_key;
	}

	/**
	 * Set the option key name.
	 *
	 * @param  string $key_name The option key name.
	 * @return bool
	 */
	public function set_option_key( $key_name ) {
		// Option name can't be set after plugin booting.
		if ( did_action( 'awebooking/booting' ) ) {
			return false;
		}

		// Set new key-name.
		$this->option_key = $key_name;

		// Flush the options if set.
		if ( $this->options ) {
			$this->options = null;
		}

		return true;
	}

	/**
	 * Get all stored options.
	 *
	 * @return array
	 */
	public function get_options() {
		// Call this when option_key is not setup yet,
		// trigger an error and create a mock for options.
		if ( empty( $this->option_key ) ) {
			trigger_error( esc_html__( 'The option is not setup yet.', 'awebooking' ) );
			$this->options = new Fluent( [] );
		}

		// Load the option in the database.
		if ( is_null( $this->options ) ) {
			$this->options = new Fluent( get_option( $this->get_option_key(), [] ) );
		}

		return $this->options;
	}

	/**
	 * Retrieves an option by key-name.
	 *
	 * @param  string $key     The key name.
	 * @param  mixed  $default The default value.
	 * @return mixed
	 */
	public function get_option( $key, $default = null ) {
		$options = $this->get_options();

		return $this->sanitize_option( $key,
			$options->get( $key, $default )
		);
	}

	/**
	 * Sanitises various option values based on the nature of the option.
	 *
	 * @param  string $key   The name of the option.
	 * @param  string $value The unsanitised value.
	 * @return string
	 */
	protected function sanitize_option( $key, $value ) {
		// Pre-sanitize option by key name.
		switch ( $key ) {
			case 'enable_location':
			case 'children_bookable':
			case 'infants_bookable':
				$value = ( 'on' === abrs_sanitize_checkbox( $value ) );
				break;

			case 'price_number_decimals':
			case 'scheduler_display_duration':
				$value = absint( $value );
				break;
		}

		/**
		 * Allow custom sanitize a special option value.
		 *
		 * @param mixed $value Mixed option value.
		 * @var   mixed
		 */
		$value = apply_filters( "awebooking/sanitize_option_{$key}", $value );

		/**
		 * Allow custom sanitize option values.
		 *
		 * @param mixed  $value The option value.
		 * @param string $key   The option key name.
		 * @var   mixed
		 */
		return apply_filters( 'awebooking/sanitize_option', $value, $key );
	}
}
