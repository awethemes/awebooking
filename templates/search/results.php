<?php
/**
 * This template show the search results.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/results.php.
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

do_action( 'awebooking/template_notices' );

dump( abrs_reservation() );

?>

<?php do_action( 'awebooking/before_search_results', $results ); ?>

<div class="abrs-rooms" id="abrs-search-results">
	<?php foreach ( $results as $availability ) : ?>

		<?php abrs_get_template( 'search/result-item.php', compact( 'res_request', 'availability' ) ); ?>

	<?php endforeach; ?>
</div><!-- /.hotel-rooms -->

<?php do_action( 'awebooking/after_search_results', $results ); ?>
