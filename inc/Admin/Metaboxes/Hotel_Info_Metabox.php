<?php
namespace AweBooking\Admin\Metaboxes;

use Awethemes\Http\Request;
use AweBooking\Model\Hotel;
use AweBooking\Admin\Forms\Hotel_Information_Form;

class Hotel_Info_Metabox {
	/**
	 * Output the metabox.
	 *
	 * @param \WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		global $the_hotel;

		if ( is_null( $the_hotel ) ) {
			$the_hotel = abrs_get_hotel( $post->ID );
		}

		// Print the core nonce field.
		wp_nonce_field( 'awebooking_save_data', '_awebooking_nonce' );

		$form = new Hotel_Information_Form( $the_hotel );
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
	 * @param \Awethemes\Http\Request $request The HTTP Request.
	 */
	public function save( $post, Request $request ) {
		$hotel = new Hotel( $post->ID );

		$values = ( new Hotel_Information_Form( $hotel ) )->handle( $request );
		$hotel->fill( $values->toArray() );

		// Fire action before save.
		do_action( 'abrs_process_hotel_data', $hotel, $values, $request );

		$saved = $hotel->save();

		if ( $saved ) {
			abrs_admin_notices( 'Successfully updated', 'success' )->dialog();
		}
	}
}
