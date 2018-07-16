<?php
/**
 * The template for displaying single room form in the template-parts/single/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/single/form.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="search-rooms__form">
	<?php
	// Print the search form.
	abrs_get_search_form([
		'layout'    => 'vertical',
		'only_room' => get_the_ID(),
	]);
	?>
</div>
