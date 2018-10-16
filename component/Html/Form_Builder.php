<?php
namespace AweBooking\Component\Html;

use DateTime;
use DateTimeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Awethemes\WP_Session\Store;
use Awethemes\Http\Request;

class Form_Builder {
	/**
	 * The current model instance for the form.
	 *
	 * @var mixed
	 */
	protected $model;

	/**
	 * The http request implementation.
	 *
	 * @var \Awethemes\Http\Request|null
	 */
	protected $request;

	/**
	 * Consider Request variables while auto fill.
	 *
	 * @var bool
	 */
	protected $consider_request = false;

	/**
	 * The session store implementation.
	 *
	 * @var \Awethemes\WP_Session\Store|null
	 */
	protected $session;

	/**
	 * The types of inputs to not fill values on by default.
	 *
	 * @var array
	 */
	protected $skip_value_types = [ 'file', 'password', 'checkbox', 'radio' ];

	/**
	 * Store the current input type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * An array of label names we've created.
	 *
	 * @var array
	 */
	protected $labels = [];

	/**
	 * Store the old input payloads.
	 *
	 * @var array
	 */
	protected $payload = [];

	/**
	 * Create a new form builder instance.
	 *
	 * @param  Request $request
	 */
	public function __construct( Request $request = null ) {
		$this->request = $request;
	}

	/**
	 * Set the HTTP request instance.
	 *
	 * @return \Awethemes\Http\Request|null
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Get the HTTP request instance.
	 *
	 * @param \Awethemes\Http\Request $request
	 * @return $this
	 */
	public function set_request( Request $request ) {
		$this->request = $request;

		return $this;
	}

	/**
	 * Set the model instance on the form builder.
	 *
	 * @param  mixed $model
	 * @return void
	 */
	public function set_model( $model ) {
		$this->model = $model;
	}

	/**
	 * Get the current model instance on the form builder.
	 *
	 * @return mixed $model
	 */
	public function get_model() {
		return $this->model;
	}

	/**
	 * Create an HTML attribute string from an array.
	 *
	 * @param array $attributes
	 * @return string
	 */
	public function attributes( $attributes ) {
		return abrs_html_attributes( $attributes );
	}

	/**
	 * Create a form label element.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function label( $name, $value = null, $options = [] ) {
		$this->labels[] = $name;

		$value = $this->format_label( $name, $value );

		return $this->to_html_string( '<label for="' . $name . '"' . $this->attributes( $options ) . '>' . $value . '</label>' );
	}

	/**
	 * Format the label value.
	 *
	 * @param  string      $name
	 * @param  string|null $value
	 * @return string
	 */
	protected function format_label( $name, $value ) {
		return $value ?: ucwords( str_replace( '_', ' ', $name ) );
	}

