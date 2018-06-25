<?php
/**
 * This template show the search result item.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result-item.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @var \AweBooking\Availability\Room_Rate $room_rate */

$dialog_id = uniqid( 'breakdown_dialog_', false );

$room_only_breakdown = $room_rate->get_breakdown();

?>

<button data-a11y-dialog-show="<?php echo esc_attr( $dialog_id ); ?>">Show</button>

<div id="<?php echo esc_attr( $dialog_id ); ?>" class="awebooking-dialog" data-init="awebooking-dialog" aria-hidden="true" tabindex="-1">
	<div class="awebooking-dialog__overlay" tabindex="-1" data-a11y-dialog-hide></div>

	<div class="awebooking-dialog__dialog" role="dialog" aria-labelledby="dialog-title">
		<div class="awebooking-dialog__content">
			<button class="awebooking-dialog__close button button--secondary" type="button" data-a11y-dialog-hide aria-label="Close this dialog window">&times;</button>

			<div>

				<table>
					<tbody>
						<?php foreach ( $room_only_breakdown as $date => $amount ) : ?>
							<tr>
								<th><?php echo esc_html( abrs_format_date( $date ) ); ?></th>
								<td><?php abrs_price( $amount ); ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>

			</div>

		</div><!-- /.awebooking-dialog__content -->
	</div>
</div>
