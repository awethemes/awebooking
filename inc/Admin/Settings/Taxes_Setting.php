<?php
namespace AweBooking\Admin\Settings;

use Awethemes\Http\Request;

class Taxes_Setting implements Setting {
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
		return esc_html__( 'Taxes', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function output( Request $request ) {
		$taxes = [];

		$GLOBALS['hide_save_button'] = true;

		wp_enqueue_script( 'awebooking-settings-taxes' );

		abrs_admin_template_part( 'settings/html-section-taxes.php', compact( 'taxes' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function save( Request $request ) {
	}
}
