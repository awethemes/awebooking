<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Booking\Booking;

class Booking_General_From extends Form_Abstract {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'awebooking-general-form';

	/**
	 * Booking instance.
	 *
	 * @var Booking
	 */
	protected $booking;

	/**
	 * Create booking general form.
	 *
	 * @param Booking $booking The booking instance.
	 */
	public function __construct( Booking $booking ) {
		$this->booking = $booking;

		parent::__construct();
	}

	/**
	 * Register fields in to the CMB2.
	 *
	 * @return void
	 */
	protected function fields() {
		$this->add_field([
			'id'          => 'booking_created_date',
			'type'        => 'text_datetime_timestamp',
			'name'        => esc_html__( 'Booking Date', 'awebooking' ),
			'date_format' => AweBooking::DATE_FORMAT,
			'time_format' => 'H:i:s',
			'validate'    => 'required',
			'attributes'  => [
				'data-timepicker' => json_encode([
					'timeFormat' => 'HH:mm:ss',
					'stepMinute' => 1,
				]),
			],
		]);

		$this->add_field([
			'id'       => 'booking_status',
			'type'     => 'select',
			'name'     => esc_html__( 'Booking status', 'awebooking' ),
			'validate' => 'required',
			'options'  => awebooking( 'setting' )->get_booking_statuses(),
		]);

		$this->add_field([
			'id'         => 'booking_customer',
			'type'       => 'select',
			'name'       => esc_html__( 'Customer', 'awebooking' ),
			'default'    => 0,
			'options_cb' => [ $this, '_get_customer' ],
			'attributes' => [
				'data-allow-clear' => true,
				'data-placeholder' => esc_html__( 'Guest', 'awebooking' ),
			],
		]);
	}

	/**
	 * Setup the fields value, attributes, etc...
	 *
	 * @return void
	 */
	public function setup_fields() {
		$this['booking_status']->set_value( $this->booking->get_status() );
		$this['booking_created_date']->set_value( $this->booking->get_booking_date()->getTimestamp() );
		$this['booking_customer']->set_value( $this->booking->get_customer_id() );
	}

	public function _get_customer() {
		$booking_customer = $this->booking->get_customer_id();

		if ( $booking_customer ) {
			$user = get_user_by( 'id', $this->booking->get_customer_id() );

			/* translators: 1: user display name 2: user ID 3: user email */
			$user_string = sprintf(
				esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'awebooking' ),
				$user->display_name,
				absint( $user->ID ),
				$user->user_email
			);

			return [ $booking_customer => $user_string ];
		}

		return [];
	}
}
