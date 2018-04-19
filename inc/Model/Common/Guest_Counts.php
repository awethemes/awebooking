<?php
namespace AweBooking\Model\Common;

class Guest_Counts {
	/**
	 * The listed guest count.
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
	 * Get the Guest_Count instance by given a age code.
	 *
	 * @param  string $age_code The age code.
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
	 * @param  \AweBooking\Model\Common\Guest_Count $guest_count The guest count to add.
	 * @return $this
	 */
	public function add( Guest_Count $guest_count ) {
		$this->guest_counts[ $guest_count->get_age_code() ] = $guest_count;

		return $this;
	}

	/**
	 * Gets total guest counts.
	 *
	 * @return int
	 */
	public function get_totals() {
		return array_reduce( $this->guest_counts, function( $total, $guest_count ) {
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
		$count = max( 1, (int) $count );

		if ( isset( $this->guest_counts['adults'] ) ) {
			$this->get( 'adults' )->set_count( $count );
		} else {
			$this->add( new Guest_Count( 'adults', $count ) );
		}

		return $this;
	}

	/**
	 * Get the children count.
	 *
	 * @return \AweBooking\Model\Common\Guest_Count
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
		$count = absint( $count );

		if ( isset( $this->guest_counts['children'] ) ) {
			$this->get( 'children' )->set_count( $count );
		} else {
			$this->add( new Guest_Count( 'children', $count ) );
		}

		return $this;
	}

	/**
	 * Get the infants count.
	 *
	 * @return \AweBooking\Model\Common\Guest_Count
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
		$count = absint( $count );

		if ( isset( $this->guest_counts['infants'] ) ) {
			$this->get( 'infants' )->set_count( $count );
		} else {
			$this->add( new Guest_Count( 'infants', $count ) );
		}

		return $this;
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
