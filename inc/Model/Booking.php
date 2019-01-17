<?php

namespace AweBooking\Model;

use AweBooking\Constants;
use AweBooking\Model\Booking\Item;
use AweBooking\Support\Period_Collection;

class Booking extends Model {
	/**
	 * Name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = Constants::BOOKING;

	/**
	 * The booking items group by type.
	 *
	 * @var array
	 */
	protected $items = [];

	/**
	 * Stores data about status changes so relevant hooks can be fired.
	 *
	 * @var array|null
	 */
	protected $status_transition;

	/**
	 * Mark true to force calculate_totals.
	 *
	 * @var boolean
	 */
	protected $force_calculate_totals = false;

	public function get_check_in_date() {
		$items = $this->get_line_items();

		if ( 0 === count( $items ) ) {
			return '';
		}

		if ( count( $items ) === 1 ) {
			return abrs_optional( $items->first() )->get( 'check_in' );
		}

		return $this->get( 'check_in_date' );
	}

	public function get_check_out_date() {
		$items = $this->get_line_items();

		if ( 0 === count( $items ) ) {
			return '';
		}

		if ( count( $items ) === 1 ) {
			return abrs_optional( $items->first() )->get( 'check_out' );
		}

		return $this->get( 'check_out_date' );
	}

	/**
	 * Returns the booking number.
	 *
	 * Apply filters allow users can be change this.
	 *
	 * @return string
	 */
	public function get_booking_number() {
		return apply_filters( $this->prefix( 'get_booking_number' ), $this->get_id(), $this );
	}

	/**
	 * Returns the customer full name.
	 *
	 * @return string
	 */
	public function get_customer_fullname() {
		$title = $this->get( 'customer_title' );

		$titles = abrs_list_common_titles();

		$customer_name = trim( sprintf( '%1$s %2$s %3$s',
			isset( $titles[ $title ] ) ? $titles[ $title ] : '',
			$this->get( 'customer_first_name' ),
			$this->get( 'customer_last_name' )
		));

		return apply_filters( $this->prefix( 'get_customer_fullname' ), $customer_name, $this );
	}

	/**
	 * Return an array of items within this booking.
	 *
	 * @param  string $type Type of line items to get.
	 * @return \AweBooking\Support\Collection|null
	 */
	public function get_items( $type = 'line_item' ) {
		$classmap = abrs_booking_item_classmap();

		if ( ! array_key_exists( $type, $classmap ) ) {
			return null;
		}

		if ( ! array_key_exists( $type, $this->items ) ) {
			$items = ! $this->exists() ? [] : abrs_get_raw_booking_items( $this->id, $type );

			$this->items[ $type ] = abrs_collect( $items )
				->pluck( 'booking_item_id' )
				->map_into( $classmap[ $type ] );
		}

		return $this->items[ $type ];
	}

	/**
	 * Get rooms of this booking.
	 *
	 * @return \AweBooking\Support\Collection \AweBooking\Model\Booking\Room_Item[]
	 */
	public function get_line_items() {
		return $this->get_rooms();
	}

	/**
	 * Get rooms of this booking.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_rooms() {
		return $this->get_items( 'line_item' );
	}

	/**
	 * Get payments of this booking.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_payments() {
		return $this->get_items( 'payment_item' );
	}

	/**
	 * Get services of this booking.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_services() {
		return $this->get_items( 'service_item' );
	}

	/**
	 * Returns a list of fees within this booking.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_fees() {
		return $this->get_items( 'fee_item' );
	}

	/**
	 * Returns a list of taxes within this booking.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_taxes() {
		return $this->get_items( 'tax_item' );
	}

	/**
	 * Flush the items.
	 *
	 * @return void
	 */
	public function flush_items() {
		$this->items = [];

		wp_cache_delete( $this->get_id(), 'awebooking_booking_items' );
	}

	/**
	 * Remove all items fromd database.
	 *
	 * @return void
	 */
	public function remove_items( $type = null ) {
		$items = abrs_get_raw_booking_items( $this->get_id(), 'all' );

		foreach ( $items as $item ) {
			abrs_optional( abrs_get_booking_item( $item ) )->delete();
		}

		$this->flush_items();
	}

