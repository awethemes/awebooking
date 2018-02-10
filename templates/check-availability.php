<?php
/**
 * The Template for displaying check availability page.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-availability.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'awebooking/template_notices' );
?>
<div class="awebooking-availability-container has-sidebar">
	<ul class="room_types awebooking-availability-room-types">
		<form action="<?php echo esc_url( awebooking( 'url' )->route( 'reservation' ) ); ?>" method="POST">
			<?php wp_nonce_field( 'awebooking_reservation', '_wpnonce', true ); ?>
			<?php foreach ( $items as $key => $item ) : ?>
				<?php $room_type_id = $item->get_room_type()->get_id(); ?>
				<li class="awebooking-availability-room-type">
					<div class="awebooking-availability-room-type__media">
						<div class="awebooking-availability-room-type__thumbnail">
							<a href="<?php echo esc_url( get_the_permalink( $room_type_id ) ) ?>" target="_blank">
							<?php
							if ( has_post_thumbnail( $room_type_id ) ) {
								echo get_the_post_thumbnail( $room_type_id, 'awebooking_catalog' );
							} elseif ( awebooking_placeholder_img_src() ) {
								echo awebooking_placeholder_img( 'awebooking_catalog' ); // WPCS: xss ok.
							}
							?>
							</a>

							<h2 class="awebooking-availability-room-type__title">
								<a href="<?php echo esc_url( get_permalink( $room_type_id ) ); ?>" rel="bookmark" target="_blank">
									<?php echo esc_html( $item->get_room_type()->get_title() );?>
								</a>
							</h2>
						</div>
					</div>

					<div class="awebooking-availability-room-type__info">
						<div class="awebooking-rates">

							<div id="rate-1" class="awebooking-rate js-awebooking-rate">
								<div class="awebooking-rate__body">
									<!-- <div class="awebooking-discount">
										<span>Special Deal</span>
									</div> -->

									<h2 class="awebooking-rate__name"><?php echo esc_html( $item->get_room_type()->get_title() ); ?></h2>

									<div class="awebooking-rate__content">
										<div class="awebooking-rate__price">
											<div class="price">
												<!-- <del>
													<span class="awebooking-price-amount amount">
														<span class="awebooking-price-currencySymbol">$</span>99.00
													</span>
												</del> -->

												<ins>
													<span class="awebooking-price-amount amount">
														<span class="awebooking-price-currencySymbol"><?php print $item->get_room_type()->get_base_price(); ?></span>
													</span>
												</ins>

												<a href="#popup-rate-<?php echo absint( $room_type_id ); ?>" class="awebooking-price__info awebooking-price-info"><strong>&#161;</strong></a>
											</div>
											<div class="awebooking-rate__price_detail">
												<p><?php printf( esc_html__( 'price for %d Nights', 'awebooking' ), absint( $reservation->get_stay()->nights() ) ); ?></p>
												<p><?php print $guest->as_string(); // WPCS: xss ok. ?></p>
											</div>
										</div>

										<div class="awebooking-rate__info">
											<p class="awebooking-rate__occupancy">Maximum Occupancy: 2 adults, 1 child</p>
											<p class="awebooking-rate__desc">Tax included in room price</p>
											<p class="awebooking-rate__included">Breakfast included , Free Cancellation</p>
										</div>

									</div>
								</div>

								<div class="awebooking-rate__bottom">
									<?php if ( $item->get_room_type()->get_services() ) : ?>
										<div class="awebooking-rate__actions-left">
											<a class="awebooking-rate__service-btn" href="#" data-init="awebooking-dropdown" data-dropdown="#dr-rate-1">
												<?php esc_html_e( 'Extra services', 'awebooking' ); ?><i><?php echo esc_html_x( '&#x25BC;','awebooking-dropdown', 'awebooking' ); ?></i>
											</a>
											<span class="awebooking-rate__list-services js-awebooking-list-service"></span>
										</div>
									<?php endif; ?>

									<div class="awebooking-rate__actions-right js-awebooking-rate-actions">
										<span class="awebooking-rate__rooms"><span class="count js-awebooking-room-left">10</span> <?php esc_html_e( 'Rooms Left', 'awebooking' ); ?></span>
										<div class="awebooking-rate__book-action">
											<input type="submit" class="awebooking-rate__book js-awebooking-add-room" name="submit[<?php echo esc_attr( $room_type_id ); ?>]" value="<?php esc_html_e( 'Add room', 'awebooking' ); ?>">

										</div>

									</div>

								</div>

								<?php if ( $item->get_room_type()->get_services() ) : ?>
									<div class="awebooking-rate__services awebooking-dropdown-content" id="dr-rate-1">
										<div class="awebooking-booking-form" id="awebooking-booking-form">
											<div class="awebooking-service-items" id="awebooking-service-items">
												<?php foreach ( $item->get_room_type()->get_services() as $service ) : ?>
													<?php $mandatory = ( 'mandatory' === $service->get_type()  ) ? 'checked="checked" disabled="disabled"' : ''; ?>
													<div class="awebooking-service__item">
														<input type="checkbox" id="extra_id_<?php echo esc_attr( $service->get_id() ); ?>" name="awebooking_services[]" value="<?php echo esc_attr( $service->get_id() ); ?>" <?php echo esc_attr( $mandatory ); ?>>
														<label for="extra_id_<?php echo esc_attr( $service->get_id() ); ?>"><?php echo esc_html( $service->get_name() ); ?></label>
														<span><?php print $service->get_describe(); // WPCS: xss ok.?></span>
													</div>
												<?php endforeach; ?>

											</div>
										</div>
									</div>
								<?php endif; ?>

								<!-- <div class="awebooking-rate__bottom">
									<div class="awebooking-rate__actions-left">
										<a href="#">Room Info</a>
										<a href="#">Enquire</a>
									</div>
									<div class="awebooking-rate__actions-right">
										<span class="awebooking-rate__rooms"><span class="count js-awebooking-room-left">10</span> Rooms Left</span>
										<div class="awebooking-rate__book-action">
											<div class="awebooking-rate__nums">
												<a class="awebooking-cal js-awebooking-decrease-room" href="#">-</a>
												<span class="js-awebooking-count-room">0</span>
												<a class="awebooking-cal js-awebooking-increase-room" href="#">+</a>
											</div>
										</div>
									</div>
								</div> -->

								<div id="popup-rate-<?php echo absint( $room_type_id ); ?>" class="breakdown-popup mfp-hide">
									<div class="awebooking-breakdown">
										<div class="awebooking-breakdown__wrapper">
											<div class="awebooking-breakdown__header">
												<h3><?php esc_html_e( 'Rate Informations', 'awebooking' ); ?></h3>
											</div>

											<div class="awebooking-breakdown__content">
												<h5 class="awebooking-breakdown__title"><?php echo esc_html( $item->get_room_type()->get_title() ); ?></h5>

												<table class="table table-condensed awebooking-breakdown__table">
													<thead>
														<tr>
														<th>Date</th>
														<th>Per night</th>
														<th>Extra Adults Cost</th>
														<th>Extra Children Cost</th>
														</tr>
													</thead>

													<tbody>
														<tr>
															<td>Mon 11, Dec</td>
															<td><del>5500</del>400</td>
															<td>3300</td>
															<td>1100</td>
														</tr>

														<tr>
															<td>Mon 11, Dec</td>
															<td><del>5500</del>400</td>
															<td>3300</td>
															<td>1100</td>
														<tr>

													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>

							</div>

						</div>
					</div>
				</li>
			<?php endforeach; ?>

			<input type="hidden" name="room_unit" value="30">
			<input type="hidden" name="room_rate" value="0">
			<input type="hidden" name="adults" value="<?php echo esc_attr( $guest->get_adults() ); ?>">
			<input type="hidden" name="children" value="<?php echo esc_attr( $guest->get_children() ); ?>">
			<input type="hidden" name="infants" value="<?php echo esc_attr( $guest->get_infants() ); ?>">
		</form>
	</ul>

	<div class="awebooking-availability-sidebar">
		<h2 class="awebooking-cart-title"><?php esc_html_e( 'Booking Summary', 'awebooking' ); ?></h2>

		<div class="awebooking-cart">
			<form action="<?php echo esc_url( awebooking( 'url' )->route( 'reservation' ) ); ?>" method="POST">
				<?php if ( isset( $reservation ) && $reservation->get_rooms()->isNotEmpty() ) : ?>
					<div class="awebooking-cart__header">
						<div class="awebooking-cart__checktime">
							<label for="">Dates</label>
							<p>
								14 Dec 2017 - 16 Dec 2017
							</p>
						</div>

						<div class="awebooking-cart__nights">
							<label for="">Nights</label>
							<p>
								2
							</p>
						</div>
					</div>

					<div class="awebooking-cart__items">
						<ul>
							<?php foreach ( $reservation->get_rooms() as $room_id => $room_item ) : ?>
								<li class="awebooking-cart-item" data-rate="rate-1">
									<div class="awebooking-cart-item__info">
										<h5 class="awebooking-cart-item__rate">
											<?php echo esc_html( $room_item->get_label() ); ?>
										</h5>

										<p class="awebooking-cart-item__guess">
											<?php echo wp_kses_post( $room_item->get_guest() ); ?>
										</p>
										<p><span class="awebooking-rate__list-services js-awebooking-list-service">Buffet Breakfast, Gym Ticket</span></p>
									</div>

									<p class="awebooking-cart-item__price">
										<a href="#" class="awebooking-cart-item__edit js-awebooking-edit-room"><span class="screen-reader-text"><?php esc_html_e( 'Edit', 'awebooking' ); ?></span><?php echo esc_html_x( '&#9998;','awebooking-edit', 'awebooking' ); ?></a>
										<span><?php echo esc_html( $room_item->get_pricing()->get_amount() ); ?></span>
									</p>

									<!-- <a href="#" class="awebooking-cart-item__remove js-awebooking-remove-room">&#x2715;</a> -->
									<input type="submit" name="submit[<?php echo esc_attr( $room_id ); ?>]" value="<?php echo esc_html_x( '&#x2715;','awebooking-remove', 'awebooking' ); ?>">
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<?php $totals = $reservation->get_totals(); ?>

					<div class="awebooking-cart__footer">
						<div class="awebooking-cart__total">
							<label for=""><?php esc_html_e( 'Total', 'awebooking' ); ?></label>
							<p class="awebooking-cart__total-amount js-awebooking-cart-total"><?php echo $totals->get_subtotal(); ?></p>
						</div>
						<div class="awebooking-cart__buttons">
							<a class="btn button awebooking-button" href="#"><?php esc_html_e( 'Book', 'awebooking' ); ?></a>
						</div>
					</div>
				<?php else : ?>
					<?php esc_html_e( 'No Room(s) Selected', 'awebooking' ); ?>
				<?php endif; ?>
			</form>
		</div>

	</div>
</div>
