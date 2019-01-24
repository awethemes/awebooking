<?php

namespace AweBooking\Admin\Forms;

use AweBooking\Model\Room_Type;
use AweBooking\Component\Form\Form;

class Room_Type_Data_Form extends Form {
	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Room_Type $room_type The room type instance.
	 */
	public function __construct( Room_Type $room_type = null ) {
		parent::__construct( 'room-type', $room_type, 'static' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_fields() {
		// Section general.
		$general = $this->add_section( 'general', [
			'title'    => esc_html__( 'General', 'awebooking' ),
			'priority' => 10,
		]);

		$general->add_field([
			'id'      => '_rooms',
			'type'    => 'include',
			'name'    => esc_html__( 'Rooms', 'awebooking' ),
			'include' => dirname( __DIR__ ) . '/Metaboxes/views/html-room-type-rooms.php',
		]);

		$general->add_field([
			'id'              => 'maximum_occupancy',
			'type'            => 'text_medium',
			'name'            => esc_html__( 'Maximum occupancy', 'awebooking' ),
			'default'         => 2,
			'after'           => $this->datalist_number_callback( 1, 20 ),
			'attributes'      => [ 'list' => '_maximum_occupancy_datalist' ],
			'sanitization_cb' => 'absint',
			'grid_row'        => true,
			'grid_column'     => '3',
		]);

		$general->add_field([
			'id'              => 'number_adults',
			'type'            => 'text',
			'name'            => esc_html__( 'Number Adults', 'awebooking' ),
			'default'         => 2,
			'attributes'      => [ 'list' => 'number_adults_datalist' ],
			'sanitization_cb' => 'absint',
			'grid_column'     => '3',
			'after'           => $this->datalist_number_callback( 1, 20 ),
		]);

		$general->add_field([
			'id'              => 'number_children', // _number_children
			'type'            => 'text',
			'name'            => esc_html__( 'Number Children', 'awebooking' ),
			'default'         => 0,
			'sanitization_cb' => 'absint',
			'show_on_cb'      => 'abrs_children_bookable',
			'attributes'      => [ 'list' => 'number_children_datalist' ],
			'grid_column'     => '3',
			'after'           => $this->datalist_number_callback( 1, 20 ),
		]);

		$general->add_field([
			'id'              => 'number_infants', // _number_infants
			'type'            => 'text',
			'name'            => esc_html__( 'Number Infants', 'awebooking' ),
			'default'         => 0,
			'sanitization_cb' => 'absint',
			'show_on_cb'      => 'abrs_children_bookable',
			'attributes'      => [ 'list' => 'number_infants_datalist' ],
			'grid_column'     => '3',
			'after'           => $this->datalist_number_callback( 1, 20 ),
		]);

		/*$general->add_field( [
			'id'              => 'calculation_infants',
			'type'            => 'abrs_toggle',
			'desc'            => esc_html__( 'Include infants in max calculations?', 'awebooking' ),
			'default'         => 'off',
			'show_names'      => false,
			'translatable'    => false,
			'grid_row'        => true,
		]);*/

		$general->add_field( [
			'id'       => '__occupancy_note',
			'type'     => 'note',
			'note'     => __( "The number of adults, children etc. <b>do not</b> need to add up to the maximum occupancy.\n A room could sleep a maximum of 4 people, but the max adults may be 2 and max children 3. \n This would allow your guests to choose 2 adults and 2 children, or 1 adult and 3 children.\n (But never 2 adults and 3 children as this would exceed the max capacity.)", 'awebooking' ),
			'heading'  => esc_html__( 'Some notes on setting capacity', 'awebooking' ),
			'grid_row' => true,
		]);

		// Section pricing.
		$pricing = $this->add_section( 'pricing', [
			'title'    => esc_html__( 'Pricing', 'awebooking' ),
			'priority' => 50,
		]);

		$pricing->add_field([
			'id'           => 'rack_rate', // _rack_rate
			'type'         => 'abrs_amount',
			'name'         => esc_html__( 'Rack Rate', 'awebooking' ),
			'append'       => abrs_currency_symbol(),
			'tooltip'      => esc_html__( 'Rack rate is the regular everyday rate.', 'awebooking' ),
			'grid_column'  => 3,
			'translatable' => true,
		]);

		$pricing->add_field([
			'id'               => 'tax_rate_id',
			'type'             => 'select',
			'name'             => esc_html__( 'Tax', 'awebooking' ),
			'classes'          => 'with-selectize',
			'options_cb'       => 'abrs_get_tax_rates_for_dropdown',
			'show_option_none' => esc_html__( 'No Tax', 'awebooking' ),
			'translatable'     => true,
			'grid_column'      => 3,
			'show_on_cb'       => function () {
				return abrs_tax_enabled() && ( 'per_room' === abrs_get_tax_rate_model() );
			},
		]);

		$pricing->add_field([
			'id'              => 'rate_min_los',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Min LOS', 'awebooking' ),
			'desc'            => esc_html__( 'Minimum Length of Stay', 'awebooking' ),
			'default'         => 1,
			'tooltip'         => true,
			'sanitization_cb' => 'absint',
			'grid_row'        => true,
			'grid_column'     => 3,
		]);

		$pricing->add_field([
			'id'              => 'rate_max_los',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Max LOS', 'awebooking' ),
			'desc'            => esc_html__( 'Maximum Length of Stay', 'awebooking' ),
			'default'         => 0,
			'tooltip'         => true,
			'sanitization_cb' => 'absint',
			'grid_column'     => 3,
		]);

		$pricing->add_field([
			'id'           => 'rate_services',
			'type'         => 'include',
			'name'         => esc_html__( 'Services (included)', 'awebooking' ),
			'include'      => dirname( __DIR__ ) . '/Metaboxes/views/html-room-type-services.php',
			'grid_row'     => true,
			'translatable' => true,
		]);

		$pricing->add_field([
			'id'           => 'rate_inclusions',
			'type'         => 'text',
			'name'         => esc_html__( 'Inclusions (display in search result)', 'awebooking' ),
			'desc'         => esc_html__( 'What does the package/service include? Ex. Breakfast, Shuttle, etc.', 'awebooking' ),
			'text'         => [ 'add_row_text' => esc_html__( 'Add More', 'awebooking' ) ],
			'translatable' => true,
			'repeatable'   => true,
			'tooltip'      => true,
			'grid_row'     => true,
		]);

		$pricing->add_field([
			'id'           => 'rate_policies',
			'type'         => 'text',
			'name'         => esc_html__( 'Policies (display in search result)', 'awebooking' ),
			'text'         => [ 'add_row_text' => esc_html__( 'Add More', 'awebooking' ) ],
			'desc'         => esc_html__( 'What does the policies apply for this room? Ex. Cancelable, Non-refundable., etc.', 'awebooking' ),
			'translatable' => true,
			'repeatable'   => true,
			'tooltip'      => true,
			'grid_row'     => true,
		]);

		// Section pricing.
		/*$availability = $this->add_section( 'availability', [
			'title'    => esc_html__( 'Availability', 'awebooking' ),
			'priority' => 60,
		]);

		$availability->add_field( [
			'id'                => 'availability_allowed_checkin_days',
			'type'              => 'multicheck_inline',
			'name'              => esc_html__( 'Allowed check-in days', 'awebooking' ),
			'desc'              => esc_html__( 'All the days passed to the list will be abled.', 'awebooking' ),
			'options_cb'        => 'abrs_days_of_week',
			'default'           => [ 0, 1, 2, 3, 4, 5, 6 ],
			'select_all_button' => false,
			'tooltip'           => true,
			'grid_row'          => true,
		]);

		$availability->add_field([
			'id'              => 'availability_period_bookable',
			'type'            => 'text',
			'name'            => esc_html__( 'Period bookable', 'awebooking' ),
			'desc'            => esc_html__( 'When set to "0", there is unlimit. A number of days from today. For example 7 represents seven days from today. All the dates after the additional date will be disabled.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'step'  => 1,
				'style' => 'width: 100px;',
			],
			'tooltip'         => true,
			'grid_row'        => true,
		]);*/

		// Section rooms.
		$rooms = $this->add_section( 'rooms', [
			'title'    => esc_html__( 'Rooms & Amenities', 'awebooking' ),
			'priority' => 100,
		]);

		$rooms->add_field([
			'name'         => esc_html__( 'Area size', 'awebooking' ),
			'id'           => 'area_size',
			'type'         => 'text_small',
			'translatable' => true,
			'grid_row'     => true,
			'grid_column'  => 3,
			'before'       => '<div class="abrs-input-addon">',
			'after'        => '<label for="area_size">' . abrs_get_measure_unit_label() . '</label></div>',
		]);

		$rooms->add_field([
			'id'           => 'view',
			'type'         => 'text_medium',
			'name'         => esc_html__( 'View', 'awebooking' ),
			'translatable' => true,
			'grid_column'  => 3,
			'attributes'   => [ 'list' => 'view_datalist' ],
			'after'        => $this->datalist_view_callback(),
		]);

		$rooms->add_field([
			'id'              => 'bedrooms',
			'type'            => 'select',
			'name'            => esc_html__( 'Bedrooms', 'awebooking' ),
			'grid_column'     => 3,
			'sanitization_cb' => 'absint',
			'options'         => [
				1 => esc_html__( 'Single bedroom', 'awebooking' ),
				2 => esc_html__( 'Double bedrooms', 'awebooking' ),
				3 => esc_html__( '3 bedrooms', 'awebooking' ),
				4 => esc_html__( '4 bedrooms', 'awebooking' ),
				5 => esc_html__( '5 bedrooms', 'awebooking' ),
			],
		]);

		$rooms->add_field([
			'id'              => 'beds',
			'type'            => 'include',
			'name'            => esc_html__( 'Beds', 'awebooking' ),
			'text'            => [ 'add_row_text' => esc_html__( 'Add More', 'awebooking' ) ],
			'include'         => dirname( __DIR__ ) . '/Metaboxes/views/html-room-type-bed.php',
			'repeatable'      => true,
			'translatable'    => true,
			'grid_row'        => true,
			'sanitization_cb' => [ $this, 'sanitize_beds' ],
		]);

		$rooms->add_field([
			'id'          => '__amenities',
			'type'        => '__',
			'name'        => esc_html__( 'Amenities', 'awebooking' ),
			'grid_row'    => true,
			'after_field' => function () {
				post_categories_meta_box( get_post(), [
					'args' => [ 'taxonomy' => 'hotel_amenity' ],
				] );
			},
		]);

		// Section description.
		$description = $this->add_section( 'description', [
			'title'    => esc_html__( 'Description', 'awebooking' ),
			'priority' => 150,
		]);

		$description->add_field( [
			'id'           => 'excerpt',
			'type'         => 'wysiwyg',
			'name'         => esc_html__( 'Short Description', 'awebooking' ),
			'translatable' => true,
			'save_field'   => false,
			'escape_cb'    => false,
			'grid_row'     => true,
			'options'      => [ 'textarea_rows' => 80 ],
			'default_cb'   => function () {
				return get_post_field( 'post_excerpt', get_the_ID() );
			},
		]);

		$description->add_field( [
			'id'           => 'gallery_ids',
			'type'         => 'file_list',
			'name'         => esc_html__( 'Gallery', 'awebooking' ),
			'query_args'   => [ 'type' => 'image' ],
			'text'         => [ 'add_upload_files_text' => esc_html__( 'Select Images', 'awebooking' ) ],
			'preview_size' => [ 125, 125 ],
			'translatable' => true,
			'grid_row'     => true,
		]);

		/*
		 * Fire action after setup fields.
		 *
		 * @param \AweBooking\Component\Form\Form $form The form instance.
		 */
		do_action( 'abrs_setup_room_type_fields', $this );
	}

	/**
	 * Generate datalist HTML callback.
	 *
	 * @param  int $min Min.
	 * @param  int $max Max.
	 * @return \Closure
	 */
	protected function datalist_number_callback( $min, $max ) {
		/**
		 * The datalist callback.
		 *
		 * @param array       $field_args The field args.
		 * @param \CMB2_Field $field      The CMB2 field instance.
		 */
		return function( $field_args, $field ) use ( $min, $max ) {
			echo '<datalist id="' . esc_attr( $field->id() ) . '_datalist">';

			for ( $i = $min; $i <= $max; $i++ ) {
				echo '<option value="' . esc_attr( $i ) . '">';
			}

			echo '</datalist>';
		};
	}

	/**
	 * Generate view datalist HTML callback.
	 *
	 * @return \Closure
	 */
	protected function datalist_view_callback() {
		return function() {
			$view_datalist = apply_filters( 'abrs_list_room_views', [
				esc_html__( 'Airport view', 'awebooking' ),
				esc_html__( 'Bay view', 'awebooking' ),
				esc_html__( 'City view', 'awebooking' ),
				esc_html__( 'Courtyard view', 'awebooking' ),
				esc_html__( 'Golf view', 'awebooking' ),
				esc_html__( 'Harbor view', 'awebooking' ),
				esc_html__( 'Intercoastal view', 'awebooking' ),
				esc_html__( 'Lake view', 'awebooking' ),
				esc_html__( 'Marina view', 'awebooking' ),
				esc_html__( 'Mountain view', 'awebooking' ),
				esc_html__( 'Ocean view', 'awebooking' ),
				esc_html__( 'Pool view', 'awebooking' ),
				esc_html__( 'River view', 'awebooking' ),
				esc_html__( 'Water view', 'awebooking' ),
				esc_html__( 'Beach view', 'awebooking' ),
				esc_html__( 'Garden view', 'awebooking' ),
				esc_html__( 'Park view', 'awebooking' ),
				esc_html__( 'Forest view', 'awebooking' ),
				esc_html__( 'Rain forest view', 'awebooking' ),
				esc_html__( 'Various views', 'awebooking' ),
				esc_html__( 'Limited view', 'awebooking' ),
				esc_html__( 'Slope view', 'awebooking' ),
				esc_html__( 'Strip view', 'awebooking' ),
				esc_html__( 'Countryside view', 'awebooking' ),
				esc_html__( 'Sea view', 'awebooking' ),
			]);

			echo '<datalist id="view_datalist">';

			foreach ( $view_datalist as $val ) {
				echo '<option value="' . esc_attr( $val ) . '">';
			}

			echo '</datalist>';
		};
	}

	/**
	 * Sanitize beds.
	 *
	 * TODO: ...
	 *
	 * @param  array $beds Input data.
	 * @return array
	 */
	public function sanitize_beds( $beds ) {
		$values = [];

		foreach ( (array) $beds as $key => $value ) {
			if ( empty( $value['type'] ) ) {
				continue;
			}

			$values[ $key ] = abrs_clean( $value );
		}

		return array_filter( $values );
	}
}
