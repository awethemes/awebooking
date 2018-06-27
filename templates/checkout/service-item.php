<?php
/**
 * Output the service item.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/service-item.php
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @var \AweBooking\Model\Service $service */
/* @var \AweBooking\Support\Collection $includes */

$includes_ids = $includes->pluck( 'id' )->all();

$is_checked  = false;
$is_included = in_array( $service->get_id(), $includes_ids );

if ( $is_included ) {
	$is_checked = true;
}

?>

<div class="checkout-service">
	<div class="columns">

		<div class="column-3">
			<div class="checkout-service__media">
				<?php print abrs_get_thumbnail( $service->get_id(), 'awebooking_thumbnail' ); // WPCS: xss ok. ?>
			</div>
		</div>

		<div class="column-9">
			<div class="checkout-service__info">

				<h3 class="checkout-service__title">
					<?php echo esc_html( $service->get( 'name' ) ); ?>
				</h3>

				<div class="checkout-service__description">
					<?php echo esc_html( $service->get( 'description' ) ); ?>
				</div>

				<div class="checkout-service__pay">
					<div class="checkout-service__price">
						<?php print abrs_format_service_price( $service->get( 'amount' ), $service->get( 'operation' ) ); // WPCS: xss ok. ?>
					</div>

					<div class="nice-checkbox">
						<input type="checkbox" id="service_id_<?php echo esc_attr( $service->get_id() ); ?>" name="awebooking_services[]"  value="<?php echo esc_attr( $service->get_id() ); ?>" <?php disabled( $is_included ); ?> <?php checked( $is_checked ); ?> />
						<label for="service_id_<?php echo esc_attr( $service->get_id() ); ?>"></label>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>
