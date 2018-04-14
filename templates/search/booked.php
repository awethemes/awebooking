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
