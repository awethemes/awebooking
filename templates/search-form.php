<?php
/**
 * Display the search form.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search-form.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$form_classes = [
	$atts['layout'] ? 'searchbox--' . $atts['layout'] : '',
	$atts['alignment'] ? 'searchbox--align-' . $atts['alignment'] : '',
	$atts['container_class'] ? $atts['container_class'] : '',
];

$action = abrs_get_page_permalink( 'search_results' );
?>

<form method="GET" action="<?php echo apply_filters( 'abrs_search_form_action', esc_url( $action ) ); ?>" role="search">

	<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
		<input type="hidden" name="p" value="<?php echo esc_attr( abrs_get_page_id( 'check_availability' ) ); ?>">
	<?php endif ?>

	<?php if ( abrs_running_on_multilanguage() ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( abrs_multilingual()->get_current_language() ); ?>">
	<?php endif ?>

	<?php if ( $atts['only_room'] ) : ?>
		<input type="hidden" name="only" value="<?php echo esc_attr( implode( ',', wp_parse_id_list( $atts['only_room'] ) ) ); ?>">
	<?php endif ?>

	<div class="searchbox <?php echo esc_attr( abrs_html_class( $form_classes ) ); ?>">
		<div class="searchbox__wrap">
			<input type="text" data-hotel="rangepicker" style="display: none;"/>

			<?php
			abrs_get_template( 'search-form/hotel.php', compact( 'atts', 'res_request' ) );
			abrs_get_template( 'search-form/dates.php', compact( 'atts', 'res_request' ) );
			abrs_get_template( 'search-form/occupancy.php', compact( 'atts', 'res_request' ) );

			do_action( 'abrs_before_search_form_button', compact( 'atts', 'res_request' ) );

			abrs_get_template( 'search-form/button.php', compact( 'atts', 'res_request' ) );

			do_action( 'abrs_after_search_form_button', compact( 'atts', 'res_request' ) );
			?>

		</div><!-- /.searchbox__wrapper-->
	</div><!-- /.searchbox-->
</form>
