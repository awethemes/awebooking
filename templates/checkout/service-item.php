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

/* @var array $includes */
/* @var \AweBooking\Model\Service $service */

$res_request = abrs_reservation()->get_previous_request();

$is_checked  = false;
$is_included = in_array( $service->get_id(), $includes );

if ( $is_included || abrs_reservation()->has_service( $service ) ) {
	$is_checked = true;
}

$price = abrs_calc_service_price( $service, [
	'nights'     => $res_request->nights,
	'base_price' => abrs_reservation()->get_totals()->get( 'rooms_subtotal' ),
] );

$input_prefix = 'services[' . $service->get_id() . ']';
$booked_service = abrs_reservation()->get_service( $service->get_id() );

$js_data = $service->only( 'id', 'name' );
$js_data['quantity'] = $service->is_quantity_selectable() && $booked_service ? $booked_service->get_quantity() : 0;
$js_data['price']    = $price ?: 0;

?>

<div class="checkout-service" id="checkout-service-<?php echo esc_attr( $service->get_id() ); ?>">
	<input type="hidden" name="<?php echo esc_attr( $input_prefix ); ?>[id]" value="<?php echo esc_attr( $service->get_id() ); ?>" <?php disabled( $is_included ); ?>>

	<div class="columns">
		<div class="column-lg-3">
			<div class="checkout-service__media">
				<?php print abrs_get_thumbnail( $service->get_id(), 'awebooking_thumbnail' ); // WPCS: xss ok. ?>
			</div>
		</div>

		<div class="column-lg-9">
			<div class="checkout-service__info">

				<div class="checkout-service__content">
					<h3 class="checkout-service__title">
						<?php echo esc_html( $service->get( 'name' ) ); ?>
					</h3>

					<div class="checkout-service__description">
						<?php echo esc_html( $service->get( 'description' ) ); ?>
					</div>
				</div>

				<div class="checkout-service__pay">
					<div class="checkout-service__price" data-bind="html: totalHtml">
						<?php if ( $is_included ) : ?>
							<?php abrs_price( 0 ); ?>
						<?php else : ?>
							<?php abrs_price( $price ); ?>
						<?php endif; ?>
					</div>

					<div class="checkout-service__input-box">
						<?php if ( $service->is_quantity_selectable() ) : ?>
							<input type="number" data-bind="value: quantity" min="0" class="form-input" value="<?php echo esc_attr( $js_data['quantity'] ); ?>" name="<?php echo esc_attr( $input_prefix ); ?>[quantity]" />
						<?php else : ?>
							<div class="nice-checkbox">
								<input type="checkbox" id="service_id_<?php echo esc_attr( $service->get_id() ); ?>" name="<?php echo esc_attr( $input_prefix ); ?>[quantity]" value="1" <?php disabled( $is_included ); ?> <?php checked( $is_checked ); ?> />
								<label for="service_id_<?php echo esc_attr( $service->get_id() ); ?>"></label>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	(function ($) {
		'use strict';

		var Service = function (data) {
			this.name = data.name;
			this.price = data.price;
			this.quantity = ko.observable(data.quantity);
			this.totalHtml = ko.computed(() => {
				var quantity = parseInt(this.quantity(), 10);

				if (isNaN(quantity) || quantity === 0) {
					quantity = 1;
				}

				return awebooking.formatPrice(quantity * parseFloat(this.price));
			});
		}

		$(function () {
			var data = <?php echo wp_json_encode( $js_data ); ?>;
			ko.applyBindings(new Service(data), document.getElementById('checkout-service-<?php echo esc_attr( $service->get_id() ); ?>'));
		})
	})(jQuery)
</script>
