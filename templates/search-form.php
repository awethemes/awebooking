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
 *
 * @var $search_form \AweBooking\Frontend\Search\Search_Form
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$form_classes = [
	$atts['layout'] ? 'searchbox--' . $atts['layout'] : '',
	$atts['alignment'] ? 'searchbox--align-' . $atts['alignment'] : '',
	$atts['container_class'] ? $atts['container_class'] : '',
];

?>

<form id="<?php echo esc_attr( $search_form->id() ); ?>" action="<?php echo esc_url( $search_form->action() ); ?>" method="GET" role="search">
	<?php $search_form->hiddens(); ?>

	<div class="searchbox <?php echo esc_attr( abrs_html_class( $form_classes ) ); ?>">
		<div class="searchbox__wrap">
			<?php $search_form->components(); ?>
		</div><!-- /.searchbox__wrapper-->

		<pre data-bind="text: ko.toJSON($root)"></pre>
	</div><!-- /.searchbox-->
</form>

