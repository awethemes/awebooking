<?php

namespace AweBooking\Model\Booking;

use WP_Error;
use AweBooking\Support\Period;
use AweBooking\Model\Room;
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

	/**
	 * Flag mark to force change the timespan.
	 *
	 * @var boolean
	 */
	public $force_change_timespan = false;

	/**
	 * Gets the Guest_Counts.
	 *
	 * @return \AweBooking\Model\Common\Guest_Counts
	 */
	public function get_guests() {
		$guests = new Guest_Counts( $this->get( 'adults' ) );

		if ( abrs_children_bookable() ) {
			$guests->set_children( $this->get( 'children' ) );
		}

		if ( abrs_infants_bookable() ) {
			$guests->set_infants( $this->get( 'infants' ) );
		}

		return $guests;
	}

	/**
	 * Sets the Guest_Counts.
	 *
	 * @param  \AweBooking\Model\Common\Guest_Counts $guests The Guest_Counts.
	 * @return bool|WP_Error
	 */
	public function set_guests( Guest_Counts $guests ) {
		$room_type = abrs_get_room_type( $this->get( 'room_type_id' ) );

		if ( $room_type && $guests->get_totals() > $room_type->get( 'maximum_occupancy' ) ) {
			return new WP_Error( 'out_of_bounds_occupancy', esc_html__( 'Out of bounds occupancy.', 'awebooking' ) );
		}

		$this->set_attribute( 'adults', $guests->get( 'adults' )->get_count() );

		if ( abrs_children_bookable() && $children = $guests->get( 'children' ) ) {
			$this->set_attribute( 'children', $children->get_count() );
		}

		if ( abrs_infants_bookable() && $infants = $guests->get( 'infants' ) ) {
			$this->set_attribute( 'infants', $infants->get_count() );
		}

		return true;
	}

	/**
	 * Gets nights stayed.
	 *
	 * @return int
	 */
	public function get_nights_stayed() {
		return abrs_optional( $this->get_timespan() )->nights();
	}

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
	 * Sets the Timespan (call when create new item).
	 *
	 * @param  \AweBooking\Model\Common\Timespan $timespan The timespan.
	 * @return bool
	 */
	public function set_timespan( Timespan $timespan ) {
		// This action works only in create new item.
		if ( $this->exists() ) {
			return false;
		}

		if ( empty( $this->attributes['room_id'] ) ) {
			return false; // Do it wrong.
		}

		if ( abrs_room_available( $this->get( 'room_id' ), $timespan ) ) {
			$this->attributes['check_in']  = $timespan->get_start_date();
			$this->attributes['check_out'] = $timespan->get_end_date();
		}

		return true;
	}

	/**
	 * Perform change the timespan.
	 *
	 * @param  Timespan $timespan The timespan change to.
	 * @return WP_Error|bool
	 */
	public function change_timespan( Timespan $timespan ) {
		if ( ! $this->exists() ) {
			return false;
		}

		try {
			$timespan->requires_minimum_nights( 1 );
		} catch ( \LogicException $e ) {
			return new WP_Error( 'timespan_error', $e->getMessage() );
		}

		if ( ! $this->timespan_changeable( $timespan ) ) {
			return new WP_Error( 'room_occupied', esc_html__( 'Dates could not be changed because at least one of the rooms is occupied on the selected dates.', 'awebooking' ) );
		}

		// Force to change the timespan.
		$this->force_change_timespan   = true;

		$this->attributes['check_in']  = $timespan->get_start_date();
		$this->attributes['check_out'] = $timespan->get_end_date();

		try {
			return $this->save();
		} catch ( \Exception $e ) {
			return new WP_Error( 'error', $e->getMessage() );
		}
	}

	/**
	 * Determines whether can be change to new timespan.
	 *
	 * @param  Timespan $to_timespan The timespan change to.
	 * @return bool
	 */
	public function timespan_changeable( Timespan $to_timespan ) {
		if ( ! $this->exists() ) {
			return false;
		}

		try {
			$to_timespan->requires_minimum_nights( 1 );
		} catch ( \LogicException $e ) {
			return false;
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
	 * Swap to a another room unit.
	 *
	 * @param \AweBooking\Model\Room|int $swap_to
	 * @return WP_Error|bool
	 */
	public function swap_room( $swap_to ) {
		if ( ! $swap_to instanceof Room ) {
			$swap_to = abrs_get_room( $swap_to );
		}

		if ( empty( $swap_to ) || (int) $swap_to->get( 'room_type' ) !== (int) $this->get( 'room_type_id' ) ) {
			return new WP_Error( 'room_type_mismatch', esc_html__( 'Can not swap to diffrent room type.', 'awebooking' ) );
		}

		// Check the availability of the rooms.
		$timespan = $this->get_timespan();

		if ( ! $timespan || ! abrs_room_available( $swap_to, $timespan ) ) {
			return new WP_Error( 'swap_error', esc_html__( 'Can not swap to selected room type.', 'awebooking' ) );
		}

		// Start a mysql transaction.
		abrs_db_transaction( 'start' );

		$updated1 = abrs_clear_booking_event( $this->get( 'room_id' ), $this->get( 'booking_id' ), $timespan );
		$updated2 = abrs_apply_booking_event( $swap_to->get_id(), $this->get( 'booking_id' ), $timespan );

		if ( true !== $updated1 || true !== $updated2 ) {
			abrs_db_transaction( 'rollback' );
			return false;
		}

		// Commit the transaction.
		abrs_db_transaction( 'commit' );

		$this->set_attribute( 'room_id', $swap_to->get_id() );
		$this->save();

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
		$subtotal = $this->get( 'subtotal' );

		if ( '' === $subtotal || $subtotal < $amount ) {
			$this->set_subtotal( $amount );
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function updating() {
		if ( ! $this->force_change_timespan ) {
			$this->revert_attribute( 'check_in' );
			$this->revert_attribute( 'check_out' );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function saved() {
		if ( $this->recently_created ) {
			$this->apply_booking_event();
		} elseif ( true === $this->force_change_timespan ) {
			$this->perform_change_timespan( $this->get_timespan() );
		}

		abrs_rescue( function () {
			abrs_optional( $this->get_booking() )->setup_dates();
		});
	}

	/**
	 * Perfrom change the timespan after saved.
	 *
	 * @param  Timespan $to_timespan The timespan change to.
	 * @return void
	 *
	 * @throws \RuntimeException
	 */
	protected function perform_change_timespan( Timespan $to_timespan ) {
		$this->force_change_timespan = false;

		$to_timespan->requires_minimum_nights( 1 );
		$from_timespan = abrs_timespan( $this->original['check_in'], $this->original['check_out'] );

		// Start a mysql transaction.
		abrs_db_transaction( 'start' );

		$updated1 = abrs_clear_booking_event( $this->get( 'room_id' ), $this->get( 'booking_id' ), $from_timespan );
		$updated2 = abrs_apply_booking_event( $this->get( 'room_id' ), $this->get( 'booking_id' ), $to_timespan );

		if ( true !== $updated1 || true !== $updated2 ) {
			abrs_db_transaction( 'rollback' );
			throw new \RuntimeException( 'Can not change the timespan.' );
		}

		// Commit the transaction.
		abrs_db_transaction( 'commit' );

		// Add the booking note for the change.
		// translators: 1 Room name, 2 change from date, 3 to date.
		$transition_note = sprintf( esc_html__( '[%1$s] Timespan change from "%2$s" to "%3$s".', 'awebooking' ), $this->get( 'name' ), esc_html( $from_timespan->as_string() ), esc_html( $to_timespan->as_string() ) );
		abrs_add_booking_note( $this->get( 'booking_id' ), $transition_note, false, true );

		do_action( $this->prefix( 'timespan_changed' ), $from_timespan, $to_timespan, $this );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_delete( $force ) {
		parent::perform_delete( $force );

		$this->clear_booking_event();
	}

	/**
	 * Perform clear booking & availability event data.
	 *
	 * @return bool|WP_Error|null
	 */
	public function clear_booking_event() {
		if ( $timespan = $this->get_timespan() ) {
			return abrs_clear_booking_event( $this->get( 'room_id' ), $this->get( 'booking_id' ), $timespan );
		}
	}

	/**
	 * Apply booking event.
	 *
	 * @return bool|\WP_Error
	 */
	public function apply_booking_event() {
		return abrs_apply_booking_event( $this->get( 'room_id' ), $this->get( 'booking_id' ), $this->get_timespan() );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), array_merge( $this->attributes, [
			'room_id'        => 0,
			'room_type_id'   => 0,
			'rate_plan_id'   => 0,

			'check_in'       => '',
			'check_out'      => '',
			'adults'         => 0,
			'children'       => 0,
			'infants'        => 0,

			'subtotal'       => 0, // Pre-discount.
			'subtotal_tax'   => 0,
			'total'          => 0,
			'total_tax'      => 0,
			'taxes'          => [], // ['total' => [ 1 => 100 ]].
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

			'check_in'       => '_check_in',
			'check_out'      => '_check_out',
			'adults'         => '_adults',
			'children'       => '_children',
			'infants'        => '_infants',

			'subtotal'       => '_line_subtotal',
			'subtotal_tax'   => '_line_subtotal_tax',
			'total'          => '_line_total',
			'total_tax'      => '_line_total_tax',
			'taxes'          => '_taxes',
			'breakdowns'     => [], // Cache the price breakdowns.
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
