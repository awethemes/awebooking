<style type="text/css">
	.awebooking-plugin-cover {
		display: block;
		position: relative;
		text-decoration: none;
		z-index: 10;
	}
	.awebooking-plugin-cover:after {
		content: '';
		top: 70%;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 10;
		position: absolute;
		background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.5) 99%);
	}
	.awebooking-plugin-cover > h3 {
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 11;
		position: absolute;
		margin: 0;
		padding: 7px 12px;
		color: #fff;
		font-size: 1.2em;
	}

	h3.awebooking-theme-name {
		margin-top: 0;
	}

	.awebooking-theme-name a {
		color: #333;
		text-decoration: none;
	}
</style>

<div class="wrap about-wrap">
	<h1><?php esc_html_e( 'Welcome to AweBooking', 'awebooking' ); ?></h1>

	<p class="about-text"><?php esc_html_e( 'AweBooking is both simple and powerful when it comes to its purpose: booking hotel room. It allows you to setup any reservations quickly, pleasantly and easily.', 'awebooking' ); ?></p>
	<div class="wp-badge awebooking-badge"><?php printf( esc_html__( 'Version %s', 'awebooking' ), AweBooking::VERSION ); ?></div>

	<div class="feature-section two-col">
		<div class="col">
			<h3><?php esc_html_e( 'What\' news', 'awebooking' ); ?></h3>
			<p><?php printf( __( 'Always stay up-to-date with the latest version of AweBooking by checking our <a href="%s">change log</a> regularly.', 'awebooking' ), esc_url( 'https://docs.awethemes.com/awebooking/changelog/awebooking/' ) ); ?></p>

			<h3><?php esc_html_e( 'How to use', 'awebooking' ); ?></h3>
			<p><?php printf( __( 'Check out <a href="%s">the plugin\'s documentation</a> if you need more information on how to use AweBooking.', 'awebooking' ), esc_url( 'https://docs.awethemes.com/awebooking/' ) ); // TODO: "and video tutorial" if video exist. ?></p>
		</div>

		<div class="col">
			<h3><?php esc_html_e( 'Get Support and Pro Features?', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Find help in our forum and get free updates when purchasing the PRO version which boats a lot more advanced features', 'awebooking' ); ?></p>
			<p><a class="button-primary" href="https://awethemes.com/plugins/awebooking" target="_blank">Get Support and Pro Features</a></p>
		</div>
	</div>

	<?php if ( ! abrs_blank( $available_addons ) ) : ?>
		<hr>
		<h2 style="text-align: left;"><?php esc_html_e( 'Premium addons', 'awebooking' ); ?></h2>
		<p><?php esc_html_e( 'The PRO version will never fail to impress you. With a bunch of premium features added, your online booking system will be pushed to the next level', 'awebooking' ); ?></p>

		<div class="under-the-hood three-col">
			<?php foreach ( $available_addons as $_addon ) : ?>

				<div class="col">
					<a class="awebooking-plugin-cover" href="<?php echo esc_url( 'https://awethemes.com/plugins/awebooking?ref=plugin-core' ); ?>" target="_blank">
						<img src="<?php echo esc_url( $_addon['thumbnail'] ); ?>">

						<h3>
							<?php echo esc_html( $_addon['label'] ); ?>
						</h3>
					</a>

					<p><?php echo wp_kses_post( $_addon['description'] ); ?></p>
				</div>
			<?php endforeach ?>
		</div>
	<?php endif; ?>

	<?php if ( ! abrs_blank( $available_themes ) ) : ?>
		<hr>
		<h2 style="text-align: left;"><?php esc_html_e( 'AweBooking themes', 'awebooking' ); ?></h2>

		<div class="under-the-hood two-col">
			<?php foreach ( $available_themes as $_theme ) : ?>
				<div class="col">
					<a href="<?php echo esc_url( isset( $_theme['link'] ) ? $_theme['link'] : 'https://awethemes.com/themes' ); ?>" target="_blank">
						<img src="<?php echo esc_url( isset( $_theme['label'] ) ? $_theme['thumbnail'] : '' ); ?>">
					</a>

					<h3 class="awebooking-theme-name">
						<a href="<?php echo esc_url( isset( $_theme['link'] ) ? $_theme['link'] : 'https://awethemes.com/themes' ); ?>" target="_blank">
							<?php echo esc_html( isset( $_theme['label'] ) ? $_theme['label'] : '' ); ?>
						</a>
					</h3>
				</div>
			<?php endforeach ?>
		</div>
	<?php endif; ?>

	<hr>
	<div class="return-to-dashboard">
		<a href="<?php echo esc_url( self_admin_url( 'admin.php?awebooking=/settings' ) ); ?>"><?php esc_html_e( 'Go to Settings &rarr;', 'awebooking' ); ?></a>
	</div>
</div>
