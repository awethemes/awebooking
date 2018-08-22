<?php
/**
 * This template displaying the fees details.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/fees.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

$fees = abrs_reservation()->get_fees();

if ( abrs_blank( $fees ) ) {
	return;
}

?>

<div class="reservation__section reservation__section--fees">
	<?php foreach ( $fees as $item ) : ?>
		<dl>
			<dt><?php echo esc_html( $item->get_name() ); ?></dt>
			<dd><?php abrs_price( $item->get_total() ); ?></dd>
		</dl>
	<?php endforeach; ?>
</div>
