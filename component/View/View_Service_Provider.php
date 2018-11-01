<?php

namespace AweBooking\Component\View;

use Twig_Environment;
use AweBooking\Support\Service_Provider;

class View_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		$this->register_view_finder();
		$this->register_twig_environment();
		$this->register_engine_resolver();
		$this->register_view_factory();
	}

	/**
	 * Register the View Finder instance to the plugin.
	 *
	 * @return void
	 */
	protected function register_view_finder() {
		$this->plugin->singleton( 'view.finder', function ( $container ) {
			return new File_Finder( [ $this->plugin->plugin_path( 'templates/' ) ] );
		} );
	}

	/**
	 * Register the Engine_Resolver instance to the plugin.
	 *
	 * @return void
	 */
	protected function register_engine_resolver() {
		$this->plugin->singleton( 'view.engine_resolver', function () {
			$resolver = new Engine_Resolver;

			$this->register_php_engine( $resolver );
			$this->register_twig_engine( $resolver );

			return $resolver;
		} );
	}

	/**
	 * Register the PHP engine to the Engine_Resolver.
	 *
	 * @param \AweBooking\Component\View\Engine_Resolver $resolver
	 */
	protected function register_php_engine( Engine_Resolver $resolver ) {
		$resolver->register( 'php', function () {
			return new Engines\Php_Engine;
		} );
	}

	/**
	 * Register the Twig engine to the Engine_Resolver.
	 *
	 * @param \AweBooking\Component\View\Engine_Resolver $resolver
	 */
	protected function register_twig_engine( Engine_Resolver $resolver ) {
		$resolver->register( 'twig', function () {
			return new Engines\Twig_Engine( $this->plugin['twig'] );
		} );
	}

	/**
	 * Register Twig environment and its loader.
	 *
	 * @return void
	 */
	protected function register_twig_environment() {
		$this->plugin->singleton( 'twig.loader', function () {
			return new Twig\Loader( $this->plugin['view.finder'] );
		} );

		$this->plugin->singleton( 'twig', function ( $plugin ) {
			return new Twig_Environment( $plugin['twig.loader'], [
				'debug'       => defined( 'WP_DEBUG' ) && WP_DEBUG,
				'cache'       => false,
				'auto_reload' => true,
			] );
		} );
	}

	/**
	 * Register the view factory.
	 *
	 * @return void
	 */
	protected function register_view_factory() {
		$this->plugin->singleton( 'view', function ( $plugin ) {
			$factory = new Factory( $plugin['view.engine_resolver'], $plugin['view.finder'] );

			$factory->share( 'awebooking', $plugin );

			return $factory;
		} );
	}
}
