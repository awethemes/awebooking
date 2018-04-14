<?php
/**
 * The template for displaying search item.
 *
 * @version 3.1.0
 */

/* @vars $availability, $res_request */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

list( $room_type ) = [ $availability->get_room_type() ];

$remain_rooms = $availability->remain_rooms();

?>

<div class="master-room">
	<div class="">
		<?php
		if ( has_post_thumbnail( $room_type->get_id() ) ) {
			echo get_the_post_thumbnail( $room_type->get_id(), 'awebooking_catalog' );
		}
		?>
	</div>

	<div class="">
		<h2 class=""><?php echo esc_html( $room_type['title'] ); ?></h2>

		<div class="">
			<strong><?php esc_html_e( 'What\'s included', 'awebooking' ); ?></strong>

			<?php if ( is_array( $room_type['rate_inclusions'] ) && ! empty( $room_type['rate_inclusions'] ) ) : ?>
				<?php foreach ( $room_type['rate_inclusions'] as $string ) : ?>

					<p><?php echo abrs_esc_text( $string ); // WPCS: XSS OK. ?></p>

				<?php endforeach ?>
			<?php endif ?>
		</div>

		<span class="">
			<?php /* translators: %s Number of remain rooms */ ?>
			<?php printf( _nx( '%s room left', '%s rooms left', $remain_rooms->count(), 'remain rooms', 'awebooking' ), number_format_i18n( $remain_rooms->count() ) ); // WPCS: XSS OK. ?>
		</span>

		<div>
			<?php
			abrs_book_room_button( $res_request, [
				'room' => $remain_rooms->last()['room']->get_id(),
				'text' => esc_html__( 'Book Now', 'awebooking' ),
			]);
			?>
		</div>

	</div>
</div>
