<?php

namespace AweBooking\Calendar\Event;

use AweBooking\Support\Period;
use AweBooking\Support\Traits\Fluent_Getter;
use AweBooking\Calendar\Resource\Resource_Interface;

class Event implements Event_Interface {
	use Fluent_Getter;

	/**
	 * The start date for the event.
	 *
	 * @var \AweBooking\Support\Carbonate
	 */
	protected $start_date;

	/**
	 * The end date for the event.
	 *
	 * @var \AweBooking\Support\Carbonate
	 */
	protected $end_date;

	/**
	 * The resource of event belong to.
	 *
	 * @var \AweBooking\Calendar\Resource\Resource_Interface
	 */
	protected $resource;

	/**
	 * The value associated with this event.
	 *
	 * This can represent an availability state or a pricing value.
	 *
	 * @var int|mixed
	 */
	protected $value;

	/**
	 * The event UID.
	 *
	 * @var string
	 */
	protected $uid;

	/**
	 * The event status.
	 *
	 * @var string
	 */
	protected $status;

	/**
	 * The event summary.
	 *
	 * @var string
	 */
	protected $summary;

	/**
	 * The event description.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * The event URL.
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * The created date of the event.
	 *
	 * @var \AweBooking\Support\Carbonate|null
	 */
	protected $created;

	/**
	 * The last modified of the event.
	 *
	 * @var \AweBooking\Support\Carbonate|null
	 */
	protected $last_modified;

	/**
	 * Create an event.
	 *
	 * @param Resource_Interface $resource   The resource implementation.
	 * @param \DateTime|string   $start_date The start date of the event.
	 * @param \DateTime|string   $end_date   The end date of the event.
	 * @param int                $value      The event value.
	 */
	public function __construct( Resource_Interface $resource, $start_date, $end_date, $value = 0 ) {
		static::assert_valid_dates(
			$start_date = abrs_date_time( $start_date ),
			$end_date   = abrs_date_time( $end_date )
		);

		$this->start_date = $start_date;
		$this->end_date = $end_date;

		$this->set_value( $value );
		$this->set_resource( $resource );
	}

	/**
	 * Assert that given valid dates.
	 *
	 * @param  \AweBooking\Support\Carbonate $start_date The start date of the event.
	 * @param  \AweBooking\Support\Carbonate $end_date   The end date of the event.
	 * @return void
	 *
	 * @throws \LogicException
	 */
	protected static function assert_valid_dates( $start_date, $end_date ) {
		if ( $start_date > $end_date ) {
			throw new \LogicException( 'The ending datepoint must be greater or equal to the starting datepoint.' );
		}
	}

	/**
	 * Returns the start date as new instance.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_start_date() {
		return $this->start_date->copy();
	}

	/**
	 * Set the start date.
	 *
	 * @param \DateTime|string $start_date The start date of the event.
	 * @return void
	 */
	public function set_start_date( $start_date ) {
		$start_date = abrs_date_time( $start_date );

		static::assert_valid_dates( $start_date, $this->end_date );

		$this->start_date = $start_date;
	}

	/**
	 * Returns the end date as new instance.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_end_date() {
		return $this->end_date->copy();
	}

	/**
	 * Set the end date.
	 *
	 * @param  \DateTime|string $end_date The end date of the event.
	 * @return void
	 */
	public function set_end_date( $end_date ) {
		$end_date = abrs_date_time( $end_date );

		static::assert_valid_dates( $this->start_date, $end_date );

		$this->end_date = $end_date;
	}

	/**
	 * The resource of the event belongs to.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource_Interface
	 */
	public function get_resource() {
		return $this->resource;
	}

	/**
	 * Set the event resource.
	 *
	 * @param  Resource_Interface $resource The resource instance.
	 * @return $this
	 */
	public function set_resource( Resource_Interface $resource ) {
		$this->resource = $resource;

		return $this;
	}

	/**
	 * Determines if this event have untrusted resource.
	 *
	 * @return boolean
	 */
	public function is_untrusted_resource() {
		return $this->resource && $this->resource->get_id() < 1;
	}

	/**
	 * Returns the event value.
	 *
	 * @return int
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Set the event value.
	 *
	 * @param  int $value The event value.
	 * @return $this
	 */
	public function set_value( $value ) {
		$this->value = $value;

		return $this;
	}

	/**
	 * Returns an unique identifier for the Event.
	 *
	 * @return string
	 */
	public function get_uid() {
		return $this->uid;
	}

	/**
	 * Set the event UID.
	 *
	 * @param  string $uid The event UID.
	 * @return $this
	 */
	public function set_uid( $uid ) {
		$this->uid = $uid;

		return $this;
	}

	/**
	 * Get the event status.
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Set the event status.
	 *
	 * @param  string $status The event status.
	 * @return $this
	 */
	public function set_status( $status ) {
		$this->status = $status;

		return $this;
	}

	/**
	 * Get the event summary.
	 *
	 * @return string
	 */
	public function get_summary() {
		return $this->summary;
	}

	/**
	 * Set the event summary.
	 *
	 * @param  string $summary The summary.
	 * @return $this
	 */
	public function set_summary( $summary ) {
		$this->summary = $summary;

		return $this;
	}

	/**
	 * Get the event URL.
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Set the event URL.
	 *
	 * @param  string $url The url.
	 * @return $this
	 */
	public function set_url( $url ) {
		$this->url = $url;

		return $this;
	}

	/**
	 * Get the event description
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set the event description.
	 *
	 * @param  string $description The description.
	 * @return $this
	 */
	public function set_description( $description ) {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get the event date created.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_created() {
		return $this->created;
	}

	/**
	 * Set the event created date.
	 *
	 * @param  \AweBooking\Support\Carbonate $created The created date.
	 * @return $this
	 */
	public function set_created( $created ) {
		$this->created = abrs_date_time( $created );

		return $this;
	}

	/**
	 * Get the event date last modified.
	 *
	 * @return \DateTimeInterface|string|int
	 */
	public function get_last_modified() {
		return $this->last_modified;
	}

	/**
	 * Set the event last_modified date.
	 *
	 * @param  \DateTimeInterface|string|int $last_modified The last_modified date.
	 * @return $this
	 */
	public function set_last_modified( $last_modified ) {
		$this->last_modified = abrs_date_time( $last_modified );

		return $this;
	}

	/**
	 * Get the event Period instance.
	 *
	 * @return \AweBooking\Support\Period
	 */
	public function get_period() {
		return new Period( $this->get_start_date(), $this->get_end_date() );
	}

	/**
	 * Check if the given date is during the event.
	 *
	 * @param  \DateTimeInterface|string|int $date The datetime given.
	 * @return bool
	 */
	public function contains( $date ) {
		return $this->get_period()->contains( abrs_date_time( $date ) );
	}

	/**
	 * Check if the given period is during the event.
	 *
	 * @param  Period $period The period given.
	 * @return bool
	 */
	public function contains_period( Period $period ) {
		return $this->get_period()->contains( $period );
	}

	/**
	 * {@inheritdoc}
	 */
	public function format( Formatter $formater ) {
		return $formater->format( $this );
	}
}
