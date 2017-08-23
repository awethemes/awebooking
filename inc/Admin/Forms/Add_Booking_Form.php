<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Booking\Request;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Support\Date_Period;

class Add_Booking_Form extends Form_Abstract {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'add_booking_form';

	/**
	 * Register fields in to the CMB2.
	 *
	 * @return void
	 */
	protected function fields() {
		$this->add_field([
			'id'          => 'add_check_in_out',
			'type'        => 'date_range',
			'name'        => esc_html__( 'Check-in/out', 'awebooking' ),
			'validate'    => 'required|datePeriod',
			'attributes'  => [ 'placeholder' => AweBooking::DATE_FORMAT, 'tabindex' => '-1' ],
			'date_format' => AweBooking::DATE_FORMAT,
		]);

		$this->add_field([
			'id'          => 'add_room',
			'type'        => 'select',
			'name'        => esc_html__( 'Room', 'awebooking' ),
			'validate'    => 'required|integer|min:1',
			'sanitization_cb'  => 'absint',
			'show_option_none' => esc_html__( 'Choose a room...', 'awebooking' ),
		]);

		$this->add_field([
			'id'               => 'add_adults',
			'type'             => 'select',
			'name'             => esc_html__( 'Number of adults', 'awebooking' ),
			'default'          => 1,
			'validate'         => 'required|numeric|min:1',
			'validate_label'   => esc_html__( 'Adults', 'awebooking' ),
			'sanitization_cb'  => 'absint',
		]);

		$this->add_field([
			'id'              => 'add_children',
			'type'            => 'select',
			'name'            => esc_html__( 'Number of children', 'awebooking' ),
			'default'         => 0,
			'validate'        => 'required|numeric|min:0',
			'sanitization_cb' => 'absint',
		]);

		$this->add_field([
			'id'              => 'add_price',
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
	 * @return Line_Item|false
	 */
	public function handle( array $data = null, $check_nonce = true ) {
		$sanitized = parent::handle( $data, $check_nonce );

		if ( empty( $data['booking_id'] ) ) {
			return false;
		}

		$period = new Date_Period(
			$sanitized['add_check_in_out'][0],
			$sanitized['add_check_in_out'][1]
		);

		$booking_id = absint( $data['booking_id'] );

		// Get objects from input.
		$the_room = Factory::get_room_unit( $sanitized['add_room'] );
		$the_booking = Factory::get_booking( $booking_id );

		if ( ! $the_room->exists() || ! $the_booking->exists() ) {
			return false;
		}

		if ( $the_room->is_free( $period ) ) {
			$the_item = new Line_Item;

			$the_item['room_id']   = $the_room->get_id();
			$the_item['name']      = $the_room->get_room_type()->get_title();
			$the_item['check_in']  = $period->get_start_date()->toDateString();
			$the_item['check_out'] = $period->get_end_date()->toDateString();
			$the_item['adults']    = isset( $sanitized['add_adults'] ) ? absint( $sanitized['add_adults'] ) : 0;
			$the_item['children']  = isset( $sanitized['add_children'] ) ? absint( $sanitized['add_children'] ) : 0;
			$the_item['total']     = $sanitized['add_price'];

			// Add booking item then save.
			$the_booking->add_item( $the_item );
			$the_booking->save();

			return $the_item;
		}

		return false;
	}

	/**
	 * Setup the fields value, attributes, etc...
	 *
	 * @return void
	 */
	public function setup_fields() {
		$this['add_price']->hide();
		$this['add_adults']->hide();
		$this['add_children']->hide();

		// Required check_in and check_out from request to continue.
		if ( empty( $_REQUEST['check_in'] ) || empty( $_REQUEST['check_out'] ) ) {
			return;
		}

		// First, try validate and the check-in check-out input.
		try {
			$period = new Date_Period(
				sanitize_text_field( wp_unslash( $_REQUEST['check_in'] ) ),
				sanitize_text_field( wp_unslash( $_REQUEST['check_out'] ) )
			);

			// Alway require minimum one night for booking.
			$period->require_minimum_nights();

			$this['add_check_in_out']->set_value([
				$period->get_start_date()->toDateString(),
				$period->get_end_date()->toDateString(),
			]);
		} catch ( \Exception $e ) {
			$this->add_validation_error( 'add_check_in_out', $e->getMessage() );
			return;
		}

		// Next, call to Concierge and check availability our hotel.
		$results = awebooking( 'concierge' )->check_availability( new Request( $period ) );

		$rooms_options = $this->generate_select_rooms( $results );
		$this['add_room']->set_prop( 'options', $rooms_options );

		if ( isset( $_REQUEST['add_room'] ) && array_key_exists( (int) $_REQUEST['add_room'], $rooms_options ) ) {
			$the_room = Factory::get_room_unit( (int) $_REQUEST['add_room'] );
			if ( ! $the_room->exists() ) {
				return;
			}

			$room_type = $the_room->get_room_type();
			$a = range( 1, $room_type->get_allowed_adults() );
			$b = range( 0, $room_type->get_allowed_children() );

			$this['add_room']
				->set_value( (int) $_REQUEST['add_room'] );

			$this['add_price']
				->show()
				->set_value( $room_type->get_base_price()->get_amount() );

			$this['add_adults']
				->show()
				->set_prop( 'options', array_combine( $a, $a ) );

			$this['add_children']
				->show()
				->set_prop( 'options', array_combine( $b, $b ) );
		} // End if().
	}

	/**
	 * Loop througth seach results and build flat rooms for select.
	 *
	 * TODO: Improve this!!!
	 *
	 * @param  array $results //.
	 * @return array
	 */
	protected function generate_select_rooms( array $results ) {
		$options = [];

		foreach ( $results as $availability ) {
			if ( $availability->unavailable() ) {
				continue;
			}

			$room_type = $availability->get_room_type();
			foreach ( $availability->get_rooms() as $room ) {
				$options[ $room->get_id() ] = $room_type->get_title() . ' (' . $room->get_name() . ')';
			}
		}

		return $options;
	}
}
