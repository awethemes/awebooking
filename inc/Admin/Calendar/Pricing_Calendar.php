<?php

namespace AweBooking\Admin\Calendar;

use Carbon\Carbon;
use AweBooking\Room_Type;
use AweBooking\Pricing\Price;
use AweBooking\Support\Formatting;
use \AweBooking\BAT\Calendar;

class Pricing_Calendar {

	protected $year = 2017;

	/**
	 * The room-type instance.
	 *
	 * @var Room_Type
	 */
	protected $room_type;

	/**
	 * List months of year.
	 *
	 * @var array
	 */
	protected $months = [];

	public function __construct( Room_Type $room_type, $year = null ) {
		$year = 2017;

		// The room-type we'll working on.
		$this->room_type = $room_type;

		$today = Carbon::today();

		// Set Carbon months of year.
		for ( $month = 1; $month <= Carbon::MONTHS_PER_YEAR; $month++ ) {
			if ( $today->year === $year && $today->month > $month ) {
				continue;
			}

			$this->months[ $month ] = Carbon::createFromDate( $year, $month, 1 )->startOfMonth();
		}

		$units = [];
		$units[] = $this->room_type->get_standard_rate();

		$calendar = $this->calendar = new Calendar( $units, awebooking( 'store.pricing' ), 0 );

		$current_year = Carbon::createFromDate( $year, 1, 1 );
		$next_year = $current_year->copy()->addYear();
		$res = $calendar->getEventsItemized( $current_year, $next_year, 'bat_daily' );

		$this->res = $res;
		// [$room_type->get_id()]['bat_day'][$year]
	}

	public function display() {

		?>
		<style type="text/css">
			.range-start,
			.range-end,
			.in-range,
			.in-hover {
				background-color: #c8f9ff;
			}

			.dbale {
				user-select: none
			}

			.sdd {
				background-color: #ecffd5;
			}
		</style>

		<div class="dbale pricing-calendar">
			<div class="dbale-left">
				<table class="">
					<tbody>
						<tr><th style="height: 40px;"><?php echo $this->room_type->get_title(); ?></th></tr>
						<tr><td><?php echo esc_html__( 'Standard Price', 'awebooking' ); ?></td></tr>
					</tbody>
				</table>
			</div>

			<div class="dbale-calendar">
				<div style="overflow: auto;">
					<table class="sad-tae">
						<thead>
							<tr>
							<?php foreach ( $this->months as $month ) : ?>
								<th colspan="<?php echo $month->daysInMonth; ?>" style="text-align: center;">
									<?php echo $month->format( 'M Y' ); ?>
								</th>
							<?php endforeach; ?>
							</tr>

							<tr><?php $this->print_days( 'th', [] ); ?></tr>
						</thead>

						<tbody>
							<tr><?php
								$value = get_post($this->room_type->get_id(), ARRAY_A);
								$this->print_days( 'td', $value, function( $day, $month ) use ($value) {
									if ( isset( $this->res[$value['ID']]['bat_day'][2017][ $month->month ][ 'd' . $day->day ] ) ) {
										$price = $this->res[$value['ID']]['bat_day'][2017][ $month->month ][ 'd' . $day->day ];
										$price = Price::from_amount( $price );

										$aprice = $this->room_type->get_base_price();

										if ( ! $price->equals( $aprice ) ) {
											return '<span class="sdd">'.$price.'</span>';
										}

										return $price;
									}
								} );
							?></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * //
	 *
	 * @param  string $tag     //.
	 * @param  string $content //.
	 * @return void
	 */
	protected function print_days( $tag = 'td', $post, $content = '%day%' ) {
		foreach ( $this->months as $month ) {
			// @codingStandardsIgnoreLine
			for ( $d = 1; $d <= $month->daysInMonth; $d++ ) {
				$day = $month->copy()->day( $d );

				if ( is_callable( $content ) ) {
					$td_content = call_user_func_array( $content, [ $day, $month ] );
				} else {
					$td_content = str_replace( '%day%', $day->day, $content );
				}

				printf(
					'<%1$s class="day" data-day="%2$d" data-month="%3$d" data-date="%4$s" title="%5$s" data-room-type="%7$s">%6$s</%1$s>',
					esc_html( $tag ),
					esc_attr( $day->day ),
					esc_attr( $day->month ),
					esc_attr( $day->toDateString() ),
					esc_attr( $day->format( 'l, M j, Y' ) ),
					$td_content,
					isset( $post['ID'] ) ? esc_attr( $post['ID'] ) : ''
				);
			}
		}
	}
}
