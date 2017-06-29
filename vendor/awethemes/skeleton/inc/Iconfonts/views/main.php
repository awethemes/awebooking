<?php
/**
 * Icon manager main template.
 *
 * @author  Awethemes
 * @package awethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="icon-manager-wrap" class="wrap theme-install-php icon-manager-wrap">
	<h2>
		<?php esc_html_e( 'Font Icon Manager', 'awethemes' ); ?>
		<a href="#upload-icon" data-toggle="collapse" class="add-new-h2"><?php esc_html_e( 'Upload Icon Pack', 'awethemes' ); ?></a>
	</h2>

	<div id="upload-icon" class="collapse">
		<?php $this->output_upload_form(); ?>
	</div><!-- /.welcome-panel -->

	<?php $this->show_messages(); ?>

	<div class="wp-filter">
		<div class="filter-count">
			<span class="count theme-count"><?php // echo esc_html( count( $icons ) ); ?></span>
		</div>

		<ul class="filter-links">
			<li><a href="<?php // echo esc_url( admin_url( 'admin.php?page=ac-icon-manager&tab=system' ) ); ?>" class="<?php // echo 'system' === $current_tab ? 'current' : ''; ?>"><?php esc_html_e( 'System Icons', 'awethemes' ); ?></a> </li>
			<li><a href="<?php // echo esc_url( admin_url( 'admin.php?page=ac-icon-manager&tab=uploaded' ) ); ?>" class="<?php // echo 'uploaded' === $current_tab ? 'current' : ''; ?>"><?php esc_html_e( 'Uploaded', 'awethemes' ); ?></a> </li>
		</ul>

		<div class="search-form search-icons">
			<input type="search" class="wp-filter-search search" placeholder="<?php esc_html_e( 'Search Icon Pack', 'awethemes' ); ?>">
		</div>
	</div>

	<div class="metabox-holder ac-icon-manager">
		<?php foreach ( $this->manager->all() as $icon ) :
			$this->add_icons_box( $icon );
		endforeach; ?>
	</div>

	<ul class="pagination ac-pagination clear"></ul>
	<p class="no-themes no-icons hide-if-no-js"><?php esc_html_e( 'No icon pack found.', 'awethemes' ); ?></p>
</div>

<div id="skeleton-iconfonts-manager"></div>
