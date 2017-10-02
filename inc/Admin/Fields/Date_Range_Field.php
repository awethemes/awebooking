<?php
namespace AweBooking\Admin\Fields;

use AweBooking\AweBooking;
use Skeleton\Fields\CMB2_Field;

class Date_Range_Field extends CMB2_Field {
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
		$escaped_value = (array) $escaped_value;

		$is_locked = false;
		if ( $field->prop( 'locked' ) ) {
			$is_locked = true;

			$field->args['attributes'] = array_merge( $field->args['attributes'], [
				'disabled' => true,
			]);
		}

		print $field_type_object->input( array( // WPCS: XSS OK.
			'type'    => 'text',
			'id'      => $field_type_object->_id( '_0' ),
			'name'    => $field_type_object->_name( '[0]' ),
			'value'   => isset( $escaped_value[0] ) ? $escaped_value[0] : '',
			'class'   => 'cmb2-text-small cmb2-date-range',
		) );

		print $field_type_object->input( array( // WPCS: XSS OK.
			'type'    => 'text',
			'id'      => $field_type_object->_id( '_1' ),
			'name'    => $field_type_object->_name( '[1]' ),
			'value'   => isset( $escaped_value[1] ) ? $escaped_value[1] : '',
			'class'   => 'cmb2-text-small cmb2-date-range',
		) );

		if ( $is_locked ) {
			printf(
				'<span id="%s" class="button cmb2-date-range-lock" title="%s"><span class="dashicons dashicons-edit"></span></span>',
				esc_attr( $field_type_object->_id( '_togglelock' ) ),
				esc_html__( 'Edit period date', 'awebooking' )
			);
		}

		$this->prints_inline_js(
			$field_type_object->_id( '_0' ),
			$field_type_object->_id( '_1' ),
			$is_locked ? $field_type_object->_id( '_togglelock' ) : null
		);

		$field->add_js_dependencies( [ 'awebooking-admin' ] );
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
		return awebooking_sanitize_period( $value );
	}

	/**
	 * Prints inline JS for the datepicker range.
	 *
	 * @param string $start_date_id Start date ID.
	 * @param string $end_date_id   End date ID.
	 * @param string $toggle_lock   Toggle lock ID.
	 * @return void
	 */
	protected function prints_inline_js( $start_date_id, $end_date_id, $toggle_lock ) {
		?><script type="text/javascript">
			jQuery(function($) {
				'use strict';

				var _fromDateID = '#<?php echo esc_attr( $start_date_id ); ?>',
					_toDateID   = '#<?php echo esc_attr( $end_date_id ); ?>';

				var rangepicker = new TheAweBooking.RangeDatepicker(_fromDateID, _toDateID);
				rangepicker.init();

				<?php if ( $toggle_lock ) : ?>
					$('#<?php echo esc_attr( $toggle_lock ); ?>').on('click', function(e) {
						e.preventDefault();

						$(this).toggleClass('active');
						$(_fromDateID).prop('disabled', ! $(_fromDateID).prop('disabled'));
						$(_toDateID).prop('disabled', ! $(_toDateID).prop('disabled'));
					});
				<?php endif; ?>
			});
		</script><?php
	}
}
