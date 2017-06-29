<?php
namespace Skeleton\Container;

/**
 * Container service hooks abstract.
 */
abstract class Service_Hooks {
	/**
	 * Determine run init action only in admin.
	 *
	 * @var boolean
	 */
	public $in_admin = false;

	/**
	 * The container instance.
	 *
	 * @var Container
	 */
	public $container;

	/**
	 * Instantiate the service provider.
	 *
	 * @param Container $container Container instance.
	 */
	public function __construct( Container $container = null ) {
		$this->container = $container;
	}

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 *
	 * @param Container $container Container instance.
	 */
	public function register( $container ) {}

	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param Container $container Container instance.
	 */
	public function init( $container ) {}
}
