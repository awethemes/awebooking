<?php
namespace AweBooking\Admin\Settings;

use Awethemes\Http\Request;
use AweBooking\Admin\Admin_Settings;

class Reservation_Setting extends Abstract_Setting {
	/**
	 * The setting ID.
	 *
	 * @var string
	 */
	protected $form_id = 'reservation';

	/**
	 * Get the setting label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Reservation', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function output( Request $request ) {
		$this->prepare_fields();

		$this->output_sections();

		switch ( $this->current_section ) {
			case 'sources':
				abrs_admin_template_part( 'settings/html-section-sources.php' );
				break;
			case 'taxes':
				abrs_admin_template_part( 'settings/html-section-taxes.php' );
				break;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$this->add_section( 'sources', [
			'title' => esc_html__( 'Sources', 'awebooking' ),
		]);

		$this->add_section( 'taxes', [
			'title' => esc_html__( 'Taxes', 'awebooking' ),
		]);
	}
}
