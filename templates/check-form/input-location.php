<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-form/input-location.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
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
