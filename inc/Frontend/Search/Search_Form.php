<?php

namespace AweBooking\Frontend\Search;

use WPLibs\Form\Helper\Html_Form;
use AweBooking\Availability\Search\Search_Form as Form;

class Search_Form extends Form {
	/**
	 * The form builder instance.
	 *
	 * @var Html_Form
	 */
	protected $builder;

	/**
	 * //
	 *
	 * @var array
	 */
	protected $atts = [
		'template'        => '',
		'layout'          => '', // Default vertical layout.
		'alignment'       => '',
		'hotel_location'  => true,
		'occupancy'       => true,
		'only_room'       => null,
		'container_class' => '',
	];

	/**
	 * Constructor.
	 *
	 * @param array $atts
	 */
	public function __construct( $atts = [] ) {
		parent::__construct();

		$this->builder = new Html_Form;
		$this->atts    = $atts;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {
		$this->builder->set_model( $this->request );

		if ( $this->http_request ) {
			$this->builder->set_request( $this->http_request );
		}

		$template = $this->atts['template']
			? "search-form-{$this->atts['template']}.php"
			: 'search-form.php';

		return abrs_get_template_content( $template, $this->get_template_data() );
	}

	/**
	 * Return search form ID.
	 *
	 * @param  string|null $name Optional. Name suffix.
	 * @return string
	 */
	public function id( $name = null ) {
		return 'awebooking_searchbox_' . $this->get_instance_number() . ( $name ? '_' . $name : '' );
	}

	/**
	 * Return the search URL.
	 *
	 * @return string
	 */
	public function action() {
		return apply_filters( 'abrs_search_form_action', abrs_get_page_permalink( 'search_results' ) );
	}

	/**
	 * Load default components.
	 *
	 * @return void
	 */
	public function components() {
		if ( $this->atts['hotel_location'] && 'false' !== $this->atts['hotel_location'] ) {
			$this->component( 'hotel' );
		}

		$this->component( 'dates' );
		$this->component( 'occupancy' );

		$this->component( 'button' );
	}

	/**
	 * Load a single component.
	 *
	 * @param string $name The template name without .php suffix.
	 */
	public function component( $name ) {
		$name = rtrim( $name, '.php' ) . '.php';

		$template = 'search-form/' . $name;

		if ( 'on' === abrs_get_option( 'use_experiment_style', 'off' ) ) {
			$template = "search-form/{$this->atts['layout']}/{$name}";

			if ( ! file_exists( abrs_locate_template( $template ) ) ) {
				$template = 'search-form/default/' . $name;
			}
		}

		abrs_get_template( $template, $this->get_template_data() );
	}

	/**
	 * Returns data for the template.
	 *
	 * @return array
	 */
	protected function get_template_data() {
		return apply_filters( 'abrs_search_form_data', [
			'search_form'  => $this,
			'res_request'  => $this->get_request(),
			'http_request' => $this->get_http_request(),
			'atts'         => $this->atts,
		], $this );
	}

	/**
	 * Print hidden inputs.
	 *
	 * @return void
	 */
	public function hiddens() {
		$inputs = [];

		if ( ! get_option( 'permalink_structure' ) ) {
			$inputs[] = $this->builder->hidden( 'p', abrs_get_page_id( 'check_availability' ) );
		}

		if ( abrs_running_on_multilanguage() ) {
			$inputs[] = $this->builder->hidden( 'lang', $this->parameter( 'lang' ) ?: abrs_multilingual()->get_current_language() );
		}

		if ( abrs_is_room_type() && abrs_multiple_hotels() ) {
			$room_type = abrs_get_room_type( get_the_ID() );

			$inputs[] = $this->builder->hidden( 'hotel', $room_type ? $room_type->get( 'hotel_id' ) : '' );
		}

		if ( ! empty( $this->atts['only_room'] ) ) {
			$inputs[] = $this->builder->hidden( 'only', implode( ',', wp_parse_id_list( $this->atts['only_room'] ) ) );
		}

		if ( count( $inputs ) > 0 ) {
			print implode( "\n", $inputs ); // @WPCS: XSS OK.
		}
	}

	/**
	 * Print the "hotels" select.
	 *
	 * @param array $attributes
	 */
	public function hotels( $attributes = [] ) {
		$attributes = $this->prepare_attributes( 'hotel', $attributes );

		// TODO: Improve this in next version.
		$list = abrs_list_hotels()->pluck( 'name', 'id' );

		print $this->builder->select( 'hotel', $list, $this->parameter( 'hotel' ), $attributes ); // @WPCS: XSS OK.
	}

	/**
	 * Print the "check_in" input.
	 *
	 * @param array $attributes
	 * @param array $alt_attributes
	 */
	public function check_in( $attributes = [], $alt_attributes = [] ) {
		$value = $this->parameter( 'check_in' );

		$this->input( 'hidden', 'check_in', $attributes + [
			'value' => $value,
		] );

		$this->input( 'text', 'check_in_alt', $alt_attributes + [
			'name'          => false, // Remove "name" attribute.
			'value'         => $value ? abrs_format_date( $value ) : '',
			'placeholder'   => abrs_get_date_format(),
			'autocomplete'  => 'off',
			'aria-haspopup' => 'true',
		] );
	}

	/**
	 * Print the "check_out" input.
	 *
	 * @param array $attributes
	 * @param array $alt_attributes
	 */
	public function check_out( $attributes = [], $alt_attributes = [] ) {
		$value = $this->parameter( 'check_out' );

		$this->input( 'hidden', 'check_out', $attributes + [
			'value' => $value,
		] );

		$this->input( 'text', 'check_out_alt', array_merge( $alt_attributes, [
			'name'          => false, // Remove "name" attribute.
			'value'         => $value ? abrs_format_date( $value ) : '',
			'placeholder'   => abrs_get_date_format(),
			'autocomplete'  => 'off',
			'aria-haspopup' => 'true',
		] ) );
	}

	/**
	 * Print adults input.
	 *
	 * @param array $attributes
	 */
	public function adults( $attributes = [] ) {
		// $attributes = $this->prepare_attributes( 'adults', $attributes );
		//
		// echo $this->builder->select_range( 'adults', 1, absint( abrs_get_option( 'search_form_max_adults', 6 ) ), null, $attributes );
		// return;

		$this->input( 'number', 'adults', wp_parse_args( $attributes, [
			'step' => 1,
			'min'  => 1,
			'max'  => absint( abrs_get_option( 'search_form_max_adults', 6 ) ),
		] ) );
	}

	/**
	 * Print children input.
	 *
	 * @param array $attributes
	 */
	public function children( $attributes = [] ) {
		$this->input( 'number', 'children', wp_parse_args( $attributes, [
			'step' => 1,
			'min'  => 0,
			'max'  => absint( abrs_get_option( 'search_form_max_children', 6 ) ),
		] ) );
	}

	/**
	 * Print infants input.
	 *
	 * @param array $attributes
	 */
	public function infants( $attributes = [] ) {
		$this->input( 'number', 'infants', wp_parse_args( $attributes, [
			'step' => 1,
			'min'  => 0,
			'max'  => absint( abrs_get_option( 'search_form_max_infants', 6 ) ),
		] ) );
	}

	/**
	 * Print a input.
	 *
	 * @param string $type
	 * @param string $name
	 * @param array  $attributes
	 */
	protected function input( $type, $name, $attributes ) {
		$attributes = $this->prepare_attributes( $name, $attributes );

		if ( isset( $attributes['type'] ) ) {
			$type = $attributes['type'];
		}

		if ( ! isset( $attributes['data-element'] ) ) {
			$attributes['data-element'] = $name;
		}

		$value = isset( $attributes['value'] )
			? $attributes['value']
			: $this->parameter( $name );

		print $this->builder->input( $type, $name, $value, $attributes ); // @WPCS: XSS OK.
	}

	/**
	 * Prepare some attributes by name.
	 *
	 * @param  string $name
	 * @param  array  $defaults
	 * @return array
	 */
	protected function prepare_attributes( $name, $defaults = [] ) {
		$attributes          = [];
		$attributes['id']    = $this->id( $name );
		$attributes['class'] = 'form-input abrs-searchbox__input abrs-searchbox__input--' . $name;

		if ( $this->request->is_locked( $name ) ) {
			$attributes['readonly'] = 'readonly';
		}

		return wp_parse_args( $defaults, $attributes );
	}
}
