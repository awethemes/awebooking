<?php
/**
 * This template show the search result item.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result-item.php.
 *
 * HOWEVER, on occasion AweBooking will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Leave if we have invalid availability variable.
if ( empty( $availability['room_type'] ) || empty( $availability['room_rate'] ) ) {
	return;
}

$room_type = $availability['room_type'];
$room_rate = $availability['room_rate'];

// $remain_rooms = $rooms->remains();

$price_display = 'total'; // per_night, first_night.

?>

<div class="abroom">
	<div class="abroom__data">
		<table class="abroom__table">
			<tbody>
				<tr>
					<td class="column-room-info">
						<div class="clearfix">
							<div class="abroom__image">
								<?php
								if ( has_post_thumbnail( $room_type->get_id() ) ) {
									echo get_the_post_thumbnail( $room_type->get_id(), 'awebooking_catalog' );
								}
								?>
							</div>

							<div class="abroom__room">
								<h2 class="abroom__roomname"><a href="<?php echo esc_url( get_permalink( $room_type->get_id() ) ); ?>" rel="bookmark" target="_blank"><?php echo esc_html( $room_type->get( 'title' ) ); ?></a></h2>

								<div class="abroom__occupancy">
									<?php
									$max_capacity = $room_type->get( 'maximum_occupancy' );

									/* translators: %s Maximum capacity */
									echo sprintf( esc_html( _nx( 'Maximum capacity for %s person', 'Maximum capacity for %s people', $max_capacity, 'awebooking' ) ), esc_html( number_format_i18n( $max_capacity ) ) );
									?>
								</div>

								<div>
								<strong><?php esc_html_e( 'What\'s included', 'awebooking' ); ?></strong>

								<?php if ( ! empty( $room_type['rate_inclusions'] ) ) : ?>
									<?php foreach ( $room_type->get( 'rate_inclusions' ) as $string ) : ?>

										<p><?php echo abrs_esc_text( $string ); // WPCS: XSS OK. ?></p>

									<?php endforeach ?>
								<?php endif ?>
								</div>

							</div>
						</div>
					</td>

					<td class="column-room-inventory">
						<div class="abroom__inventory">
							<?php
							switch ( $price_display ) {
								case 'total':
									echo $room_rate->get_total();
									echo 'Cost for % nights for % guests';
									break;

								case 'first_night':
									break;
							}
							?>
						</div>
					</td>

					<td class="column-room-button">
						<button><?php echo esc_html__( 'Book Now', 'awebooking' ); ?></button>

						<span class="abroom__remaining-rooms">
							<?php
							// $rooms_left = $remain_rooms->count();

							// if ( $rooms_left <= 2 ) {
							// 	/* translators: %s Number of remain rooms */
							// 	printf( esc_html( _nx( 'Only %s room left', 'Only %s rooms left', $rooms_left, 'remain rooms', 'awebooking' ) ), esc_html( number_format_i18n( $rooms_left ) ) );
							// } else {
							// 	/* translators: %s Number of remain rooms */
							// 	printf( esc_html_x( '%s rooms left', 'remain rooms', 'awebooking' ), esc_html( number_format_i18n( $rooms_left ) ) );
							// }
							?>
						</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="abroom__details">
		sdasdasd
		sdasdasds
	</div>
</div>
