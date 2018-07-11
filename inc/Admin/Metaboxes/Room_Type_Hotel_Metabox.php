<?php
namespace AweBooking\Admin\Metaboxes;

use AweBooking\Constants;

class Room_Type_Hotel_Metabox extends Abstract_Metabox {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id       = 'awebooking-room-type-hotel';
		$this->title    = esc_html__( 'Select Hotel', 'awebooking' );
		$this->screen   = Constants::ROOM_TYPE;
		$this->context  = 'side';
	}

	/**
	 * {@inheritdoc}
	 */
	public function should_show() {
		return abrs_multiple_hotels();
	}

	/**
	 * Output the metabox.
	 *
	 * @param \WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		$hotels = abrs_list_hotels( [], true )->pluck( 'name', 'id' );

		include trailingslashit( __DIR__ ) . 'views/html-room-type-hotel.php';
	}
}
