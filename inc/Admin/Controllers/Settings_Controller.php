<?php
namespace AweBooking\Admin\Controllers;

use Skeleton\CMB2\CMB2;
use Awethemes\Http\Request;
use AweBooking\Setting;
use AweBooking\Admin\Admin_Settings;
use AweBooking\Support\Utils as U;

class Settings_Controller extends Controller {
	/**
	 * The Setting instance.
	 *
	 * @var \AweBooking\Setting
	 */
	protected $setting;

	/**
	 * The Admin_Setting instance.
	 *
	 * @var \AweBooking\Admin\Admin_Settings
	 */
	protected $admin_settings;

	/**
	 * Constructor.
	 *
	 * @param Setting        $setting        The Setting instance.
	 * @param Admin_Settings $admin_settings The Admin_Setting instance.
	 */
	public function __construct( Setting $setting, Admin_Settings $admin_settings ) {
		$this->setting = $setting;
		$this->admin_settings = $admin_settings;
	}

	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function store( Request $request ) {
		$request->verify_nonce( $this->admin_settings->nonce(), $this->admin_settings->nonce() )
				->validate( [ '_setting_section' => 'required' ] );

		// Create new settings based on current requested section.
		$settings = $this->create_section_settings( $request['_setting_section'] );

		$sanitized = $settings->get_sanitized_values( $request->all() );

		// If failed the validation, just redirect back.
		if ( $settings->fails() ) {
			awebooking( 'admin_notices' )->error( esc_html__( 'Input data has failed validation, please check again.', 'awebooking' ) );

			return $this->redirect()->back( $this->fallback_redirect_back() );
		}

		// Save the sanitized values.
		$this->admin_settings->store( $sanitized );

		awebooking( 'admin_notices' )->success( esc_html__( 'Settings updated successfully!', 'awebooking' ) );

		return $this->redirect()->back( $this->fallback_redirect_back() );
	}

	/**
	 * Fallback redirect back.
	 *
	 * @return string
	 */
	protected function fallback_redirect_back() {
		return admin_url( 'admin.php?page=awebooking-settings' );
	}

	/**
	 * Create new admin_settings by given a sepcial section.
	 *
	 * @param  string $section The section name.
	 * @return \Skeleton\CMB2\CMB2
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function create_section_settings( $section ) {
		$section = $this->admin_settings->get_section( $section );

		// Throw an exception if we not found the section of admin_settings.
		if ( is_null( $section ) ) {
			throw new \InvalidArgumentException( esc_html__( 'Not found the setting section.', 'awebooking' ) );
		}

		// Clone the admin_settings.
		$settings = new CMB2([
			'id'          => $this->admin_settings->cmb_id,
			'hookup'      => false,
			'cmb_styles'  => false,
		], $this->admin_settings->object_id );

		// Ensure we working on an 'options-page'.
		$settings->object_type( 'options-page' );

		// Collect fields only requested section.
		$fields = U::collect( $this->admin_settings->prop( 'fields' ) )
			->reject( function( $field ) use ( $section ) {
				return ( empty( $field['section'] ) || ( $field['section'] !== $section->id ) );
			});

		$fields->each(function( $field ) use ( $settings ) {
			$settings->add_field( $field );
		});

		return $settings;
	}
}
