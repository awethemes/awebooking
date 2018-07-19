<?php
namespace AweBooking\Admin;

use Awethemes\Http\Request;
use AweBooking\Support\Manager;
use AweBooking\Admin\Settings\Setting;
use AweBooking\Admin\Settings\Abstract_Setting;

class Admin_Settings extends Manager {
	/**
	 * The core settings.
	 *
	 * @var array
	 */
	protected $core_settings = [
		\AweBooking\Admin\Settings\General_Setting::class,
		\AweBooking\Admin\Settings\Hotel_Setting::class,
		\AweBooking\Admin\Settings\Taxes_Setting::class,
		\AweBooking\Admin\Settings\Checkout_Setting::class,
		\AweBooking\Admin\Settings\Appearance_Setting::class,
		\AweBooking\Admin\Settings\Email_Setting::class,
		\AweBooking\Admin\Settings\Premium_Setting::class,
	];

	/**
	 * Register a setting.
	 *
	 * @param  \AweBooking\Admin\Settings\Setting $setting The setting instance.
	 * @return \AweBooking\Admin\Settings\Setting|false
	 */
	public function register( $setting ) {
		if ( ! $setting instanceof Setting ) {
			return false;
		}

		if ( ! $setting->get_id() ) {
			return false;
		}

		return $this->drivers[ $setting->get_id() ] = $setting;
	}

	/**
	 * Setup the settings.
	 *
	 * @access private
	 */
	public function setup() {
		$settings = apply_filters( 'abrs_admin_settings', $this->core_settings );

		foreach ( $settings as $setting ) {
			$this->register( $this->plugin->make( $setting ) );
		}

		do_action( 'abrs_register_admin_settings', $this );
	}

	/**
	 * Perform handle save a setting.
	 *
	 * @param  string                  $setting The setting name.
	 * @param  \Awethemes\Http\Request $request The http request instance.
	 * @return void
	 */
	public function save( $setting, Request $request ) {
		// Leave if given an empty setting name.
		if ( ! is_string( $setting ) || empty( $setting ) ) {
			return;
		}

		// Makes sure that request was referred from admin page.
		check_admin_referer( 'awebooking-settings' );

		// Handle save the setting.
		if ( apply_filters( 'abrs_handle_save_setting_' . $setting, true ) && $instance = $this->get( $setting ) ) {
			abrs_rescue( function () use ( $instance, $request ) {
				$instance->save( $request );
			});
		}

		// Fire update_setting actions.
		do_action( 'abrs_update_setting_' . $setting, $this );
		do_action( 'abrs_update_settings', $setting, $this );

		// Add an success notices.
		abrs_admin_notices( esc_html__( 'Your settings have been saved.', 'awebooking' ), 'success' )->dialog();

		// Force flush_rewrite_rules.
		@flush_rewrite_rules();

		// Fire abrs_settings_updated action.
		do_action( 'abrs_settings_updated', $this );
	}

	/**
	 * Gets the default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		$defaults = [ [] ];

		foreach ( $this->all() as $setting ) {
			if ( $setting instanceof Abstract_Setting ) {
				$defaults[] = abrs_collect( $setting->prop( 'fields' ) )
					->whereNotIn( 'type', [ 'title', 'include' ] )
					->where( 'default', '!==', null )
					->pluck( 'default', 'id' )
					->all();
			}
		}

		return array_merge( ...$defaults );
	}

	/**
	 * Returns all translatable fields.
	 *
	 * @return array
	 */
	public function get_translatable_fields() {
		$translatable = [ [] ];

		foreach ( $this->all() as $setting ) {
			if ( $setting instanceof Abstract_Setting ) {
				$translatable[] = abrs_collect( $setting->prop( 'fields' ) )
					->where( 'translatable', '=', true )
					->pluck( 'id' )
					->all();
			}
		}

		return array_unique( array_merge( ... $translatable ) );
	}
}