	/**
	 * Create a form input field.
	 *
	 * @param  string $type
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function input( $type, $name, $value = null, $options = [] ) {
		$this->type = $type;

		if ( ! isset( $options['name'] ) ) {
			$options['name'] = $name;
		}

		// We will get the appropriate value for the given field. We will look for the
		// value in the session for the value in the old input data then we'll look
		// in the model instance if one is set. Otherwise we will just use empty.
		$id = $this->get_id_attribute( $name, $options );

		if ( ! in_array( $type, $this->skip_value_types ) ) {
			$value = $this->get_value_attribute( $name, $value );
		}

		// Once we have the type, value, and ID we can merge them into the rest of the
		// attributes array so we can convert them into their HTML attribute format
		// when creating the HTML element. Then, we will return the entire input.
		$merge = compact( 'type', 'value', 'id' );

		$options = array_merge( $options, $merge );

		return $this->to_html_string( '<input' . $this->attributes( $options ) . '>' );
	}

	/**
	 * Create a text input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function text( $name, $value = null, $options = [] ) {
		return $this->input( 'text', $name, $value, $options );
	}

	/**
	 * Create a password input field.
	 *
	 * @param  string $name
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function password( $name, $options = [] ) {
		return $this->input( 'password', $name, '', $options );
	}

	/**
	 * Create a range input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function range( $name, $value = null, $options = [] ) {
		return $this->input( 'range', $name, $value, $options );
	}

	/**
	 * Create a hidden input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function hidden( $name, $value = null, $options = [] ) {
		return $this->input( 'hidden', $name, $value, $options );
	}

	/**
	 * Create a search input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function search( $name, $value = null, $options = [] ) {
		return $this->input( 'search', $name, $value, $options );
	}

	/**
	 * Create an e-mail input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function email( $name, $value = null, $options = [] ) {
		return $this->input( 'email', $name, $value, $options );
	}

	/**
	 * Create a tel input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function tel( $name, $value = null, $options = [] ) {
		return $this->input( 'tel', $name, $value, $options );
	}

	/**
	 * Create a number input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function number( $name, $value = null, $options = [] ) {
		return $this->input( 'number', $name, $value, $options );
	}

	/**
	 * Create a color input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function color( $name, $value = null, $options = [] ) {
		return $this->input( 'color', $name, $value, $options );
	}

	/**
	 * Create a HTML reset input element.
	 *
	 * @param  string $value
	 * @param  array  $attributes
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function reset( $value, $attributes = [] ) {
		return $this->input( 'reset', null, $value, $attributes );
	}

	/**
	 * Create a submit button element.
	 *
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function submit( $value = null, $options = [] ) {
		return $this->input( 'submit', null, $value, $options );
	}

	/**
	 * Create a button element.
	 *
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function button( $value = null, $options = [] ) {
		if ( ! array_key_exists( 'type', $options ) ) {
			$options['type'] = 'button';
		}

		return $this->to_html_string( '<button' . $this->attributes( $options ) . '>' . $value . '</button>' );
	}

	/**
	 * Create a month input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function month( $name, $value = null, $options = [] ) {
		if ( $value instanceof DateTime ) {
			$value = $value->format( 'Y-m' );
		}

		return $this->input( 'month', $name, $value, $options );
	}

	/**
	 * Create a date input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function date( $name, $value = null, $options = [] ) {
		if ( $value instanceof DateTimeInterface ) {
			$value = $value->format( 'Y-m-d' );
		}

		return $this->input( 'date', $name, $value, $options );
	}

	/**
	 * Create a datetime input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function datetime( $name, $value = null, $options = [] ) {
		if ( $value instanceof DateTimeInterface ) {
			$value = $value->format( DateTimeInterface::RFC3339 );
		}

		return $this->input( 'datetime', $name, $value, $options );
	}

	/**
	 * Create a datetime-local input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function datetime_local( $name, $value = null, $options = [] ) {
		if ( $value instanceof DateTimeInterface ) {
			$value = $value->format( 'Y-m-d\TH:i' );
		}

		return $this->input( 'datetime-local', $name, $value, $options );
	}

	/**
	 * Create a time input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function time( $name, $value = null, $options = [] ) {
		if ( $value instanceof DateTimeInterface ) {
			$value = $value->format( 'H:i' );
		}

		return $this->input( 'time', $name, $value, $options );
	}

	/**
	 * Create a week input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function week( $name, $value = null, $options = [] ) {
		if ( $value instanceof DateTimeInterface ) {
			$value = $value->format( 'Y-\WW' );
		}

		return $this->input( 'week', $name, $value, $options );
	}

	/**
	 * Create a file input field.
	 *
	 * @param  string $name
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function file( $name, $options = [] ) {
		return $this->input( 'file', $name, null, $options );
	}

	/**
	 * Create a textarea input field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function textarea( $name, $value = null, $options = [] ) {
		$this->type = 'textarea';

		if ( ! isset( $options['name'] ) ) {
			$options['name'] = $name;
		}

		// Next we will look for the rows and cols attributes, as each of these are put
		// on the textarea element definition. If they are not present, we will just
		// assume some sane default values for these attributes for the developer.
		$options = $this->set_textarea_size( $options );

		$options['id'] = $this->get_id_attribute( $name, $options );

		$value = (string) $this->get_value_attribute( $name, $value );

		unset( $options['size'] );

		// Next we will convert the attributes into a string form. Also we have removed
		// the size attribute, as it was merely a short-cut for the rows and cols on
		// the element. Then we'll create the final textarea elements HTML for us.
		$options = $this->attributes( $options );

		return $this->to_html_string( '<textarea' . $options . '>' . esc_textarea( $value ) . '</textarea>' );
	}

	/**
	 * Set the text area size on the attributes.
	 *
	 * @param  array $options
	 * @return array
	 */
	protected function set_textarea_size( $options ) {
		if ( isset( $options['size'] ) ) {
			list( $cols, $rows ) = explode( 'x', $options['size'] );
		} else {
			// If the "size" attribute was not specified, we will just look for the regular
			// columns and rows attributes, using sane defaults if these do not exist on
			// the attributes array. We'll then return this entire options array back.
			$cols = Arr::get( $options, 'cols', 50 );

			$rows = Arr::get( $options, 'rows', 10 );
		}

		return array_merge( $options, compact( 'cols', 'rows' ) );
	}

