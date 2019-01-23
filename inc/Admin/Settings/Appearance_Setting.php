<?php

namespace AweBooking\Admin\Settings;

class Appearance_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->form_id  = 'appearance';
		$this->label    = esc_html__( 'Appearance', 'awebooking' );
		$this->priority = 20;
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	public function setup_fields() {
		$general = $this->add_section( 'general', [
			'title' => esc_html__( 'General', 'awebooking' ),
		] );

		$general->add_field( [
			'id'   => '__search_page',
			'type' => 'title',
			'name' => esc_html__( 'Search page', 'awebooking' ),
		] );

		$general->add_field( [
			'id'      => 'display_search_form_on_search',
			'type'    => 'abrs_toggle',
			'name'    => esc_html__( 'Display search form?', 'awebooking' ),
			'default' => 'on',
		] );

		$general->add_field( [
			'id'      => 'display_filter_form',
			'type'    => 'abrs_toggle',
			'name'    => esc_html__( 'Display filter form?', 'awebooking' ),
			'default' => 'on',
		] );

		$general->add_field( [
			'id'   => '__search_form',
			'type' => 'title',
			'name' => esc_html__( 'Search form', 'awebooking' ),
		] );

		$general->add_field( [
			'id'      => 'use_experiment_style',
			'type'    => 'abrs_toggle',
			'name'    => esc_html__( 'Enable experiment form style', 'awebooking' ),
			'default' => 'off',
		] );

		$general->add_field( [
			'id'      => 'search_form_style',
			'type'    => 'select',
			'name'    => esc_html__( 'Search form style', 'awebooking' ),
			'options' => apply_filters( 'abrs_search_form_style', [
				'horizontal'       => esc_html__( 'Horizontal', 'awebooking' ),
				'horizontal-agoda' => esc_html__( 'Horizontal Agoda', 'awebooking' ),
			] ),
		] );

		$general->add_field( [
			'id'      => 'search_form_aligment',
			'type'    => 'select',
			'name'    => esc_html__( 'Search form aligment', 'awebooking' ),
			'options' => apply_filters( 'abrs_search_form_aligment', [
				'left'   => esc_html__( 'Left', 'awebooking' ),
				'center' => esc_html__( 'Center', 'awebooking' ),
				'right'  => esc_html__( 'Right', 'awebooking' ),
			] ),
		] );

		$general->add_field( [
			'id'              => 'search_form_max_adults',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Maximum adults', 'awebooking' ),
			'desc'            => esc_html__( 'The maximum number of adults can be selected', 'awebooking' ),
			'tooltip'         => true,
			'attributes'      => [
				'type' => 'number',
			],
			'default'         => '6',
			'sanitization_cb' => 'absint',
		] );

		$general->add_field( [
			'id'              => 'search_form_max_children',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Maximum children', 'awebooking' ),
			'desc'            => esc_html__( 'The maximum number of children can be selected', 'awebooking' ),
			'tooltip'         => true,
			'default'         => '6',
			'show_on_cb'      => 'abrs_children_bookable',
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type' => 'number',
			],
		] );

		$general->add_field( [
			'id'              => 'search_form_max_infants',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Maximum infants', 'awebooking' ),
			'desc'            => esc_html__( 'The maximum number of infants can be selected', 'awebooking' ),
			'tooltip'         => true,
			'default'         => '6',
			'show_on_cb'      => 'abrs_infants_bookable',
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type' => 'number',
			],
		] );

		$general->add_field( [
			'id'   => '__search_result',
			'type' => 'title',
			'name' => esc_html__( 'Search result', 'awebooking' ),
		] );

		$general->add_field( [
			'id'      => 'display_price',
			'type'    => 'select',
			'name'    => esc_html__( 'Price displayed on search', 'awebooking' ),
			'options' => apply_filters( 'abrs_display_price', [
				'total'       => esc_html__( 'Total', 'awebooking' ),
				'average'     => esc_html__( 'Average', 'awebooking' ),
				'first_night' => esc_html__( 'First night', 'awebooking' ),
			] ),
		] );

		$general->add_field( [
			'id'   => '__images',
			'type' => 'title',
			'name' => esc_html__( 'Room images', 'awebooking' ),
			/* translators: Link */
			'desc' => sprintf( wp_kses_post( __( 'These settings affect the display and dimensions of images. After changing these settings you may need to <a target="_blank" href="%s">regenerate your thumbnails</a>.', 'awebooking' ) ), esc_url( 'https://wordpress.org/plugins/regenerate-thumbnails' ) ),
		] );

		$general->add_field( [
			'id'      => 'archive_image_size',
			'type'    => 'abrs_image_size',
			'name'    => esc_html__( 'Archive images', 'awebooking' ),
			'desc'    => esc_html__( 'This size is usually used in room type listings. (W x H)', 'awebooking' ),
			'tooltip' => true,
			'default' => [
				'width'  => 600,
				'height' => 400,
				'crop'   => 'on',
			],
		] );

		$general->add_field( [
			'id'      => 'single_image_size',
			'type'    => 'abrs_image_size',
			'name'    => esc_html__( 'Single room image', 'awebooking' ),
			'desc'    => esc_html__( 'This is the size used by the main image on the room type page. (W x H)', 'awebooking' ),
			'tooltip' => true,
			'default' => [
				'width'  => 900,
				'height' => 600,
				'crop'   => 'on',
			],
		] );

		$general->add_field( [
			'id'      => 'thumbnail_image_size',
			'type'    => 'abrs_image_size',
			'name'    => esc_html__( 'Room thumbnails', 'awebooking' ),
			'desc'    => esc_html__( 'This size is usually used for the gallery of images on the room type page. (W x H)', 'awebooking' ),
			'tooltip' => true,
			'default' => [
				'width'  => 300,
				'height' => 300,
				'crop'   => 'on',
			],
		] );

		$general->add_field( [
			'id'   => '__custom_css__',
			'type' => 'title',
			'name' => esc_html__( 'Custom CSS', 'awebooking' ),
		] );

		$general->add_field( [
			'id'              => 'custom_css',
			'type'            => 'textarea_code',
			'name'            => esc_html__( 'Custom CSS', 'awebooking' ),
			'show_names'      => false,
			'before'          => function () {
				echo '<p class="abrs-mt0">' . esc_html__( 'Add your own CSS code here to customize the appearance and layout of current theme',
						'awebooking' ) . '. <a href="https://codex.wordpress.org/CSS" class="external-link" target="_blank">' . esc_html__( 'Learn more about CSS',
						'awebooking' ) . '</a></p>';
			},
			'sanitization_cb' => function ( $value ) {
				return strip_tags( $value );
			},
			'attributes'      => [
				'data-codeeditor' => json_encode( [
					'codemirror' => [ 'mode' => 'css' ],
				] ),
			],
		] );

		$datepicker = $this->add_section( 'datepicker', [
			'title' => esc_html__( 'Datepicker', 'awebooking' ),
		] );

		$datepicker->add_field( [
			'id'   => '__display_datepicker_title',
			'type' => 'title',
			'name' => esc_html__( 'Date Picker', 'awebooking' ),
		] );

		$datepicker->add_field( [
			'id'              => 'display_datepicker_minnights',
			'type'            => 'text',
			'name'            => esc_html__( 'Minimum nights', 'awebooking' ),
			'desc'            => esc_html__( 'Minimum nights required to select a range of dates.', 'awebooking' ),
			'default'         => 1,
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'min'   => 1,
				'step'  => 1,
				'style' => 'width: 100px;',
			],
		] );

		$datepicker->add_field( [
			'id'              => 'display_datepicker_maxnights',
			'type'            => 'text',
			'name'            => esc_html__( 'Maximum nights', 'awebooking' ),
			'desc'            => esc_html__( 'Maximum nights required to select a range of dates.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'step'  => 1,
				'min'   => 0,
				'style' => 'width: 100px;',
			],
		] );

		$datepicker->add_field( [
			'id'              => 'display_datepicker_mindate',
			'type'            => 'text',
			'name'            => esc_html__( 'Minimum dates', 'awebooking' ),
			'desc'            => esc_html__( 'The minimum selectable date. When set to "0", there is no minimum. A number of days from today. For example 2 represents two days from today. All the dates before the additional date will be disabled.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'step'  => 1,
				'style' => 'width: 100px;',
			],
		] );

		$datepicker->add_field( [
			'id'              => 'display_datepicker_maxdate',
			'type'            => 'text',
			'name'            => esc_html__( 'Maximum dates', 'awebooking' ),
			'desc'            => esc_html__( 'The maximum selectable date. When set to "0", there is no maximum. A number of days from today. For example 2 represents two days from today. All the dates after the additional date will be disabled.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'step'  => 1,
				'style' => 'width: 100px;',
			],
		] );

		$datepicker->add_field( [
			'id'   => 'display_datepicker_disabledates',
			'type' => 'text',
			'name' => esc_html__( 'Disabled dates', 'awebooking' ),
			'desc' => esc_html__( 'Enter dates by ", " separating values in this format: `Y-m-d`. All the dates passed to the list will be disabled.', 'awebooking' ),
		] );

		$datepicker->add_field( [
			'id'                => 'display_datepicker_disabledays',
			'type'              => 'multicheck_inline',
			'name'              => esc_html__( 'Disabled week days', 'awebooking' ),
			'desc'              => esc_html__( 'All the days passed to the list will be disabled.', 'awebooking' ),
			'select_all_button' => false,
			'options_cb'        => 'abrs_days_of_week',
			'sanitization_cb'   => 'abrs_sanitize_days_of_week',
		] );

		$datepicker->add_field( [
			'id'              => 'display_datepicker_showmonths',
			'type'            => 'select',
			'name'            => esc_html__( 'Months displayed', 'awebooking' ),
			'desc'            => esc_html__( 'Number of months show in date picker.', 'awebooking' ),
			'options'         => [ '1' => 1, '2' => 2 ], // @codingStandardsIgnoreLine
			'sanitization_cb' => 'absint',
		] );
	}
}
