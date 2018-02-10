<?php

use AweBooking\Money\Money;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* @vars $availability, $guest, $reservation */

list( $stay, $room_type ) = [ // @codingStandardsIgnoreLine
	$availability->get_stay(), $availability->get_room_type()
];

$remain_rooms = $availability->remain_rooms();

?>

<div class="awebooking-availability-room-type__media">
	<div class="awebooking-availability-room-type__thumbnail">

		<a href="<?php echo esc_url( get_the_permalink( $room_type->get_id() ) ); ?>" target="_blank">
			<?php
			if ( has_post_thumbnail( $room_type->get_id() ) ) {
				echo get_the_post_thumbnail( $room_type->get_id(), 'awebooking_catalog' );
			} elseif ( awebooking_placeholder_img_src() ) {
				echo awebooking_placeholder_img( 'awebooking_catalog' ); // WPCS: XSS OK.
			}
			?>
		</a>

		<h2 class="awebooking-availability-room-type__title">
			<a href="<?php echo esc_url( get_permalink( $room_type->get_id() ) ); ?>" target="_blank">
				<?php echo esc_html( $room_type->get_title() ); ?>
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

				<h2 class="awebooking-rate__name"><?php echo esc_html( $room_type->get_title() ); ?></h2>

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
									<span class="awebooking-price-currencySymbol"><?php echo Money::of( $room_type->get_base_price() )->as_string(); // WPCS: XSS OK. ?></span>
								</span>
							</ins>

							<a href="#popup-rate-<?php echo absint( $room_type->get_id() ); ?>" class="awebooking-price__info awebooking-price-info"><strong>&#161;</strong></a>
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
				<?php if ( $room_type->get_services() ) : ?>
					<div class="awebooking-rate__actions-left">
						<a class="awebooking-rate__service-btn" href="#" data-init="awebooking-dropdown" data-dropdown="#dr-rate-1">
							<?php esc_html_e( 'Extra services', 'awebooking' ); ?><i><?php echo esc_html_x( '&#x25BC;','awebooking-dropdown', 'awebooking' ); ?></i>
						</a>
						<span class="awebooking-rate__list-services js-awebooking-list-service"></span>
					</div>
				<?php endif; ?>

				<div class="awebooking-rate__actions-right js-awebooking-rate-actions">
					<span class="awebooking-rate__rooms">
						<span class="count js-awebooking-room-left">
							<?php /* translators: %s Number of remain rooms */ ?>
							<?php printf( _nx( '%s room left', '%s rooms left', $remain_rooms->count(), 'remain rooms', 'awebooking' ), number_format_i18n( $remain_rooms->count() ) ); // @codingStandardsIgnoreLine ?>
						</span>

					<div class="awebooking-rate__book-action">
						<input type="submit" class="awebooking-rate__book js-awebooking-add-room" name="submit[<?php echo esc_attr( $room_type->get_id() ); ?>]" value="<?php esc_html_e( 'Add room', 'awebooking' ); ?>">
					</div>
				</div>
			</div>

			<?php if ( $room_type->get_services() ) : ?>
				<div class="awebooking-rate__services awebooking-dropdown-content" id="dr-rate-1">
					<div class="awebooking-booking-form" id="awebooking-booking-form">
						<div class="awebooking-service-items" id="awebooking-service-items">
							<?php foreach ( $room_type->get_services() as $service ) : ?>
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

		</div>

	</div>
</div>
