<?php
namespace AweBooking\Model\Booking;

use AweBooking\Constants;
use AweBooking\Calendar\Event\Event;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;
use AweBooking\Support\Period;

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

	/**
	 * Get the Timespan of check-in, check-out.
	 *
	 * @return \AweBooking\Model\Common\Timespan|null
	 */
	public function get_timespan() {
		$timespan = abrs_timespan( $this->get( 'check_in' ), $this->get( 'check_out' ), 1 );

		return ! is_wp_error( $timespan ) ? $timespan : null;
	}

	/**
	 * Sets the Timespan.
	 *
	 * @param  \AweBooking\Model\Common\Timespan $timespan The timespan.
	 * @return void
	 */
	public function set_timespan( Timespan $timespan ) {
		if ( ! $this->exists() ) {
			$this->attributes['check_in']  = $timespan->get_start_date();
			$this->attributes['check_out'] = $timespan->get_end_date();
		}
	}

	/**
	 * Determines whether can be change to new timespan.
	 *
	 * @param  Timespan $to_timespan Change to timespan.
	 * @return bool
	 */
	public function is_timespan_changeable( Timespan $to_timespan ) {
		if ( ! $this->exists() ) {
			return;
		}

		$to_period = $to_timespan->get_period();
		$original_period = new Period( $this->original['check_in'], $this->original['check_out'] );

		// If new period inside the current-period,
		// so it alway can be change.
		if ( $original_period->contains( $to_period ) ) {
			return true;
		}

		// If both period not overlaps, so we just
		// determines new period is bookable or not.
		if ( ! $original_period->overlaps( $to_period ) ) {
			return abrs_room_available( $this->get( 'room_id' ), $to_timespan );
		}

		// Create an array difference between two Period.
		// @see http://period.thephpleague.com/api/comparing/#perioddiff .
		$diff = $original_period->diff( $to_period );

		// Loop each piece of diff-period, if one of them
		// unavailable for changing just leave and return false.
		foreach ( $diff as $piece ) {
			$timespan = Timespan::from_period( $piece );

			if ( ! $original_period->contains( $piece ) && ! abrs_room_available( $this->get( 'room_id' ), $timespan ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Sets the room subtotal (before discounts).
	 *
	 * @param  mixed $amount The amount.
	 * @return $this
	 */
	public function set_subtotal( $amount ) {
		$amount = abrs_sanitize_decimal( $amount );

		if ( ! is_numeric( $amount ) ) {
			$amount = 0;
		}

		return $this->set_attribute( 'subtotal', $amount );
	}

	/**
	 * Sets the room total (after discounts).
	 *
	 * @param  string $amount The amount.
	 * @return $this
	 */
	public function set_total( $amount ) {
		$amount = abrs_sanitize_decimal( $amount );

		if ( ! is_numeric( $amount ) ) {
			$amount = 0;
		}

		$this->set_attribute( 'total', $amount );

		// Subtotal cannot be less than total.
		$subtotal = $this->get_subtotal();

		if ( '' === $subtotal || $subtotal < $amount ) {
			$this->set_subtotal( $amount );
		}

		return $this;
	}

	/**
	 * Returns nights stayed of this line item.
	 *
	 * @return int
	 */
	public function get_nights_stayed() {
		return abrs_optional( $this->get_timespan() )->nights();
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
	protected function finish_save() {
		parent::finish_save();

		if ( $this->recently_created ) {
			abrs_apply_booking_state( $this->get( 'room_id' ), $this->get( 'booking_id' ), $this->get_timespan() );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_delete( $force ) {
		parent::perform_delete( $force );

		abrs_clear_booking_state( $this->get( 'room_id' ), $this->get( 'booking_id' ), $this->get_timespan() );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), array_merge( $this->attributes, [
			'room_id'        => 0,
			'room_type_id'   => 0,
			'rate_plan_id'   => 0,
			'room_type_name' => '',
			'rate_plan_name' => '',

			'check_in'       => '',
			'check_out'      => '',
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
			'room_type_id'   => '_room_type_id',
			'rate_plan_id'   => '_rate_plan_id',
			'room_type_name' => '_room_type_name',
			'rate_plan_name' => '_rate_plan_name',

			'check_in'       => '_check_in',
			'check_out'      => '_check_out',
			'adults'         => '_adults',
			'children'       => '_children',
			'infants'        => '_infants',

			'subtotal'       => '_line_subtotal',
			'subtotal_tax'   => '_line_subtotal_tax',
			'total'          => '_line_total',
			'total_tax'      => '_line_total_tax',
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
