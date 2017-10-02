<?php
namespace AweBooking\Booking;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Support\Period;
use AweBooking\Support\Collection;

class Request extends Collection {
	/**
	 * Date Period instance.
	 *
	 * @var Period
	 */
	protected $period;

	/**
	 * Booking requests.
	 *
	 * @var array
	 */
	protected $defaults = [
		'adults'   => 1,
		'children' => 0,
	];

	/**
	 * Create request from array data.
	 *
	 * @param  array $request Request data.
	 * @return static
	 */
	public static function from_array( array $request ) {
		$period = new Period( $request['check_in'], $request['check_out'], true );

		return new static( $period, $request );
	}

	/**
	 * Booking request constructor.
	 *
	 * @param Period $period   Period days.
	 * @param array  $requests An array of booking requests.
	 */
	public function __construct( Period $period, array $requests = [] ) {
		$this->period = $period;

		parent::__construct(
			array_merge( $this->defaults, $requests )
		);
	}

	/**
	 * Gets the date period for the booking.
	 *
	 * @return Period
	 */
	public function get_period() {
		return $this->period;
	}

	/**
	 * Set the request period.
	 *
	 * @param  Period $period Period instance.
	 * @return $this
	 */
	public function set_period( Period $period ) {
		$this->period = $period;

		return $this;
	}

	/**
	 * Gets the request adults for the booking.
	 *
	 * @return int
	 */
	public function get_adults() {
		return $this->get( 'adults' );
	}

	/**
	 * Set the request adults for the booking.
	 *
	 * @param  int $adults Number adults for the booking.
	 * @return $this
	 */
	public function set_adults( $adults ) {
		$this['adults'] = max( 1, absint( $adults ) );

		return $this;
	}

	/**
	 * Gets the number of children for the booking.
	 *
	 * @return int
	 */
	public function get_children() {
		return $this->get( 'children' );
	}

	/**
	 * Set the request children for the booking.
	 *
	 * @param  int $children Number children for the booking.
	 * @return $this
	 */
	public function set_children( $children ) {
		$this['children'] = absint( $children );

		return $this;
	}

	/**
	 * Get number of people (adults + children).
	 *
	 * @return int
	 */
	public function get_people() {
		return $this->get_adults() + $this->get_children();
	}

	/**
	 * Returns the Carbonate of check-in.
	 *
	 * @return Carbonate
	 */
	public function get_check_in() {
		return $this->period->get_start_date();
	}

	/**
	 * Returns the Carbonate of check-out.
	 *
	 * @return Carbonate
	 */
	public function get_check_out() {
		return $this->period->get_end_date();
	}

	/**
	 * Gets the stayed nights.
	 *
	 * @return int
	 */
	public function get_nights() {
		return $this->period->nights();
	}

	/**
	 * Get the request of items as a plain array.
	 *
	 * Overwrite: parent::toArray()
	 *
	 * @return array
	 */
	public function toArray() {
		return array_merge( parent::toArray(), [
			'check_in'  => $this->get_check_in()->toDateString(),
			'check_out' => $this->get_check_out()->toDateString(),
		]);
	}

	/**
	 * Get booking request.
	 *
	 * TODO: Remove this.
	 *
	 * @param  string $key Booking request key name.
	 * @return mixed
	 */
	public function get_request( $key ) {
		$request = $this->get( $key );

		if ( 'extra_services' === $key ) {
			if ( ! $this->has( 'room-type' ) ) {
				return [];
			}

			$room_type = Factory::get_room_type( $this->get( 'room-type' ) );
			if ( ! $room_type || ! $room_type->exists() ) {
				return [];
			}

			$services = (array) $this->get( 'extra_services', [] );
			return array_filter( $services, function( $service_id ) use ( $room_type ) {
				return in_array( $service_id, $room_type['service_ids'] );
			});
		}

		return $request;
	}

	/**
	 * If has a booking request.
	 *
	 * TODO: Remove this.
	 *
	 * @param  string $request Booking request.
	 * @return bool
	 */
	public function has_request( $request ) {
		return $this->has( $request );
	}

	/**
	 * Set a booking request.
	 *
	 * TODO: Remove this.
	 *
	 * @param  string $key   Booking request key.
	 * @param  string $value Booking request value.
	 * @return $this
	 */
	public function set_request( $key, $value ) {
		return $this->put( $key, $value );
	}

	/**
	 * Get request services.
	 *
	 * TODO: Remove this.
	 *
	 * @return array
	 */
	public function get_services() {
		if ( ! $this->has( 'extra_services' ) ) {
			return [];
		}

		$services = [];
		foreach ( (array) $this->get( 'extra_services' ) as $service_id => $quantity ) {
			if ( is_int( $service_id ) ) {
				$service_id = $quantity;
				$quantity = 1;
			}

			$services[ $service_id ] = $quantity ? absint( $quantity ) : 1;
		}

		return $services;
	}

	/**
	 * Gets formatted guest number HTML.
	 *
	 * TODO: Remove this.
	 *
	 * @param  boolean $echo Echo or return output.
	 * @return string|void
	 */
	public function get_fomatted_guest_number( $echo = true ) {
		$html = '';

		$html .= sprintf(
			'<span class="">%1$d %2$s</span>',
			$this->get_adults(),
			_n( 'adult', 'adults', $this->get_adults(), 'awebooking' )
		);

		if ( $this->get_children() ) {
			$html .= sprintf(
				' &amp; <span class="">%1$d %2$s</span>',
				$this->get_children(),
				_n( 'child', 'children', $this->get_children(), 'awebooking' )
			);
		}

		if ( $echo ) {
			print $html; // WPCS: XSS OK.
		} else {
			return $html;
		}
	}
}
