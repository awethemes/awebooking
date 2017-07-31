<?php

use AweBooking\Room;
use AweBooking\Room_type;
use AweBooking\Support\Date_Utils;
use AweBooking\Support\Date_Period;
use AweBooking\BAT\Booking_Request;
use Skeleton\CMB2\CMB2;

$cmb2 = new CMB2([
	'id' => 'asdasdasda',
	'show_on' => [ 'options-page' => 'add-booking-item' ],
	'hookup' => false,
	'object_types' => 'options-page',
]);

$cmb2->add_field( array(
	'id'                => 'check_in',
	'type'              => 'text_date',
	'name'              => esc_html__( 'Check-in', 'awebooking' ),
	'validate'          => 'required|date',
	'attributes'        => [ 'placeholder' => AweBooking::DATE_FORMAT ],
	'date_format'       => AweBooking::DATE_FORMAT,
));

$cmb2->add_field( array(
	'id'          => 'check_out',
	'type'        => 'text_date',
	'name'        => esc_html__( 'Check-out', 'awebooking' ),
	'validate'    => 'required|date',
	'attributes'  => [ 'placeholder' => AweBooking::DATE_FORMAT ],
	'date_format' => AweBooking::DATE_FORMAT,
));

$cmb2->add_field( array(
	'id'          => 'add_room',
	'type'        => 'select',
	'name'        => esc_html__( 'Room', 'awebooking' ),
	'validate'    => 'required',
	'show_option_none' => esc_html__( 'Choose a room...', 'awebooking' ),
));

$cmb2->add_field( array(
	'id'               => 'adults',
	'type'             => 'select',
	'name'             => esc_html__( 'Number of adults', 'awebooking' ),
	'default'          => 1,
	'validate'         => 'required|numeric|min:1',
	'validate_label'   => esc_html__( 'Adults', 'awebooking' ),
	'sanitization_cb'  => 'absint',
));

$cmb2->add_field( array(
	'id'              => 'children',
	'type'            => 'select',
	'name'            => esc_html__( 'Number of children', 'awebooking' ),
	'default'         => 0,
	'validate'        => 'required|numeric|min:0',
	'sanitization_cb' => 'absint',
));

$cmb2->add_field( array(
	'id'         => 'price',
	'type'       => 'text_small',
	'name'       => esc_html__( 'Price (per night)', 'awebooking' ),
	'validate'   => 'required|numeric:min:0',
	'sanitization_cb' => 'awebooking_sanitize_price_number',
));

if ( ! empty( $_REQUEST['check_in'] ) ) {
	$check_in_request = sanitize_text_field( wp_unslash( $_REQUEST['check_in'] ) );

	if ( Date_Utils::is_standard_date_format( $check_in_request ) ) {
		$cmb2->get_field( 'check_in' )->set_prop( 'default', $check_in_request );
	}
}

if ( ! empty( $_REQUEST['check_out'] ) ) {
	$check_out_request = sanitize_text_field( wp_unslash( $_REQUEST['check_out'] ) );

	if ( Date_Utils::is_standard_date_format( $check_out_request ) ) {
		$cmb2->get_field( 'check_out' )->set_prop( 'default', $check_out_request );
	}
}

if ( ! empty( $_REQUEST['add_room'] ) ) {
	$add_room = sanitize_text_field( wp_unslash( $_REQUEST['add_room'] ) );
	$cmb2->get_field( 'add_room' )->set_prop( 'default', $add_room );
}

if ( ! empty( $_REQUEST['check_in'] ) && ! empty( $_REQUEST['check_out'] ) ) {
	try {
		$date_period = new Date_Period(
			sanitize_text_field( wp_unslash( $_REQUEST['check_in'] ) ),
			sanitize_text_field( wp_unslash( $_REQUEST['check_out'] ) ),
			false
		);
	} catch ( \Exception $e ) {
		$cmb2->add_validation_error( 'check_out', $e->getMessage() );
	}

	if ( isset( $date_period ) ) {
		$concierge = awebooking( 'concierge' );

		$request = new Booking_Request( $date_period );
		$results = $concierge->check_availability( $request );

		$options = [];

		foreach ( $results as $availability ) {
			if ( $availability->unavailable() ) {
				continue;
			}

			$room_type = $availability->get_room_type();

			foreach ( $availability->get_rooms() as $room ) {
				$options[ $room->get_id() ] = $room_type['title'] . ' (' . $room['name'] . ')';
			}
		}

		if ( ! empty( $options ) ) {
			$cmb2->get_field( 'add_room' )->set_prop( 'options', $options );
		}
	}
}

if ( isset( $_REQUEST['add_room'] ) ) {
	$the_room = new Room( absint( $_REQUEST['add_room'] ) );

	if ( $the_room->exists() ) {
		$room_type = $the_room->get_room_type();

		$max_adults = $room_type->get_number_adults() + $room_type->get_max_adults();
		$max_children = $room_type->get_number_children() + $room_type->get_max_children();

		$a = range( 1, $max_adults );
		$b = range( 0, $max_children );
		$cmb2->get_field( 'adults' )->set_prop( 'options', array_combine( $a, $a ) );
		$cmb2->get_field( 'children' )->set_prop( 'options', array_combine( $b, $b ) );

		$cmb2->get_field( 'price' )->set_prop( 'default', $room_type->get_base_price()->get_amount() );
	}
}

if ( isset( $_POST['add_room_submit'] ) ) {
	$the_room = new Room( absint( $_REQUEST['add_room'] ) );

	if ( $the_room->exists() ) {
		var_dump($the_booking);
	}
}

CMB2_hookup::enqueue_cmb_css();
CMB2_hookup::enqueue_cmb_js();

?>

<style type="text/css">
	.cmb2-options-page .cmb2-wrap input,
	.cmb2-options-page .cmb2-wrap select {
		width: 100% !important;
	}
</style>

<div class="wrap cmb2-options-page">
	<h1 class="wp-heading-inline" style="margin-bottom: 15px;">Add room to booking #</h1>
	<hr class="wp-header-end">

	<form method="GET" action="">
		<input type="hidden" name="page" value="add-booking-item">

		<div class="" style="width: 420px; float: left; margin-right: 15px;">
			<?php $cmb2->show_form(); ?>
		</div>

		<div class="clear"></div>
		<button class="button" type="submit">Request</button>

		<input class="button" type="submit" name="add_room_submit" value="Add Room">
	</form>

</div>
</div>
