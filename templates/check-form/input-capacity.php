<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-form/input-capacity.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="awebooking-sidebar-group awebooking-guest-fields">
	<?php if ( $max_adults ) : ?>
	<div class="awebooking-field awebooking-adults-field">
		<label for="awebooking-adults"><?php esc_html_e( 'Adults', 'awebooking' ); ?></label>
		<div class="awebooking-field-group">
			<i class="awebookingf awebookingf-select"></i>
			<select name="adults" class="awebooking-select" id="awebooking-adults">
				<?php for ( $i = 1; $i <= $max_adults; $i++ ) : ?>
				<option value="<?php echo esc_attr( $i ); ?>" <?php echo isset( $_GET['adults'] ) ? selected( $_GET['adults'], $i, false ) : ''; ?>><?php echo esc_attr( $i ); ?></option>
				<?php endfor; ?>
			</select>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( awebooking( 'setting' )->is_children_bookable() && $max_children ) : ?>
	<div class="awebooking-field awebooking-children-field">
		<label for="awebooking-children"><?php esc_html_e( 'Children', 'awebooking' ); ?></label>
		<div class="awebooking-field-group">
			<i class="awebookingf awebookingf-select"></i>
			<select name="children" class="awebooking-select" id="awebooking-children">
				<?php for ( $i = 0; $i <= $max_children; $i++ ) : ?>
				<option value="<?php echo esc_attr( $i ); ?>" <?php echo isset( $_GET['children'] ) ? selected( $_GET['children'], $i, false ) : ''; ?>><?php echo esc_attr( $i ); ?></option>
				<?php endfor; ?>
			</select>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( awebooking( 'setting' )->is_infants_bookable() && $max_infants ) : ?>
	<div class="awebooking-field awebooking-infants-field">
		<label for="awebooking-infants"><?php esc_html_e( 'Infants', 'awebooking' ); ?></label>
		<div class="awebooking-field-group">
			<i class="awebookingf awebookingf-select"></i>
			<select name="infants" class="awebooking-select" id="awebooking-infants">
				<?php for ( $i = 0; $i <= $max_infants; $i++ ) : ?>
				<option value="<?php echo esc_attr( $i ); ?>" <?php echo isset( $_GET['infants'] ) ? selected( $_GET['infants'], $i, false ) : ''; ?>><?php echo esc_attr( $i ); ?></option>
				<?php endfor; ?>
			</select>
		</div>
	</div>
	<?php endif; ?>
</div>
