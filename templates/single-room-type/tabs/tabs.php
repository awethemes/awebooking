<?php
/**
 * Single Room type tabs
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/tabs/tabs.php.
 *
 * @author  Awethemes
 * @package Awethemes/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 * @see abkng_default_room_type_tabs()
 */
$tabs = apply_filters( 'awebooking/room_type_tabs', array() );

if ( ! empty( $tabs ) ) : ?>

	<div class="awebooking-tab">
		<ul class="awebooking-tab__controls">
			<?php foreach ( $tabs as $key => $tab ) : ?>
				<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
					<a href="#tab-<?php echo esc_attr( $key ); ?>"><?php echo apply_filters( 'awebooking_room_type_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="awebooking-tab__wrapper">
		<?php foreach ( $tabs as $key => $tab ) : ?>
			<div class="awebooking-tab__content entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
				<?php call_user_func( $tab['callback'], $key, $tab ); ?>
			</div>
		<?php endforeach; ?>
		</div>
	</div>

<?php endif; ?>
