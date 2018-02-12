<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Model\Rate;
use AweBooking\Model\Room_Type;
use AweBooking\Reservation\Reservation;
use AweBooking\Support\Decimal;
use AweBooking\Support\Collection;

class Resolver {
	/**
	 * The reservation.
	 *
	 * @var \AweBooking\Reservation\Reservation
	 */
	protected $reservation;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 */
	public function __construct( Reservation $reservation ) {
		$this->reservation = $reservation;
	}

	/**
	 * Resolve the price amount.
	 *
	 * @param  \AweBooking\Model\Room_Type $room_type The room-type.
	 * @return \AweBooking\Reservation\Pricing\Pricing_Items
	 */
	public function resolve( Room_Type $room_type ) {
		$rates = $this->filter_select_rates( $room_type->get_rates(), $room_type );

		$base_rate = $this->resolve_base_rate( $rates, $room_type );

		return Pricing_Items::make( [ $base_rate ] )->map( function( $rate ) {
			return new Pricing( $rate, $this->reservation->get_stay() );
		});
	}

	/**
	 * [filter_rates description]
	 *
	 * @param  [type] $rates     [description]
	 * @param  [type] $room_type [description]
	 * @return [type]
	 */
	protected function filter_select_rates( $rates, $room_type ) {
		$rates = $rates->reject( function( $rate ) {
			return Rate::GROUP_CUMULATIVE === $rate->get_group();
		});

		return apply_filters( 'awebooking/pricing/filter_rates', $rates, $room_type );
	}

	/**
	 * Resolve the base rate.
	 *
	 * @param  \AweBooking\Support\Collection $rates     List rates.
	 * @param  \AweBooking\Support\Collection $room_type The room-type.
	 * @return \AweBooking\Model\Rate
	 */
	protected function resolve_base_rate( $rates, $room_type ) {
		$base_rate = $rates->first( function( $rate ) {
			return $rate->is_standard_rate();
		});

		return apply_filters( 'awebooking/pricing/resolve_base_rate', $base_rate, $rates, $room_type );
	}
}
