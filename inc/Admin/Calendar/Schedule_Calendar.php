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
	 * Get the actions_menu.
	 *
	 * @return array
	 */
	protected function get_actions_menu() {
		return [];
	}

	/**
	 * Generate the actions_menu.
	 *
	 * @return string
	 */
	protected function generate_actions_menu() {
		$actions_menu = $this->get_actions_menu();

		if ( is_string( $actions_menu ) ) {
			return $actions_menu;
		}

		$output_menu = '';
		foreach ( $actions_menu as $id => $menu ) {
			$output_menu .= '<li>' . $this->generate_action_link( $menu ) . '</li>';
		}

		return '<ul class="awebooking-schedule__actions-menu">' . $output_menu . '</ul>';
	}

	/**
	 * Generate the action_link.
	 *
	 * @param  string|array $menu The menu item.
	 * @return string
	 */
	protected function generate_action_link( $menu ) {
		if ( is_string( $menu ) ) {
			return '<a href="#">' . esc_html( $menu ) . '</a>';
		}

		$href = ! empty( $menu['href'] ) ? $menu['href'] : '#';
		$name = ! empty( $menu['name'] ) ? $menu['name'] : '';

		$item_id = ! empty( $menu['id'] ) ? ' id="' . esc_attr( $menu['id'] ) . '"' : '';
		$icon_class = ! empty( $menu['icon'] ) ? $menu['icon'] : '';

		return '<a' . $item_id . ' href="' . esc_attr( $href ) . '"><span class="awebooking-schedule__action-icon ' . esc_attr( $icon_class ) . '"></span>' . esc_html( $name ) . '</a>';
	}

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
	protected function get_room_types( $args = [] ) {
		$args = apply_filters( 'awebooking/calendar/query_room_types_args', wp_parse_args( $args, [
			'posts_per_page' => 50,
		]), $this );

		return Collection::make( Room_Type::query( $args )->posts )
			->map( function ( $post ) {
				return new Room_Type( $post );
			})->reject( function ( $room_type ) {
				return $room_type->get_total_rooms() <= 0;
			});
	}
}
