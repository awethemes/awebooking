<?php
/**
 * The template for displaying search availability results.
 *
 * @version 3.1.0
 */

/* @vars $results, $res_request */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$guest_counts = $res_request->get_guest_counts();

do_action( 'awebooking/template_notices' );

?>

<div class="">
	<?php foreach ( $results as $availability ) : ?>

		<?php abrs_get_template( 'search/result-item.php', compact( 'availability', 'res_request' ) ); ?>

	<?php endforeach; ?>
</div>
