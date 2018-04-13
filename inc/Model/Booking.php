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
	 * Get payments of this booking.
	 *
	 * @return array \AweBooking\Support\Collection
	 */
	public function get_payments() {
		return $this->get_items( 'payment_item' );
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
			'post_status'   => $this['status'] ?: 'awebooking-pending',
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
