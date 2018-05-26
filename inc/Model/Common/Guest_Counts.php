<?php
namespace AweBooking\Model\Common;

class Guest_Counts implements \ArrayAccess, \JsonSerializable {
	/**
	 * The listed guests.
	 *
	 * @var array
	 */
	protected $guest_counts = [];

	/**
	 * Constructor.
	 *
	 * @param int $adults   The count of adults.
	 * @param int $children The count of children.
	 * @param int $infants  The count of infants.
	 */
	public function __construct( $adults, $children = 0, $infants = 0 ) {
		$this->set_adults( $adults );

		if ( is_int( $children ) && $children > 0 ) {
			$this->set_children( $children );
		}

		if ( is_int( $infants ) && $infants > 0 ) {
			$this->set_infants( $infants );
		}
	}

	/**
	 * Determines whether the given age_code exists.
	 *
	 * @param  string $age_code The age code name.
	 * @return bool
	 */
	public function has( $age_code ) {
		return array_key_exists( $age_code, $this->guest_counts );
	}

	/**
	 * Get the Guest_Count instance by given a age code.
	 *
	 * @param  string $age_code The age code name.
	 * @return \AweBooking\Model\Common\Guest_Count|null
	 */
	public function get( $age_code ) {
		return array_key_exists( $age_code, $this->guest_counts )
			? $this->guest_counts[ $age_code ]
			: null;
	}

	/**
	 * Add a guest count.
	 *
	 * @param  \AweBooking\Model\Common\Guest_Count|string $guest_count The guest to add.
	 * @param  int                                         $count       Optional, the count.
	 * @return $this
	 */
	public function add( $guest_count, $count = 0 ) {
		if ( ! $guest_count instanceof Guest_Count ) {
			$guest_count = new Guest_Count( $guest_count, $count );
		}

		$this->guest_counts[ $guest_count->get_age_code() ] = $guest_count;

		return $this;
	}

	/**
	 * Gets total guest counts.
	 *
	 * @return int
	 */
	public function get_totals() {
		return array_reduce( $this->guest_counts, function( $total, Guest_Count $guest_count ) {
			return $total + $guest_count->get_count();
		}, 0 );
	}

	/**
	 * Get the adults count.
	 *
	 * @return \AweBooking\Model\Common\Guest_Count
	 */
	public function get_adults() {
		return $this->get( 'adults' );
	}

	/**
	 * Set the adults count.
	 *
	 * @param  int $count The adults count.
	 * @return $this
	 */
	public function set_adults( $count ) {
		$this->offsetSet( 'adults', max( 1, (int) $count ) );

		return $this;
	}

	/**
	 * Get the children count.
	 *
	 * @return \AweBooking\Model\Common\Guest_Count|null
	 */
	public function get_children() {
		return $this->get( 'children' );
	}

	/**
	 * Set the children count.
	 *
	 * @param  int $count The children count.
	 * @return $this
	 */
	public function set_children( $count ) {
		$this->offsetSet( 'children', absint( $count ) );

		return $this;
	}

	/**
	 * Get the infants count.
	 *
	 * @return \AweBooking\Model\Common\Guest_Count|null
	 */
	public function get_infants() {
		return $this->get( 'infants' );
	}

	/**
	 * Set the infants count.
	 *
	 * @param  int $count The infants count.
	 * @return $this
	 */
	public function set_infants( $count ) {
		$this->offsetSet( 'infants', absint( $count ) );

		return $this;
	}

	/**
	 * Convert the guests to an array.
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
		return $this->has( $offset );
	}

	/**
	 * Fetch the offset.
	 *
	 * @param  string $offset The offset name.
	 * @return \AweBooking\Model\Common\Guest_Count|null
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * Assign the offset.
	 *
	 * @param  string $offset The offset name.
	 * @param  mixed  $value  The offset value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		if ( array_key_exists( $offset, $this->guest_counts ) ) {
			$this->get( $offset )->set_count( $value );
		} else {
			$this->add( $offset, $value );
		}
	}

	/**
	 * Unset the offset.
	 *
	 * @param  mixed $offset The offset name.
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		// ...
	}

	/**
	 * {@inheritdoc}
	 */
	public function as_string() {
		$html = sprintf(
			'<span class="awebooking_guest__adults">%1$d %2$s</span>',
			esc_html( $this->get_adults()->get_count() ),
			esc_html( _n( 'adult', 'adults', $this->get_adults()->get_count(), 'awebooking' ) )
		);

		if ( $children = $this->get_children() ) {
			$html .= sprintf(
				' , <span class="awebooking_guest__children">%1$d %2$s</span>',
				esc_html( $children->get_count() ),
				esc_html( _n( 'child', 'children', $children->get_count(), 'awebooking' ) )
			);
		}

		if ( $infants = $this->get_infants() ) {
			$html .= sprintf(
				' &amp; <span class="awebooking_guest__infants">%1$d %2$s</span>',
				esc_html( $infants->get_count() ),
				esc_html( _n( 'infant', 'infants', $infants->get_count(), 'awebooking' ) )
			);
		}

		return apply_filters( 'awebooking/html_guest_counts', $html, $this );
	}
}