	/**
	 * Create a select box field.
	 *
	 * @param  string      $name
	 * @param  array       $list
	 * @param  string|bool $selected
	 * @param  array       $attributes
	 * @param  array       $options_attributes
	 * @param  array       $optgroups_attributes
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function select(
		$name,
		$list = [],
		$selected = null,
		array $attributes = [],
		array $options_attributes = [],
		array $optgroups_attributes = []
	) {
		$this->type = 'select';

		// When building a select box the "value" attribute is really the selected one
		// so we will use that when checking the model or session for a value which
		// should provide a convenient method of re-populating the forms on post.
		$selected = $this->get_value_attribute( $name, $selected );

		$attributes['id'] = $this->get_id_attribute( $name, $attributes );

		if ( ! isset( $attributes['name'] ) ) {
			$attributes['name'] = $name;
		}

		// We will simply loop through the options and build an HTML value for each of
		// them until we have an array of HTML declarations. Then we will join them
		// all together into one single HTML element that can be put on the form.
		$html = [];

		if ( isset( $attributes['placeholder'] ) ) {
			$html[] = $this->placeholder_option( $attributes['placeholder'], $selected );
			unset( $attributes['placeholder'] );
		}

		foreach ( $list as $value => $display ) {
			$option_atts   = Arr::get( $options_attributes, $value, [] );
			$optgroup_atts = Arr::get( $optgroups_attributes, $value, [] );

			$html[] = $this->get_select_option( $display, $value, $selected, $option_atts, $optgroup_atts );
		}

		// Once we have all of this HTML, we can join this into a single element after
		// formatting the attributes into an HTML "attributes" string, then we will
		// build out a final select statement, which will contain all the values.
		$attributes = $this->attributes( $attributes );

		$list = implode( '', $html );

		return $this->to_html_string( "<select{$attributes}>{$list}</select>" );
	}

	/**
	 * Create a select range field.
	 *
	 * @param  string $name
	 * @param  string $begin
	 * @param  string $end
	 * @param  string $selected
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function select_range( $name, $begin, $end, $selected = null, $options = [] ) {
		$range = array_combine( $range = range( $begin, $end ), $range );

		return $this->select( $name, $range, $selected, $options );
	}

	/**
	 * Create a select year field.
	 *
	 * @param  string $name
	 * @param  string $begin
	 * @param  string $end
	 * @param  string $selected
	 * @param  array  $options
	 * @return mixed
	 */
	public function select_year( $name, $begin, $end, $selected = null, $options = [] ) {
		return $this->select_range( ...func_get_args() );
	}

