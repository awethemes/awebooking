<?php
namespace AweBooking\Frontend\Providers;

use AweBooking\Constants;
use AweBooking\Support\Service_Provider;

class Template_Loader_Service_Provider extends Service_Provider {
	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'template_include', [ $this, 'template_loader' ] );
	}

	/**
	 * Overwrite awebooking template in some case.
	 *
	 * @param  string $template The template file-path.
	 * @return string
	 *
	 * @access private
	 */
	public function template_loader( $template ) {
		if ( is_embed() ) {
			return $template;
		}

		if ( $overwrite_template = $this->find_overwrite_template() ) {
			$template = abrs_locate_template( $overwrite_template );
		}

		return $template;
	}

	/**
	 * Find the overwrite template by guest current context.
	 *
	 * @return string
	 */
	protected function find_overwrite_template() {
		$template = '';

		switch ( true ) {
			case is_singular( Constants::ROOM_TYPE ):
				$template = 'single-room.php';
				break;

			case is_post_type_archive( Constants::ROOM_TYPE ):
				$template = 'archive-room.php';
				break;

			case is_page( abrs_get_page_id( 'search_results' ) ):
				$template = 'search.php';
				break;

			case is_page( abrs_get_page_id( 'checkout' ) ):
				$template = 'checkout.php';
				break;
		}

		return $template;
	}
}
