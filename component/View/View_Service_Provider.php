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
		$this->register_twig_environment();
		$this->register_view_factory();
	}

	/**
	 * Register Twig environment and its loader.
	 *
	 * @return void
	 */
	protected function register_twig_environment() {
		$this->plugin->singleton( 'twig', function ( $plugin ) {
			$loader = new Twig\Loader( $this->plugin['view.finder'] );

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
			$finder = new File_Finder( $plugin['config']['view']['paths'] );

			$factory = new Factory( $this->create_engine_resolver( 'php' ), $finder );
			$factory->share( 'awebooking', $plugin );

			return $factory;
		} );

		$this->plugin->singleton( 'admin_view', function ( $plugin ) {
			$finder = new File_Finder( $plugin['config']['admin_view']['paths'] );

			$factory = new Factory( $this->create_engine_resolver( 'php', 'twig' ), $finder );
			$factory->share( 'awebooking', $plugin );

			return $factory;
		} );
	}

	/**
	 * Register the Engine_Resolver instance to the plugin.
	 *
	 * @param array|string $engines
	 * @return \AweBooking\Component\View\Engine_Resolver
	 */
	protected function create_engine_resolver( $engines ) {
		$resolver = new Engine_Resolver;

		foreach ( is_array( $engines ) ? $engines : func_get_args() as $engine ) {
			$this->{"register_{$engine}_engine"}( $resolver );
		}

		return $resolver;
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
			return new Engines\Twig_Engine( $this->plugin->make( 'twig' ) );
		} );
	}
}
