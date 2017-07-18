<?php
namespace AweBooking;

use Skeleton\WP_Option;
use AweBooking\Interfaces\Config as Config_Interface;
use AweBooking\AweBooking;

class Config implements Config_Interface {
	/**
	 * WP_Option instance.
	 *
	 * @var WP_Option
	 */
	protected $wp_option;

	/**
	 * Default all settings of AweBooking.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * AweBooking configure.
	 *
	 * This class should only for get setting default and from databases.
	 *
	 * @param WP_Option|null $wp_option WP_Option class.
	 */
	public function __construct( WP_Option $wp_option ) {
		$this->wp_option = $wp_option;
		$this->prepare_default_settings();
	}

	/**
	 * //
	 *
	 * @return array
	 */
	public function all() {
		// TODO: ...
		return $this->wp_option->all();
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

		return $this->wp_option->get( $key, $default );
	}

	/**
	 * Get default setting by key.
	 *
	 * @param  string $key Default setting ket.
	 * @return null|mixed
	 */
	public function get_default( $key ) {
		return isset( $this->defaults[ $key ] ) ? $this->defaults[ $key ] : null;
	}


	public static function get_hotel_location_default() {

		if ( awebooking_config( 'location_default' ) ) {
			$term_default = get_term( intval( awebooking_config( 'location_default' ) ), AweBooking::HOTEL_LOCATION );
		} else {
			$term_default = static::get_first_term( AweBooking::HOTEL_LOCATION, array(
				'hide_empty' => false,
			) );
		}

		return $term_default;
	}


	public function get_admin_notify_emails() {
		$admin_emails = [];

		if ( awebooking_config( 'email_admin_notify' ) ) {
			$admin_emails[] = get_option( 'admin_email' );
		}

		$another_emails = awebooking_config( 'email_notify_another_emails' );
		if ( ! empty( $another_emails ) ) {
			$another_emails = array_map( 'trim', explode( ',', $another_emails ) );
			$admin_emails   = array_merge( $admin_emails, $another_emails );
		}

		return $admin_emails;
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
			'date_format'              => get_option( 'date_format' ),
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
			'email_copyright'           => get_bloginfo() . esc_html__( ' - Powered by Awebooking', 'awebooking' ),

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
