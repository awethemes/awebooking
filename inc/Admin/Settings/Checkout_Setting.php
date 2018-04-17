<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Gateway\Gateway;
use AweBooking\Admin\Admin_Settings;

class Checkout_Setting extends Abstract_Setting {
	/**
	 * The setting ID.
	 *
	 * @var string
	 */
	protected $form_id = 'checkout';

	/**
	 * Get the setting label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Checkout', 'awebooking' );
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	public function setup_fields() {
		$options = $this->add_section( 'checkout-options', [
			'title'      => esc_html__( 'Checkout Options', 'awebooking' ),
			'capability' => 'manage_awebooking',
		]);

		$options->add_field([
			'id'         => '__payments_title',
			'type'       => 'title',
			'name'       => esc_html__( 'Payment Gateways', 'awebooking' ),
			'desc'       => esc_html__( 'Installed gateways are listed below. Drag and drop gateways to control their display order on the frontend.', 'awebooking' ),
		]);

		$options->add_field([
			'id'         => 'list_gateway_order',
			'type'       => 'include',
			'name'       => esc_html__( 'Gateway Display Order', 'awebooking' ),
			'include'    => trailingslashit( __DIR__ ) . 'views/html-gateways-sorter.php',
		]);

		// Register the gateways custom fields.
		foreach ( awebooking( 'gateways' )->all() as $gateway ) {
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
			'title' => $gateway->get_method_title(),
		]);

		$section->add_field([
			'id'    => '__title_' . $prefix,
			'type'  => 'title',
			'name'  => $gateway->get_method_title(),
			'desc'  => $gateway->get_method_description(),
		]);

		// Loop each settings field and doing register.
		foreach ( $gateway->get_setting_fields() as $key => $args ) {
			$field_args = array_merge( $args, [ 'id' => $prefix . '_' . $key ] );

			if ( 'checkbox' === $field_args['type'] ) {
				$field_args['type'] = 'abrs_checkbox';
			} elseif ( 'toggle' === $field_args['type'] ) {
				$field_args['type'] = 'abrs_toggle';
			}

			$section->add_field( $field_args );
		}

		do_action( 'awebooking/register_gateway_settings', $gateway, $section );
	}
}
