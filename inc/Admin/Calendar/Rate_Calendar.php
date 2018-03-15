<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Pricing\Base_Rate;
use AweBooking\Reservation\Pricing\Rate_Pricing;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Collection;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Calendar\Provider\Pricing_Provider;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resources;

use AweBooking\Support\Decimal;
use AweBooking\Support\Utils as U;
use AweBooking\Formatting as format;

class Rate_Calendar extends Abstract_Scheduler {
	/**
	 * Display the toolbars.
	 *
	 * @return void
	 */
	protected function display_toolbars() {
		echo '<div class="scheduler-flexspace"></div>';
		awebooking( 'admin_template' )->partial( 'scheduler/toolbar/datepicker.php', [ 'calendar' => $this ] );
	}

	/**
	 * Display the actions.
	 *
	 * @return void
	 */
	protected function display_actions() { ?>
		<ul class="scheduler__actions">
			<li><a href="#" data-schedule-action="set-price"><i class="afc afc-dollar-sign"></i><span><?php echo esc_html__( 'Adjust Price', 'awebooking' ); ?></span></a></li>
			<li><a href="#" data-schedule-action="reset-price"><i class="dashicons dashicons-image-rotate"></i><span><?php echo esc_html__( 'Revert Price', 'awebooking' ); ?></span></a></li>
		</ul>
		<?php
	}

	/**
	 * Display the event column.
	 *
	 * @param  [type] $day           [description]
	 * @param  [type] $loop_calendar [description]
	 * @return [type]
	 */
	protected function display_event_column( $day, $loop_calendar ) {
		$cid = $loop_calendar->get_uid();
		$index = $day->format( 'Y-m-d' );

		// Leave when the price not found.
		if ( ! isset( $this->matrices[ $cid ] ) || ! isset( $this->matrices[ $cid ][ $index ] ) ) {
			return;
		}

		// Get back the rate class instance from calendar resource.
		$rate_unit = U::optional( $loop_calendar->get_resource() )->get_reference();
		if ( is_null( $rate_unit ) ) {
			return;
		}

		// Prepare output amount.
		$base_amount = $rate_unit->get_amount();
		$amount = Decimal::from_raw_value( $this->matrices[ $cid ]->get( $index ) );

		// Build the state class.
		$state_class = '';
		if ( $amount->greater_than( $base_amount ) ) {
			$state_class = 'stateup';
		} elseif ( $amount->less_than( $base_amount ) ) {
			$state_class = 'statedown';
		}

		// @codingStandardsIgnoreLine
		echo '<span class="scheduler__rate-amount ' . $state_class . '">' . format::money( $amount, true ) . '</span>';
	}

	/**
	 * Get the scheduler.
	 *
	 * @return \AweBooking\Calendar\Scheduler
	 */
	protected function create_scheduler() {
		$resources = $this->create_resources();

		$provider = new Cached_Provider( new Pricing_Provider( $resources ) );

		$calendars = Collection::make( $resources )->map(function( $resource ) use ( $provider ) {
			$calendar = new Calendar( $resource, $provider );
			$calendar->set_name( $resource->get_title() );

			return $calendar;
		});

		return Scheduler::make( $calendars );
	}

	/**
	 * Create the calendar resources.
	 *
	 * @return \AweBooking\Calendar\Resource\Resources
	 */
	protected function create_resources() {
		return Resources::make(
			$this->get_room_types()->map(function( $room_type ) {
				$rate = new Base_Rate( $room_type );
				$resource = new Resource( $rate->get_id(), $rate->get_amount()->as_raw_value() );

				$resource->set_title( $room_type->get_title() );
				$resource->set_reference( $rate );

				return $resource;
			})
		);
	}
}
