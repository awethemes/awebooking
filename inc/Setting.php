<?php
namespace AweBooking;

use Skeleton\WP_Option;
use AweBooking\Model\Service;
use AweBooking\Booking\Booking;
use AweBooking\Currency\Currency;
use Illuminate\Support\Arr;

class Setting extends WP_Option {
	use Deprecated\Setting_Deprecated;

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
	 * Get the setting key.
	 *
	 * @return string
	 */
	public function get_setting_key() {
		return $this->key;
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
	 * Refresh the options.
	 *
	 * @return void
	 */
	public function refresh() {
		$this->options = (array) get_option( $this->key, [] );
	}

	/**
	 * Return the price format depending on the currency position.
	 *
	 * @return string
	 */
	public function get_money_format( $position = null ) {
		$position = is_null( $position ) ? $this->get( 'currency_position' ) : $position;

		$positions = apply_filters( 'awebooking/price_format_positions', [
			Constants::CURRENCY_POS_LEFT         => '%2$s%1$s',
			Constants::CURRENCY_POS_RIGHT        => '%1$s%2$s',
			Constants::CURRENCY_POS_LEFT_SPACE   => '%2$s&nbsp;%1$s',
			Constants::CURRENCY_POS_RIGHT_SPACE  => '%1$s&nbsp;%2$s',
		]);

		$format = array_key_exists( $position, $positions )
			? $positions[ $position ]
			: $positions[ Constants::CURRENCY_POS_LEFT ];

		return apply_filters( 'awebooking/get_price_format', $format, $position );
	}

	/**
	 * Is the children bookable?
	 *
	 * @return boolean
	 */
	public function is_children_bookable() {
		return (bool) $this->get( 'children_bookable' );
	}

	/**
	 * Is the infants bookable?
	 *
	 * @return boolean
	 */
	public function is_infants_bookable() {
		return (bool) $this->get( 'infants_bookable' );
	}

	public function is_multi_location() {
		return (bool) $this->get( 'enable_location' );
	}

	/**
	 * Get default setting by key ID.
	 *
	 * @param  string $key Default setting key.
	 * @return mixed|null
	 */
	public function get_default( $key ) {
		return Arr::get( $this->defaults, $key );
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
			$term = get_term( $default_location, Constants::HOTEL_LOCATION );
		} else {
			$terms = get_terms([
				'taxonomy'   => Constants::HOTEL_LOCATION,
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
			Constants::STATE_AVAILABLE   => esc_html__( 'Available', 'awebooking' ),
			Constants::STATE_UNAVAILABLE => esc_html__( 'Unavailable', 'awebooking' ),
			Constants::STATE_PENDING     => esc_html__( 'Pending', 'awebooking' ),
			Constants::STATE_BOOKED      => esc_html__( 'Booked', 'awebooking' ),
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
			'enable_location'           => false,

			'children_bookable'             => true,
			'children_bookable_description' => esc_html__( 'Ages 2 - 12', 'awebooking' ),
			'infants_bookable'              => false,
			'infants_bookable_description'  => esc_html__( 'Under 2', 'awebooking' ),

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
			'check_availability_max_infants'  => 6,

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
			'email_new_content'           => "Your booking is on-hold until we confirm payment has been received. Your booking details are shown below for your reference:\n\nBooking #{booking_id}\n\n{contents}\n\n{customer_details}",

			'email_admin_new_content'     => "You have new booking. The booking details are shown below:\n\nBooking #{booking_id}\n\n{contents}\n\n{customer_details}",

			'email_processing_enable'     => true,
			'email_processing_content'    => "Your recent booking on {site_title} is being processed. Your booking details are shown below for your reference:\n\nBooking #{booking_id}\n\n{contents}",

			'email_cancelled_enable'      => true,
			'email_cancelled_content'     => 'Your booking #{booking_id} from {site_title} has been cancelled',

			'email_complete_enable'       => true,
			'email_complete_content'      => "Your recent booking on {site_title} has been completed. Your booking details are shown below for your reference:\n\nBooking #{booking_id}\n\n{contents}",

			// Showing price.
			'showing_price'               => 'start_prices',
		) );
	}
}
