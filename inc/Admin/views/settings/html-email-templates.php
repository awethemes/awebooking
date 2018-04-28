<?php

$email_templates = awebooking( 'mailer' )->all();

?><ul class="abrs-sortable">
	<?php foreach ( $email_templates as $template ) : ?>
		<li class="abrs-sortable__item">
			<?php $_link = abrs_admin_route( '/settings', [ 'setting' => 'email', 'section' =>  $template->get_id() ] );  // @codingStandardsIgnoreLine ?>

			<div class="abrs-sortable__head">
				<span class="abrs-sortable__order">
					<?php if ( $template->is_enabled() ) : ?>
						<span class="abrs-state-indicator abrs-state-indicator--on tippy" title="<?php esc_html_e( 'Enabled', 'awebooking' ); ?>"></span>
					<?php else : ?>
						<span class="abrs-state-indicator tippy" title="<?php esc_html_e( 'Disabled', 'awebooking' ); ?>"></span>
					<?php endif ?>
				</span>
			</div>

			<div class="abrs-sortable__body">
				<div class="abrow">
					<div class="abcol-6">
						<a href="<?php echo esc_url( $_link ); ?>"><strong><?php echo esc_html( $template->get_title() ); ?></strong></a>
					</div>
					<div class="abcol-6"></div>
				</div>
			</div>

			<div class="abrs-sortable__actions">
				<span class="tippy" style="color: #999;" title="<?php echo esc_html( $template->get_description() ); ?>"><i class="dashicons dashicons-editor-help"></i></span>

				<a href="<?php echo esc_url( $_link ); ?>" title="<?php echo esc_html__( 'Configure Template', 'awebooking' ); ?>">
					<span class="screen-reader-text"><?php esc_html_e( 'Gateway Setting', 'awebooking' ); ?></span>
					<span class="dashicons dashicons-admin-generic"></span>
				</a>
			</div>
		</li>
	<?php endforeach ?>
</ul><!-- /.abrs-sortable -->