	/**
	 * Create a select month field.
	 *
	 * @param  string $name
	 * @param  string $selected
	 * @param  array  $options
	 * @param  string $format
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function select_month( $name, $selected = null, $options = [], $format = '%B' ) {
		$months = [];

		foreach ( range( 1, 12 ) as $month ) {
			$months[ $month ] = strftime( $format, mktime( 0, 0, 0, $month, 1 ) );
		}

		return $this->select( $name, $months, $selected, $options );
	}

	/**
	 * Get the select option for the given value.
	 *
	 * @param  string|array $display
	 * @param  string       $value
	 * @param  string       $selected
	 * @param  array        $attributes
	 * @param  array        $optgroup_attributes
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function get_select_option(
		$display,
		$value,
		$selected,
		array $attributes = [],
		array $optgroup_attributes = []
	) {
		if ( is_iterable( $display ) ) {
			return $this->option_group( $display, $value, $selected, $optgroup_attributes, $attributes );
		}

		return $this->option( $display, $value, $selected, $attributes );
	}

	/**
	 * Create an option group form element.
	 *
	 * @param  array   $list
	 * @param  string  $label
	 * @param  string  $selected
	 * @param  array   $attributes
	 * @param  array   $options_attributes
	 * @param  integer $level
	 * @return \AweBooking\Component\Html\Html_String
	 */
	protected function option_group(
		$list,
		$label,
		$selected,
		array $attributes = [],
		array $options_attributes = [],
		$level = 0
	) {
		$html  = [];

		$space = str_repeat( '&nbsp;', $level );

		foreach ( $list as $value => $display ) {
			$option_attributes = Arr::get( $options_attributes, $value, [] );

			if ( is_iterable( $display ) ) {
				$html[] = $this->option_group( $display, $value, $selected, $attributes, $option_attributes, $level + 5 );
			} else {
				$html[] = $this->option( $space . $display, $value, $selected, $option_attributes );
			}
		}

		return $this->to_html_string( '<optgroup label="' . esc_attr( $space . $label ) . '"' . $this->attributes( $attributes ) . '>' . implode( '', $html ) . '</optgroup>' );
	}

	/**
	 * Create a select element option.
	 *
	 * @param  string $display
	 * @param  string $value
	 * @param  string $selected
	 * @param  array  $attributes
	 * @return \AweBooking\Component\Html\Html_String
	 */
	protected function option( $display, $value, $selected, array $attributes = [] ) {
		$selected = $this->get_selected_value( $value, $selected );

		// @codingStandardsIgnoreLine
		$options = array_merge( [ 'value' => $value, 'selected' => $selected ], $attributes );

		$string = '<option' . $this->attributes( $options ) . '>';

		if ( null !== $display ) {
			$string .= esc_html( $display ) . '</option>';
		}

		return $this->to_html_string( $string );
	}

	/**
	 * Create a placeholder select element option.
	 *
	 * @param string $display
	 * @param mixed  $selected
	 * @return \AweBooking\Component\Html\Html_String
	 */
	protected function placeholder_option( $display, $selected ) {
		$selected = $this->get_selected_value( null, $selected );

		$options = [
			'selected' => $selected,
			'value'    => '',
		];

		return $this->to_html_string( '<option' . $this->attributes( $options ) . '>' . esc_html( $display ) . '</option>' );
	}

	/**
	 * Determine if the value is selected.
	 *
	 * @param  string $value
	 * @param  string $selected
	 * @return string|null
	 */
	protected function get_selected_value( $value, $selected ) {
		if ( is_array( $selected ) ) {
			return in_array( $value, $selected, true ) || in_array( (string) $value, $selected, true ) ? 'selected' : null;
		}

		if ( $selected instanceof Collection ) {
			return $selected->contains( $value ) ? 'selected' : null;
		}

		if ( is_int( $value ) && is_bool( $selected ) ) {
			return (bool) $value === $selected;
		}

		return ( (string) $value === (string) $selected ) ? 'selected' : null;
	}

