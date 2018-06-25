<?php
namespace AweBooking\Admin\Settings;

use Awethemes\Http\Request;

class Taxes_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	public function get_id() {
		return 'taxes';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label() {
		return esc_html__( 'Tax', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$this->add_field([
			'id'       => '__tax_title',
			'type'     => 'title',
			'name'     => esc_html__( 'Tax options', 'awebooking' ),
		]);

		$this->add_field([
			'id'       => 'calc_taxes',
			'type'     => 'abrs_toggle',
			'default'  => 'on',
			'name'     => esc_html__( 'Enable taxes', 'awebooking' ),
		]);

		$this->add_field([
			'id'       => 'tax_rate_model',
			'type'     => 'select',
			'name'     => esc_html__( 'Tax rate model', 'awebooking' ),
			'default'  => 'single',
			'classes'  => 'with-selectize',
			'options'  => [
				'single'   => esc_html__( 'A specified tax rate applies for all room', 'awebooking' ),
				'per_room' => esc_html__( 'Sets tax rate in each room', 'awebooking' ),
			],
		]);

		$this->add_field([
			'id'              => 'prices_include_tax',
			'type'            => 'radio',
			'name'            => esc_html__( 'Are prices included tax?', 'awebooking' ),
			'default'         => 'no',
			// 'sanitization_cb' => 'abrs_sanitize_checkbox',
			'options'         => [
				'no'  => esc_html__( 'No, I will enter prices exclusive of tax.', 'awebooking' ),
				'yes' => esc_html__( 'Yes, I will enter prices inclusive of tax.', 'awebooking' ),
			],
		]);

		if ( 'single' === abrs_get_option( 'tax_rate_model', 'single' ) ) {
			$this->add_field([
				'id'         => 'single_tax_rate',
				'type'       => 'select',
				'name'       => esc_html__( 'Select tax rate', 'awebooking' ),
				'classes'    => 'with-selectize',
				'options_cb' => function () {
					return abrs_get_tax_rates()->pluck( 'name', 'id' )->all();
				},
			]);
		}

		$this->add_field([
			'id'   => '__rates_title',
			'type' => 'title',
			'name' => esc_html__( 'Tax rates', 'awebooking' ),
		]);

		$this->add_field([
			'id'          => '__tax_rates',
			'type'        => 'include',
			'name'        => esc_html__( 'Tax rates', 'awebooking' ),
			'include'     => trailingslashit( dirname( __DIR__ ) ) . 'views/settings/html-tax-rates.php',
			'save_fields' => false,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function save( Request $request ) {
		parent::save( $request );
	}
}
