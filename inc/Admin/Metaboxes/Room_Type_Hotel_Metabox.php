<?php
namespace AweBooking\Admin\Metaboxes;

class Room_Type_Hotel_Metabox {
	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		$hotels = abrs_list_hotels( [], true )->pluck( 'name', 'id' );

		include trailingslashit( __DIR__ ) . 'views/html-room-type-hotel.php';
	}
}
