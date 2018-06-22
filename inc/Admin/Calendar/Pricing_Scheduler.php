<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Model\Pricing\Base_Rate;

class Pricing_Scheduler extends Abstract_Scheduler {
	/**
	 * Cache results of room types.
	 *
	 * @var string
	 */
	protected $room_types;

	/**
	 * {@inheritdoc}
	 */
	protected function create_scheduler() {
		$this->room_types = $this->query_room_types();

		// Pluck the base rate in each room-type.
		$rates = $this->room_types
			->map_into( Base_Rate::class );

		return $this->create_scheduler_for(
			$resources = $this->create_rate_resources( $rates ),
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
		echo '<span class="tippy" style="background-color: #1565c0;" title="' . esc_html__( 'Modified Higher', 'awebooking' ) . '"></span>';
		echo '<span class="tippy" style="background-color: #d40e00;" title="' . esc_html__( 'Modified Lower', 'awebooking' ) . '"></span>';
	}

	/**
	 * Display the toolbars.
	 *
	 * @return void
	 */
	protected function display_toolbars() {
		echo '<div class="scheduler-flexspace"></div>';
		$this->template( 'toolbar/datepicker.php' );
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
	 * @param  \AweBooking\Calendar\Period\Day $day           The day.
	 * @param  \AweBooking\Calendar\Calendar   $loop_calendar The current calendar.
	 * @return  void
	 */
	protected function display_day_column( $day, $loop_calendar ) {
		$indexed = $day->format( 'Y-m-d' );

		$matrix = $this->get_matrix( $loop_calendar->get_uid() );
		if ( is_null( $matrix ) ) {
			return;
		}

		// Get back the rate class instance from calendar resource.
		$rate_unit = abrs_optional( $loop_calendar->get_resource() )->get_reference();
		if ( is_null( $rate_unit ) ) {
			return;
		}

		// Prepare output amount.
		$base_amount = $rate_unit->get_rack_rate();
		$amount = abrs_decimal_raw( $matrix->get( $indexed ) );

		// Build the state class.
		$state_class = '';
		if ( $amount->greater_than( $base_amount ) ) {
			$state_class = 'stateup';
		} elseif ( $amount->less_than( $base_amount ) ) {
			$state_class = 'statedown';
		}

		// @codingStandardsIgnoreLine
		echo '<span class="scheduler__rate-amount ' . $state_class . '">' . abrs_format_price( $amount ) . '</span>';
	}
}
