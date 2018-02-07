<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type form.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$page_id = get_the_ID();

$form_classes = apply_filters( 'awebooking/check_availability_form_classes', array(
	'awebooking-check-form',
	'awebooking-check-form--vertical',
	awebooking( 'setting' )->is_children_bookable() ? 'has-children' : '',
	awebooking( 'setting' )->is_infants_bookable() ? 'has-infants' : '',
) );
?>
<form action="<?php echo esc_url( get_the_permalink() ); ?>" class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $form_classes ) ) ); ?>" method="POST">
	<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
		<input type="hidden" name="p" value="<?php echo esc_attr( $page_id ) ?>">
	<?php endif ?>

	<?php if ( awebooking()->is_running_multilanguage() ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( awebooking( 'multilingual' )->get_active_language() ) ?>">
	<?php endif ?>

	<div class="awebooking-check-form__wrapper">
		<h2 class="awebooking-heading"><?php esc_html_e( 'Your Reservation', 'awebooking' ); ?></h2>
		<div class="awebooking-check-form__content">
			<div class="awebooking-field awebooking-arrival-field">
				<label for=""><?php esc_html_e( 'Arrival Date', 'awebooking' ); ?></label>
				<div class="awebooking-field-group">
					<i class="awebookingf awebookingf-calendar"></i>
					<input type="text" class="awebooking-datepicker awebooking-input awebooking-start-date" data-init="datepicker" data-alt-field="#start-date" data-date-format="<?php echo esc_attr( $date_format ); ?>" data-min-nights="<?php echo esc_attr( $min_night ); ?>" placeholder="<?php esc_html_e( 'Arrival Date', 'awebooking' ); ?>">
					<input type="hidden" id="start-date" name="start-date" value="<?php echo isset( $_GET['start-date'] ) ? $_GET['start-date'] : ''; ?>" />
				</div>
			</div>

			<div class="awebooking-field awebooking-departure-field">
				<label for=""><?php esc_html_e( 'Departure Date', 'awebooking' ); ?></label>
				<div class="awebooking-field-group">
					<i class="awebookingf awebookingf-calendar"></i>
					<input type="text" class="awebooking-datepicker awebooking-input awebooking-end-date" data-init="datepicker" data-alt-field="#end-date" data-date-format="<?php echo esc_attr( $date_format ); ?>" placeholder="<?php esc_html_e( 'Departure Date', 'awebooking' ); ?>">
					<input type="hidden" id="end-date" name="end-date" value="<?php echo isset( $_GET['end-date'] ) ? $_GET['end-date'] : ''; ?>" />
				</div>
			</div>

			<div class="awebooking-sidebar-group">
				<?php if ( $max_adults ) : ?>
				<div class="awebooking-field awebooking-adults-field">
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

				<?php if ( awebooking( 'setting' )->is_children_bookable() && $max_children ) : ?>
				<div class="awebooking-field awebooking-children-field">
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

				<?php if ( awebooking( 'setting' )->is_infants_bookable() && $max_infants ) : ?>
				<div class="awebooking-field awebooking-infants-field">
					<label for=""><?php esc_html_e( 'Infants', 'awebooking' ); ?></label>
					<div class="awebooking-field-group">
						<i class="awebookingf awebookingf-select"></i>
						<select name="infants" class="awebooking-select">
							<?php for ( $i = 0; $i <= $max_infants; $i++ ) : ?>
							<option value="<?php echo esc_attr( $i ); ?>" <?php echo isset( $_GET['infants'] ) ? selected( $_GET['infants'], $i, false ) : ''; ?>><?php echo esc_attr( $i ); ?></option>
							<?php endfor; ?>
						</select>
					</div>
				</div>
				<?php endif; ?>
			</div>

			<input type="hidden" name="room-type" value="<?php echo get_the_ID() ?>">

			<div class="awebooking-field mb-0">
				<div class="awebooking-field-group">
					<button type="submit" class="awebooking-btn"><?php esc_html_e( 'CHECK AVAILABILITY', 'awebooking' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php //wp_nonce_field( 'check_availability' . get_the_ID() ); ?>
</form>
