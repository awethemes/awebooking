<?php
namespace AweBooking\Support;

use Skeleton\Container\Service_Hooks;

class Skeleton_Support extends Service_Hooks {
	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 *
	 * @param Container $container Container instance.
	 */
	public function register( $container ) {

		$container->extend( 'cmb2_manager', function( $manager ) {
			$manager->register_field( 'date_range', 'AweBooking\\Support\\Fields\\Date_Range_Field' );

			return $manager;
		});

	}
}
