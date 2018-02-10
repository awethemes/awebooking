<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Support\Utils as U;

class Edit_Source_Form extends Form_Abstract {
	/**
	 * The form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'awebooking_edit_reservation_source';

	/**
	 * {@inheritdoc}
	 */
	protected function fields() {
		$this->add_field([
			'id'          => 'tax_rates',
			'type'        => 'select',
			'name'        => esc_html__( 'Tax rates', 'awebooking' ),
			'options'     => $this->get_tax_rates(),
		]);
	}

	/**
	 * Get tax rates.
	 *
	 * @return array
	 */
	protected function get_tax_rates() {
		global $wpdb;

		$query = "SELECT * FROM {$wpdb->prefix}awebooking_tax_rates AS taxes ORDER BY created_date DESC";

		$results = [''];

		foreach ( $wpdb->get_results( $query ) as $key => $value ) {
			$results[$value->id] = $value->name;
		}

		return $results;
	}
}
