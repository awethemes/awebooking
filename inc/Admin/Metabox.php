<?php

namespace AweBooking\Admin;

abstract class Metabox {
	/**
	 * Meta box ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Title of the meta box.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * The screen or screens on which to show the box.
	 *
	 * @var array|string|null
	 */
	public $screen;

	/**
	 * The context within the screen.
	 *
	 * @var string
	 */
	public $context = 'advanced';

	/**
	 * The priority within the context ('high', 'low').
	 *
	 * @var string
	 */
	public $priority = 'default';

	/**
	 * The array of taxonomies.
	 *
	 * @var array
	 */
	public $taxonomies = [];

	/**
	 * Returns the output callback.
	 *
	 * @return array
	 */
	public function callback() {
		return [ $this, 'output' ];
	}

	/**
	 * Determine whether this metabox should show.
	 *
	 * @return bool
	 */
	public function should_show() {
		return true;
	}

	/**
	 * Returns the IDs get from the WP_Screen ID.
	 *
	 * @return array
	 */
	public function get_screen_ids() {
		if ( empty( $this->screen ) ) {
			return [];
		}

		return array_filter(
			array_map( function( $screen ) {
				return convert_to_screen( $screen )->id;
			}, (array) $this->screen )
		);
	}

	/**
	 * Output the form controls.
	 *
	 * @param \AweBooking\Component\Form\Form $form The form controls.
	 */
	protected function output_controls( $form ) {
		wp_nonce_field( 'awebooking_save_data', '_awebooking_nonce' );

		echo '<div class="cmb2-wrap awebooking-wrap"><div class="cmb2-metabox">';

		foreach ( $form->prop( 'fields' ) as $args ) {
			$form->show_field( $args['id'] );
		}

		echo '</div></div>';
	}

	/**
	 * Determines if current screen is "add" screen.
	 *
	 * @return bool
	 */
	public function on_add_screen() {
		$screen = get_current_screen();

		return $screen && 'post' === $screen->base && 'add' === $screen->action;
	}

	/**
	 * Determines if current screen is "edit" screen.
	 *
	 * @return bool
	 */
	public function on_edit_screen() {
		$screen = get_current_screen();

		return $screen && 'post' === $screen->base && '' === $screen->action;
	}

	/**
	 * Hide a element on "add" screen.
	 *
	 * @return void
	 */
	public function show_on_add() {
		printf( 'style="display: %s;"', $this->on_add_screen() ? 'block' : 'none' );
	}

	/**
	 * Hide a element on "edit" screen.
	 *
	 * @return void
	 */
	public function show_on_edit() {
		printf( 'style="display: %s;"', $this->on_edit_screen() ? 'block' : 'none' );
	}
}
