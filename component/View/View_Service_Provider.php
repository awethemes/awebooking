<?php

namespace AweBooking\Component\View;

use Twig_Loader_Filesystem;
use AweBooking\Support\Service_Provider;

class View_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerTwigEnvironment();
		$this->registerEngineResolver();
		$this->registerViewFactory();
	}

	/**
	 * Register the EngineResolver instance to the application.
	 */
	protected function registerEngineResolver() {
		$this->plugin->singleton( 'view.engine_resolver', function () {
			$resolver = new Engine_Resolver;

			$this->registerPhpEngine( $resolver );

			return $resolver;
		} );
	}

	/**
	 * Register the PHP engine to the EngineResolver.
	 *
	 * @param string                                  $engine Name of the engine.
	 * @param \Illuminate\View\Engines\EngineResolver $resolver
	 */
	protected function registerPhpEngine( $resolver ) {
		$resolver->register( 'php', function () {
			return new Engines\Php_Engine();
		} );
	}

	/**
	 * Register the Twig engine to the EngineResolver.
	 *
	 * @param string         $engine
	 * @param EngineResolver $resolver
	 */
	protected function registerTwigEngine( $engine, EngineResolver $resolver ) {
		$container = $this->app;
		$resolver->register( $engine, function () use ( $container ) {
			// Set the loader main namespace (paths).
			$container['twig.loader']->setPaths( $container['view.finder']->getPaths() );

			return new TwigEngine( $container['twig'], $container['view.finder'] );
		} );
	}

	/**
	 * Register Twig environment and its loader.
	 */
	protected function registerTwigEnvironment() {
		$this->plugin->singleton( 'twig.loader', function () {
			return new Twig_Loader_Filesystem();
		} );

		$this->plugin->singleton( 'twig', function ( $container ) {
			return new Twig\Environment( $container['twig.loader'], [
				'auto_reload' => true,
				'cache'       => $container['path.storage'] . 'twig',
			] );
		} );
	}

	/**
	 * Register the view factory. The factory is
	 * available in all views.
	 */
	protected function registerViewFactory() {
		$this->plugin->singleton( 'view.finder', function ( $container ) {
			return new File_Finder( [ $this->plugin->plugin_path( 'templates/' ) ] );
		} );

		$this->plugin->singleton( 'view', function ( $plugin ) {
			$factory = new Factory( $plugin['view.engine_resolver'], $plugin['view.finder'] );

			$factory->share( 'awebooking', $plugin );

			return $factory;
		} );

		// dd( $this->plugin['view']->make( 'hotel' ) );
	}
}