	/**
	 * Create a checkbox input field.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  bool   $checked
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function checkbox( $name, $value = 1, $checked = null, $options = [] ) {
		return $this->checkable( 'checkbox', $name, $value, $checked, $options );
	}

	/**
	 * Create a radio button input field.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  bool   $checked
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function radio( $name, $value = null, $checked = null, $options = [] ) {
		if ( is_null( $value ) ) {
			$value = $name;
		}

		return $this->checkable( 'radio', $name, $value, $checked, $options );
	}

	/**
	 * Create a checkable input field.
	 *
	 * @param  string $type
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  bool   $checked
	 * @param  array  $options
	 * @return \AweBooking\Component\Html\Html_String
	 */
	protected function checkable( $type, $name, $value, $checked, $options ) {
		$this->type = $type;

		$checked = $this->get_checked_state( $type, $name, $value, $checked );

		if ( $checked ) {
			$options['checked'] = 'checked';
		}

		return $this->input( $type, $name, $value, $options );
	}

	/**
	 * Get the check state for a checkable input.
	 *
	 * @param  string $type
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  bool   $checked
	 * @return bool
	 */
	protected function get_checked_state( $type, $name, $value, $checked ) {
		switch ( $type ) {
			case 'checkbox':
				return $this->get_checkbox_checked_state( $name, $value, $checked );

			case 'radio':
				return $this->get_radio_checked_state( $name, $value, $checked );

			default:
				return $this->compare_values( $name, $value );
		}
	}

	/**
	 * Get the check state for a checkbox input.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  bool   $checked
	 * @return bool
	 */
	protected function get_checkbox_checked_state( $name, $value, $checked ) {
		$request = $this->request( $name );

		if ( ! $request && null !== $this->session && ! $this->old_input_is_empty() && is_null( $this->old( $name ) ) ) {
			return false;
		}

		if ( $this->missing_old_and_model( $name ) && is_null( $request ) ) {
			return $checked;
		}

		$posted = $this->get_value_attribute( $name, $checked );

		if ( is_array( $posted ) ) {
			return in_array( $value, $posted );
		}

		if ( $posted instanceof Collection ) {
			return $posted->contains( 'id', $value );
		}

		return (bool) $posted;
	}

	/**
	 * Get the check state for a radio input.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  bool   $checked
	 * @return bool
	 */
	protected function get_radio_checked_state( $name, $value, $checked ) {
		$request = $this->request( $name );

		if ( ! $request && $this->missing_old_and_model( $name ) ) {
			return $checked;
		}

		return $this->compare_values( $name, $value );
	}

	/**
	 * Determine if the provide value loosely compares to the value assigned to the field.
	 * Use loose comparison because Laravel model casting may be in affect and therefore
	 * 1 == true and 0 == false.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return bool
	 */
	protected function compare_values( $name, $value ) {
		/* @noinspection TypeUnsafeComparisonInspection */
		return $this->get_value_attribute( $name ) == $value;
	}

	/**
	 * Determine if old input or model input exists for a key.
	 *
	 * @param  string $name
	 * @return bool
	 */
	protected function missing_old_and_model( $name ) {
		return ( is_null( $this->old( $name ) ) && is_null( $this->get_model_value_attribute( $name ) ) );
	}

	/**
	 * Create a datalist box field.
	 *
	 * @param  string $id
	 * @param  array  $list
	 * @return \AweBooking\Component\Html\Html_String
	 */
	public function datalist( $id, $list = [] ) {
		$this->type = 'datalist';

		$attributes['id'] = $id;

		$html = [];

		if ( $this->is_associative_array( $list ) ) {
			foreach ( $list as $value => $display ) {
				$html[] = $this->option( $display, $value, null, [] );
			}
		} else {
			foreach ( $list as $value ) {
				$html[] = $this->option( $value, $value, null, [] );
			}
		}

		$attributes = $this->attributes( $attributes );

		$list = implode( '', $html );

		return $this->to_html_string( "<datalist{$attributes}>{$list}</datalist>" );
	}

