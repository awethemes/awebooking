<?php
namespace AweBooking;
/**
 * The template for displaying admin welcome after active theme.
 *
 * @package AweBooking
 */
?>

<div class="wrap about-wrap">
	<h1><?php esc_html_e( 'Welcome to AweBooking', 'awebooking' ); ?></h1>

	<p class="about-text"><?php esc_html_e( 'Awebooking is both simple and powerful when it comes to its purpose: booking hotel room. It allows you to setup any reservations quickly, pleasantly and easily.', 'awebooking' ); ?></p>
	<div class="wp-badge awebooking-badge"><?php printf( esc_html__( 'Version %s', 'awebooking' ), AweBooking::VERSION ); ?></div>

	<?php //awebooking( 'admin_welcome' )->display_nav_tabs( 'welcome' ); ?>
	<hr style="margin: 60px 0 30px;" />

	<div class="feature-section two-col">

		<div class="col">
			<h3><?php esc_html_e( 'What\' news', 'awebooking' ); ?></h3>
			<p><?php printf( __( 'Always stay up-to-date with the latest version of Awebooking by checking our <a href="%s">change log</a> regularly.', 'awebooking' ) , esc_url( 'http://docs.awethemes.com/awebooking/change-logs/' ) );	?>
			</p>

			<h3><?php esc_html_e( 'How to use', 'awebooking' ); ?></h3>
			<p><?php printf( __( 'Check out <a href="%s">the plugin\'s documentation</a> if you need more information on how to use Awebooking.', 'awebooking' ), esc_url( 'http://docs.awethemes.com/awebooking/' ) ); // TODO: "and video tutorial" if video exist. ?></p>
		</div>

		<!-- <div class="col">
			<h3><?php // esc_html_e( 'Get Support and Pro Features?', 'awebooking' ); ?></h3>
			<p><?php // esc_html_e( 'Find help in our forum and get free updates when purchasing the PRO version which boats a lot more advanced features', 'awebooking' ); ?></p>
			<p><a class="button-primary" href="#" target="_blank">Get Support and Pro Features</a></p>
		</div> -->
	</div>

	<hr />
<?php /*
	<h2 style="text-align: left;"><?php esc_html_e( 'Premium features', 'awebooking' ); ?></h2>
	<p><?php esc_html_e( 'The PRO version will never fail to impress you. With a bunch of premium features added, your online booking system will be pushed to the next level', 'awebooking' ); ?></p>


	<div class="under-the-hood three-col">
		<div class="col">
			<h3><?php esc_html_e( 'Rule', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.', 'awebooking' ); ?></p>
		</div>

		<div class="col">
			<h3><?php esc_html_e( 'User profile', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.', 'awebooking' ); ?></p>
		</div>

		<div class="col">
			<h3><?php esc_html_e( 'Fees', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.', 'awebooking' ); ?></p>
		</div>
		<div class="col">
			<h3><?php esc_html_e( 'Booking form builder', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.', 'awebooking' ); ?></p>
		</div>

		<div class="col">
			<h3><?php esc_html_e( 'Role permission', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.', 'awebooking' ); ?></p>
		</div>

		<div class="col">
			<h3><?php esc_html_e( 'iCal sync', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.', 'awebooking' ); ?></p>
		</div>

		<div class="col">
			<h3><?php esc_html_e( 'Online payment', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.', 'awebooking' ); ?></p>
		</div>

		<div class="col">
			<h3><?php esc_html_e( 'Contact form 7', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.', 'awebooking' ); ?></p>
		</div>

		<div class="col">
			<h3><?php esc_html_e( 'Calendar in roomtype', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis tellus quis est posuere ornare. Curabitur a erat lorem.', 'awebooking' ); ?></p>
		</div>
	</div>*/
?>
	<hr />

	<div class="return-to-dashboard">
		<a href="<?php echo esc_url( self_admin_url( 'admin.php?page=awebooking-settings' ) ); ?>"><?php esc_html_e( 'Go to Settings &rarr;', 'awebooking' ); ?></a>
	</div>
</div>
