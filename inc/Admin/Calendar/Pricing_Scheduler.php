<?php

namespace AweBooking\Admin\Calendar;

use AweBooking\Calendar\Resource\Resource;
use AweBooking\Model\Pricing\Standard_Rate_Interval;

class Pricing_Scheduler extends Abstract_Scheduler {
	/**
	 * {@inheritdoc}
	 */
	protected function create_scheduler() {
		$this->query_room_types();

		// Pluck the base rate in each room-type.
		$rates = $this->room_types
			->map_into( Standard_Rate_Interval::class );

		$resources = $this->create_rate_resources( $rates )
			->each( function( Resource $resource ) {
				$resource->set_title( get_the_title( $resource->get_id() ) );
			});

		return $this->create_scheduler_for( $resources,
			abrs_calendar_provider( 'pricing', $resources, true )
		);
	}

	/**
	 * Display the legends.
	 *
	 * @return void
	 */
	protected function display_legends() {
		echo '<span class="tippy" title="' . esc_html__( 'Not Modified', 'awebooking' ) . '"></span>';
		echo '<span class="tippy" style="background: #1565c0;" title="' . esc_html__( 'Modified Higher', 'awebooking' ) . '"></span>';
		echo '<span class="tippy" style="background: #d40e00;" title="' . esc_html__( 'Modified Lower', 'awebooking' ) . '"></span>';
	}

	/**
	 * Display the actions.
	 *
	 * @return void
	 */
	protected function display_actions() {
		?>
		<li><a href="#" data-schedule-action="set-price"><i class="aficon aficon-logo-usd"></i><span><?php echo esc_html__( 'Adjust Price', 'awebooking' ); ?></span></a></li>
		<li><a href="#" data-schedule-action="reset-price"><i class="dashicons dashicons-image-rotate"></i><span><?php echo esc_html__( 'Revert Price', 'awebooking' ); ?></span></a></li>
		<?php
	}

	/**
	 * Display the event column.
	 *
	 * @param  \AweBooking\Calendar\Period\Day     $day       The current day.
	 * @param  \AweBooking\Calendar\Calendar       $calendar  The current loop calendar.
	 * @param  \AweBooking\Calendar\Scheduler|null $scheduler The current loop scheduler.
	 * @return  void
	 */
	protected function display_day_column( $day, $calendar, $scheduler ) {
		$itemized = $this->get_matrix( $calendar->get_uid() );

		if ( is_null( $itemized ) ) {
			return;
		}

		echo $this->retrieve_rate_amount( $itemized, $day, $calendar ); // WPCS: XSS OK.
	}

	/**
	 * Retrieve the rate amount as HTML.
	 *
	 * @param  \AweBooking\Support\Collection  $itemized The pricing as itemized.
	 * @param  \AweBooking\Calendar\Period\Day $day      The day.
	 * @param  \AweBooking\Calendar\Calendar   $calendar The current calendar.
	 *
	 * @return string
	 */
	protected function retrieve_rate_amount( $itemized, $day, $calendar ) {
		// Get back the rate class instance from calendar resource.
		/* @var \AweBooking\Model\Pricing\Contracts\Rate_Interval $rate_unit */
		$rate_unit = abrs_optional( $calendar->get_resource() )->get_reference();

		if ( is_null( $rate_unit ) ) {
			return '';
		}

		$rack_amount = abrs_decimal( $rate_unit->get_rack_rate() );
		$amount      = abrs_decimal_raw( $itemized->get( $day->format( 'Y-m-d' ) ) );

		// Build the state class.
		$state_class = '';
		if ( $amount->greater_than( $rack_amount ) ) {
			$state_class = 'stateup';
		} elseif ( $amount->less_than( $rack_amount ) ) {
			$state_class = 'statedown';
		}

		return '<span class="scheduler__rate-amount ' . $state_class . '">' . abrs_format_price( $amount ) . '</span>';
	}
}