	/**
	 * Determine if an array is associative.
	 *
	 * @param  array $array
	 * @return bool
	 */
	protected function is_associative_array( $array ) {
		return ( array_values( $array ) !== $array );
	}

	/**
	 * Get the ID attribute for a field name.
	 *
	 * @param  string $name
	 * @param  array  $attributes
	 * @return string|null
	 */
	public function get_id_attribute( $name, $attributes ) {
		if ( array_key_exists( 'id', $attributes ) ) {
			return $attributes['id'];
		}

		if ( in_array( $name, $this->labels ) ) {
			return $name;
		}

		return null;
	}

	/**
	 * Get the value that should be assigned to the field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return mixed
	 */
	public function get_value_attribute( $name, $value = null ) {
		if ( is_null( $name ) ) {
			return $value;
		}

		$old = $this->old( $name );
		if ( '_method' !== $name && ! is_null( $old ) ) {
			return $old;
		}

		$request = $this->request( $name );
		if ( '_method' !== $name && ! is_null( $request ) ) {
			return $request;
		}

		if ( ! is_null( $value ) ) {
			return $value;
		}

		if ( $this->model ) {
			return $this->get_model_value_attribute( $name );
		}

		return null;
	}

	/**
	 * Take Request in fill process
	 *
	 * @param bool $consider
	 */
	public function consider_request( $consider = true ) {
		$this->consider_request = $consider;
	}

	/**
	 * Get value from current Request
	 *
	 * @param string $name
	 * @return string|array|null
	 */
	protected function request( $name ) {
		if ( ! $this->consider_request || ! $this->request ) {
			return null;
		}

		return $this->request->input(
			$this->transform_key( $name )
		);
	}

	/**
	 * Get the model value that should be assigned to the field.
	 *
	 * @param  string $name
	 * @return mixed
	 */
	protected function get_model_value_attribute( $name ) {
		$key = $this->transform_key( $name );

		if ( method_exists( $this->model, 'get' ) ) {
			return $this->model->get( $key );
		}

		return data_get( $this->model, $this->transform_key( $name ) );
	}

	/**
	 * Get a value from the session's old input.
	 *
	 * @param  string $name
	 * @return mixed
	 */
	public function old( $name ) {
		if ( ! $this->session ) {
			return null;
		}

		$payload = $this->session->get_old_input(
			$key = $this->transform_key( $name )
		);

		if ( ! is_array( $payload ) ) {
			return $payload;
		}

		if ( ! in_array( $this->type, [ 'select', 'checkbox' ] ) ) {
			if ( ! isset( $this->payload[ $key ] ) ) {
				$this->payload[ $key ] = abrs_collect( $payload );
			}

			if ( ! empty( $this->payload[ $key ] ) ) {
				return $this->payload[ $key ]->shift();
			}
		}

		return $payload;
	}

	/**
	 * Determine if the old input is empty.
	 *
	 * @return bool
	 */
	public function old_input_is_empty() {
		return $this->session && count( (array) $this->session->get_old_input() ) === 0;
	}

	/**
	 * Transform key from array to dot syntax.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	protected function transform_key( $key ) {
		return str_replace( [ '.', '[]', '[', ']' ], [ '_', '', '.', '' ], $key );
	}

	/**
	 * Transform the string to an Html serializable object.
	 *
	 * @param string $html
	 * @return \AweBooking\Component\Html\Html_String
	 */
	protected function to_html_string( $html ) {
		return new Html_String( $html );
	}

	/**
	 * Get the session store implementation.
	 *
	 * @return \Awethemes\WP_Session\Store $session
	 */
	public function get_session_store() {
		return $this->session;
	}

	/**
	 * Set the session store implementation.
	 *
	 * @param \Awethemes\WP_Session\Store $session
	 * @return $this
	 */
	public function set_session_store( Store $session ) {
		$this->session = $session;

		return $this;
	}
}
