<?php
namespace AweBooking\Model;

use AweBooking\Support\Contracts\Stringable;

class Guest implements Stringable {
	/**
	 * The number of adults in party.
	 *
	 * @var int
	 */
	protected $adults = 1;

	/**
	 * The number of children in party.
	 *
	 * @var int
	 */
	protected $children = 0;

	/**
	 * The number of infants in party.
	 *
	 * @var int
	 */
	protected $infants = 0;

	/**
	 * Constructor.
	 *
	 * @param int $adults   The number of adults.
	 * @param int $children The number of children.
	 * @param int $infants  The number of infants.
	 */
	public function __construct( $adults, $children = 0, $infants = 0 ) {
		$this->set_adults( $adults );

		$this->set_children( $children );

		$this->set_infants( $infants );
	}

	/**
	 * Get number of adults.
	 *
	 * @return int
	 */
	public function get_adults() {
		return $this->adults;
	}

	/**
	 * Set the adults for the party.
	 *
	 * @param int $adults The number of adults.
	 */
	public function set_adults( $adults ) {
		$this->adults = $this->requires_minimum( $adults, 1 );

		return $this;
	}

	/**
	 * Get number of children.
	 *
	 * @return int
	 */
	public function get_children() {
		return $this->children;
	}

	/**
	 * Set the children for the party.
	 *
	 * @param int $children The number of children.
	 */
	public function set_children( $children ) {
		$this->children = $this->requires_minimum( $children, 0 );

		return $this;
	}

	/**
	 * Get number of infants.
	 *
	 * @return int
	 */
	public function get_infants() {
		return $this->infants;
	}

	/**
	 * Set the infants for the party.
	 *
	 * @param int $infants The number of infants.
	 */
	public function set_infants( $infants ) {
		$this->infants = $this->requires_minimum( $infants, 0 );

		return $this;
	}

	/**
	 * Get the total number guest (include infants).
	 *
	 * @return int
	 */
	public function total() {
		return ( $this->adults + $this->children + $this->infants );
	}

	/**
	 * Get the total number guest without infants.
	 *
	 * @return int
	 */
	public function total_without_infants() {
		return ( $this->adults + $this->children );
	}

	/**
	 * Magic output this class as string.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->as_string();
	}

	/**
	 * {@inheritdoc}
	 */
	public function as_string() {
		$adults = $this->get_adults();

		$html = sprintf(
			'<span class="awebooking_guest__adults">%1$d %2$s</span>',
			esc_html( $adults ),
			esc_html( _n( 'adult', 'adults', $this->get_adults(), 'awebooking' ) )
		);

		if ( $children = $this->get_children() ) {
			$html .= sprintf(
				' , <span class="awebooking_guest__children">%1$d %2$s</span>',
				esc_html( $children ),
				esc_html( _n( 'child', 'children', $children, 'awebooking' ) )
			);
		}

		if ( $infants = $this->get_infants() ) {
			$html .= sprintf(
				' &amp; <span class="awebooking_guest__infants">%1$d %2$s</span>',
				esc_html( $infants ),
				esc_html( _n( 'infant', 'infants', $infants, 'awebooking' ) )
			);
		}

		return $html;
	}

	/**
	 * Check to requires at least a $minimum number.
	 *
	 * @param  mixed   $value   The value.
	 * @param  integer $minimum The minium.
	 * @return int
	 */
	protected function requires_minimum( $value, $minimum = 0 ) {
		return absint( $value < $minimum ? $minium : $value );
	}
}
