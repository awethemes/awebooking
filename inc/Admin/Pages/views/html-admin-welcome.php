<?php

use AweBooking\Support\Collection;

$awebooking_available_addons = new Collection([
	[
		'id'          => 'awethemes.awebooking-extra',
		'label'       => 'AweBooking Extra',
		'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.',
	],
	[
		'id'          => 'awethemes.awebooking-woocommerce',
		'label'       => 'Woocommerce Integration',
		'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.',
	],
	[
		'id'          => 'awethemes.awebooking-payment',
		'label'       => 'Payment',
		'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.',
	],
	[
		'id'          => 'awethemes.awebooking-user-profile',
		'label'       => 'User Profile',
		'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.',
	],
	[
		'id'          => 'awethemes.awebooking-simple-reservation',
		'label'       => 'Simple Reservation',
		'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.',
	],
]);

?><div class="wrap about-wrap">
	<h1><?php esc_html_e( 'Welcome to AweBooking', 'awebooking' ); ?></h1>

	<p class="about-text"><?php esc_html_e( 'AweBooking is both simple and powerful when it comes to its purpose: booking hotel room. It allows you to setup any reservations quickly, pleasantly and easily.', 'awebooking' ); ?></p>
	<div class="wp-badge awebooking-badge"><?php printf( esc_html__( 'Version %s', 'awebooking' ), AweBooking::VERSION ); ?></div>

	<?php awebooking( 'admin_welcome' )->display_nav_tabs( 'welcome' ); ?>

	<div class="feature-section two-col">
		<div class="col">
			<h3><?php esc_html_e( 'What\' news', 'awebooking' ); ?></h3>
			<p><?php printf( __( 'Always stay up-to-date with the latest version of AweBooking by checking our <a href="%s">change log</a> regularly.', 'awebooking' ), esc_url( 'http://docs.awethemes.com/awebooking/change-logs/' ) );	?>
			</p>

			<h3><?php esc_html_e( 'How to use', 'awebooking' ); ?></h3>
			<p><?php printf( __( 'Check out <a href="%s">the plugin\'s documentation</a> if you need more information on how to use AweBooking.', 'awebooking' ), esc_url( 'http://docs.awethemes.com/awebooking/' ) ); // TODO: "and video tutorial" if video exist. ?></p>
		</div>

		<div class="col">
			<h3><?php esc_html_e( 'Get Support and Pro Features?', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Find help in our forum and get free updates when purchasing the PRO version which boats a lot more advanced features', 'awebooking' ); ?></p>
			<p><a class="button-primary" href="https://awethemes.com/plugins/awebooking" target="_blank">Get Support and Pro Features</a></p>
		</div>
	</div>

	<hr>

	<h2 style="text-align: left;"><?php esc_html_e( 'Premium features', 'awebooking' ); ?></h2>
	<p><?php esc_html_e( 'The PRO version will never fail to impress you. With a bunch of premium features added, your online booking system will be pushed to the next level', 'awebooking' ); ?></p>

	<div class="under-the-hood three-col">
		<?php foreach ( $awebooking_available_addons as $_addon ) :
			$installed_addon = awebooking()->get_addon( $_addon['id'] ); ?>

			<div class="col">
				<h3>
					<?php echo esc_html( $_addon['label'] ); ?>

					<?php if ( $installed_addon && ! $installed_addon->has_errors() ) : ?>
						<span class="awebooking-addon-installed"><?php echo esc_html__( 'Installed', 'awebooking' ) ?></span>
					<?php endif ?>
				</h3>

				<p><?php echo wp_kses_post( $_addon['description'] ); ?></p>
			</div>
		<?php endforeach ?>
	</div>

	<hr>

	<div class="return-to-dashboard">
		<a href="<?php echo esc_url( self_admin_url( 'admin.php?page=awebooking-settings' ) ); ?>"><?php esc_html_e( 'Go to Settings &rarr;', 'awebooking' ); ?></a>
	</div>
</div>
