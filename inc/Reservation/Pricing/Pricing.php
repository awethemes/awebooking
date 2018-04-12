<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Model\Pricing\Rate;
use AweBooking\Model\Common\Timespan;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Provider\Core\Pricing_Provider;
use AweBooking\Calendar\Event\Core\Pricing_Event;

class Pricing {
	/**
	 * Get the price of rate in a timespan.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate    $rate     The rate.
	 * @param  \AweBooking\Model\Common\Timespan $timespan The timespan.
	 * @return array
	 */
	public function get( Rate $rate, Timespan $timespan ) {
		$resource = new Resource( $rate->get_id(), $rate->get_rack_rate()->as_raw_value() );

		$provider = apply_filters( 'awebooking/pricing/pricing_provider',
			new Pricing_Provider( $resource ), $resource
		);

		// Get the events itemized.
		$itemized = ( new Calendar( $resource, $provider ) )
			->get_events( $timespan->to_period() )
			->itemize();

		// Calcuate price & breakdown.
		$price = abrs_decimal_raw( $itemized->sum() );

		$breakdown = Breakdown::make( $itemized )
			->transform( function( $amount, $key ) {
				return new Night( $key, abrs_decimal_raw( $amount ) );
			});

		return [ $price, $breakdown ];
	}

	/**
	 * Apply a custom price of a rate in a timespan.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate    $rate      The rate.
	 * @param  \AweBooking\Model\Common\Timespan $timespan  The timespan.
	 * @param  float|int                         $amount    The custom amount.
	 * @param  string                            $operation The operation to apply price, default: 'replace'. @see Pricing_Event::apply_operation().
	 * @param  array|null                        $only_days Optional, apply custom price only on special days.
	 * @return bool
	 */
	public function set( Rate $rate, Timespan $timespan, $amount, $operation = 'replace', $only_days = null ) {
		// Create the calendar.
		$resource = new Resource( $rate->get_id(), $rate->get_rack_rate()->as_raw_value() );

		$provider = apply_filters( 'awebooking/pricing/pricing_provider',
			new Pricing_Provider( $resource ), $resource
		);

		$calendar = new Calendar( $resource, $provider );

		// In case replace the price, just create an events
		// in period of start_date and end_date. Otherwise we
		// need get all events of period to perform each pieces.
		if ( 'replace' === $operation ) {
			$events = [ new Pricing_Event( $resource, $timespan->get_start_date(), $timespan->get_end_date() ) ];
		} else {
			$events = $calendar->get_events( $timespan->to_period() );
		}

		// Loop over events and apply price operation.
		foreach ( $events as $event ) {
			$event->only_days( $only_days );

			$event->apply_operation( $amount, $operation );

			// Store the piece of event.
			$calendar->store( $event );
		}

		return true;
	}
}
