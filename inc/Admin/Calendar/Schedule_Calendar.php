<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Model\Room_Type;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Collection;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Event\State_Event;
use AweBooking\Calendar\Event\Pricing_Event;
use AweBooking\Calendar\Event\Booking_Event;
use AweBooking\Calendar\Html\Html_Skeleton_Calendar;

abstract class Schedule_Calendar {
	use Html_Skeleton_Calendar;

	/**
	 * The Calendar default options.
	 *
	 * @var array
	 */
	protected $options = [
		'date_title'    => 'l, M j, Y',
		'month_label'   => 'abbrev',  // 'abbrev', 'full'.
		'weekday_label' => 'abbrev',  // 'initial', 'abbrev', 'full'.
	];

	public function cell_date_contents( $date, $calendar ) {}
	public function cell_event_contents( $events, $date, $calendar ) {}

	/**
	 * Get events from the Calendar in a Period.
	 *
	 * @param  Calendar $calendar [description]
	 * @param  Period   $period   [description]
	 * @return [type]
	 */
	public function get_calendar_events( Calendar $calendar, Period $period ) {
		$period = $period
			->moveEndDate( '2 DAYS' )
			->moveStartDate( '2 DAYS' );

		return $calendar->get_events( $period )
			->reject(function( $e ) {
				return ( $e instanceof State_Event
					|| $e instanceof Booking_Event
					|| $e instanceof Pricing_Event ) && ! $e->get_value();
			});
	}

	/**
	 * Get room types for the scheduler.
	 *
	 * @param  array $args Custom WP_Query args.
	 * @return \AweBooking\Support\Collection
	 */
	protected function fetch_room_types( $args = [] ) {
		$args = apply_filters( 'awebooking/calendar/query_room_types_args', wp_parse_args( $args, [
			'posts_per_page' => 50, // Limit 50 items.
		]), $this );

		return Collection::make( Room_Type::query( $args )->posts )
			->map( function ( $post ) {
				return new Room_Type( $post );
			})->reject( function ( $room_type ) {
				return $room_type->get_total_rooms() <= 0;
			});
	}
}
