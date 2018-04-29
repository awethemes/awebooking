<?php

$email_templates = awebooking( 'mailer' )->all();

?><ul class="abrs-sortable abrs-email-templates">
	<?php foreach ( $email_templates as $email ) : ?>
		<li class="abrs-sortable__item">
			<?php $_link = abrs_admin_route( '/settings', [ 'setting' => 'email', 'section' =>  $email->get_id() ] );  // @codingStandardsIgnoreLine ?>

			<div class="abrs-sortable__head">
				<span class="abrs-sortable__order">
					<?php if ( $email->is_manually() ) : ?>
						<span class="abrs-state-indicator tippy" style="background-color: #607d8b;" title="<?php esc_html_e( 'Manually Sent', 'awebooking' ); ?>"></span>
					<?php elseif ( $email->is_enabled() ) : ?>
						<span class="abrs-state-indicator abrs-state-indicator--on tippy" title="<?php esc_html_e( 'Enabled', 'awebooking' ); ?>"></span>
					<?php else : ?>
						<span class="abrs-state-indicator tippy" title="<?php esc_html_e( 'Disabled', 'awebooking' ); ?>"></span>
					<?php endif ?>
				</span>
			</div>

			<div class="abrs-sortable__body">
				<a href="<?php echo esc_url( $_link ); ?>"><strong><?php echo esc_html( $email->get_title() ); ?></strong></a>
				<span class="sup-placeholder"><?php echo esc_html( $email->get_content_type() ); ?></span>
			</div>

			<div class="abrs-sortable__actions">
				<?php if ( $email->is_customer_email() ) : ?>
					<span class="abrs-badge"><?php echo esc_html__( 'Customer', 'awebooking' ); ?></span>
				<?php else : ?>
					<span class="tippy" title="<?php echo esc_attr( $email->get_recipient() ); ?>" style="color: #999;"><span class="dashicons dashicons-email"></span></span>
				<?php endif ?>

				<span class="tippy" style="color: #999;" title="<?php echo esc_html( $email->get_description() ); ?>"><i class="dashicons dashicons-editor-help"></i></span>
				<a href="<?php echo esc_url( $_link ); ?>" title="<?php echo esc_html__( 'Configure email template', 'awebooking' ); ?>"><span class="dashicons dashicons-admin-generic"></span></a>
			</div>
		</li>
	<?php endforeach ?>
</ul><!-- /.abrs-sortable -->
