<?php
namespace AweBooking\Admin\Metaboxes;

use Awethemes\Http\Request;
use AweBooking\Model\Booking;
use AweBooking\Component\Form\Form_Builder;

class Booking_Main_Metabox {
	/**
	 * The form builder.
	 *
	 * @var \AweBooking\Component\Form\Form_Builder
	 */
	protected $form_builder;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->form_builder = new Form_Builder( 'room-type' );

		$this->form_fields( $this->form_builder );
	}

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		global $the_booking;

		if ( is_null( $the_booking ) ) {
			$the_booking = abrs_get_booking( $post );
		}

		if ( 0 == $the_booking->total ) {
			$the_booking->calculate_totals();
		}

		// Prepare form builder.
		$form = $this->form_builder;
		$form->object_id( $post->ID );
		$form->prepare_fields();

		// Print the core nonce field.
		wp_nonce_field( 'awebooking_save_data', '_awebooking_nonce' );

		include trailingslashit( __DIR__ ) . 'views/html-booking-main.php';
	}

	/**
	 * Handle save the the metabox.
	 *
	 * @param \WP_Post                $post    The WP_Post object instance.
	 * @param \Awethemes\Http\Request $request The HTTP Request.
	 */
	public function save( $post, Request $request ) {
		$booking = abrs_get_booking( $post );

		// Get the sanitized values.
		$values = $this->form_builder->handle( $request );

		$booking->fill([
			'date_created'            => $values->get( '_date_created', current_time( 'timestamp' ) ),
			'source'                  => $values->get( '_source', 'website' ),
			'customer_id'             => absint( $values->get( '_customer_id', 0 ) ),
			'arrival_time'            => $values->get( '_arrival_time', '' ),
			'customer_title'          => $values->get( '_customer_title', '' ),
			'customer_first_name'     => $values->get( '_customer_first_name', '' ),
			'customer_last_name'      => $values->get( '_customer_last_name', '' ),
			'customer_address'        => $values->get( '_customer_address', '' ),
			'customer_address_2'      => $values->get( '_customer_address_2', '' ),
			'customer_city'           => $values->get( '_customer_city', '' ),
			'customer_state'          => $values->get( '_customer_state', '' ),
			'customer_postal_code'    => $values->get( '_customer_postal_code', '' ),
			'customer_country'        => $values->get( '_customer_country', '' ),
			'customer_company'        => $values->get( '_customer_company', '' ),
			'customer_phone'          => $values->get( '_customer_phone', '' ),
			'customer_email'          => $values->get( '_customer_email', '' ),
		]);

		// Manually set the status.
		$booking->set_status( $values->get( '_status' ) );

		// Fire action before save.
		do_action( 'awebooking/process_booking_data', $booking, $values, $request );

		// Save the data.
		if ( $booking->save() ) {
			abrs_admin_notices( 'Successfully updated', 'success' )->dialog();
		}

		$booking->setup_dates();
	}

	/**
	 * Register the fields on the form.
	 *
	 * @param  \AweBooking\Component\Form\Form_Builder $form The form builder.
	 * @return void
	 */
	protected function form_fields( $form ) {
		$general = $form->add_section( 'general' );

		$general->add_field([
			'id'          => '_date_created',
			'type'        => 'text_datetime_timestamp',
			'name'        => esc_html__( 'Date created', 'awebooking' ),
			'escape_cb'   => false,
			'date_format' => 'Y-m-d',
			'time_format' => 'H:i',
			'attributes'  => [
				'data-timepicker' => json_encode([
					'timeFormat' => 'HH:mm',
					'stepMinute' => 1,
				]),
			],
			'default_cb'  => function() {
				return get_the_time( 'U' );
			},
		]);

		$general->add_field([
			'id'          => '_source',
			'type'        => 'select',
			'name'        => esc_html__( 'Source', 'awebooking' ),
			'classes'     => 'with-selectize',
			// 'options_cb'  => Dropdown::cb( 'get_reservation_sources' ),
		]);

		$general->add_field([
			'id'          => '_status',
			'type'        => 'select',
			'name'        => esc_html__( 'Status', 'awebooking' ),
			'classes'     => 'with-selectize',
			'options_cb'  => 'abrs_get_booking_statuses',
			'escape_cb'   => false,
			'default_cb'  => function() {
				return get_post_status( get_the_ID() );
			},
		]);

		$general->add_field([
			'id'         => '_customer_id',
			'type'       => 'select',
			'name'       => esc_html__( 'Customer', 'awebooking' ),
			'options_cb' => $this->get_customer_options(),
			'classes'    => 'selectize-search-customer abrs-mb0',
		]);

		// Booking.
		$booking = $form->add_section( 'booking' );

		$booking->add_field([
			'id'               => '_arrival_time',
			'type'             => 'select',
			'name'             => esc_html__( 'Estimated time of arrival', 'awebooking' ),
			'options_cb'       => 'abrs_list_hours',
			'classes'          => 'with-selectize',
			'show_option_none' => esc_html__( 'I don\'t know', 'awebooking' ),
		]);

		$booking->add_field([
			'id'              => 'excerpt',
			'type'            => 'textarea',
			'name'            => esc_html__( 'Special requests', 'awebooking' ),
			'save_field'      => false, // Let wp handle save this.
			'escape_cb'       => false,
			'attributes'      => [ 'rows' => 5 ],
			'default_cb'      => function() {
				return get_post_field( 'post_excerpt', get_the_ID() );
			},
		]);

		// Customer.
		$customer = $form->add_section( 'customer' );

		$customer->add_field([
			'id'               => '_customer_title',
			'type'             => 'select',
			'name'             => esc_html__( 'Title', 'awebooking' ),
			'options_cb'       => 'abrs_list_common_titles',
			'classes'          => 'with-selectize',
			'show_option_none' => '---',
		]);

		$customer->add_field([
			'id'       => '_customer_first_name',
			'type'     => 'text',
			'name'     => esc_html__( 'First name', 'awebooking' ),
			'col-half' => true,
		]);

		$customer->add_field([
			'id'       => '_customer_last_name',
			'type'     => 'text',
			'name'     => esc_html__( 'Last name', 'awebooking' ),
			'col-half' => true,
		]);

		$customer->add_field([
			'id'       => '_customer_company',
			'type'     => 'text',
			'name'     => esc_html__( 'Company', 'awebooking' ),
		]);

		$customer->add_field([
			'id'       => '_customer_address',
			'type'     => 'text',
			'name'     => esc_html__( 'Address', 'awebooking' ),
			'col-half' => true,
		]);

		$customer->add_field([
			'id'       => '_customer_address_2',
			'type'     => 'text',
			'name'     => esc_html__( 'Address 2', 'awebooking' ),
			'col-half' => true,
		]);

		$customer->add_field([
			'id'       => '_customer_city',
			'type'     => 'text',
			'name'     => esc_html__( 'City', 'awebooking' ),
			'col-half' => true,
		]);

		$customer->add_field([
			'id'       => '_customer_state',
			'type'     => 'text',
			'name'     => esc_html__( 'State', 'awebooking' ),
			'col-half' => true,
		]);

		$customer->add_field([
			'id'               => '_customer_country',
			'type'             => 'select',
			'name'             => esc_html__( 'Country', 'awebooking' ),
			'classes'          => 'with-selectize',
			'options_cb'       => 'abrs_list_countries',
			'show_option_none' => '---',
			'col-half'         => true,
		]);

		$customer->add_field([
			'id'       => '_customer_postal_code',
			'type'     => 'text',
			'name'     => esc_html__( 'Postal code', 'awebooking' ),
			'col-half' => true,
		]);

		$customer->add_field([
			'id'       => '_customer_phone',
			'type'     => 'text',
			'name'     => esc_html__( 'Phone number', 'awebooking' ),
			'col-half' => true,
		]);

		$customer->add_field([
			'id'       => '_customer_email',
			'type'     => 'text',
			'name'     => esc_html__( 'Email address', 'awebooking' ),
			'col-half' => true,
		]);
	}

	/**
	 * Returns the current booking customer for the select.
	 *
	 * @return \Closure
	 */
	protected function get_customer_options() {
		return function() {
			global $the_booking;

			if ( is_null( $the_booking ) || empty( $the_booking['customer_id'] ) ) {
				return [];
			}

			// Retrieve user info.
			$user = get_user_by( 'id', $the_booking['customer_id'] );

			/* translators: 1: user display name 2: user ID 3: user email */
			$user_string = sprintf( esc_html__( '%1$s (#%2$s - %3$s)', 'awebooking' ), $user->display_name, absint( $user->ID ), $user->user_email );

			return [ $user->ID => $user_string ];
		};
	}
}
