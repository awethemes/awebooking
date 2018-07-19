<?php
/**
 * HTML displaying the room type form data.
 *
 * @var \AweBooking\Model\Room_Type     $the_room_type
 * @var \AweBooking\Component\Form\Form $form
 *
 * @package AweBooking\Admin\Metaboxes
 */

?>

<div class="cmb2-wrap awebooking-wrap">
	<div class="cmb2-metabox cmb2-inline-metabox">

		<div class="abrow no-gutters">
			<div class="abcol-3 abcol-sm-12">
				<ul class="awebooking-tabs">
					<?php foreach ( $form->sections() as $key => $section ) : ?>
						<li class="tab-<?php echo sanitize_html_class( $section['uid'] ); ?> <?php echo ( 'general' === $key ? 'active' : '' ); ?>"><a href="#<?php echo esc_attr( $section['uid'] ); ?>"><?php echo esc_html( $section['title'] ); ?></a></li>
					<?php endforeach ?>

					<?php do_action( 'abrs_room_type_print_nav_tabs', $form ); ?>
				</ul>
			</div>

			<div class="abcol-9 abcol-sm-12">
				<div class="awebooking-tabs-panels">
					<?php
					// Show controls group by section.
					foreach ( $form->sections() as $key => $section ) {
						$_display = 'general' === $key ? 'block' : 'none';

						echo '<div class="awebooking-panel abwrap" id="' . esc_attr( $section['uid'] ) . '" style="display: ' . esc_attr( $_display ) . ';">';
						abrs_admin_print_form( $form, $key );
						echo '</div>';
					}

					// Fire action after output tabs.
					do_action( 'abrs_room_type_print_tabs', $form );
					?>
				</div>
			</div>
		</div><!-- /.abrow -->

	</div>
</div><!-- /.awebooking-wrap -->
