<?php

namespace AweBooking\Calendar\Event;

use AweBooking\Support\Period;
use AweBooking\Calendar\Resource\Resource_Interface;

interface Event_Interface {
	/**
	 * Returns the start date instance.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_start_date();

	/**
	 * Set the start date.
	 *
	 * @param \DateTimeInterface|string $start_date The start date of the event.
	 * @return void
	 */
	public function set_start_date( $start_date );

	/**
	 * Returns the end date instance.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_end_date();

	/**
	 * Set the end date.
	 *
	 * @param  \DateTimeInterface|string $end_date The end date of the event.
	 * @return void
	 */
	public function set_end_date( $end_date );

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
	 * Set the event resource.
	 *
	 * @param  \AweBooking\Calendar\Resource\Resource_Interface $resource The resource instance.
	 * @return $this
	 */
	public function set_resource( Resource_Interface $resource );

	/**
	 * Is current event contains untrusted resource?
	 *
	 * @return bool
	 */
	public function is_untrusted_resource();

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
	 * @param  \DateTimeInterface|string|int $date The datetime given.
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

	/**
	 * Format the event to a specified format (JSON, iCal).
	 *
	 * @param  \AweBooking\Calendar\Event\Formatter $formater The formater class.
	 * @return mixed
	 */
	public function format( Formatter $formater );
}
