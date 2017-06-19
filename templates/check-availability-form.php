<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-availability-form.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$link = get_the_permalink( intval( abkng_config( 'page_check_availability' ) ) );
?>
<form action="<?php echo esc_url( $link ); ?>" class="awebooking-check-form">
	<div class="awebooking-check-form__wrapper">
		<h2 class="awebooking-heading"><?php esc_html_e( 'Your Reservation', 'awebooking' ); ?></h2>
		<div class="awebooking-check-form__content">
			<div class="awebooking-field">
				<label for=""><?php esc_html_e( 'Arrival Date', 'awebooking' ); ?></label>
				<div class="awebooking-field-group">
					<i class="awebookingf awebookingf-calendar"></i>
					<input type="text" class="awebooking-datepicker awebooking-input awebooking-start-date" data-init="datepicker" data-alt-field="#start-date" data-date-format="<?php echo esc_attr( $date_format ); ?>" data-min-nights="1" placeholder="<?php esc_html_e( 'Arrival Date', 'awebooking' ); ?>">
					<input type="hidden" id="start-date" name="start-date" value="<?php echo isset( $_GET['start-date'] ) ? $_GET['start-date'] : ''; ?>" />
				</div>
			</div>

			<div class="awebooking-field">
				<label for=""><?php esc_html_e( 'Departure Date', 'awebooking' ); ?></label>
				<div class="awebooking-field-group">
					<i class="awebookingf awebookingf-calendar"></i>
					<input type="text" class="awebooking-datepicker awebooking-input awebooking-end-date" data-init="datepicker" data-alt-field="#end-date" data-date-format="<?php echo esc_attr( $date_format ); ?>" placeholder="<?php esc_html_e( 'Departure Date', 'awebooking' ); ?>">
					<input type="hidden" id="end-date" name="end-date" value="<?php echo isset( $_GET['end-date'] ) ? $_GET['end-date'] : ''; ?>" />
				</div>
			</div>

			<?php if ( abkng_config( 'enable_location' ) && $locations && empty( $GLOBALS['awebooking-hide-location'] ) ) : ?>
			<div class="awebooking-field">
				<label for=""><?php esc_html_e( 'Location', 'awebooking' ); ?></label>
				<div class="awebooking-field-group">
					<i class="awebookingf awebookingf-select"></i>
					<select name="location" class="awebooking-select">
						<?php foreach ( $locations as $location ) : ?>
						<option value="<?php echo esc_attr( $location->slug ); ?>" <?php echo isset( $_GET['location'] ) ? selected( $_GET['location'], $location->slug, false ) : selected( $term_default->slug, $location->slug, false ); ?>><?php echo esc_attr( $location->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( $max_adults || $max_children ) : ?>
			<div class="list-room">
				<div class="awebooking-sidebar-group">
					<?php if ( $max_adults ) : ?>
					<div class="awebooking-field">
						<label for=""><?php esc_html_e( 'Adults', 'awebooking' ); ?></label>
						<div class="awebooking-field-group">
							<i class="awebookingf awebookingf-select"></i>
							<select name="adults" class="awebooking-select">
								<?php for ( $i = 1; $i <= $max_adults; $i++ ) : ?>
								<option value="<?php echo esc_attr( $i ); ?>" <?php echo isset( $_GET['adults'] ) ? selected( $_GET['adults'], $i, false ) : ''; ?>><?php echo esc_attr( $i ); ?></option>
								<?php endfor; ?>
							</select>
						</div>
					</div>
					<?php endif; ?>

					<?php if ( $max_children ) : ?>
					<div class="awebooking-field">
						<label for=""><?php esc_html_e( 'Children', 'awebooking' ); ?></label>
						<div class="awebooking-field-group">
							<i class="awebookingf awebookingf-select"></i>
							<select name="children" class="awebooking-select">
								<?php for ( $i = 0; $i <= $max_children; $i++ ) : ?>
								<option value="<?php echo esc_attr( $i ); ?>" <?php echo isset( $_GET['children'] ) ? selected( $_GET['children'], $i, false ) : ''; ?>><?php echo esc_attr( $i ); ?></option>
								<?php endfor; ?>
							</select>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

			<div class="awebooking-field mb-0">
				<div class="awebooking-field-group">
					<button type="submit" class="awebooking-btn"><?php esc_html_e( 'CHECK AVAILABILITY', 'awebooking' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php //wp_nonce_field( 'check_availability' . get_the_ID() ); ?>
</form>
