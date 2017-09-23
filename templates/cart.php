<?php
/**
 * The Template for shortcode cart.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/cart.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="awebooking-cart">
	<div class="awebooking-cart-items awebooking-accordion">
		<h5 class="awebooking-accordion__header">Booking Room 1</h5>
		<div class="awebooking-accordion__content">

			<div class="awebooking-cart-item">
				<div class="awebooking-cart-item__content">
					<div class="awebooking-cart-item__media">
						<a href="#">
							<img width="600" height="338" src="http://awebooking.dev/wp-content/uploads/2017/08/bg-fs-panel.png" class="attachment-awebooking_catalog size-awebooking_catalog wp-post-image" alt="" srcset="http://awebooking.dev/wp-content/uploads/2017/08/bg-fs-panel.png 1920w, http://awebooking.dev/wp-content/uploads/2017/08/bg-fs-panel-300x169.png 300w, http://awebooking.dev/wp-content/uploads/2017/08/bg-fs-panel-768x432.png 768w, http://awebooking.dev/wp-content/uploads/2017/08/bg-fs-panel-1024x576.png 1024w" sizes="(max-width: 600px) 85vw, 600px">
						</a>
					</div>

					<div class="awebooking-cart-item__info">
						<h2 class="awebooking-cart-item__title">
							<a href="#" rel="bookmark">
								Luxury Room
							</a>
						</h2>

						<p class="awebooking-cart-item__price">
							<strong><?php esc_html_e( 'Total:', 'awebooking' ); ?></strong>100$
						</p>
					</div>
				</div>
				<div class="awebooking-cart-item__reservation">
					<h6 class="awebooking-cart-item__reservation-title"><?php esc_html_e( 'Reservation', 'awebooking' ); ?></h6>
					<span><strong><?php esc_html_e( 'Check-in:', 'awebooking' ); ?></strong> September 22, 2017</span>
					<span><strong><?php esc_html_e( 'Check-out:', 'awebooking' ); ?></strong> September 23, 2017</span>
					<span><strong><?php esc_html_e( 'Night(s):', 'awebooking' ); ?></strong> 1</span>
					<span><strong><?php esc_html_e( 'Guest(s):', 'awebooking' ); ?></strong> 2 adults & 1 child</span>
					<span><strong><?php esc_html_e( 'Extra service(s):', 'awebooking' ); ?></strong> Breakfast, Dinner, Taxi</span>
				</div>

				<div class="awebooking-cart-item__buttons">
					<a class="awebooking-cart-item__edit" href="#"><?php esc_html_e( 'Edit', 'awebooking' ); ?></a>
					<a class="awebooking-cart-item__remove" href="#"><?php esc_html_e( 'Remove', 'awebooking' ); ?></a>
				</div>
			</div>
		</div>

		<h5 class="awebooking-accordion__header">Booking Room 2</h5>
		<div class="awebooking-accordion__content">
			<p>
			Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer
			ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit
			amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut
			odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
			</p>
		</div>

		<h5 class="awebooking-accordion__header">Booking Room 3</h5>
		<div class="awebooking-accordion__content">
			<p>
			Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer
			ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit
			amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut
			odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
			</p>
		</div>

		<h5 class="awebooking-accordion__header">Booking Room 4</h5>
		<div class="awebooking-accordion__content">
			<p>
			Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer
			ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit
			amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut
			odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
			</p>
		</div>

	</div>
	<table class="awebooking-cart__total">
		<tbody>
			<tr>
				<td class="text-right"><b><?php esc_html_e( 'Total', 'awebooking' ); ?></b></td>
				<td><b>500$</b></td>
			</tr>
		</tbody>
	</table>

	<div class="awebooking-cart__buttons">
		<a class="btn button awebooking-button" href="#"><?php esc_html_e( 'Proceed to Checkout', 'awebooking' ); ?></a>
	</div>
</div>
