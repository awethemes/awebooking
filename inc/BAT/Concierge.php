<?php
namespace AweBooking\BAT;

use DateInterval;
use Carbon\Carbon;

use AweBooking\Room;
use AweBooking\Room_State;
use AweBooking\Rate;
use AweBooking\Rate_Pricing;
use AweBooking\Room_Type;
use AweBooking\AweBooking;

use AweBooking\Support\Utils;
use AweBooking\Support\Mail;
use AweBooking\Support\Date_Period;
use AweBooking\Pricing\Price;
use Roomify\Bat\Store\StoreInterface;
use Roomify\Bat\Valuator\IntervalValuator;

use AweBooking\Mails\Booking_Created;

use AweBooking\Interfaces\Price as Price_Interface;
use AweBooking\Interfaces\Concierge as Concierge_Interface;
use AweBooking\Interfaces\Booking_Request as Request_Interface;
use AweBooking\Interfaces\Availability as Availability_Interface;
use Roomify\Bat\Event\Event;

class Concierge implements Concierge_Interface {
	/**
	 * BAT Store instance.
	 *
	 * @var \Roomify\Bat\Store\StoreInterface
	 */
	protected $store;

	/**
	 * Concierge constructor.
	 *
	 * @param StoreInterface $store Store instance.
	 */
	public function __construct( StoreInterface $store ) {
		$this->store = $store;
	}

	/**
	 * Get room price by booking request.
	 *
	 * @param  Room_Type         $room_type Room type instance.
	 * @param  Request_Interface $request   Booking request instance.
	 * @return Price
	 */
	public function get_room_price( Room_Type $room_type, Request_Interface $request ) {
		$valuator = new IntervalValuator(
			$request->get_check_in(),
			$request->get_check_out()->subMinute(),
			$room_type->get_standard_rate(),
			awebooking()->make( 'store.pricing' ),
			new DateInterval( 'P1D' )
		);

		return Price::from_amount(
			$valuator->determineValue()
		);
	}

	/**
	 * Set price for room (by rate).
	 *
	 * @param  Rate            $rate    The rate instance.
	 * @param  Date_Period     $period  Date period instance.
	 * @param  Price_Interface $amount  The price instance.
	 * @param  array           $options Price setting options.
	 * @return boolean
	 */
	public function set_room_price( Rate $rate, Date_Period $period, Price_Interface $amount, array $options = [] ) {
		$rate = new Rate_Pricing( $rate, $period->get_start_date(), $period->get_end_date(), $amount );

		if ( ! empty( $options['only_days'] ) && is_array( $options['only_days'] ) ) {
			$rate->set_only_days( $options['only_days'] );
		}

		return $rate->save();
	}

	/**
	 * Set the room state.
	 *
	 * @param  Room        $room    The Room instance.
	 * @param  Date_Period $period  Date period instance.
	 * @param  integer     $state   Room state, default is Room_State::AVAILABLE.
	 * @param  array       $options Setting options.
	 * @return boolean
	 */
	public function set_room_state( Room $room, Date_Period $period, $state = Room_State::AVAILABLE, array $options = [] ) {
		if ( ! array_key_exists( $state, Utils::get_room_states() ) ) {
			return false;
		}

		$options = wp_parse_args( $options, [
			'force'    => false,
			'only_days' => null,
		]);

		$state = new Room_State( $room, $period->get_start_date(), $period->get_end_date()->subMinute(), $state );

		// Find room state booked, pending.
		$calendar = Factory::create_availability_calendar( [ $room ], Room_State::PENDING );

		if ( ! $options['force'] ) {
			$ignore = [ Room_State::PENDING, Room_State::BOOKED ];

			$response_events = $calendar->getEvents(
				$period->get_start_date(),
				$period->get_end_date()->subMinute()
			);

			if ( isset( $response_events[ $room->get_id() ] ) ) {
				foreach ( $response_events[ $room->get_id() ] as $event ) {
					if ( in_array( $event->getValue(), $ignore ) ) {
						return false;
					}
				}
			}
		}

		if ( ! empty( $options['only_days'] ) && is_array( $options['only_days'] ) ) {
			$state->set_only_days( $options['only_days'] );
		}

		return $state->save();
	}

