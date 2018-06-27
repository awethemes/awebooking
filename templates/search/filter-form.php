<?php
/**
 * This template show the filter form.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/filter-form.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

use AweBooking\Reservation\Url_Generator;

global $res_request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! abrs_get_option( 'display_filter_form', true ) ) {
	return;
}

$url_generator = new Url_Generator( $res_request );
?>

<div class="filterbox">
	<div class="filterbox__wrap">
		<div class="columns">
			<div class="column filterbox__box">
				<div class="filterbox__box-wrap">
					<label class="filterbox__label"><?php esc_html_e( 'Sort by:', 'awebooking' ); ?></label>
					<select name="sortby" class="filterbox__sortby" onChange="window.document.location.href=this.options[this.selectedIndex].value;">
						<option value="<?php echo esc_url( $url_generator->get_availability_url( [ 'sortby' => 'cheapest' ] ) ); ?>"><?php esc_html_e( 'Cheapest price first', 'awebooking' ); ?></option>
						<option value="<?php echo esc_url( $url_generator->get_availability_url( [ 'sortby' => 'highest' ] ) ); ?>"><?php esc_html_e( 'Highest price first', 'awebooking' ); ?></option>
					</select>
				</div>
			</div>

			<div class="column filterbox__box">
				<div class="filterbox__box-wrap">
					<label class="filterbox__label"><?php esc_html_e( 'Show price:', 'awebooking' ); ?></label>
					<ul class="filterbox__showprice">
						<li class="filterbox__showprice-item active">
							<a href="<?php echo esc_url( $url_generator->get_availability_url( [ 'showprice' => 'average' ] ) ); ?>"><?php esc_html_e( 'Per room per night', 'awebooking' ); ?></a>
						</li>
						<li class="filterbox__showprice-item">
							<a href="<?php echo esc_url( $url_generator->get_availability_url( [ 'showprice' => 'total' ] ) ); ?>"><?php esc_html_e( 'Price for whole stay', 'awebooking' ); ?></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
