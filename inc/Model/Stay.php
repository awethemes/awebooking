<?php
namespace AweBooking\Model;

use LogicException;
use AweBooking\Support\Carbonate;
use AweBooking\Calendar\Period\Period;
use AweBooking\Support\Contracts\Stringable;

class Stay implements Stringable {
	/**
	 * Get the check-in date.
	 *
	 * @var Carbonate
	 */
	protected $check_in;

	/**
	 * Get the check-out date.
	 *
	 * @var Carbonate
	 */
	protected $check_out;

	/**
	 * Create the stay.
	 *
	 * @param string|Carbonate $check_in  The check-in date point.
	 * @param string|Carbonate $check_out The check-out date point.
	 *
	 * @throws LogicException
	 */
	public function __construct( $check_in, $check_out ) {
		$check_in  = Carbonate::create_datetime( $check_in );
		$check_out = Carbonate::create_datetime( $check_out );

		if ( $check_in > $check_out ) {
			throw new LogicException( esc_html__( 'The check-in datepoint must be greater or equal to the check-out datepoint', 'awebooking' ) );
		}

		$this->check_in  = $check_in;
		$this->check_out = $check_out;
	}

	/**
	 * Returns the check-in date point.
	 *
	 * @return Carbonate
	 */
	public function get_check_in() {
		return $this->check_in->copy();
	}

	/**
	 * Returns the check-out datepoint.
	 *
	 * @return Carbonate
	 */
	public function get_check_out() {
		return $this->check_out->copy();
	}

	/**
	 * Get nights stayed.
	 *
	 * @return int
	 */
	public function nights() {
		return (int) $this->get_period()->getDateInterval()->format( '%r%a' );
	}

	/**
	 * Get the Period object instance.
	 *
	 * @return \AweBooking\Calendar\Period\Period
	 */
	public function get_period() {
		return new Period( $this->check_in, $this->check_out );
	}

	/**
	 * The magic __toString method.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->as_string();
	}

	/**
	 * Return human readable of the request.
	 *
	 * @return string
	 */
	public function as_string() {
		$nights = $this->nights();

		return sprintf( '<strong>%1$d %2$s</strong> <br> <span>%3$s</span> - <span>%4$s</span>',
			esc_html( $nights ),
			esc_html( _n( 'night', 'nights', $nights, 'awebooking' ) ),
			esc_html( $this->check_in->to_wp_date_string() ),
			esc_html( $this->check_out->to_wp_date_string() )
		);
	}

	/**
	 * Validate period for require minimum night(s).
	 *
	 * @param  integer $nights Minimum night(s) to required, default 1.
	 * @return void
	 *
	 * @throws \LogicException
	 */
	public function require_minimum_nights( $nights = 1 ) {
		if ( $this->nights() < $nights ) {
			/* translators: %d: Number of nights */
			throw new \LogicException( sprintf( esc_html__( 'The date period must be have minimum %d night(s).', 'awebooking' ), esc_html( $nights ) ) );
		}
	}

	/**
	 * Validate the period in strict.
	 *
	 * @param  bool $strict Strict mode validation past date.
	 * @return void
	 *
	 * @throws \RangeException
	 */
	protected static function validate_period( $strict ) {
		if ( $strict && $this->isBefore( Carbonate::today() ) ) {
			throw new \RangeException( esc_html__( 'The date period must be greater or equal to the today.', 'awebooking' ) );
		}
	}
}
