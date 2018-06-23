<?php
/**
 * Output the services included.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/services.php
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */
use AweBooking\Model\Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $services->isEmpty() ) {
	return;
}

$operations = Service::get_operations();

?>

<div id="checkout-services" class="checkout-services">
	<h3 class="checkout-services__header"><?php esc_html_e( 'Services', 'awebooking' ); ?></h3>

	<?php foreach ( $services as $service ) : ?>
		<div class="checkout-service">
			<div class="columns">
				<div class="column-3">
					<div class="checkout-service__media">
						<?php print abrs_get_thumbnail( $service->get_id(), 'awebooking_thumbnail' ); // WPCS: xss ok. ?>
					</div>
				</div>

				<div class="column-9">
					<div class="checkout-service__info">
						<div class="columns">
							<div class="column-9">
								<h3 class="checkout-service__title">
									<?php echo esc_html( $service->get( 'name' ) ); ?>
								</h3>

								<div class="checkout-service__description">
									<?php echo esc_html( $service->get( 'description' ) ); ?>
								</div>

								<div class="checkout-service__operation">
									<?php echo esc_html( $operations[ $service->get( 'operation' ) ] ); ?>
								</div>
							</div>

							<div class="column-3">
								<div class="checkout-service__pay">
									<div class="checkout-service__price">
										<?php print abrs_format_service_price( $service->get( 'value' ), $service->get( 'operation' ) ); // WPCS: xss ok. ?>
									</div>

									<div class="nice-checkbox">
										<input type="checkbox" id="service_id_<?php echo esc_attr( $service->get_id() ); ?>" name="awebooking_services[]" value="<?php echo esc_attr( $service->get_id() ); ?>" />
										<label for="service_id_<?php echo esc_attr( $service->get_id() ); ?>"></label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
