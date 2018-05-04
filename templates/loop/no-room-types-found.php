<?php
/**
 * Displayed when no room types are found matching the current query
 *
 * This template can be overridden by copying it to yourtheme/awebooking/loop/no-room-types-found.php.
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
<p class="awebooking-info awebooking-notice awebooking-notice--warning"><?php esc_html_e( 'No Room types were found matching your selection.', 'awebooking' ); ?></p>
