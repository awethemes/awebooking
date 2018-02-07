<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Constants;
use AweBooking\Model\Stay;
use AweBooking\Model\Room_Type;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Period\Month;
use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resource_Collection;
use AweBooking\Calendar\Provider\Pricing_Provider;
use AweBooking\Reservation\Pricing\Pricing;
use AweBooking\Support\Utils as U;

class Pricing_Calendar extends Schedule_Calendar {
	/**
	 * The room-type instance.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * Constructor.
	 *
	 * @param Room_Type $room_type The room-type instance.
	 * @param array     $options   The calendar options.
	 */
	public function __construct( Room_Type $room_type, array $options = [] ) {
		$this->options = array_merge( $this->options, $options );
		$this->room_type = $room_type;
	}

	/**
	 * Get the room-type instance.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Get Calendar resources from room_type.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource_Collection
	 */
	public function get_resources() {
		return $this->resources;
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		$scheduler = $this->get_scheduler()
			->set_name( $this->room_type->get_title() );

		$period = new Month( 2018, 01 );

		print $this->generate( $scheduler, $period ); // @WPCS: XSS OK.
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_actions_menu() {
		return [
			[ 'id' => 'awebooking-set-price', 'icon' => 'dashicons dashicons-edit', 'href' => '#awebooking-add-line-item-popup', 'name' => 'Adjust Price' ],
			[ 'icon' => 'dashicons dashicons-update', 'name' => 'Clear Adjust' ],
		];
	}

	/**
	 * Get the scheduler.
	 *
	 * @return \AweBooking\Calendar\Scheduler
	 */
	protected function get_scheduler() {
		$resources = $this->create_calendar_resources();

		$provider  = new Pricing_Provider( $resources );

		$calendars = U::collect( $resources )
			->map(function( $resource ) use ( $provider ) {
				$calendar = new Calendar( $resource, $provider );

				$calendar->set_name( $resource->get_title() );

				return $calendar;
			});

		return new Scheduler( $calendars );
	}

	/**
	 * Create the calendar resources.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource_Collection
	 */
	protected function create_calendar_resources() {
		$rates = $this->room_type->get_rates();

		$rates = $rates->sortBy( 'order' );

		$resources = U::collect( $rates )
			->map(function( $rate ) {
				$resource = new Resource( $rate->get_id(), $rate->get_base_amount()->as_raw_value() );

				$resource->set_reference( $rate );
				$resource->set_title( $rate->get_name() );

				return $resource;
			});

		return Resource_Collection::make( $resources );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_calendar_events( Calendar $calendar, Period $period ) {
		$stay = new Stay( $period->get_start_date(), $period->get_end_date() );

		$pricing = new Pricing(
			$calendar->get_resource()->get_reference(), $stay
		);

		return $pricing->get_breakdown();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_cell_event_contents( $pricing, $date, $calendar ) {
		if ( ! $pricing->has( $index = $date->toDateString() ) ) {
			return;
		}

		$rate = $calendar->get_resource()->get_reference();

		$base_amount = $rate->get_base_amount();

		$amount = $pricing->get( $index )->get_amount();

		$append = '';
		if ( $amount->greater_than( $base_amount ) ) {
			$append = '<span class="dashicons dashicons-arrow-up"></span>';
		} else if ( $amount->less_than( $base_amount ) ) {
			$append = '<span class="dashicons dashicons-arrow-down"></span>';
		}

		return '<span class="' . esc_attr( $this->html_class( '&__float-amount' ) ) . '">' . esc_html( $amount->as_string() ) . $append . '</span>';
	}
}
