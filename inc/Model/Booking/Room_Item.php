<?php
namespace AweBooking\Model\Booking;

use AweBooking\Constants;
use AweBooking\Calendar\Event\Event;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;

class Room_Item extends Item {
	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'room_item';

	/**
	 * Name of item type.
	 *
	 * @var string
	 */
	protected $type = 'line_item';

	public function set_timespan( Timespan $timespan ) {
		$state_calendar = abrs_calendar( $this->attributes['room_id'], 'state' );
		$booking_calendar = abrs_calendar( $this->attributes['room_id'], 'booking' );

		$period = $timespan->to_period( Constants::GL_NIGHTLY );

		$state_calendar->store(
			new Event( $state_calendar->get_resource(), $period->start_date, $period->end_date, Constants::STATE_BOOKING )
		);

		$booking_calendar->store(
			new Event( $state_calendar->get_resource(), $period->start_date, $period->end_date, $this->attributes['booking_id'] )
		);

		$this->attributes['check_in']  = $timespan->get_start_date();
		$this->attributes['check_out'] = $timespan->get_end_date();

		$this->save();
	}

	/**
	 * Get the Timespan of check-in, check-out.
	 *
	 * @return \AweBooking\Model\Common\Timespan|null
	 */
	public function get_timespan() {
		return abrs_rescue( function () {
			return new Timespan( $this->get( 'check_in' ), $this->get( 'check_out' ) );
		});
	}

	/**
	 * Returns nights stayed of this line item.
	 *
	 * @return int
	 */
	public function get_nights_stayed() {
		$timespan = $this->get_timespan();

		return ! is_null( $timespan ) ? $timespan->nights() : 0;
	}

	/**
	 * Create the Guest_Counts.
	 *
	 * @return \AweBooking\Model\Common\Guest_Counts|null
	 */
	public function get_guest_counts() {
		return abrs_rescue( function () {
			return new Guest_Counts( $this['adults'], $this['children'], $this['infants'] );
		});
	}

	/**
	 * {@inheritdoc}
	 */
	protected function updating() {
		if ( $this->is_dirty( 'room_id' ) || $this->is_dirty( 'booking_id' ) ) {
			$this->revert_attribute( 'room_id' );
			$this->revert_attribute( 'booking_id' );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), array_merge( $this->attributes, [
			'room_id'        => 0,
			'room_type'      => 0,
			'rate_plan'      => 0,
			'room_type_name' => '',
			'rate_plan_name' => '',

			'check_in'       => null,
			'check_out'      => null,

			'adults'         => 0,
			'children'       => 0,
			'infants'        => 0,

			'subtotal'       => 0, // Pre-discount.
			'subtotal_tax'   => 0,
			'total'          => 0,
			'total_tax'      => 0,
		]));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'room_id'        => '_room_id',
			'room_type'      => '_room_type_id',
			'rate_plan'      => '_rate_plan_id',
			'room_type_name' => '_room_type_name',
			'rate_plan_name' => '_rate_plan_name',

			'check_in'     => '_check_in',
			'check_out'    => '_check_out',
			'adults'       => '_adults',
			'children'     => '_children',
			'infants'      => '_infants',

			'subtotal'     => '_line_subtotal',
			'subtotal_tax' => '_line_subtotal_tax',
			'total'        => '_line_total',
			'total_tax'    => '_line_total_tax',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'room_id':
			case 'room_type':
			case 'rate_plan':
			case 'adults':
			case 'infants':
			case 'children':
				$value = absint( $value );
				break;

			case 'total':
			case 'total_tax':
			case 'subtotal':
			case 'subtotal_tax':
				$value = abrs_sanitize_decimal( $value );
				break;
		}

		return parent::sanitize_attribute( $key, $value );
	}
}
