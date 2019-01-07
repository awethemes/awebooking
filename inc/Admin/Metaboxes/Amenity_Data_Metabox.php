<?php

namespace AweBooking\Admin\Metaboxes;

use AweBooking\Constants;
use AweBooking\Admin\Metabox;
use AweBooking\Component\Form\Form;

class Amenity_Data_Metabox extends Metabox {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id         = 'awebooking-amenity';
		$this->taxonomies = Constants::HOTEL_AMENITY;
	}

	/**
	 * {@inheritdoc}
	 */
	public function output( $taxonomy, $term = null ) {
		if ( ! $this->should_show() ) {
			return;
		}

		$form = $this->get_controls();
		$form->prepare_fields();

		$this->output_controls( $form );
	}

	/**
	 * {@inheritdoc}
	 */
	public function save( $term_id, $taxonomy, $request ) {
		$form = $this->get_controls( $term_id );

		$form->save_fields( 0, 'term', $request->all() );
	}

	/**
	 * Gets the controls.
	 *
	 * @param \WP_Term|int|null $term The term ID or term object.
	 *
	 * @return \AweBooking\Component\Form\Form
	 */
	protected function get_controls( $term = null ) {
		$controls = new Form( $this->id, abrs_parse_object_id( $term ), 'term' );

		if ( function_exists( 'wp_simple_iconfonts' ) ) {
			$controls->add_field([
				'name' => esc_html__( 'Icon', 'awebooking' ),
				'id'   => '_icon',
				'type' => 'simple_iconfonts',
			]);
		}

		do_action( 'abrs_register_amenity_fields', $controls );

		return $controls;
	}
}
