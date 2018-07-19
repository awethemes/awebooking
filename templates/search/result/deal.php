<?php
/**
 * This template show the search result deal.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result/deal.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @var \AweBooking\Model\Room_Type $room_type */
/* @var \AweBooking\Availability\Room_Rate $room_rate */

$rate_plan = $room_rate->get_rate_plan();
?>

<div class="roommaster-deal roommaster-box">
	<h4 class="roommaster-content__title"><?php esc_html_e( 'Choose your deal', 'awebooking' ); ?></h4>

	<?php if ( $rate_inclusions = $rate_plan->get_inclusions() ) : ?>
		<div class="roommaster-deal__item">
			<span class="roommaster-deal__bucketspan"><?php esc_html_e( 'Inclusions', 'awebooking' ); ?></span>
			<?php foreach ( $rate_inclusions as $inclusion ) : ?>
				<div class="roommaster-deal__info">
					<span class="info-icon">
						<i class="aficon aficon-checkmark"></i>
					</span>
					<span class="info-text"><?php echo esc_html( $inclusion ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( $rate_policies = $rate_plan->get_policies() ) : ?>
		<div class="roommaster-deal__item">
			<span class="roommaster-deal__bucketspan"><?php esc_html_e( 'Policies', 'awebooking' ); ?></span>
			<?php foreach ( $rate_policies as $policy ) : ?>
				<div class="roommaster-deal__info">
					<span class="info-icon">
						<i class="aficon aficon-checkmark"></i>
					</span>
					<span><?php echo esc_html( $policy ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
