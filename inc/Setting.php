<?php
namespace AweBooking;

use Skeleton\WP_Option;
use AweBooking\Hotel\Service;
use AweBooking\Booking\Booking;
use AweBooking\Currency\Currency;

class Setting extends WP_Option {
	/**
	 * Default all settings of AweBooking.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Initiate the setting.
	 *
	 * @param string $key Option key where data will be saved.
	 */
	public function __construct( $key ) {
		parent::__construct( $key );

		$this->prepare_default_settings();
	}

	/**
	 * Get a config by key.
	 *
	 * @param  string $key     A string configure key.
	 * @param  mixed  $default Default value will be return if key not set,
	 *                         if null pass, default setting value will be return.
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		if ( is_null( $default ) ) {
			$default = $this->get_default( $key );
		}

		return parent::get( $key, $default );
	}

	/**
	 * Get default setting by key ID.
	 *
	 * @param  string $key Default setting key.
	 * @return mixed|null
	 */
	public function get_default( $key ) {
		return isset( $this->defaults[ $key ] ) ? $this->defaults[ $key ] : null;
	}

	public function get_date_format() {
		return apply_filters( 'awebooking/date_format', get_option( 'date_format' ) );
	}

	public function get_time_format() {
		return apply_filters( 'awebooking/time_format', get_option( 'time_format' ) );
	}

	/**
	 * //
	 *
	 * @return WP_Term
	 */
	public function get_default_hotel_location() {
		$default_location = (int) $this->get( 'location_default' );

		if ( $default_location && 0 < $default_location ) {
			$term = get_term( $default_location, AweBooking::HOTEL_LOCATION );
		} else {
			$terms = get_terms([
				'taxonomy'   => AweBooking::HOTEL_LOCATION,
				'hide_empty' => false,
			]);

			if ( is_array( $terms ) && isset( $terms[0] ) ) {
				$term = $terms[0];
			} else {
				$term = null;
			}
		}

		if ( ! is_wp_error( $term ) && ! is_null( $term ) ) {
			return $term;
		}

		return null;
	}

	public function get_admin_notify_emails() {
		$admin_emails = [];

		if ( $this->get( 'email_admin_notify' ) ) {
			$admin_emails[] = get_option( 'admin_email' );
		}

		$another_emails = $this->get( 'email_notify_another_emails' );
		if ( ! empty( $another_emails ) ) {
			$another_emails = array_map( 'trim', explode( ',', $another_emails ) );
			$admin_emails   = array_merge( $admin_emails, $another_emails );
		}

		return $admin_emails;
	}

	/**
	 * Return list room states.
	 *
	 * @return array
	 */
	public function get_room_states() {
		return [
			AweBooking::STATE_AVAILABLE   => esc_html__( 'Available', 'awebooking' ),
			AweBooking::STATE_UNAVAILABLE => esc_html__( 'Unavailable', 'awebooking' ),
			AweBooking::STATE_PENDING     => esc_html__( 'Pending', 'awebooking' ),
			AweBooking::STATE_BOOKED      => esc_html__( 'Booked', 'awebooking' ),
		];
	}

	/**
	 * Get all order statuses.
	 *
	 * @return array
	 */
	public function get_booking_statuses() {
		return apply_filters( 'awebooking/get_booking_statuses', [
			Booking::PENDING    => _x( 'Pending',    'Booking status', 'awebooking' ),
			Booking::PROCESSING => _x( 'Processing', 'Booking status', 'awebooking' ),
			Booking::COMPLETED  => _x( 'Completed',  'Booking status', 'awebooking' ),
			Booking::CANCELLED  => _x( 'Cancelled',  'Booking status', 'awebooking' ),
		]);
	}

	/**
	 * Get all service operations.
	 *
	 * @return array
	 */
	public function get_service_operations() {
		return apply_filters( 'awebooking/service_operations', [
			Service::OP_ADD               => esc_html__( 'Add to price', 'awebooking' ),
			Service::OP_ADD_DAILY         => esc_html__( 'Add to price per night', 'awebooking' ),
			Service::OP_ADD_PERSON        => esc_html__( 'Add to price per person', 'awebooking' ),
			Service::OP_ADD_PERSON_DAILY  => esc_html__( 'Add to price per person per night', 'awebooking' ),
			Service::OP_SUB               => esc_html__( 'Subtract from price', 'awebooking' ),
			Service::OP_SUB_DAILY         => esc_html__( 'Subtract from price per night', 'awebooking' ),
			Service::OP_INCREASE          => esc_html__( 'Increase price by % amount', 'awebooking' ),
			Service::OP_DECREASE          => esc_html__( 'Decrease price by % amount', 'awebooking' ),
		]);
	}

	/**
	 * Get list position for dropdown.
	 *
	 * @return arrays
	 */
	public function get_currency_positions() {
		$symbol = awebooking( 'currency' )->get_symbol();

		return array(
			Currency::POS_LEFT        => sprintf( esc_html__( 'Left (%s99.99)', 'awebooking' ), $symbol ),
			Currency::POS_RIGHT       => sprintf( esc_html__( 'Right (99.99%s)', 'awebooking' ), $symbol ),
			Currency::POS_LEFT_SPACE  => sprintf( esc_html__( 'Left with space (%s 99.99)', 'awebooking' ), $symbol ),
			Currency::POS_RIGHT_SPACE => sprintf( esc_html__( 'Right with space (99.99 %s)', 'awebooking' ), $symbol ),
		);
	}

	/**
	 * Prepare setup default settings.
	 *
	 * @return void
	 */
	protected function prepare_default_settings() {
		$this->defaults = apply_filters( 'awebooking/default_settings', array(
			'enable_location'          => false,

			// Currency and price format.
			'currency'                 => 'USD',
			'currency_position'        => 'left',
			'price_thousand_separator' => ',',
			'price_decimal_separator'  => '.',
			'price_number_decimals'    => 2,

			// Display.
			'page_check_availability'  => 0,
			'page_booking'             => 0,
			'page_checkout'            => 0,
			'check_availability_max_adults'   => 7,
			'check_availability_max_children' => 6,

			'email_from_name'           => '@' . get_option( 'blogname' ),
			'email_from_address'        => get_option( 'admin_email' ),
			'email_base_color'          => '#557da1',
			'email_bg_color'            => '#fdfdfd',
			'email_body_bg_color'       => '#fdfdfd',
			'email_body_text_color'     => '#505050',
			'email_copyright'           => get_bloginfo() . esc_html__( ' - Powered by AweBooking', 'awebooking' ),

			'email_admin_notify'          => true,
			'email_notify_another_emails' => '',

			'email_new_enable'            => true,
			'email_new_subject'           => '[{site_title}] New customer booking #{order_number} - {order_date}',
			'email_cancelled_enable'      => true,
			'email_complete_enable'       => true,

			// Showing price.
			'showing_price'			  => 'start_prices',
		) );
	}
}