	/**
	 * [force_calculate_totals description]
	 *
	 * @param  boolean $force [description]
	 * @return [type]
	 */
	public function force_calculate_totals( $force = true ) {
		$this->force_calculate_totals = $force;

		return $this;
	}

	/**
	 * Calculate totals by looking at the contents of the booking.
	 *
	 * @param  bool $and_taxes Calc taxes if true.
	 * @return void
	 */
	public function calculate_totals( $with_taxes = true ) {
		do_action( $this->prefix( 'before_calculate_totals' ), $with_taxes, $this );

		$room_subtotal    = 0;
		$room_total       = 0;
		$service_subtotal = 0;
		$service_total    = 0;
		$fee_total        = 0;

		// Sum the room costs.
		foreach ( $this->get_rooms() as $room ) {
			$room_subtotal += $room->get( 'subtotal' );
			$room_total    += $room->get( 'total' );
		}

		// Sum the service costs.
		foreach ( $this->get_services() as $service ) {
			$service_subtotal += $service->get( 'subtotal' );
			$service_total    += $service->get( 'total' );
		}

		// Sun the fees.
		foreach ( $this->get_fees() as $fee ) {
			$fee_total += $fee->get( 'amount' );
		}

		$this->set_attribute( 'subtotal', $room_subtotal + $service_total + $fee_total );
		$this->set_attribute( 'discount_total', $room_subtotal - $room_total );
		$this->set_attribute( 'total', $this->get( 'subtotal' ) - $this->get( 'discount_total' ) );

		$this->set_attribute( 'tax_total', $this->get_rooms()->sum( 'total_tax' ) );

		$this->set_attribute( 'paid', $this->get_payments()->sum( 'amount' ) );
		$this->set_attribute( 'balance_due', $this->attributes['total'] - $this->attributes['paid'] );

		do_action( $this->prefix( 'after_calculate_totals' ), $with_taxes, $this );

		$this->save();
	}

	/**
	 * Determines if booking can be edited.
	 *
	 * @return bool
	 */
	public function is_editable() {
		return apply_filters( $this->prefix( 'is_editable' ),
			in_array( $this->get_status(), [ 'pending', 'on-hold', 'deposit', 'auto-draft' ], true ), $this
		);
	}

	/**
	 * Determines if this booking have multiple rooms.
	 *
	 * @return boolean
	 */
	public function is_multiple_rooms() {
		return count( $this->get_rooms() ) > 1;
	}

	/**
	 * Get the booking status without 'awebooking-' prefix.
	 *
	 * @return string
	 */
	public function get_status() {
		$status = $this->get( 'status' );

		if ( 0 === strpos( $status, 'awebooking-' ) ) {
			$status = substr( $status, 11 );
		}

		return $status;
	}

	/**
	 * Sets the booking status.
	 *
	 * @param  string $new_status Status to change the booking to.
	 * @return array
	 */
	public function set_status( $new_status ) {
		$old_status = $this->get( 'status' );
		$new_status = abrs_prefix_booking_status( $new_status );

		// If setting the status, ensure it's set to a valid status.
		if ( $this->exists() ) {
			$valid_statuses = array_keys( abrs_get_booking_statuses() );

			if ( 'trash' !== $new_status && ! in_array( $new_status, $valid_statuses ) ) {
				$new_status = 'pending';
			}

			// If the old status is set but unknown (e.g. draft) assume its pending for action usage.
			if ( $old_status && 'trash' !== $old_status && ! in_array( $old_status, $valid_statuses ) ) {
				$old_status = 'pending';
			}

			// Set the status transition.
			if ( $old_status && $old_status !== $new_status ) {
				$this->status_transition = [ $old_status, $new_status ];
			}
		}

		// Set the new status.
		$this->set_attribute( 'status', $new_status );

		return $this->status_transition;
	}

