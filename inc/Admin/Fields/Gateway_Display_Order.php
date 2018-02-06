<?php
namespace AweBooking\Admin\Fields;

use AweBooking\AweBooking;
use Skeleton\Fields\CMB2_Field;

class Gateway_Display_Order extends CMB2_Field {
	/**
	 * Adding this field to the blacklist of repeatable field-types.
	 *
	 * @var boolean
	 */
	public $repeatable = false;

	/**
	 * Render custom field type callback.
	 *
	 * @param CMB2_Field $field             The passed in `CMB2_Field` object.
	 * @param mixed      $escaped_value     The value of this field escaped.
	 * @param string|int $object_id         The ID of the current object.
	 * @param string     $object_type       The type of object you are working with.
	 * @param CMB2_Types $field_type_object The `CMB2_Types` object.
	 */
	public function output( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		?>
		<table class="awebooking_gateways widefat" cellspacing="0">
			<thead>
				<tr>
					<th class="sort"></th>
					<th class="name"><?php esc_html_e( 'Gateway', 'awebooking' ); ?></th>
					<th class="id"><?php esc_html_e( 'Gateway ID', 'awebooking' ); ?></th>
					<th class="status"><?php esc_html_e( 'Enabled', 'awebooking' ); ?></th>
				</tr>
			</thead>
			<tbody class="ui-sortable">
				<?php foreach ( awebooking( 'gateways' )->all() as $id => $gateway ) : ?>
					<tr class="">
						<td class="sort ui-sortable-handle">
							<input type="hidden" name="<?php echo esc_attr( $field_type_object->_id( '[]' ) ); ?>" value="<?php echo esc_attr( $gateway->get_method() ); ?>">
						</td>
						<td class="name">
							<a href="#"><?php echo esc_html( $gateway->get_method_title() ); ?></a>
						</td>

						<td class="id"><?php echo esc_html( $id ); ?></td>
						<td class="status">
							<?php
								echo ( $gateway->is_enabled() ) ? '<span class="status-enabled tips">' . esc_html__( 'Yes', 'awebooking' ) . '</span>' : '-';
							?>
						</td>
					</tr>
				<?php endforeach; ?>

			</tbody>
		</table>
		<?php
		wp_enqueue_script( 'jquery-ui-sortable' );
		print $this->prints_inline_js(); // WPCS: xss ok.
	}

	/**
	 * Filter the value before it is saved.
	 *
	 * @param bool|mixed    $override_value Sanitization/Validation override value to return.
	 * @param mixed         $value      The value to be saved to this field.
	 * @param int           $object_id  The ID of the object where the value will be saved.
	 * @param array         $field_args The current field's arguments.
	 * @param CMB2_Sanitize $sanitizer  The `CMB2_Sanitize` object.
	 */
	public function sanitization( $override_value, $value, $object_id, $field_args, $sanitizer ) {
		return $value;
	}

	/**
	 * Prints inline JS for the datepicker range.
	 *
	 * @param string $start_date_id Start date ID.
	 * @param string $end_date_id   End date ID.
	 * @param string $toggle_lock   Toggle lock ID.
	 * @return void
	 */
	protected function prints_inline_js() {
		?><script>
			jQuery(document).ready(function($) {
				// Sorting
				$( 'table.awebooking_gateways tbody' ).sortable({
				    items: 'tr',
				    cursor: 'move',
				    axis: 'y',
				    handle: 'td.sort',
				    scrollSensitivity: 40,
				    helper: function( event, ui ) {
				      ui.children().each( function() {
				        $( this ).width( $( this ).width() );
				      });
				      return ui;
				    },
				    start: function( event, ui ) {
				      ui.item.css( 'background-color', '#f6f6f6' );
				    },
				    stop: function( event, ui ) {
				      ui.item.removeAttr( 'style' );
				    }
				});
			});
		</script>
		<?php
	}
}
