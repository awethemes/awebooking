<?php
namespace AweBooking\Admin\Settings;

class Appearance_Setting extends Abstract_Setting {
	/**
	 * The setting ID.
	 *
	 * @var string
	 */
	protected $form_id = 'appearance';

	/**
	 * Get the setting label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Appearance', 'awebooking' );
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	public function setup_fields() {
		$this->add_field([
			'id'    => '__images',
			'type'  => 'title',
			'name'  => esc_html__( 'Room images', 'awebooking' ),
			'desc' 	=> sprintf( __( 'These settings affect the display and dimensions of images in your catalog - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a target="_blank" href="%s">regenerate your thumbnails</a>.', 'awebooking' ), 'https://wordpress.org/plugins/regenerate-thumbnails/' ),
		]);

		$this->add_field([
			'id'              => 'awebooking_archive_image_size',
			'type'            => 'abrs_image_size',
			'name'            => esc_html__( 'Archive images', 'awebooking' ),
			'desc'            => esc_html__( 'This size is usually used in room type listings. (W x H)', 'awebooking' ),
			'tooltip'         => true,
			'default'         => [
				'width'  => 600,
				'height' => 400,
				'crop'   => 'on',
			],
		]);

		$this->add_field([
			'id'              => 'awebooking_single_image_size',
			'type'            => 'abrs_image_size',
			'name'            => esc_html__( 'Single room image', 'awebooking' ),
			'desc'            => esc_html__( 'This is the size used by the main image on the room type page. (W x H)', 'awebooking' ),
			'tooltip'         => true,
			'default'         => [
				'width'  => 600,
				'height' => 400,
				'crop'   => 'on',
			],
		]);

		$this->add_field([
			'id'              => 'awebooking_thumbnail_image_size',
			'type'            => 'abrs_image_size',
			'name'            => esc_html__( 'Room thumbnails', 'awebooking' ),
			'desc'            => esc_html__( 'This size is usually used for the gallery of images on the room type page. (W x H)', 'awebooking' ),
			'tooltip'         => true,
			'default'         => [
				'width'  => 300,
				'height' => 300,
				'crop'   => 'on',
			],
		]);

		$this->add_field([
			'id'    => '__display_datepicker_title',
			'type'  => 'title',
			'name'  => esc_html__( 'Date Picker', 'awebooking' ),
		]);

		$this->add_field([
			'id'              => 'display_datepicker_minnights',
			'type'            => 'text',
			'name'            => esc_html__( 'Minimum nights', 'awebooking' ),
			'desc'            => esc_html__( 'Minimum nights required to select a range of dates.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'step'  => 1,
				'style' => 'width: 100px;',
			],
		]);

		$this->add_field([
			'id'              => 'display_datepicker_maxnights',
			'type'            => 'text',
			'name'            => esc_html__( 'Maximum dates', 'awebooking' ),
			'desc'            => esc_html__( 'The maximum selectable date. When set to null, there is no maximum. A number of days from today. For example 2 represents two days from today. All the dates after the additional date will be disabled.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'step'  => 1,
				'style' => 'width: 100px;',
			],
		]);

		$this->add_field([
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
		]);

		$this->add_field([
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
		]);

		$this->add_field([
			'id'              => 'display_datepicker_disabledates',
			'type'            => 'text',
			'name'            => esc_html__( 'Disabled dates', 'awebooking' ),
			'desc'            => esc_html__( 'Enter dates by ", " separating values in this format: `Y-m-d`. All the dates passed to the list will be disabled.', 'awebooking' ),
		]);

		$this->add_field([
			'id'                => 'display_datepicker_disabledays',
			'type'              => 'multicheck_inline',
			'name'              => esc_html__( 'Disabled days', 'awebooking' ),
			'desc'              => esc_html__( 'All the days passed to the list will be disabled.', 'awebooking' ),
			'options_cb'        => 'abrs_days_of_week',
			'select_all_button' => false,
		]);

		$this->add_field([
			'id'              => 'display_datepicker_showmonths',
			'type'            => 'select',
			'name'            => esc_html__( 'The number of months displayed', 'awebooking' ),
			'desc'            => esc_html__( 'Display on month or two months.', 'awebooking' ),
			'options'         => [
				1 => 1,
				2 => 2,
			],
			'sanitization_cb' => 'absint',
		]);
	}
}
