<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Gateway\Gateway;
use AweBooking\Admin\Admin_Settings;

class Checkout_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	public function registers( Admin_Settings $settings ) {
		$checkout = $settings->add_panel( 'checkout', [
			'title'      => esc_html__( 'Checkout', 'awebooking' ),
			'capability' => 'manage_awebooking',
		]);

		$checkout_options = $settings->add_section( 'checkout-options', [
			'title'      => esc_html__( 'Checkout Options', 'awebooking' ),
			'capability' => 'manage_awebooking',
		])->as_child_of( 'checkout' );

		$checkout_options->add_field( array(
			'id'         => 'gateway_order',
			'type'       => 'gateway_display_order',
			'name'       => esc_html__( 'Gateway display order', 'awebooking' ),
		) );

		// Register the gateways custom fields.
		foreach ( awebooking( 'gateways' )->all() as $gateway ) {
			if ( ! $gateway->has_settings() ) {
				continue;
			}

			$this->register_gateway_settings( $settings, $gateway );
		}
	}

	/**
	 * Register the gateway settings.
	 *
	 * @param  \AweBooking\Admin\Admin_Settings $settings The admin settings instance.
	 * @param  \AweBooking\Gateway\Gateway      $gateway  The gateway.
	 * @return void
	 */
	protected function register_gateway_settings( Admin_Settings $settings, Gateway $gateway ) {
		$prefix = sanitize_key( 'gateway_' . $gateway->get_method() );

		$section = $settings->add_section( $prefix, [
			'title'      => $gateway->get_method_title(),
			'capability' => 'manage_awebooking',
		])->as_child_of( 'checkout' );

		$section->add_field([
			'id'    => $prefix . '__heading_title__',
			'type'  => 'title',
			'name'  => $gateway->get_method_title(),
			'desc'  => $gateway->get_method_description(),
		]);

		// Loop each settings field and doing register.
		foreach ( $gateway->get_setting_fields() as $key => $args ) {
			$section->add_field(
				array_merge( $args, [ 'id' => $prefix . '_' . $key ] )
			);
		}

		do_action( 'awebooking/after_register_gateway_settings', $gateway, $section );
	}
}
