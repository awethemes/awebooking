<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Ruler\Rule;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Model\Common\Timespan;
use AweBooking\Reservation\Request;
use AweBooking\Support\Collection;
use AweBooking\Support\Utils as U;

use AweBooking\Calendar\Finder\Finder;
use AweBooking\Calendar\Finder\Response;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Calendar\Provider\Core\Pricing_Provider;

class Selector {
	/**
	 * Perform the select room-rate by given.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate_Plan|null $rate_plan The rate-plan.
	 * @param  \AweBooking\Model\Room_Type              $room_type The room-type.
	 * @return \AweBooking\Reservation\Pricing\Room_Rate|null
	 */
	public function select( Rate_Plan $rate_plan, Room_Type $room_type, Timespan $timespan, $constraints = [] ) {
		$room_rate = new Room_Rate( $room_type, $timespan );

		// Perform filter rates.
		$rates = $room_type->get_rates( $rate_plan );
		$response = $this->perform_filter_rates( $rates, $timespan, $constraints );

		$passed_rates = Collection::make( $response->get_included() )
			->transform( function ( $matching ) {
				$matching['rate'] = $matching['resource']->get_reference();

				unset( $matching['resource'] );

				return $matching;
			})
			->sortBy( function ( $matching ) {
				return $matching['rate']->get_priority();
			});

		if ( $passed_rates->isNotEmpty() ) {
			$passed_rates = $passed_rates->first();
			$room_rate->select( $passed_rates['rate'] );
		}

		return $room_rate;
	}

	/**
	 * Perform filter rates with constraints.
	 *
	 * @param  mixed                             $rates       The rates.
	 * @param  \AweBooking\Model\Common\Timespan $timespan    The timespan.
	 * @param  array                             $constraints The constraints.
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	protected function perform_filter_rates( $rates, Timespan $timespan, $constraints ) {
		// Transform the resources.
		$resources = Collection::make( $rates )
			->keyBy( function( $rate ) {
				return $rate->get_id();
			})
			->transform( function( $rate ) {
				$resource = new Resource( $rate->get_id(), $rate->get_base_amount()->as_raw_value() );

				$resource->set_reference( $rate );
				$resource->set_title( $rate->get_name() );

				return $resource;
			});

		// New calendar response.
		$response = new Response( $timespan->to_period(), $resources );

		foreach ( $resources as $resource ) {
			$response->add_match( $resource, 'valid' );

			// Applly the resource constraints.
			// $response->apply_constraints( $resource->get_constraints() );
		}

		// Apply the global constraints.
		$response->apply_constraints( $constraints );

		return $response;
	}
}
