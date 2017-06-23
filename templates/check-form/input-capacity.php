<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-form/input-capacity.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
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
