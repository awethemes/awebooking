<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Booking\Booking_Room_Item;

class Edit_Booking_Form extends Form_Abstract {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'edit_booking_form';

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
	public function __construct( Booking_Room_Item $booking_item ) {
		$this->booking_item = $booking_item;
	}

	/**
	 * Register fields in to the CMB2.
	 *
	 * @return void
	 */
	protected function fields() {
		$this->add_field([
			'id'          => 'edit_check_in_out',
			'type'        => 'date_range',
			'name'        => esc_html__( 'Check-in/out', 'awebooking' ),
			'validate'    => 'required',
			'attributes'  => [ 'placeholder' => AweBooking::DATE_FORMAT, 'required' => true ],
			'date_format' => AweBooking::DATE_FORMAT,
		]);

		$this->add_field([
			'id'               => 'edit_adults',
			'type'             => 'select',
			'name'             => esc_html__( 'Number of adults', 'awebooking' ),
			'default'          => 1,
			'validate'         => 'required|numeric|min:1',
			'validate_label'   => esc_html__( 'Adults', 'awebooking' ),
			'sanitization_cb'  => 'absint',
		]);

		$this->add_field([
			'id'              => 'edit_children',
			'type'            => 'select',
			'name'            => esc_html__( 'Number of children', 'awebooking' ),
			'default'         => 0,
			'validate'        => 'required|numeric|min:0',
			'sanitization_cb' => 'absint',
		]);

		$this->add_field([
			'id'              => 'edit_price',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Price (per night)', 'awebooking' ),
			'validate'        => 'required|price',
			'sanitization_cb' => 'awebooking_sanitize_price',
		]);
	}

	/**
	 * Handle process the form.
	 *
	 * @param  array|null $data        An array input data, if null $_POST will be use.
	 * @param  boolean    $check_nonce Run verity nonce from request.
	 * @return Booking_Room_Item|false
	 */
	public function handle( array $data = null, $check_nonce = true ) {
		$sanitized = parent::handle( $data, $check_nonce );

		if ( empty( $data['booking_id'] ) || empty( $data['item_id'] ) ) {
			return false;
		}

		// Ensure booking exists in database.
		$the_booking = Factory::get_booking( (int) $data['booking_id'] );
		if ( ! $the_booking->exists() ) {
			return false;
		}

		$item_id = absint( $data['item_id'] );
		if ( ! $the_booking->has_item( $item_id ) ) {
			return false;
		}

		$booking_item = $the_booking->get_item( $item_id );

		// Fill the input data then save them.
		$booking_item['adults'] = $sanitized['edit_adults'];
		$booking_item['children'] = $sanitized['edit_children'];

		if ( isset( $sanitized['edit_price'] ) ) {
			$booking_item['total'] = $sanitized['edit_price'];
		}

		if ( isset( $sanitized['check_in_out'] ) ) {
			$booking_item['check_in'] = $sanitized['edit_check_in_out'][0];
			$booking_item['check_out'] = $sanitized['edit_check_in_out'][1];
		}

		$booking_item->save();
	}
}
