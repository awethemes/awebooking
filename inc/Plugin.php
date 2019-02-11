<?php

namespace AweBooking;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\BufferHandler;
use Monolog\Formatter\LineFormatter;
use Illuminate\Support\Arr;
use Illuminate\Container\Container;
use AweBooking\Support\Fluent;
use Awethemes\Relationships\Manager as Relationships;
use Awethemes\Http\Request;

final class Plugin extends Container {
	use Core\Concerns\Plugin_Provider,
		Core\Concerns\Plugin_Options;

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	const VERSION = '3.2.6';

	/**
	 * The plugin file path.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * Store the static configuration.
	 *
	 * @var \AweBooking\Support\Fluent
	 */
	protected $configuration;

	/**
	 * Indicates if the plugin has "booted".
	 *
	 * @var bool
	 */
	protected $booted = false;

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

		Constants::defines( $this );
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
	 * Load the static configuration.
	 *
	 * @param string $path The absolute path to the config file.
	 */
	public function load_config( $path ) {
		$config = require $path;

		$this->configuration = new Fluent( $config );

		$this->instance( 'config', $this->configuration );
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

		$this->singleton( 'multilingual', function() {
			return new Multilingual;
		});

		$this->singleton( 'logger', function () {
			return new Logger( 'awebooking', [ new BufferHandler( $this->get_monolog_handler() ) ] );
		});

		if ( class_exists( Relationships::class ) ) {
			$this->singleton( 'relationships', function () {
				return Relationships::get_instance();
			} );
		}

		$this->alias( 'logger', Logger::class );
		$this->alias( 'logger', LoggerInterface::class );
		$this->alias( 'multilingual', Multilingual::class );

		$this->alias( Request::class, 'Awethemes\\Http\\Request' );
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
	 * Get the relationships.
	 *
	 * @return \Awethemes\Relationships\Manager
	 */
	public function relationships() {
		return $this->make( 'relationships' );
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
		$this->configuration['bootstrappers'][] = $bootstrap;
	}

	/**
	 * Register a provider into the plugin.
	 *
	 * @param string $provider The provider class name.
	 * @param bool   $prepend  Prepend or append.
	 */
	public function provider( $provider, $prepend = true ) {
		if ( $prepend ) {
			$this->configuration['service_providers'] = Arr::prepend( $this->configuration['service_providers'], $provider );
		} else {
			$this->configuration['service_providers'][] = $provider;
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
		foreach ( $this->configuration['bootstrappers'] as $bootstrapper ) {
			$this->make( $bootstrapper )->bootstrap( $this );
		}

		/**
		 * Fire the init action.
		 *
		 * @param \AweBooking\Plugin $awebooking The awebooking class instance.
		 */
		do_action( 'awebooking_init', $this );

		// Filter the service_providers.
		$providers = apply_filters( 'abrs_service_providers', $this->configuration['service_providers'], $this );

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
	 * @throws mixed
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
}
