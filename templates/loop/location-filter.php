<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/awebooking/loop/location-filter.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $locations ) {
	return;
}
?>
<div class="awebooking-location-filter">
	<form action="<?php echo esc_url( get_post_type_archive_link( 'room_type' ) ); ?>" method="GET">
		<div class="awebooking-field">
			<label><?php esc_html_e( 'Location', 'awebooking' ); ?></label>
			<div class="awebooking-field-group">
				<i class="awebookingf awebookingf-select"></i>
				<select name="location" class="awebooking-select" onchange="this.form.submit()">
					<?php foreach ( $locations as $location ) : ?>
					<option value="<?php echo esc_attr( $location->slug ); ?>" <?php echo isset( $_GET['location'] ) ? selected( $_GET['location'], $location->slug, false ) : selected( $term_default->slug, $location->slug, false ); ?>><?php echo esc_attr( $location->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</form>
</div>
