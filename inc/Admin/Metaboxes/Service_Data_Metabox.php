<?php

namespace AweBooking\Admin\Metaboxes;

use AweBooking\Constants;
use WPLibs\Http\Request;
use AweBooking\Model\Service;
use AweBooking\Admin\Metabox;
use AweBooking\Admin\Forms\Service_Data_Form;

class Service_Data_Metabox extends Metabox {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id     = 'awebooking-service-data';
		$this->title  = esc_html__( 'Service', 'awebooking' );
		$this->screen = Constants::HOTEL_SERVICE;
	}

	/**
	 * Output the metabox.
	 *
	 * @param \WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		global $the_service;

		if ( is_null( $the_service ) ) {
			$the_service = abrs_get_service( $post->ID );
		}

		// Print the core nonce field.
		wp_nonce_field( 'awebooking_save_data', '_awebooking_nonce' );

		$form = new Service_Data_Form( $the_service );

		echo '<div class="cmb2-wrap awebooking-wrap abrs-cmb2-float"><div class="cmb2-metabox">';

		foreach ( $form->prop( 'fields' ) as $args ) {
			$form->show_field( $args['id'] );
		}

		echo '</div></div>';
	}

	/**
	 * Handle save the the metabox.
	 *
	 * @param \WP_Post                $post    The WP_Post object instance.
	 * @param \WPLibs\Http\Request $request The HTTP Request.
	 */
	public function save( $post, Request $request ) {
		$service = new Service( $post->ID );
		$values = ( new Service_Data_Form( $service ) )->handle( $request );

		$service->fill( $values->toArray() );

		// Fire action before save.
		do_action( 'abrs_process_service_data', $service, $values, $request );

		$saved = $service->save();

		if ( $saved ) {
			abrs_flash_notices( 'Successfully updated', 'success' )->dialog();
		}
	}
}