	/**
	 * Check available.
	 *
	 * @param  Request_Interface $request Booking request instance.
	 * @return array
	 */
	public function check_availability( Request_Interface $request ) {
		$room_types = awebooking( 'store.room_type' )->query_room_types([
			'booking_adults'   => $request->get_adults(),
			'booking_children' => $request->get_children(),
			'booking_nights'   => $request->get_nights(),
			'hotel_location'   => $request->get_request( 'location' ),
		]);

		$room_types = $room_types->posts;

		$room_ids = wp_list_pluck( $room_types, 'ID' );
		$rooms = awebooking( 'store.room' )->list_by_room_type( $room_ids );

		if ( empty( $rooms ) ) {
			return [];
		}

		$rooms = array_map( function( $args ) {
			return new Room( $args['id'] );
		}, $rooms );

		// Temp code, exclude room from request.
		if ( $request->get_request( 'exclude_rooms' ) ) {
			$exclude_rooms = (array) $request->get_request( 'exclude_rooms' );
			$exclude_rooms = array_map( 'absint', $exclude_rooms );

			foreach ( $rooms as $key => $room ) {
				if ( in_array( $room->get_id(), $exclude_rooms ) ) {
					unset( $rooms[ $key ] );
				}
			}
		}

		return $this->check_rooms_available( $rooms, $request );
	}

	/**
	 * Check available a room type.
	 * TODO: ...
	 *
	 * @param  Room_Type         $room_type Room type instance.
	 * @param  Request_Interface $request   Booking request instance.
	 * @return Availability
	 */
	public function check_room_type_availability( Room_Type $room_type, Request_Interface $request ) {
		$found = awebooking( 'store.room_type' )->query_room_types([
			'booking_adults'   => $request->get_adults(),
			'booking_children' => $request->get_children(),
			'booking_nights'   => $request->get_nights(),
			'post__in'         => [ $room_type->get_id() ],
		]);

		$room_type_array = isset( $found->posts[0] ) ? $found->posts[0] : null;
		if ( empty( $room_type_array ) ) {
			return new Availability( $room_type, $request );
		}

		$rooms = $room_type->get_rooms();
		if ( 0 === count( $rooms ) ) {
			return new Availability( $room_type, $request );
		}

		$available = $this->check_rooms_available( $rooms, $request );

		if ( isset( $available[ $room_type->get_id() ] ) ) {
			return $available[ $room_type->get_id() ];
		}

		return new Availability( $room_type, $request );
	}

	/**
	 * Check available for a list of single rooms.
	 *
	 * @param  array             $rooms   List of single rooms.
	 * @param  Request_Interface $request Booking request instance.
	 * @return array
	 */
	public function check_rooms_available( array $rooms, Request_Interface $request ) {
		$calendar = new Calendar( $rooms, $this->store );

		$response = $calendar->getMatchingUnits(
			$request->get_check_in(),
			$request->get_check_out()->subMinute(),
			$request->valid_states(),
			$request->constraints()
		);

		return $this->mapto_room_types( $response, $request );
	}

	/**
	 * Make new booking.
	 *
	 * @param  Availability_Interface $availability //.
	 * @return false|int
	 */
	public function make_booking( Availability_Interface $availability ) {
	}

	/**
	 * Mapping single-room to that room_type.
	 *
	 * @param  CalendarResponse  $response //.
	 * @param  Request_Interface $request //.
	 * @return array
	 */
	protected function mapto_room_types( $response, Request_Interface $request ) {
		$room_type = [];

		foreach ( $response->getIncluded() as $room_id => $accept ) {
			$room_type_id = (int) $accept['unit']->get_room_type()->get_id();

			if ( ! array_key_exists( $room_type_id, $room_type ) ) {
				$room_type[ $room_type_id ] = new Availability( new Room_type( $room_type_id ), $request );
			}

			$room_type[ $room_type_id ]->add_room( $accept['unit'] );
		}

		return $room_type;
	}

	/**
	 * Call limousines car, ahihi.
	 *
	 * @throws \RuntimeException
	 */
	public function call_limousines() {
		throw new \RuntimeException( 'Just kidding, I can\'t do this :)' );
	}
}
