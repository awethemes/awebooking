<?php
namespace AweBooking\Admin\Fields;

use Skeleton\Fields\CMB2_Field;

class Per_Person_Pricing_Field extends CMB2_Field {
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
		global $room_type;

		if ( is_null( $room_type ) ) {
			$room_type = call_user_func( $field->prop( 'room_type_resolver' ) );
		}

		$max_occupancy = $room_type->get_maximum_occupancy();

		$occupancy = [
			'adults'   => $room_type->get_number_adults(),
			'children' => $room_type->get_number_children(),
			'infants'  => $room_type->get_number_infants(),
		];

		$start_for_loop = min( array_values( $occupancy ) ) + 1;
		?>

		<table class="awebooking-additional-occupancy">
			<thead>
				<tr>
					<th></th>
					<?php for ( $i = $start_for_loop; $i <= $max_occupancy; $i++ ) : ?>
						<th>#<?php echo esc_html( $i ); ?></th>
					<?php endfor; ?>
				</tr>
			</thead>

			<tbody>
				<?php foreach ( $occupancy as $key => $value ) : ?>
					<tr>
						<th>
							<?php
							switch ( $key ) :
								case 'adults':
									esc_html_e( 'Adults', 'awebooking' );
									break;

								case 'children':
									esc_html_e( 'Children', 'awebooking' );
									break;

								case 'infants':
									esc_html_e( 'Infants', 'awebooking' );
									break;
							endswitch;
							?>
						</th>

						<?php for ( $i = $start_for_loop; $i <= $max_occupancy; $i++ ) : ?>
							<td>
								<?php if ( $i > $value ) : ?>
									<input type="text" name="additional_occupancy[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $i ); ?>]" placeholder="0.00">
								<?php endif ?>
							</td>
						<?php endfor; ?>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>

		<?php
	}
}
