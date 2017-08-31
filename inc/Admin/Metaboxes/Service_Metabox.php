<?php
namespace AweBooking\Admin\Metaboxes;

use AweBooking\AweBooking;
use AweBooking\Hotel\Service;

class Service_Metabox extends Taxonomy_Metabox {
	/**
	 * Taxonomy ID to register meta-boxes.
	 *
	 * @var string
	 */
	protected $taxonomy = AweBooking::HOTEL_SERVICE;

	/**
	 * Register fields.
	 *
	 * @return void
	 */
	public function register() {
		$this->add_field([
			'name'            => esc_html__( 'Operation', 'awebooking' ),
			'id'              => '_service_operation',
			'type'            => 'select',
			'options'         => awebooking( 'setting' )->get_service_operations(),
			'render_field_cb' => array( $this, '_operation_field_callback' ),
		]);

		$this->add_field([
			'id'              => '_service_value',
			'type'            => 'text_small',
			'validate'        => 'required|numeric:min:0',
			'sanitization_cb' => 'awebooking_sanitize_price',
			'show_on_cb'      => '__return_false',
		]);

		$this->add_field([
			'name'      => esc_html__( 'Type', 'awebooking' ),
			'id'        => '_service_type',
			'type'      => 'select',
			'options'   => [
				Service::OPTIONAL  => esc_html__( 'Optional', 'awebooking' ),
				Service::MANDATORY => esc_html__( 'Mandatory', 'awebooking' ),
			],
		]);
	}

	/**
	 * Render rooms callback.
	 *
	 * @return void
	 */
	public function _operation_field_callback( $field_args, $field ) {
		$price = $field->get_cmb()->get_field( '_service_value' );

		echo '<div class="skeleton-input-group">';
		skeleton_render_field( $field );
		echo '</div>';

		echo '<div class="skeleton-input-group form-required">';
		skeleton_render_field( $price );
		echo '<span class="skeleton-input-group__addon"><span class="awebooking-price-field">' . awebooking( 'currency' )->get_symbol() . '</span><span class="awebooking-value-field" style="display:none;">' . esc_html__( '%', 'awebooking' ) . '</span></span>';

		echo '</div>';

		skeleton_display_field_errors( $price );

		?><script>
			jQuery(function($) {
				var displayCallback = function() {
					var operation = $('#_service_operation').val();
					if (operation === 'increase' || operation === 'decrease') {
						$('.awebooking-price-field').hide();
						$('.awebooking-value-field').show();
					} else {
						$('.awebooking-value-field').hide();
						$('.awebooking-price-field').show();
					}
				}

				displayCallback();
				$('#_service_operation').on('change', displayCallback);
			});
		</script><?php
	}
}
