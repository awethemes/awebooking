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
	 * Getter the box property.
	 *
	 * @param  string $name The property name.
	 * @return mixed
	 */
	public function __get( $name ) {
		return $this->{$name};
	}

	/**
	 * Checks if the object has a property
	 *
	 * @param string $property The property name.
	 * @return bool
	 */
	public function __isset( $property ) {
		return property_exists( $this, $property );
	}
}
