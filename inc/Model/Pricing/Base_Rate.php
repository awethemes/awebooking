<?php
namespace AweBooking\Model\Pricing;

use AweBooking\Model\Factory;
use AweBooking\Ruler\Rule;
use AweBooking\Support\Fluent;
use AweBooking\Support\Decimal;
use AweBooking\Reservation\Request;

class Base_Rate implements Rate {
	/**
	 * The room-type instance.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $instance;

	/**
	 * [$restrictions description]
	 *
	 * @var [type]
	 */
	protected $restrictions;

	/**
	 * Create base-rate instance from a room-type.
	 *
	 * @param mixed $instance The room-type ID or instance.
	 */
	public function __construct( $instance ) {
		$this->instance = Factory::get_room_type( $instance );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_id() {
		return $this->instance->get_id();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_parent_id() {
		return $this->instance->get_id();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_amount() {
		return Decimal::create( $this->instance->get_meta( 'base_price' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_priority() {
		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_restrictions() {
		if ( is_null( $this->restrictions ) ) {
			$this->restrictions = new Rule;
		}

		return $this->restrictions;
	}

	/**
	 * Set the rule restrictions.
	 *
	 * @param \AweBooking\Ruler\Rule $restrictions The rule.
	 */
	public function set_restrictions( Rule $restrictions ) {
		$this->restrictions = $restrictions;
	}

	/**
	 * {@inheritdoc}
	 */
	public function apply( Request $request ) {
		return true;
	}
}
