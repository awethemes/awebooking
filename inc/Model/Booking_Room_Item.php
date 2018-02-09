<?php
namespace AweBooking\Model;

use WP_Error;
use AweBooking\Factory;
use AweBooking\Model\Stay;
use AweBooking\Model\Guest;
use AweBooking\Reservation\Creator;
use AweBooking\Reservation\Searcher\Checker;
use AweBooking\Calendar\Period\Period;
use AweBooking\Support\Utils as U;

use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Event\State_Event;
use AweBooking\Calendar\Event\Booking_Event;

class Booking_Room_Item extends Booking_Item {
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
		'check_in'     => null,
		'check_out'    => null,
		'adults'       => 0,
		'children'     => 0,
		'infants'      => 0,
		'subtotal'     => 0, // Pre-discount.
		'total'        => 0,
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
		'infants'  => 'int',
		'subtotal' => 'float',
		'total'    => 'float',
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
		'infants'   => '_infants',
		'check_in'  => '_check_in',
		'check_out' => '_check_out',
		'subtotal'  => '_line_subtotal',
		'total'     => '_line_total',
	];

	/**
	 * The permalinks actions.
	 *
	 * @var array
	 */
	protected $permalinks = [
		'edit'   => '/booking/{booking}/room/{item}/edit',
		'swap'   => '/booking/{booking}/room/{item}/swap',
		'update' => '/booking/{booking}/room/{item}',
		'delete' => '/booking/{booking}/room/{item}',
	];

	/**
	 * Force modify the check_in and check_out attribute.
	 *
	 * @var boolean
	 */
	protected $force_modify_stay = false;

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
		if ( $room instanceof Room ) {
			$this->attributes['room_id'] = $room->get_id();
		} else {
			$this->attributes['room_id'] = absint( $room );
		}
	}

	/**
	 * Returns instance of room unit.
	 *
	 * @return \AweBooking\Model\Room
	 */
	public function get_room_unit() {
		return Factory::get_room_unit( $this->get_room_id() );
	}

	/**
	 * Resolve the room_type from current room_unit.
	 *
	 * @return \AweBooking\Model\Room_Type|null
	 */
	public function resolve_room_type() {
		if ( empty( $this->room_id ) ) {
			return;
		}

		return U::rescue( function() {
			return $this->get_room_unit()->get_room_type();
		});
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
	 * Get the Stay of check-in, check-out.
	 *
	 * @return \AweBooking\Model\Stay|null
	 */
	public function get_stay() {
		$stay = U::rescue( function () {
			return new Stay( $this->get_check_in(), $this->get_check_out() );
		});

		return apply_filters( $this->prefix( 'get_stay' ), $stay, $this );
	}

	/**
	 * Returns nights stayed of this line item.
	 *
	 * @return int
	 */
	public function get_nights_stayed() {
		$stay = $this->get_stay();

		return ! is_null( $stay ) ? $stay->nights() : 0;
	}

	/**
	 * Returns number adults of line item.
	 *
	 * @return int
	 */
	public function get_adults() {
		return apply_filters( $this->prefix( 'get_adults' ), $this['adults'], $this );
	}

	/**
	 * Returns number children of line item.
	 *
	 * @return int
	 */
	public function get_children() {
		return apply_filters( $this->prefix( 'get_children' ), $this['children'], $this );
	}

	/**
	 * Returns number infants of line item.
	 *
	 * @return int
	 */
	public function get_infants() {
		return apply_filters( $this->prefix( 'get_infants' ), $this['infants'], $this );
	}

	/**
	 * Create the Guest instance.
	 *
	 * @return \AweBooking\Model\Guest|null
	 */
	public function get_guest() {
		$guest = U::rescue( function () {
			return new Guest( $this->get_adults(), $this->get_children(), $this->get_infants() );
		});

		return apply_filters( $this->prefix( 'get_guest' ), $guest, $this );
	}

	/**
	 * Gets subtotal.
	 *
	 * @return float
	 */
	public function get_subtotal() {
		return apply_filters( $this->prefix( 'get_subtotal' ), $this['subtotal'], $this );
	}

	/**
	 * Get total.
	 *
	 * @return float
	 */
	public function get_total() {
		return apply_filters( $this->prefix( 'get_total' ), $this['total'], $this );
	}

	/**
	 * Set line subtotal (before discounts).
	 *
	 * @param mixed $value Input value.
	 */
	public function set_subtotal( $value ) {
		$this->attributes['subtotal'] = awebooking_sanitize_price( $value );
	}

	/**
	 * Set line total (after discounts).
	 *
	 * @param mixed $value Input value.
	 */
	public function set_total( $value ) {
		$this->attributes['total'] = awebooking_sanitize_price( $value );

		// Subtotal cannot be less than total.
		if ( ! $this->get_subtotal() || $this->get_subtotal() < $this->get_total() ) {
			$this->set_subtotal( $value );
		}
	}

	/**
	 * Print the room_item label.
	 *
	 * @return string
	 */
	public function print_label() {
		$name = $this->get_name();

		if ( ! empty( $name ) ) {
			print '<strong>' . esc_html( $name ) . '</strong>';
			return;
		}

		printf( '<span><strong>%s</strong> (</span>(%s)</span>)</span>',
			esc_html( U::optional( $this->get_room_unit() )->get_name() ),
			esc_html( U::optional( $this->resolve_room_type() )->get_name() )
		);
	}

	/**
	 * Modify the stay date.
	 *
	 * @param  \AweBooking\Model\Stay $stay The new stay.
	 * @return boolean|WP_Error
	 */
	public function modify_stay( Stay $stay ) {
		// Prevent change on non-exists object.
		if ( ! $this->exists() ) {
			return false;
		}

		$this->force_modify_stay = true;

		try {
			$stay->require_minimum_nights();
		} catch ( \Exception $e ) {
			return new WP_Error( 'date_error', $e->getMessage() );
		}

		// Check the new stay date to ensure we have no any conflicts.
		if ( ! $this->can_change_stay( $stay ) ) {
			return new WP_Error( 'change_error', esc_html__( 'Dates could not be changed because at least one of the rooms is occupied on the selected dates.', 'awebooking' ) );
		}

		// Perfom set the check-in and check-out attributes.
		$this->attributes['check_in'] = $stay->get_check_in()->toDateString();
		$this->attributes['check_out'] = $stay->get_check_out()->toDateString();

		$saved = $this->save();

		$this->force_modify_stay = false;

		return $saved;
	}

	/**
	 * Determines whether this booking item can change to other stay date.
	 *
	 * @param  \AweBooking\Model\Stay $other_stay Other stay date.
	 * @return boolean
	 */
	public function can_change_stay( Stay $other_stay ) {
		if ( ! $this->exists() ) {
			return false;
		}

		try {
			$other_stay->require_minimum_nights();
		} catch ( \Exception $e ) {
			return false;
		}

		// Get and validate the room_unit.
		$room_unit = $this->get_room_unit();
		if ( ! $room_unit->exists() ) {
			return false;
		}

		$original_period = new Period(
			$this->original['check_in'], $this->original['check_out']
		);

		$checker = new Checker;
		$other_period = $other_stay->to_period();

		// If new period inside the current-period,
		// so it alway can be change.
		if ( $original_period->contains( $other_period ) ) {
			return true;
		}

		// If both period object not overlaps, so we just
		// determines new period is available or not.
		if ( ! $original_period->overlaps( $other_period ) ) {
			return $checker->is_available_for( $room_unit, $other_stay );
		}

		// Create an array difference between two Period.
		// @see http://period.thephpleague.com/3.0/api/comparing/#perioddiff.
		$diff = $original_period->diff( $other_period );

		// Loop each piece of diff-period, if one of them
		// unavailable for changing just leave and return false.
		foreach ( $diff as $piece ) {
			if ( $original_period->contains( $piece ) ) {
				continue;
			}

			if ( ! $checker->is_available_for( $room_unit, Stay::from_period( $piece ) ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Peform apply the calendar state.
	 *
	 * @return boolean
	 */
	public function apply_calendar_state() {
		if ( ! $this->exists() ) {
			return false;
		}

		$booking = $this->get_booking();
		if ( $booking->exists() ) {
			return false;
		}

		$room_unit = $this->get_room_unit();
		if ( ! $room_unit->exists() ) {
			return false;
		}

		$creator = new Creator;
		$resource = new Resource( $room_unit->get_id() );

		try {
			$state_event = new State_Event( $resource, $this->get_check_in(), $this->get_check_out(), $booking->get_state_status() );
			$booking_event = new Booking_Event( $resource, $this->get_check_in(), $this->get_check_out(), $booking );
		} catch ( sException $e ) {
			return false;
		}

		$creator->create_state_calendar( $resource )
				->store( $state_event );

		$creator->create_booking_calendar( $resource )
				->store( $booking_event );

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function finish_save() {
		parent::finish_save();

		if ( $this->recently_created ) {
			$this->apply_calendar_state();
		}
	}

	/**
	 * Perform revert attributes when update.
	 *
	 * @return void
	 */
	protected function updating() {
		// Prevent user changes `room_id` and `booking_id`.
		if ( $this->is_dirty( 'room_id', 'booking_id' ) ) {
			$this->revert_attribute( 'room_id' );
			$this->revert_attribute( 'booking_id' );
		}

		if ( ! $this->force_modify_stay && $this->is_dirty( 'check_in', 'check_out' ) ) {
			$this->revert_attribute( 'check_in' );
			$this->revert_attribute( 'check_out' );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_delete( $force ) {
		$deleted = parent::perform_delete( $force );

		// Before we delete room unit, restore available state and set booking room to zero.
		// TODO: ...

		return $deleted;
	}
}
