<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/awebooking/loop/orderby.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="awebooking-ordering">
	<div class="awebooking-field">
		<label><?php esc_html_e( 'Sort by:', 'awebooking' ); ?></label>
		<div class="awebooking-field-group">
			<a href="/?price_up"><?php esc_html_e( 'price low to high', 'awebooking' ); ?></a>
			<span>|</span>
			<a href="/?price_down"><?php esc_html_e( 'price high to low', 'awebooking' ); ?></a>
		</div>
	</div>
</div>
