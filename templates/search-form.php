<?php
/**
 * Display the search form.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search-form.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.2.0
 *
 * @var $atts array
 * @var $search_form \AweBooking\Frontend\Search\Search_Form
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$form_classes = [
	'searchbox',
	$atts['layout'] ? 'searchbox--' . $atts['layout'] : '',
	$atts['alignment'] ? 'searchbox--align-' . $atts['alignment'] : '',
	$atts['container_class'] ?: '',
];

$use_experiment_style = 'on' === abrs_get_option( 'use_experiment_style', 'off' );
if ( $use_experiment_style ) {
	$form_classes = [
		'abrs-searchbox',
		'searchbox--experiment-style',
		$atts['layout'] ? 'abrs-searchbox--' . $atts['layout'] : '',
	];
}

?>

<form id="<?php echo esc_attr( $search_form->id() ); ?>" action="<?php echo esc_url( $search_form->action() ); ?>" method="GET" role="search" novalidate>
	<?php $search_form->hiddens(); ?>

	<div class="<?php echo esc_attr( abrs_html_class( $form_classes ) ); ?>">
		<div class="<?php echo $use_experiment_style ? 'abrs-searchbox__wrap' : 'searchbox__wrap'; ?>">
			<?php $search_form->components(); ?>
		</div><!-- /.searchbox__wrapper-->

		<?php if ( $use_experiment_style ) : ?>
			<div class="abrs-searchbox__dates"></div>
		<?php endif; ?>
	</div><!-- /.searchbox-->
</form>
