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
		$state_calendar = abrs_create_calendar( $this->attributes['room_id'], 'state' );
		$booking_calendar = abrs_create_calendar( $this->attributes['room_id'], 'booking' );

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
			return new Timespan( $this->get_start_date(), $this->get_end_date() );
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
			'room_id'      => 0,
			'check_in'     => null,
			'check_out'    => null,
			'adults'       => 0,
			'children'     => 0,
			'infants'      => 0,
			'subtotal'     => 0, // Pre-discount.
			'total'        => 0,
		]));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'room_id'   => '_room_id',
			'adults'    => '_adults',
			'children'  => '_children',
			'infants'   => '_infants',
			'check_in'  => '_check_in',
			'check_out' => '_check_out',
			'subtotal'  => '_line_subtotal',
			'total'     => '_line_total',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'room_id':
			case 'adults':
			case 'infants':
			case 'children':
				$value = absint( $value );
				break;

			case 'total':
			case 'subtotal':
				$value = abrs_sanitize_decimal( $value );
				break;
		}

		return parent::sanitize_attribute( $key, $value );
	}
}
