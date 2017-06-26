<?php
namespace AweBooking\BAT;

use AweBooking\Room;
use AweBooking\Room_Type;
use AweBooking\AweBooking;
use AweBooking\Interfaces\Booking_Request as Request_Interface;
use AweBooking\Interfaces\Availability as Availability_Interface;
use AweBooking\Service;

use AweBooking\Pricing\Price;
use AweBooking\Pricing\Price_Calculator;
use AweBooking\Pricing\Calculator\Service_Calculator;

class Availability implements Availability_Interface {
	/**
	 * Room type instance.
	 *
	 * @var Room_Type
	 */
	protected $room_type;

	/**
	 * Bookign request instance.
	 *
	 * @var Request_Interface
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
	 * @param Request_Interface $request   //.
	 */
	public function __construct( Room_Type $room_type, Request_Interface $request ) {
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

	/**
	 * Get request instance.
	 *
	 * @return Request_Interface
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Is room-type available for booking.
	 *
	 * @return boolean
	 */
	public function available() {
		return $this->total_available_rooms() > 0;
	}

	/**
	 * Is room-type unavailable.
	 *
	 * @return boolean
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

	/**
	 * Get price.
	 *
	 * @return Price
	 */
	public function get_price() {
		return awebooking( 'concierge' )
			->get_room_price( $this->room_type, $this->request );
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
		$pipes = [];

		if ( $this->request->has_request( 'extra_services' ) ) {
			$this->through_services( $pipes );
		}

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

	/**
	 * //
	 *
	 * @return array
	 */
	public function to_array() {
		return [
			'check_in'        => $this->get_request()->get_check_in()->toDateString(),
			'check_out'       => $this->get_request()->get_check_out()->toDateString(),
			'nights'          => $this->get_request()->get_nights(),
			'price'           => (string) $this->get_price(),
			'total_price'     => (string) $this->get_total_price(),
			'available_rooms' => $this->get_available_rooms(),
			'room_type'       => $this->get_room_type()->to_array(),
			'rooms'           => array_values( (array) $this->get_rooms() ),
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
			$pipes[] = new Service_Calculator( $extra_service, $this->get_request() );
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
}