	/**
	 * Updates status of booking immediately.
	 *
	 * @param  string $new_status Status to change the booking to.
	 * @param  string $note       Optional note to add.
	 * @return bool
	 */
	public function update_status( $new_status, $note = '' ) {
		if ( ! $this->exists() ) {
			return false;
		}

		try {
			abrs_add_booking_note( $this, $note, false, true );

			$this->set_status( $new_status );
			$this->save();

			return true;
		} catch ( \Exception $e ) {
			abrs_logger()->error( sprintf( 'Update status of booking #%d failed!', $this->get_id() ), [ 'exception' => $e ] );
			return false;
		}
	}

	/**
	 * Handle the status transition.
	 *
	 * @return void
	 */
	protected function apply_status_transition() {
		if ( is_null( $this->status_transition ) ) {
			return;
		}

		// Retrive the status transition then flush it.
		list( $old_status, $new_status ) = $this->status_transition;

		do_action( $this->prefix( 'status_change' ), $new_status, $old_status, $this );

		if ( ! empty( $old_status ) ) {
			/* translators: 1: Old booking status 2: New booking status */
			$transition_note = sprintf( __( 'Booking status changed from %1$s to %2$s.', 'awebooking' ), abrs_get_booking_status_name( $old_status ), abrs_get_booking_status_name( $new_status ) );

			do_action( $this->prefix( 'status_changed' ), $new_status, $old_status, $this );
		} else {
			/* translators: %s: new booking status */
			$transition_note = sprintf( __( 'Booking status set to %s.', 'awebooking' ), abrs_get_booking_status_name( $new_status ) );
		}

		// Log the transition occurred in the notes.
		abrs_add_booking_note( $this->get_id(), $transition_note, false, true );
	}

	/**
	 * Set the public view token.
	 *
	 * @param string $token
	 */
	public function set_public_token( $token ) {
		$this->update_meta( '_public_token', $token );
	}

