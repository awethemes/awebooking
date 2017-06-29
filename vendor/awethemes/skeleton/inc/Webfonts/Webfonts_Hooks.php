<?php
namespace Skeleton\Webfonts;

use Skeleton\Container\Service_Hooks;

class Webfonts_Hooks extends Service_Hooks {
	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 *
	 * @param Skeleton $skeleton Skeleton instance.
	 */
	public function register( $skeleton ) {
		// If user Google Fonts key is not valid, use this webfonts.
		$skeleton['webfonts-fallback'] = $skeleton['url'] . 'public/data/webfonts.json';

		$skeleton->bind( 'webfonts', function ( $c ) {
			return new Webfonts( $c );
		});
	}

	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param Skeleton $skeleton Skeleton instance.
	 */
	public function init( $skeleton ) {}
}
