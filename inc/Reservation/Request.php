<?php
namespace AweBooking\Reservation;

use AweBooking\Support\Fluent;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;

class Request implements \ArrayAccess, \JsonSerializable {
	/**
	 * The Timespan instance.
	 *
	 * @var \AweBooking\Model\Common\Timespan
	 */
	protected $timespan;

	/**
	 * The Guest_Counts instance.
	 *
	 * @var \AweBooking\Model\Common\Guest_Counts|null
	 */
	protected $guest_counts;

	/**
	 * The request options.
	 *
	 * @var \AweBooking\Support\Fluent
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Common\Timespan          $timespan     The request timespan.
	 * @param \AweBooking\Model\Common\Guest_Counts|null $guest_counts The request guests.
	 * @param array                                      $options      Optional, the request options.
	 */
	public function __construct( Timespan $timespan, Guest_Counts $guest_counts, $options = [] ) {
		$this->timespan     = $timespan;
		$this->guest_counts = $guest_counts;
		$this->options      = new Fluent( $options );
	}

	/**
	 * Search the available rooms and rates.
	 *
	 * @param  array $constraints The search constraints.
	 * @return \AweBooking\Reservation\Search\Results
	 */
	public function search( $constraints = [] ) {
		return ( new Room_Stay\Search( $this ) )->get( $constraints );
	}

	/**
	 * Gets the Timespan.
	 *
	 * @return \AweBooking\Model\Common\Timespan
	 */
	public function get_timespan() {
		return $this->timespan;
	}

	/**
	 * Sets the Timespan.
	 *
	 * @param  \AweBooking\Model\Common\Timespan $timespan The timespan.
	 * @return $this
	 */
	public function set_timespan( Timespan $timespan ) {
		$this->timespan = $timespan;

		return $this;
	}

	/**
	 * Gets the Guest_Counts.
	 *
	 * @return \AweBooking\Model\Common\Guest_Counts
	 */
	public function get_guest_counts() {
		return $this->guest_counts;
	}

	/**
	 * Sets the Guest_Counts.
	 *
	 * @param  \AweBooking\Model\Common\Guest_Counts $guest_counts The guest_counts.
	 * @return $this
	 */
	public function set_guest_counts( Guest_Counts $guest_counts ) {
		$this->guest_counts = $guest_counts;

		return $this;
	}

	/**
	 * Gets the request options.
	 *
	 * @return \AweBooking\Support\Fluent
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Sets the request options.
	 *
	 * @param \AweBooking\Support\Fluent|array $options The options.
	 * @return self
	 */
	public function set_options( $options ) {
		$this->options = new Fluent( $options );

		return $this;
	}

	/**
	 * Convert the timespan to an array.
	 *
	 * @return array
	 */
	public function to_array() {
		return [ /* TODO */ ];
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->to_array();
	}

	/**
	 * Whether the given offset exists.
	 *
	 * @param  string $offset The offset name.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return $this->__isset( $offset );
	}

	/**
	 * Fetch the offset.
	 *
	 * @param  string $offset The offset name.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->__get( $offset );
	}

	/**
	 * Assign the offset.
	 *
	 * @param  string $offset The offset name.
	 * @param  mixed  $value  The offset value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->options[ $offset ] = $value;
	}

	/**
	 * Unset the offset.
	 *
	 * @param  mixed $offset The offset name.
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		unset( $this->options[ $offset ] );
	}

	/**
	 * Magic isset method.
	 *
	 * @param  string $property The property name.
	 * @return bool
	 */
	public function __isset( $property ) {
		return null !== $this->__get( $property );
	}

	/**
	 * Magic getter method.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		switch ( $property ) {
			case 'options':
			case 'timespan':
			case 'guest_counts':
				return $this->{$property};
			case 'nights':
				return $this->timespan->nights();
			case 'check_in':
			case 'start_date':
				return $this->timespan->get_start_date();
			case 'check_out':
			case 'end_date':
				return $this->timespan->get_end_date();
			case 'adults':
			case 'children':
			case 'infants':
				return abrs_optional( $this->guest_counts->get( $property ) )->get_count();
		}

		return $this->options->get( $property );
	}
}
