<?php

use AweBooking\Support\Collection;

$awebooking_available_addons = new Collection([
	[
		'id'          => 'awethemes.awebooking-extra',
		'label'       => 'AweBooking Extra',
		'description' => 'Show off Room Available Calendar, detail Price Breakdown, plus Room Image Slideshow - all additional awesomeness is included in just one',
	],
	[
		'id'          => 'awethemes.awebooking-woocommerce',
		'label'       => 'Woocommerce Integration',
		'description' => 'Seamlessly connects with Woocommerce to benefit from all variety of Woocommerce extensions and payment gateways',
	],
	[
		'id'          => 'awethemes.awebooking-payment',
		'label'       => 'Payment',
		'description' => 'Awebooking allows you to enable payments online via PayPal Express with guest checkout.',
	],
	[
		'id'          => 'awethemes.awebooking-form-builder',
		'label'       => 'Booking Form Builder',
		'description' => 'Build and add your own custom fields in the booking check-out form to collect any information you need for each reservation',
	],
	// [
	// 	'id'          => 'awethemes.awebooking-user-profile',
	// 	'label'       => 'User Profile',
	// 	'description' => 'Customers can manage log-in credentials, personal information and more in their own profile page. Even more, they can check and manage their bookings quickly and easily',
	// ],
	[
		'id'          => 'awethemes.awebooking-simple-reservation',
		'label'       => 'Simple Reservation',
		'description' => 'Customers just simply send contact and booking request email to admin to get support without any hassle of multiple booking steps',
	],
]);

?><div class="wrap about-wrap">
	<h1><?php esc_html_e( 'Welcome to AweBooking', 'awebooking' ); ?></h1>

	<p class="about-text"><?php esc_html_e( 'AweBooking is both simple and powerful when it comes to its purpose: booking hotel room. It allows you to setup any reservations quickly, pleasantly and easily.', 'awebooking' ); ?></p>
	<div class="wp-badge awebooking-badge"><?php printf( esc_html__( 'Version %s', 'awebooking' ), AweBooking::VERSION ); ?></div>

	<?php $this->display_nav_tabs( 'welcome' ); ?>

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
