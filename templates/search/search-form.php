<?php
/**
 * This template show the search results.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/results.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! abrs_get_option( 'display_search_form_on_search', true ) ) {
	return;
}

?>

<div class="search-rooms__form">
	<?php
	// Print the search form.
	abrs_get_search_form([
		'layout' => 'horizontal',
	]);
	?>
</div>
