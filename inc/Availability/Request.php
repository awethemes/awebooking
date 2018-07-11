<?php
namespace AweBooking\Availability;

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
	 * The availability constraints.
	 *
	 * @var array
	 */
	protected $constraints = [];

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
	 * @return \AweBooking\Availability\Query_Results
	 */
	public function search() {
		return ( new Query( $this ) )->search();
	}

	/**
	 * Gets the request property.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function get( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->{$property};
		}

		switch ( $property ) {
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
				return $this->guest_counts->has( $property )
					? $this->guest_counts[ $property ]->get_count()
					: null;
		}

		return $this->options->get( $property );
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
	 * Sets the guest count.
	 *
	 * @param  string $age_code The guest age code.
	 * @param  int    $count    The count.
	 * @return $this
	 */
	public function set_guest_count( $age_code, $count = 0 ) {
		$this->guest_counts[ $age_code ] = $count;

		return $this;
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
	 * Gets the constraints.
	 *
	 * @return array
	 */
	public function get_constraints() {
		return $this->constraints;
	}

	/**
	 * Sets the constraints.
	 *
	 * @param  array $constraints Array of constraints.
	 * @return $this
	 */
	public function set_constraints( $constraints ) {
		$this->constraints = $constraints;

		return $this;
	}

	/**
	 * Add one or more constraints.
	 *
	 * @param  array $constraints Array of constraints.
	 * @return $this
	 */
	public function add_contraints( $constraints ) {
		foreach ( (array) $constraints as $constraint ) {
			$this->constraints[] = $constraint;
		}

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
	 * Returns the request hash ID.
	 *
	 * @return string
	 */
	public function get_hash() {
		return sha1( serialize( $this->get_timespan()->to_array() ) );
	}

	/**
	 * Checks if the request is sane with other request.
	 *
	 * @param \AweBooking\Availability\Request $another Another request.
	 * @return bool
	 */
	public function same_with( Request $another ) {
		return hash_equals( $this->get_hash(), $another->get_hash() );
	}

	/**
	 * Convert the request to an array.
	 *
	 * Note: Only the timespan and guest-counts can be convert.
	 *
	 * @return array
	 */
	public function to_array() {
		$arr = [
			'check_in'  => $this->timespan->get_start_date(),
			'check_out' => $this->timespan->get_end_date(),
		];

		$arr['adults'] = $this->guest_counts['adults']->get_count();

		if ( abrs_children_bookable() && isset( $this->guest_counts['children'] ) ) {
			$arr['children'] = $this->guest_counts['children']->get_count();
		}

		if ( abrs_infants_bookable() && isset( $this->guest_counts['infants'] ) ) {
			$arr['infants'] = $this->guest_counts['infants']->get_count();
		}

		return $arr;
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
		return $this->get( $property );
	}
}
