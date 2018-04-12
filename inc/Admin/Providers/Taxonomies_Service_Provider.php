<?php
namespace AweBooking\Admin\Providers;

use AweBooking\Constants;
use AweBooking\Support\Service_Provider;

class Taxonomies_Service_Provider extends Service_Provider {
	/**
	 * Init the hooks.
	 *
	 * @access private
	 */
	public function init() {
		// Maintain hierarchy of terms.
		add_filter( 'wp_terms_checklist_args', [ $this, 'disable_checked_ontop' ] );
	}

	/**
	 * Maintain term hierarchy when editing screen.
	 *
	 * @param  array $args The term args.
	 * @return array
	 */
	public function disable_checked_ontop( $args ) {
		if ( ! empty( $args['taxonomy'] ) && in_array( $args['taxonomy'], [ 'hotel_amenity' ] ) ) {
			$args['checked_ontop'] = false;
		}

		return $args;
	}
}
