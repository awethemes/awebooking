<?php
namespace AweBooking\Calendar;

use AweBooking\Support\Collection;

class Scheduler extends Collection {
	use Traits\With_Reference;

	/**
	 * The scheduler ID.
	 *
	 * @var string
	 */
	protected $uid;

	/**
	 * The scheduler name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Description of the calendar.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Returns an unique identifier for the Scheduler.
	 *
	 * @return int
	 */
	public function get_uid() {
		return $this->uid;
	}

	/**
	 * Set the scheduler UID.
	 *
	 * @param  int $uid The UID.
	 * @return $this
	 */
	public function set_uid( $uid ) {
		$this->uid = $uid;

		return $this;
	}

	/**
	 * Get the name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the name.
	 *
	 * @param  string $name The name.
	 * @return $this
	 */
	public function set_name( $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get the description.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set the description.
	 *
	 * @param  string $description The description.
	 * @return $this
	 */
	public function set_description( $description ) {
		$this->description = $description;

		return $this;
	}
}
