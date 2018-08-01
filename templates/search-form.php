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

<form method="GET" action="<?php echo esc_url( apply_filters( 'abrs_search_form_action', $action ) ); ?>" role="search">
	<?php abrs_search_form_hidden_fields( $atts ); ?>

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
