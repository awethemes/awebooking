<?php
namespace Skeleton\CMB2\Fields;

class Range_Field extends Field_Abstract {
	/**
	 * Render custom field type callback.
	 *
	 * TODO: Improve display with label and input showing.
	 *
	 * @param \CMB2_Field $field              The passed in `CMB2_Field` object.
	 * @param mixed       $escaped_value      The value of this field escaped.
	 * @param int|string  $object_id          The ID of the current object.
	 * @param string      $object_type        The type of object you are working with.
	 * @param \CMB2_Types $field_type_object  The `CMB2_Types` object.
	 */
	public function output( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$field_args = $field->_data( 'args' );

		if ( isset( $field_args['pips'] ) && false === $field_args['pips'] ) {
			$pips_args = false;
		} else {
			/**
			 * Setting slider-pip pips.
			 *
			 * @see http://simeydotme.github.io/jQuery-ui-Slider-Pips/#options-pips
			 *
			 * @var array(
			 *      @type string|false $last    Determines the style of the first pip on the slider.
			 *                                  Value can be: "label", "pip" or false.
			 *      @type string|false $first   Determines the style of the final pip on the slider.
			 *                                  Value can be: "label", "pip" or false.
			 *      @type string|false $rest    Determines the style of all other pips on the slider.
			 *                                  Value can be: "label", "pip" or false.
			 *      @type number       $step    The step parameter will only generate every nth pip.
			 *      @type string       $prefix  Adds a string value before the pip label.
			 *      @type string       $suffix  Adds a string value after the pip label.
			 *      @type array|false  $labels  Will override the values of the pips with an array of given values.
			 *                                  eg: array( 'Monday', 'Tuesday', 'Wednesday', ...)
			 *                                  or array( 'first' => 'Monday', 'last' => 'Sunday' )
			 * )
			 */
			$pips_args = json_encode( wp_parse_args( $field->args( 'pips' ), array(
				'last'   => 'label',
				'first'  => 'label',
				'rest'   => 'pip',
				// 'step'   => 1, // Note: Not set default step at here.
				'prefix' => '',
				'suffix' => '',
				'labels' => false,
			)));
		}

		if ( isset( $field_args['float'] ) && false === $field_args['float'] ) {
			$float_args = false;
		} else {
			/**
			 * Setting slider-pip float.
			 *
			 * @see http://simeydotme.github.io/jQuery-ui-Slider-Pips/#options-float
			 *
			 * @var array(
			 *      @type string       $prefix  Adds a string value before the float label.
			 *      @type string       $suffix  Adds a string value after the float label.
			 *      @type array|false  $labels  Will override the values of the floats with an array of given values.
			 *                                  eg: array( 'Monday', 'Tuesday', 'Wednesday', ...)
			 *                                  or array( 'first' => 'Monday', 'last' => 'Sunday' )
			 * )
			 */
			$float_args = json_encode( wp_parse_args( $field->args( 'float' ), array(
				'prefix' => '',
				'suffix' => '',
				'labels' => false,
			)));
		}

		$ranger_input = $field_type_object->input( array(
			'type'       => 'hidden',
			'class'      => 'cmb2-ui-slider-input',
			'desc'       => '',
			'data-min'   => $field->args( 'min' ),
			'data-max'   => $field->args( 'max' ),
			'data-step'  => $field->args( 'step' ),
			'data-value' => is_numeric( $escaped_value ) ? $escaped_value : 0,
			'data-pips'  => esc_attr( $pips_args ),
			'data-float' => esc_attr( $float_args ),
			'js_dependencies' => array( 'jquery-ui-slider-pips' ),
		));

		// Enqueue jquery-ui-slider-pips before.
		wp_enqueue_style( 'jquery-ui-slider-pips' ); ?>

		<div class="cmb2-slider"><div class="cmb2-ui-slider"></div></div>
		<?php print $ranger_input; // WPCS: XSS OK. ?>
		<span class="hidden"><span class="cmb2-ui-slider-preview"></span></span>
		<?php
	}

	/**
	 * Filter the value before it is saved.
	 *
	 * @param bool|mixed     $override_value Sanitization/Validation override value to return.
	 * @param mixed          $value      The value to be saved to this field.
	 * @param int            $object_id  The ID of the object where the value will be saved.
	 * @param array          $field_args The current field's arguments.
	 * @param \CMB2_Sanitize $sanitizer  The `CMB2_Sanitize` object.
	 */
	public function sanitization( $override_value, $value, $object_id, $field_args, $sanitizer ) {
		if ( is_array( $value ) ) {
			return array_map( function ( $saved_value ) {
				return is_numeric( $saved_value ) ? $saved_value : 0;
			}, $value );
		}

		return is_numeric( $value ) ? $value : 0;
	}
}
