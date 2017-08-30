<?php
namespace AweBooking\Booking\Items;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Concierge;
use AweBooking\Pricing\Price;
use AweBooking\Support\Period;

class Line_Item extends Booking_Item {
	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'room_item';

	/**
	 * The attributes for this object.
	 *
	 * @var array
	 */
	protected $extra_attributes = [
		'room_id'      => 0,
		'check_in'     => '',
		'check_out'    => '',
		'adults'       => 0,
		'children'     => 0,
		'price'        => 0,
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $extra_casts = [
		'room_id'  => 'int',
		'adults'   => 'int',
		'children' => 'int',
		'price'    => 'float',
	];

	/**
	 * An array of attributes mapped with metadata.
	 *
	 * @var array
	 */
	protected $maps = [
		'room_id'   => '_room_id',
		'adults'    => '_adults',
		'children'  => '_children',
		'check_in'  => '_check_in',
		'check_out' => '_check_out',
		'price'     => '_line_price',
	];

	/**
	 * Returns booking item type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'line_item';
	}

	/**
	 * Returns room unit ID belongs to booking.
	 *
	 * @return int
	 */
	public function get_room_id() {
		return $this['room_id'];
	}

	/**
	 * Set the room unit ID.
	 *
	 * @param  int $room The room unit ID or instance of Room.
	 * @return void
	 */
	public function set_room_id( $room ) {
		if ( $room instanceof Price ) {
			$this->attributes['room_id'] = $room->get_id();
		} else {
			$this->attributes['room_id'] = absint( $room );
		}
	}

	/**
	 * Returns instance of room unit.
	 *
	 * @return \AweBooking\Hotel\Room
	 */
	public function get_room_unit() {
		return Factory::get_room_unit( $this->get_room_id() );
	}

	/**
	 * Returns check-in date.
	 *
	 * @return string
	 */
	public function get_check_in() {
		return apply_filters( $this->prefix( 'get_check_in' ), $this['check_in'], $this );
	}

	/**
	 * Returns check-out date.
	 *
	 * @return string
	 */
	public function get_check_out() {
		return apply_filters( $this->prefix( 'get_check_out' ), $this['check_out'], $this );
	}

	/**
	 * Get the Period of check-in, check-out.
	 *
	 * Note: An exception or more will be thrown if any invalid found.
	 *
	 * @return Period
	 */
	public function get_period() {
		return new Period( $this->get_check_in(), $this->get_check_out() );
	}

	/**
	 * Returns nights stayed of this line item.
	 *
	 * If have any errors, -1 will be return.
	 *
	 * @return int
	 */
	public function get_nights_stayed() {
		try {
			return $this->get_period()->nights();
		} catch ( \Exception $e ) {
			return -1;
		}
	}

	/**
	 * Return number adults of line item.
	 *
	 * @return int
	 */
	public function get_adults() {
		return apply_filters( $this->prefix( 'get_adults' ), $this['adults'], $this );
	}

	/**
	 * Return number children of line item.
	 *
	 * @return int
	 */
	public function get_children() {
		return apply_filters( $this->prefix( 'get_children' ), $this['children'], $this );
	}

	/**
	 * Returns price (per night) of line item.
	 *
	 * @return float
	 */
	public function get_price() {
		return apply_filters( $this->prefix( 'get_price' ), $this['price'], $this );
	}

	/**
	 * Set line item price (per night).
	 *
	 * @param float|Price $price Price amount.
	 */
	public function set_price( $price ) {
		if ( $price instanceof Price ) {
			$this->attributes['price'] = $price->get_amount();
		} else {
			$this->attributes['price'] = awebooking_sanitize_price( $price );
		}
	}

	/**
	 * Returns total price of line item.
	 *
	 * @return float
	 */
	public function get_total() {
		$nights = $this->get_nights_stayed();

		if ( $nights < 1 ) {
			return 0;
		}

		return $this->get_price() * $nights;
	}

	/**
	 * Gets formatted nights stayed.
	 *
	 * @param  boolean $echo Echo or return output.
	 * @return string|void
	 */
	public function get_formatted_nights_stayed( $echo = true ) {
		$nights = $this->get_nights_stayed();
		$nights = $nights . ' ' . _n( 'night', 'nights', $nights, 'awebooking' );

		if ( $echo ) {
			print $nights; // WPCS: XSS OK.
		} else {
			return $nights;
		}
	}

	/**
	 * Gets formatted guest number HTML.
	 *
	 * @param  boolean $echo Echo or return output.
	 * @return string|void
	 */
	public function get_fomatted_guest_number( $echo = true ) {
		$html = '';

		$html .= sprintf(
			'<span class="">%1$d %2$s</span>',
			$this->get_adults(),
			_n( 'adult', 'adults', $this->get_adults(), 'awebooking' )
		);

		if ( $this['children'] ) {
			$html .= sprintf(
				' &amp; <span class="">%1$d %2$s</span>',
				$this->get_children(),
				_n( 'child', 'children', $this->get_children(), 'awebooking' )
			);
		}

		if ( $echo ) {
			print $html; // WPCS: XSS OK.
		} else {
			return $html;
		}
	}

	/**
	 * Determines if the current item is able to be saved.
	 *
	 * @return bool
	 */
	public function can_save() {
		if ( ! $this->get_room_unit()->exists() ) {
			return false;
		}

		return parent::can_save();
	}

	/**
	 * Determines whether the new period can be changeable.
	 *
	 * @throws \LogicException
	 *
	 * @param  Period $to_period Change to date period.
	 * @return bool|null
	 */
	public function is_changeable( Period $to_period ) {
		$to_period->required_minimum_nights();

		$room_unit = $this->get_room_unit();
		if ( ! $room_unit->exists() ) {
			return;
		}

		$original_period = new Period(
			$this->original['check_in'], $this->original['check_out']
		);

		// If new period inside the current-period,
		// so it alway can be change.
		if ( $original_period->contains( $to_period ) ) {
			return true;
		}

		// If both period object not overlaps, so we just
		// determines new period is bookable or not.
		if ( ! $original_period->overlaps( $to_period ) ) {
			return Concierge::is_available( $room_unit, $to_period );
		}

		// Create an array difference between two Period.
		// @see http://period.thephpleague.com/api/comparing/#perioddiff .
		$diff = $original_period->diff( $to_period );

		// Loop each piece of diff-period, if one of them
		// un-available for changing just leave and return false.
		foreach ( $diff as $piece ) {
			if ( ! $original_period->contains( $piece ) && ! Concierge::is_available( $room_unit, $piece ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Do something before doing save.
	 *
	 * @throws \LogicException
	 * @throws \RuntimeException
	 *
	 * @return void
	 */
	protected function before_save() {
		$period = $this->get_period();
		$period->required_minimum_nights();

		// If this save action considered as update, we check `check_in`, `check_out`
		// have changes, we'll re-check available of booking room again to make sure
		// everything in AweBooking it is working perfect.
		if ( $this->exists() && $this->is_dirty( 'check_in', 'check_out' ) ) {
			if ( ! $this->is_changeable( $period ) ) {
				throw new \RuntimeException( esc_html__( 'Dates could not be changed because at least one of the rooms is occupied on the selected dates.', 'awebooking' ) );
			}
		}

		// To prevent change `room_id` and `booking_id`, we don't
		// allow change them, so just set to original value.
		if ( $this->exists() && $this->is_dirty( 'room_id', 'booking_id' ) ) {
			$this->revert_attribute( 'room_id' );
			$this->revert_attribute( 'booking_id' );
		}
	}

	/**
	 * Do somethings when finish save.
	 *
	 * BUGS: ...
	 *
	 * @return void
	 */
	protected function finish_save() {
		$period    = $this->get_period();
		$booking   = $this->get_booking();
		$room_unit = $this->get_room_unit();

		// Moving date period.
		if ( ! $this->recently_created && $this->is_dirty( 'check_in', 'check_out' ) && $this->is_changeable( $period ) ) {
			// Start a mysql transaction.
			awebooking_wpdb_transaction( 'start' );

			$original_period = new Period(
				$this->original['check_in'], $this->original['check_out']
			);

			// Clear booking and availability state.
			$clear_result = Concierge::clear_booking_state( $room_unit, $original_period, $booking );
			$set_result = Concierge::set_booking_state( $room_unit, $period, $booking );

			if ( ! $clear_result || ! $set_result ) {
				awebooking_wpdb_transaction( 'rollback' );
			} else {
				awebooking_wpdb_transaction( 'commit' );
			}

			$saved_state = ( $clear_result && $set_result );
		}

		$saved_state = Concierge::set_booking_state( $this->get_room_unit(), $period, $booking );

		if ( isset( $saved_state ) && $saved_state ) {
			$this['check_in'] = $period->get_start_date()->toDateString();
			$this['check_out'] = $period->get_end_date()->toDateString();
		}

		parent::finish_save();
	}

	/**
	 * Perform delete object.
	 *
	 * @param  bool $force Not used.
	 * @return bool
	 */
	protected function perform_delete( $force ) {
		// Before we delete room unit, restore available state and set booking room to zero.
		Concierge::clear_booking_state(
			$this->get_room_unit(), $this->get_period(), $this->get_booking()
		);

		return parent::perform_delete( $force );
	}
}