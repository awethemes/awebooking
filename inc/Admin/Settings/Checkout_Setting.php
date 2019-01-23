<?php

namespace AweBooking\Admin\Settings;

use AweBooking\Gateway\Gateway;
use AweBooking\Checkout\Form_Controls;

class Checkout_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->form_id  = 'checkout';
		$this->label    = esc_html__( 'Checkout', 'awebooking' );
		$this->priority = 40;
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	public function setup_fields() {
		$options = $this->add_section( 'checkout-options', [
			'title'    => esc_html__( 'Options', 'awebooking' ),
			'priority' => 0,
		] );

		$options->add_field( [
			'id'   => '__payments_title',
			'type' => 'title',
			'name' => esc_html__( 'Payment Gateways', 'awebooking' ),
			'desc' => abrs_esc_text( __( 'Installed gateways are listed below. You can find more payment gateways on <a href="https://awethemes.com/plugins/awebooking#premiumaddons">Premium addons</a>.',
				'awebooking' ) ),
		] );

		$options->add_field( [
			'id'      => 'list_gateway_order',
			'type'    => 'include',
			'name'    => esc_html__( 'Gateway Display Order', 'awebooking' ),
			'desc'    => esc_html__( 'Drag and drop to control display order on the frontend.', 'awebooking' ),
			'tooltip' => true,
			'include' => trailingslashit( dirname( __DIR__ ) ) . 'views/settings/html-gateways-sorter.php',
		] );

		$options->add_field( [
			'id'   => '__checkout_title',
			'type' => 'title',
			'name' => esc_html__( 'Checkout', 'awebooking' ),
		] );

		$options->add_field( [
			'id'              => 'list_checkout_controls',
			'type'            => 'include',
			'name'            => esc_html__( 'Checkout Controls', 'awebooking' ),
			'include'         => trailingslashit( dirname( __DIR__ ) ) . 'views/settings/html-checkout-controls.php',
			'sanitization_cb' => [ $this, 'sanitize_checkout_controls' ],
		] );

		// Register the gateways custom fields.
		foreach ( abrs_payment_gateways()->all() as $gateway ) {
			/* @var \AweBooking\Gateway\Gateway $gateway */
			if ( $gateway->has_settings() ) {
				$this->register_gateway_settings( $gateway );
			}
		}
	}

	/**
	 * Register the gateway settings.
	 *
	 * @param  \AweBooking\Gateway\Gateway $gateway  The gateway.
	 * @return void
	 */
	protected function register_gateway_settings( Gateway $gateway ) {
		$prefix = sanitize_key( 'gateway_' . $gateway->get_method() );

		$section = $this->add_section( $prefix, [
			'title'    => $gateway->get_method_title(),
			'priority' => 100,
		]);

		// TODO: ...
		if ( ! $gateway->is_enabled() ) {
			$section->hidden = true;
		}

		$section->add_field( [
			'id'   => '__title_' . $prefix,
			'type' => 'title',
			'name' => $gateway->get_method_title(),
			'desc' => $gateway->get_method_description(),
		] );

		// Loop each settings field and doing register.
		foreach ( $gateway->get_setting_fields() as $key => $args ) {
			$_args = array_merge( $args, [ 'id' => $prefix . '_' . $key ] );

			if ( 'checkbox' === $_args['type'] ) {
				$_args['type'] = 'abrs_checkbox';
			} elseif ( 'toggle' === $_args['type'] ) {
				$_args['type'] = 'abrs_toggle';
			}

			$section->add_field( $_args );
		}

		do_action( 'abrs_register_gateway_settings', $gateway, $section );
	}

	/**
	 * Sanitize checkout controls.
	 *
	 * @param  array $data The input controls.
	 * @return array
	 */
	public function sanitize_checkout_controls( $data ) {
		$controls = new Form_Controls;

		$controls_ids  = array_column( $controls->prop( 'fields' ), 'id' );
		$mandatory_ids = $controls->get_mandatory_controls();

		$sanitized = [];

		foreach ( $controls_ids as $key ) {
			$sanitized[ $key ] = in_array( $key, $mandatory_ids ) || in_array( $key, (array) $data );
		}

		return $sanitized;
	}
}
