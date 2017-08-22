<?php
namespace AweBooking\Support\Fields;

use AweBooking\AweBooking;
use Skeleton\CMB2\Fields\Field_Abstract;

class Date_Range_Field extends Field_Abstract {
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

		$this->prints_inline_js(
			$field_type_object->_id( '_0' ),
			$field_type_object->_id( '_1' )
		);

		$field->add_js_dependencies( [ 'jquery-ui-core', 'jquery-ui-datepicker' ] );
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
	 * @param  string $start Start ID.
	 * @param  string $end   End ID.
	 *  @return void
	 */
	protected function prints_inline_js( $start, $end ) {
		?><script type="text/javascript">
			;(function($) {
				'use strict';

				var _dateFormat = '<?php echo esc_attr( AweBooking::JS_DATE_FORMAT ); ?>',
					_fromDateID = '#<?php echo esc_attr( $start ); ?>',
					_toDateID   = '#<?php echo esc_attr( $end ); ?>';

				var _beforeShowCallback = function() {
					$('#ui-datepicker-div').addClass('cmb2-element');
				};

				var _closeCallback = function() {
					$('#ui-datepicker-div').addClass('cmb2-element');
				};

				$(function() {
					var fromDate, toDate;

					var applyFromChange = function() {
						try {
							var _minDate = $.datepicker.parseDate(_dateFormat, $(_fromDateID).val());
							_minDate.setDate(_minDate.getDate() + 1);
							toDate.datepicker('option', 'minDate', _minDate);
						} catch(e) {}
					};

					var applyToChange = function() {
						try {
							var _maxDate = $.datepicker.parseDate(_dateFormat, $(_toDateID).val());
							fromDate.datepicker('option', 'maxDate', _maxDate);
						} catch(e) {}
					};

					fromDate = $(_fromDateID).datepicker({
						onClose: _closeCallback,
						beforeShow: _beforeShowCallback,
						dateFormat: _dateFormat,
					}).on('change', applyFromChange);

					toDate = $(_toDateID).datepicker({
						onClose: _closeCallback,
						beforeShow: _beforeShowCallback,
						dateFormat: _dateFormat,
					}).on('change', applyToChange);

					applyToChange();
					applyFromChange();
				});
			})(jQuery);
		</script><?php
	}
}
