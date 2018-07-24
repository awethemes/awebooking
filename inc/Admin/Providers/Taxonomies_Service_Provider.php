<?php
namespace AweBooking\Admin\Providers;

use AweBooking\Support\Service_Provider;

class Taxonomies_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @access private
	 */
	public function register() {
		/*foreach ([ // @codingStandardsIgnoreLine
			'term_metabox.amenity' => \AweBooking\Admin\Metaboxes\Amenity_Data_Metabox::class,
		] as $abstract => $concrete ) {
			$this->plugin->bind( $abstract, $concrete );
		}*/
	}

	/**
	 * Init the hooks.
	 *
	 * @access private
	 */
	public function init() {
		/*foreach ( [ 'hotel_amenity' ] as $taxonomy ) {
			add_action( "{$taxonomy}_edit_form", $this->metaboxcb( 'term_metabox.' . $taxonomy ) );
			add_action( "{$taxonomy}_add_form_fields", $this->metaboxcb( 'term_metabox.' . $taxonomy ) );
		}*/

		// Maintain hierarchy of terms.
		add_filter( 'wp_terms_checklist_args', [ $this, 'disable_checked_ontop' ] );
	}

	/**
	 * Make a callable for metabox output.
	 *
	 * @param  string $binding The binding in the plugin.
	 * @return \Closure
	 */
	protected function metaboxcb( $binding ) {
		return function( $term ) use ( $binding ) {
			$this->plugin->make( $binding )->output( $term );
		};
	}

	/**
	 * Maintain term hierarchy when editing screen.
	 *
	 * @param  array $args The term args.
	 * @return array
	 */
	public function disable_checked_ontop( $args ) {
		if ( ! empty( $args['taxonomy'] ) && 'hotel_amenity' === $args['taxonomy'] ) {
			$args['checked_ontop'] = false;
		}

		return $args;
	}
}
