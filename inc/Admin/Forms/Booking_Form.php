<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Support\Carbonate;

class Booking_Form extends Form_Abstract {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'booking_form';

	/**
	 * Booking item instance.
	 *
	 * @var string
	 */
	protected $booking_item;

	/**
	 * Create edit form.
	 *
	 * @param Booking_Room_Item $booking_item The booking item instance.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Register fields in to the CMB2.
	 *
	 * @return void
	 */
	protected function fields() {
		$this->add_field( array(
			'id'                => '_booking_datetime',
			'type'              => 'text_datetime_timestamp',
			'name'              => esc_html__( 'Booking Date', 'awebooking' ),
			'save_field'        => false, // Dont save this field as metadata.
			'date_format'       => 'Y-m-d',
			'time_format'       => 'H:i:s',
			'default_cb'        => function( $a ) {
				global $post;
				return Carbonate::create_datetime( $post->post_date )->getTimestamp();
			},
			'attributes'        => [
				'data-timepicker' => json_encode([
					'timeFormat' => 'HH:mm:ss',
					'stepMinute' => 1,
				]),
			],
		));

		// This field is special, we set name as "post_status" standard of
		// WordPress post status, so we'll leave to WP care about that.
		$this->add_field( array(
			'id'                => 'post_status',
			'type'              => 'select',
			'name'              => esc_html__( 'Booking status', 'awebooking' ),
			'save_field'        => false, // Dont save this field as metadata.
			'default_cb'        => function() {
				global $post;
				return get_post_status( $post );
			},
			'options'           => awebooking()->get_booking_statuses(),
		));

		// Customer infomation.
		$this->add_field( array(
			'id'                => 'customer_id',
			'type'              => 'select',
			'name'              => esc_html__( 'Customer', 'awebooking' ),
			'options_cb'        => wp_data_callback( 'users' ),
			'show_option_none'  => esc_html__( 'Guest', 'awebooking' ),
		));
	}
}
