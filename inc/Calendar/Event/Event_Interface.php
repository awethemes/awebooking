<?php
namespace AweBooking\Calendar\Event;

use DateTimeInterface;
use AweBooking\Calendar\Period\Period;

interface Event_Interface {
	/**
	 * Returns the start date instance.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_start_date();

	/**
	 * Returns the end date instance.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_end_date();

	/**
	 * Returns the event value.
	 *
	 * @return int
	 */
	public function get_value();

	/**
	 * The resource of the event belongs to.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource_Interface|null
	 */
	public function get_resource();

	/**
	 * Returns an unique identifier for the Event.
	 *
	 * @return string
	 */
	public function get_uid();

	/**
	 * Returns the event status.
	 *
	 * @return string
	 */
	public function get_status();

	/**
	 * Returns the event summary.
	 *
	 * @return string
	 */
	public function get_summary();

	/**
	 * Returns the event description
	 *
	 * @return string
	 */
	public function get_description();

	/**
	 * Returns the event date created.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_created();

	/**
	 * Returns the event date last modified.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_last_modified();

	/**
	 * Returns the event Period instance.
	 *
	 * @return \AweBooking\Support\Period
	 */
	public function get_period();

	/**
	 * Check if the given date is during the event.
	 *
	 * @param  DateTimeInterface|string|int $date The datetime given.
	 * @return bool
	 */
	public function contains( $date );

	/**
	 * Check if the given period is during the event.
	 *
	 * @param  Period $period The period given.
	 * @return bool
	 */
	public function contains_period( Period $period );
}
