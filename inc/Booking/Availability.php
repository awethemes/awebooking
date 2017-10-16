<?php
namespace AweBooking\Booking;

use AweBooking\Concierge;
use AweBooking\Hotel\Room;
use AweBooking\Hotel\Room_Type;
use AweBooking\AweBooking;
use AweBooking\Hotel\Service;

use AweBooking\Pricing\Price;
use AweBooking\Pricing\Price_Calculator;
use AweBooking\Calculator\Service_Calculator;

class Availability {
	/**
	 * Room type instance.
	 *
	 * @var Room_Type
	 */
	protected $room_type;

	/**
	 * Bookign request instance.
	 *
	 * @var Request
	 */
	protected $request;

	/**
	 * List available rooms of room type.
	 *
	 * @var array
	 */
	protected $rooms = [];

	/**
	 * Availability of a room-type.
	 *
	 * @param Room_Type         $room_type //.
	 * @param Request $request   //.
	 */
	public function __construct( Room_Type $room_type, Request $request ) {
		$this->request = $request;
		$this->room_type = $room_type;

		$this->maybe_set_request();
	}

	/**
	 * Get room type.
	 *
	 * @return Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Get rooms available.
	 *
	 * @return array
	 */
	public function get_rooms() {
		return $this->rooms;
	}

	public function get_rooms_ids() {
		$ids = array_keys( wp_list_pluck( $this->rooms, 'name' ) );

		return array_map( 'absint', $ids );
	}

	/**
	 * Get request instance.
	 *
	 * @return Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Is room-type available for booking.
	 *
	 * @return bool
	 */
	public function available() {
		return $this->total_available_rooms() > 0;
	}

	/**
	 * Is room-type unavailable.
	 *
	 * @return bool
	 */
	public function unavailable() {
		return ! $this->available();
	}

	public function get_available_rooms() {
		return count( $this->get_rooms() );
	}

	/**
	 * Get total rooms available.
	 *
	 * @return int
	 */
	public function total_available_rooms() {
		return count( $this->get_rooms() );
	}

	public function get_overflow_adults() {
		$adults = $this->get_adults();

		if ( $this->room_type->get_max_adults() <= 0 ) {
			return 0;
		}

		return ($adults - $this->room_type->get_number_adults() );
	}

	/**
	 * Get price.
	 *
	 * @return Price
	 */
	public function get_price() {
		$price = Concierge::get_room_price( $this->room_type, $this->request );

		$pipes = apply_filters( 'awebooking/availability/room_price_pipes', [], $this->request, $this );

		return (new Price_Calculator( $price ))
			->through( $pipes )
			->process();
	}

	/**
	 * Get price.
	 *
	 * @return Price
	 */
	public function get_price_average() {
		return $this->get_price()->divide( $this->request->get_nights() );
	}

	public function get_extra_services_price() {
		$pipes = [];
		$this->through_services( $pipes );

		return (new Price_Calculator( new Price( 0 ) ))
			->through( $pipes )
			->process();
	}

	/**
	 * Get total price.
	 *
	 * @return string
	 */
	public function get_total_price() {
		$price = $this->get_price();

		if ( $this->request->has_request( 'extra_services' ) ) {
			$price = $price->add( $this->get_extra_services_price() );
		}

		$pipes = apply_filters( 'awebooking/availability/total_price_pipes', [], $this->request, $this );

		return (new Price_Calculator( $price ))
			->through( $pipes )
			->process();
	}

	/**
	 * Add a availability of room.
	 *
	 * @param Room $room Room instance.
	 */
	public function add_room( Room $room ) {
		$this->rooms[ $room->get_id() ] = $room;

		return $this;
	}

	public function get_booking_url() {
		return awebooking_get_page_permalink( 'booking' );
	}

	/**
	 * //
	 *
	 * @param  array $pipes //.
	 * @return void
	 */
	protected function through_services( array &$pipes ) {
		if ( ! $this->request->has_request( 'extra_services' ) ) {
			return;
		}

		foreach ( $this->request->get_request( 'extra_services' ) as $service ) {
			$term_instance = get_term( $service, AweBooking::HOTEL_SERVICE );
			if ( is_null( $term_instance ) || is_wp_error( $term_instance ) ) {
				continue;
			}

			$extra_service = new Service( $term_instance->term_id, $term_instance );
			$pipes[] = new Service_Calculator( $extra_service, $this->get_request(), $this->get_price() );
		}
	}

	protected function maybe_set_request() {
		$request_services = (array) $this->request->get_request( 'extra_services' );

		foreach ( $this->room_type->get_services() as $service ) {
			$term_id = $service->get_id();

			if ( $service->is_mandatory() && ! in_array( $term_id, $request_services ) ) {
				$request_services[] = $term_id;
			}
		}

		$this->request->set_request( 'extra_services', $request_services );
	}

	/**
	 * //
	 *
	 * @return array
	 */
	public function to_array() {
		return [
			'check_in'  => $this->get_request()->get_check_in()->toDateString(),
			'check_out' => $this->get_request()->get_check_out()->toDateString(),
			'nights'    => $this->get_request()->get_nights(),
			'price'     => $this->get_price()->get_amount(),
			'total'     => $this->get_total_price()->get_amount(),
		];
	}

	/**
	 * Allow dynamic call method from request object.
	 *
	 * @param  string $method Method name.
	 * @param  array  $args   Method arguments.
	 * @return mixed
	 */
	public function __call( $method, $args ) {
		return call_user_func_array( [ $this->get_request(), $method ], $args );
	}
}
