<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/checkout/customer-form.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Skeleton\CMB2\CMB2;

$request = awebooking()->make( 'request' );

dump( awebooking( 'session' )->get_old_input() );

$controls = [
	'customer_first_name' => [
		'type'     => 'text',
		'name'     => esc_html__( 'First Name', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_last_name'      => [
		'type'     => 'text',
		'name'     => esc_html__( 'Last Name', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_title'          => [
		'type'     => 'text',
		'name'     => esc_html__( 'First Name', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_country'        => [
		'type'     => 'text',
		'name'     => esc_html__( 'Country', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_address'        => [
		'type'     => 'text',
		'name'     => esc_html__( 'Address', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_address_2'      => [
		'type'     => 'text',
		'name'     => esc_html__( 'Address 2', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_city'           => [
		'type'     => 'text',
		'name'     => esc_html__( 'City', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_state'          => [
		'type'     => 'text',
		'name'     => esc_html__( 'State', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_postal_code'    => [
		'type'     => 'text',
		'name'     => esc_html__( 'Postal code', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_company'        => [
		'type'     => 'text',
		'name'     => esc_html__( 'Company', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_email'          => [
		'type'     => 'text',
		'name'     => esc_html__( 'Email', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],

	'customer_phone'  => [
		'type'     => 'text',
		'name'     => esc_html__( 'Phone', 'awebooking' ),
		'default'  => '',
		'required' => '',
	],
];

$cmb2 = new CMB2([
	'id' => 'awebooking-checkout-form',
	'object_types' => 'options-page',
	'cmb_styles' => false,
	'enqueue_js' => false,
	'hookup' => false,
	'save_fields' => false,
]);

foreach ( $controls as $key => $field_args ) {
	$cmb2->add_field( array_merge( $field_args, [ 'id' => $key ] ) );
}

$cmb2->show_form();

?>

<?php awebooking( 'template' )->get( 'checkout/payment-form.php' ); ?>

<?php do_action( 'awebooking/checkout/before_submit_form' ); ?>

<button type="submit" class="awebooking-btn" data-type="awebooking"><?php esc_html_e( 'Submit', 'awebooking' ); ?></button>
