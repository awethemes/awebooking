<?php

namespace AweBooking\Model\Pricing;

use AweBooking\Model\Common\Timespan;

class Custom_Rate_Interval implements Contracts\Rate_Interval {
	/**
	 * The interval name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The rack rate.
	 *
	 * @var float|int
	 */
	protected $rack_rate;

	/**
	 * The effective date.
	 *
	 * @var null|string
	 */
	protected $effective_date;

	/**
	 * The expires_date.
	 *
	 * @var null|string
	 */
	protected $expires_date;

	/**
	 * The priority.
	 *
	 * @var int
	 */
	protected $priority;

	/**
	 * Constructor.
	 *
	 * @param string      $name           The name.
	 * @param float|int   $rack_rate      The rack rate.
	 * @param string|null $effective_date The effective date.
	 * @param string|null $expires_date   The expires date.
	 * @param int         $priority       The priority.
	 */
	public function __construct( $name, $rack_rate, $effective_date = null, $expires_date = null, $priority = 10 ) {
		$this->name           = $name;
		$this->rack_rate      = $rack_rate;
		$this->effective_date = $effective_date;
		$this->expires_date   = $expires_date;
		$this->priority       = $priority;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_id() {
		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_rate_id() {
		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_rack_rate() {
		return $this->rack_rate;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_effective_date() {
		return $this->effective_date;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_expires_date() {
		return $this->expires_date;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_priority() {
		return $this->priority;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_restrictions() {
		return apply_filters( 'abrs_get_rate_restrictions', [], $this );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_breakdown( Timespan $timespan ) {
		return new Breakdown( $timespan, $this->rack_rate );
	}
}
