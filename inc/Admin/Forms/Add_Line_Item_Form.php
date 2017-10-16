<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Factory;
use AweBooking\Concierge;
use AweBooking\AweBooking;
use AweBooking\Hotel\Service;
use AweBooking\Booking\Booking;
use AweBooking\Booking\Request;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;
use AweBooking\Support\Period;

class Add_Line_Item_Form extends Form_Abstract {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'add_booking_form';

	/**
	 * The booking instance.
	 *
	 * @var Booking
	 */
	protected $booking;

	/**
	 * Form constructor.
	 *
	 * @param Booking $booking The Booking to add line item.
	 */
	public function __construct( Booking $booking ) {
		parent::__construct();
		$this->booking = $booking;
	}

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

		// TODO: ...
		$this->add_field([
			'id'              => 'add_services',
			'type'            => 'awebooking_services',
			'name'            => esc_html__( 'Services', 'awebooking' ),
			'room_type'       => 0,
		]);

		$this->add_field([
			'id'              => 'add_price',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Total price', 'awebooking' ),
			'validate'        => 'required|price',
			'sanitization_cb' => 'awebooking_sanitize_price',
		]);
	}

	/**
	 * Display some HTML or hidden input after form.
	 *
	 * @return void
	 */
	public function after_form() {
		printf( '<input type="hidden" name="booking_id" value="%d" />', esc_attr( $this->booking->get_id() ) );
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

		$period = new Period(
			$sanitized['add_check_in_out'][0],
			$sanitized['add_check_in_out'][1]
		);

		// Get room unit object from input.
		$the_room = Factory::get_room_unit( $sanitized['add_room'] );
		if ( ! $the_room->exists() ) {
			return false;
		}

		if ( Concierge::is_available( $the_room, $period ) ) {
			$the_item = new Line_Item;

			$the_item['name']      = $the_room->get_room_type()->get_title();
			$the_item['room_id']   = $the_room->get_id();
			$the_item['check_in']  = $period->get_start_date()->toDateString();
			$the_item['check_out'] = $period->get_end_date()->toDateString();
			$the_item['adults']    = isset( $sanitized['add_adults'] ) ? absint( $sanitized['add_adults'] ) : 0;
			$the_item['children']  = isset( $sanitized['add_children'] ) ? absint( $sanitized['add_children'] ) : 0;
			$the_item['total']     = $sanitized['add_price'];

			// Add booking item then save.
			$this->booking->add_item( $the_item );
			$this->booking->save();

			// TODO: ...
			if ( isset( $sanitized['add_services'] ) && ! empty( $sanitized['add_services'] ) ) {
				$add_services = array_map( 'absint', $sanitized['add_services'] );

				foreach ( $add_services as $service_id ) {
					$service = new Service( $service_id );
					if ( ! $service->exists() ) {
						continue;
					}

					$service_item = new Service_Item;
					$service_item['name']       = $service->get_name();
					$service_item['parent_id']  = $the_item->get_id();
					$service_item['service_id'] = $service->get_id();

					$this->booking->add_item( $service_item );
				}

				// Re-save the booking.
				$this->booking->save();
			}

			$this->booking->calculate_totals();

			return $the_item;
		} // End if().

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
		$this['add_services']->hide();

		// Required check_in and check_out from request to continue.
		if ( empty( $_REQUEST['add_check_in_out'][0] ) || empty( $_REQUEST['add_check_in_out'][1] ) ) {
			return;
		}

		// First, try validate and the check-in check-out input.
		try {
			$period = new Period(
				sanitize_text_field( wp_unslash( $_REQUEST['add_check_in_out'][0] ) ),
				sanitize_text_field( wp_unslash( $_REQUEST['add_check_in_out'][1] ) )
			);

			// Alway require minimum one night for booking.
			$period->required_minimum_nights();

			$this['add_check_in_out']->set_value([
				$period->get_start_date()->toDateString(),
				$period->get_end_date()->toDateString(),
			]);
		} catch ( \Exception $e ) {
			$this->add_validation_error( 'add_check_in_out', $e->getMessage() );
			return;
		}

		// Next, call to Concierge and check availability our hotel.
		$request = new Request( $period );
		$results = Concierge::check_availability( $request );

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

			$price = Concierge::get_room_price( $room_type, $request );

			$this['add_room']
				->set_value( (int) $_REQUEST['add_room'] );

			$this['add_price']
				->set_value( $price->get_amount() )
				->show();

			$this['add_adults']
				->set_prop( 'options', array_combine( $a, $a ) )
				->show();

			$this['add_children']
				->set_prop( 'options', array_combine( $b, $b ) )
				->show();

			$this['add_services']
				->set_prop( 'room_type', $room_type->get_id() )
				->show();
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
	protected function generate_select_rooms( $results ) {
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
