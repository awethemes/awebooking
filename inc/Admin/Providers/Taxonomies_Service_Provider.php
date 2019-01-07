<?php

namespace AweBooking\Admin\Providers;

use AweBooking\Admin\Metabox;
use AweBooking\Support\Service_Provider;

class Taxonomies_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @access private
	 */
	public function register() {
		foreach ([ // @codingStandardsIgnoreLine
			'term_metabox.amenity' => \AweBooking\Admin\Metaboxes\Amenity_Data_Metabox::class,
		] as $abstract => $concrete ) {
			$this->plugin->bind( $abstract, $concrete );
			$this->plugin->tag( $abstract, 'term_metaboxes' );
		}
	}

	/**
	 * Init the hooks.
	 *
	 * @access private
	 */
	public function init() {
		// Maintain hierarchy of terms.
		add_filter( 'wp_terms_checklist_args', [ $this, 'disable_checked_ontop' ] );

		// Register term metaboxes.
		foreach ( $this->plugin->tagged( 'term_metaboxes' ) as $box ) {
			if ( ! $box instanceof Metabox ) {
				continue;
			}

			foreach ( (array) $box->taxonomies as $taxonomy ) {
				add_action( "{$taxonomy}_edit_form", $box->callback(), 8, 2 );
				add_action( "{$taxonomy}_add_form_fields", $box->callback(), 8, 2 );
			}
		}

		add_action( 'created_term', [ $this, 'save_term' ], 10, 3 );
		add_action( 'edited_terms', [ $this, 'save_term' ], 10, 2 );
	}

	/**
	 * Save data from term fields.
	 *
	 * @param  int    $term_id  Term ID.
	 * @param  int    $tt_id    The term taxonomy ID.
	 * @param  string $taxonomy The taxonomy.
	 *
	 * @return void
	 */
	public function save_term( $term_id, $tt_id = 0, $taxonomy = '' ) {
		static $is_saving;

		// Correct the "taxonomy" between "created_term" and "edited_terms" hook.
		$taxonomy = $taxonomy ?: $tt_id;
		if ( empty( $term_id ) || empty( $taxonomy ) || $is_saving ) {
			return;
		}

		// Can the user edit this term?
		$taxonomy_object = get_taxonomy( $taxonomy );
		if ( empty( $taxonomy_object->cap ) || ! current_user_can( $taxonomy_object->cap->edit_terms ) ) {
			return;
		}

		// Verify the nonce.
		if ( empty( $_POST['_awebooking_nonce'] ) || ! wp_verify_nonce( $_POST['_awebooking_nonce'], 'awebooking_save_data' ) ) {
			return;
		}

		// Save the state to run once time, avoid potential endless loops.
		$is_saving = true;

		$boxes = abrs_collect( $this->plugin->tagged( 'term_metaboxes' ) )
			->filter( function ( $box ) use ( $taxonomy ) {
				return $box instanceof Metabox
						&& method_exists( $box, 'save' )
						&& in_array( $taxonomy, (array) $box->taxonomies );
			});

		// Create the HTTP request.
		$request = $this->plugin->make( 'request' );

		// Handle save the boxes.
		foreach ( $boxes as $box ) {
			try {
				$box->save( $term_id, $taxonomy, $request );
			} catch ( \Exception $e ) {
				abrs_report( $e );
			}
		}
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
