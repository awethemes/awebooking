<?php
namespace AweBooking\Model;

use AweBooking\Constants;

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
	 * Returns the booking number.
	 *
	 * Apply filters allow users can be change this.
	 *
	 * @return string
	 */
	public function get_booking_number() {
		return apply_filters( $this->prefix( 'get_booking_number' ), $this->get_id(), $this );
	}

	public function get_formatted_guest_name() {
		return $this->get( 'customer_first_name' );
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
			return;
		}

		if ( ! array_key_exists( $type, $this->items ) ) {
			$items = ! $this->exists() ? [] : abrs_get_booking_items( $this->id, $type );

			$this->items[ $type ] = abrs_collect( $items )
				->pluck( 'booking_item_id' )
				->map_into( $classmap[ $type ] );
		}

		return $this->items[ $type ];
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
	 * Returns a list of fees within this booking.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_fees() {
		return $this->get_items( 'fee' );
	}

	/**
	 * Returns a list of taxes within this booking.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_taxes() {
		return $this->get_items( 'tax' );
	}

	/**
	 * Get payments of this booking.
	 *
	 * @return array \AweBooking\Support\Collection
	 */
	public function get_payments() {
		return $this->get_items( 'payment_item' );
	}

	/**
	 * Returns the last payment item.
	 *
	 * @param  string $state Optional, filter payment matching with a state.
	 * @return \AweBooking\Model\Booking\Payment_Item|null
	 */
	public function get_last_payment( $state = null ) {
		return $this->get_payments()->last();
	}

	/**
	 * Get the amount of total paid.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_paid() {
		return $this->get_payments()->reduce( function( $total, $item ) {
			return $total->add( 0 );
		}, abrs_decimal( 0 ) );
	}

	/**
	 * Get the amount of balance_due.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_balance_due() {
		return abrs_decimal( $this->get_total() )->sub( $this->get_paid() );
	}

	/**
	 * Calculate totals by looking at the contents of the booking.
	 *
	 * @param  bool $and_taxes Calc taxes if true.
	 * @return float calculated grand total.
	 */
	public function calculate_totals( $with_taxes = true ) {
		$room_subtotal      = 0;
		$room_total         = 0;
		$room_subtotal_tax  = 0;
		$room_total_tax     = 0;
		$service_total      = 0;
		$fee_total          = 0;

		do_action( $this->prefix( 'before_calculate_totals' ), $with_taxes, $this );

		// Sum the room costs.
		foreach ( $this->get_rooms() as $room ) {
			$room_subtotal = $room_subtotal->add( $room->get_subtotal() );
			$room_total    = $room_total->add( $room->get_total() );
		}

		// Sum the service costs.
		// ...

		// Sum fee costs.
		foreach ( $this->get_fees() as $item ) {
			$amount = $item->get_amount();

			if ( 0 > $amount ) {
				$item->set_total( $amount );
				$max_discount = round( $room_total + $fee_total, wc_get_price_decimals() ) * -1;

				if ( $item->get_total() < $max_discount ) {
					$item->set_total( $max_discount );
				}
			}

			$fee_total += $item->get_total();
		}

		// Calculate taxes for rooms, discounts.
		// Note: This also triggers save().
		if ( $with_taxes ) {
			$this->calculate_taxes();
		}

		// Sum the taxes.
		foreach ( $this->get_rooms() as $room ) {
			$room_subtotal_tax = $room_subtotal_tax->add( $room->get_subtotal_tax() );
			$room_total_tax    = $room_total_tax->add( $room->get_total_tax() );
		}

		$this->set_discount_total( $cart_subtotal - $room_total );
		$this->set_discount_tax( $room_subtotal_tax - $room_total_tax );

		$this->set_total( $room_total + $fee_total + $this->get_cart_tax() );

		do_action( $this->prefix( 'after_calculate_totals' ), $with_taxes, $this );

		$this->save();

		return $this->get_total();
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

		$this->status_transition = null;

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
		abrs_add_booking_note( $this, $transition_note, false, false );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		// Reset the items after save.
		$this->items = [];

		$this['status']        = $this->instance->post_status;
		$this['date_created']  = $this->instance->post_date;
		$this['date_modified'] = $this->instance->post_modified;
		$this['customer_note'] = $this->instance->post_excerpt;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_update( array $dirty ) {
		$this->update_the_post([
			'post_status'   => $this['status'] ? abrs_prefix_booking_status( $this['status'] ) : 'awebooking-pending',
			'post_date'     => $this['date_created'] ? (string) abrs_date_time( $this['date_created'] ) : '',
			'post_modified' => $this['date_modified'] ? (string) abrs_date_time( $this['date_modified'] ) : '',
			'post_excerpt'  => $this['customer_note'],
		]);

		// Allow continue save meta-data if nothing to update post.
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function finish_save() {
		parent::finish_save();

		$this->apply_status_transition();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), [
			'status'                  => '',
			'source'                  => '',
			'date_created'            => null,
			'date_modified'           => null,
			'check_in_date'           => '',
			'check_out_date'          => '',
			'arrival_time'            => '',
			'customer_note'           => '',

			'discount_tax'            => 0,
			'discount_total'          => 0,
			'total'                   => 0,
			'total_tax'               => 0,
			'paid'                    => 0,

			// Customer attributes.
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

			// Payment logs.
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
			'check_in_date'           => '_check_in_date',
			'check_out_date'          => '_check_out_date',
			'arrival_time'            => '_arrival_time',

			'discount_tax'            => '_discount_tax',
			'discount_total'          => '_discount_total',
			'total'                   => '_total',
			'total_tax'               => '_total_tax',
			'paid'                    => '_paid',

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
		}

		return apply_filters( $this->prefix( 'sanitize_attribute' ), $value, $key );
	}
}
