<div class="wrap cmb2-options-page awebooking-settings">
	<h1 class="wp-heading-inline screen-reader-text"><?php echo esc_html__( 'Tools', 'awebooking' ); ?></h1>

	<nav class="nav-tab-wrapper abrs-nav-tab-wrapper abrs-mb1">
		<?php foreach ( $tabs as $key => $label ) : ?>
			<a class="nav-tab <?php echo ( $key === $current_tab ) ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( abrs_admin_route( "/tools/{$key}" ) ); ?>"><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
	</nav>

	<hr class="wp-header-end">

	<div class="">
		<?php if ( isset( $callback ) && is_callable( $callback ) ) : ?>
			<?php awebooking()->call( $callback ); ?>
		<?php endif; ?>
	</div>
</div><!-- /.wrap -->