	/**
	 * Return the public view token.
	 *
	 * @return string
	 */
	public function get_public_token() {
		return $this->get_meta( '_public_token' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this['status']        = $this->instance->post_status;
		$this['date_created']  = $this->instance->post_date;
		$this['date_modified'] = $this->instance->post_modified;
		$this['customer_note'] = $this->instance->post_excerpt;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_insert() {
		$this->attributes['version'] = awebooking()->version();

		if ( empty( $this->attributes['currency'] ) ) {
			$this->attributes['currency'] = abrs_current_currency();
		}

		$insert_id = wp_insert_post([
			'post_type'     => $this->object_type,
			'post_title'    => sprintf( esc_html__( 'Booking &ndash; %s', 'awebooking' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Booking date parsed by strftime', 'awebooking' ) ) ), // @codingStandardsIgnoreLine
			'post_excerpt'  => $this->get( 'customer_note' ),
			'post_status'   => $this['status'] ? $this->get( 'status' ) : 'awebooking-pending',
			'post_date'     => $this['post_date'] ? (string) abrs_date_time( $this['post_date'] ) : current_time( 'mysql' ),
			'post_password' => uniqid( 'booking_', true ),
			'ping_status'   => 'closed',
			'post_author'   => 1,
		], true );

		if ( ! is_wp_error( $insert_id ) ) {
			return $insert_id;
		}

		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_update( array $dirty ) {
		if ( $this->get_changes_only( $dirty, [ 'date_created', 'date_modified', 'status', 'customer_note' ] ) ) {
			$this->update_the_post([
				'post_excerpt'  => $this->get( 'customer_note' ),
				'post_status'   => $this['status'] ? $this->get( 'status' ) : 'awebooking-pending',
				'post_date'     => $this['date_created'] ? (string) abrs_date_time( $this->get( 'date_created' ) ) : '',
				'post_modified' => $this['date_modified'] ? (string) abrs_date_time( $this->get( 'date_modified' ) ) : '',
			]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function finish_save() {
		parent::finish_save();

		$this->apply_status_transition();

		if ( $this->status_transition ) {
			foreach ( $this->get_line_items() as $line_item ) {
				/* @var $line_item \AweBooking\Model\Booking\Room_Item */
				if ( in_array( $this->get_status(), [ 'checked-out', 'cancelled' ] ) ) {
					$line_item->clear_booking_event();
				} else {
					$line_item->apply_booking_event();
				}
			}
		}

		$this->status_transition = null;
	}

	/**
	 * Returns Period collection of booking items.
	 *
	 * @return void
	 */
	public function setup_dates() {
		$periods = $this->get_line_items()->map(function( Item $item ) {
			return $item->get_timespan()->get_period();
		});

		if ( 0 === count( $periods ) ) {
			$this->attributes['nights_stay']    = 0;
			$this->attributes['check_in_date']  = '';
			$this->attributes['check_out_date'] = '';
		} else {
			$periods = new Period_Collection( $periods );

			$collapsed = $periods->collapse();

			$this->attributes['nights_stay']    = $periods->is_continuous() ? $collapsed->days : -1;
			$this->attributes['check_in_date']  = $collapsed->get_start_date()->format( 'Y-m-d' );
			$this->attributes['check_out_date'] = $collapsed->get_end_date()->format( 'Y-m-d' );
		}

		$this->save();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function clean_cache() {
		$this->flush_items();

		clean_post_cache( $this->get_id() );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), [
			'status'                  => '',
			'source'                  => '',
			'hotel_id'                => 0,
			'created_via'             => '',
			'date_created'            => null,
			'date_modified'           => null,
			'arrival_time'            => '',
			'nights_stay'             => 0,
			'customer_note'           => '',
			'check_in_date'           => '',
			'check_out_date'          => '',
			'discount_tax'            => 0,
			'total_tax'               => 0,
			'discount_total'          => 0,
			'total'                   => 0,
			'subtotal'                => 0,
			'paid'                    => 0,
			'balance_due'             => 0,
			'customer_id'             => 0,
			'customer_title'          => '',
			'customer_first_name'     => '',
			'customer_last_name'      => '',
			'customer_address'        => '',
			'customer_address_2'      => '',
			'customer_city'           => '',
			'customer_state'          => '',
			'customer_postal_code'    => '',
			'customer_country'        => '',
			'customer_company'        => '',
			'customer_phone'          => '',
			'customer_email'          => '',
			'language'                => '',
			'currency'                => '',
			'customer_ip_address'     => '',
			'customer_user_agent'     => '',
			'version'                 => '',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'source'                  => '_source',
			'hotel_id'                => '_hotel_id',
			'created_via'             => '_created_via',
			'arrival_time'            => '_arrival_time',
			'nights_stay'             => '_nights_stay',
			'check_in_date'           => '_check_in_date',
			'check_out_date'          => '_check_out_date',
			'discount_total'          => '_discount_total',
			'discount_tax'            => '_discount_tax',
			'total'                   => '_total',
			'subtotal'                => '_subtotal',
			'total_tax'               => '_total_tax',
			'paid'                    => '_paid',
			'balance_due'             => '_balance_due',
			'customer_id'             => '_customer_id',
			'customer_title'          => '_customer_title',
			'customer_first_name'     => '_customer_first_name',
			'customer_last_name'      => '_customer_last_name',
			'customer_address'        => '_customer_address',
			'customer_address_2'      => '_customer_address_2',
			'customer_city'           => '_customer_city',
			'customer_state'          => '_customer_state',
			'customer_postal_code'    => '_customer_postal_code',
			'customer_country'        => '_customer_country',
			'customer_company'        => '_customer_company',
			'customer_phone'          => '_customer_phone',
			'customer_email'          => '_customer_email',
			'version'                 => '_version',
			'currency'                => '_currency',
			'customer_ip_address'     => '_customer_ip_address',
			'customer_user_agent'     => '_customer_user_agent',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'paid':
			case 'total':
			case 'total_tax':
			case 'discount_tax':
			case 'discount_total':
				$value = abrs_sanitize_decimal( $value );
				break;

			case 'customer_note':
				$value = abrs_sanitize_html( $value );
				break;

			case 'customer_id':
				$value = absint( $value );
				break;
		}

		return apply_filters( $this->prefix( 'sanitize_attribute' ), $value, $key );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prefix( $hook_name ) {
		return 'abrs_booking_' . $hook_name;
	}
}
