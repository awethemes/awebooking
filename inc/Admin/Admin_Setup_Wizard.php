<?php
namespace AweBooking\Admin;

use Skeleton\Menu_Page;
use Skeleton\Admin_Page;
use AweBooking\Interfaces\Config;
use AweBooking\AweBooking;
use AweBooking\Admin\Admin_Utils;
use Skeleton\Support\Priority_List;

class Admin_Setup_Wizard {

	/** @var string Currenct Step */
	private $step   = '';

	/** @var array Steps for the setup wizard */
	private $steps  = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_init', array( $this, 'setup_wizard' ) );
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'awebooking-setup', '' );
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'awebooking-setup' !== $_GET['page'] ) {
			return;
		}
		$default_steps = array(
			'introduction' => array(
				'name'    => __( 'Introduction', 'awebooking' ),
				'view'    => array( $this, 'abkng_setup_introduction' ),
				'handler' => '',
			),
			'pages' => array(
				'name'    => __( 'Page setup', 'awebooking' ),
				'view'    => array( $this, 'abkng_setup_pages' ),
				'handler' => array( $this, 'abkng_setup_pages_save' ),
			),
			'locale' => array(
				'name'    => __( 'General setup', 'awebooking' ),
				'view'    => array( $this, 'abkng_setup_locale' ),
				'handler' => array( $this, 'abkng_setup_locale_save' ),
			),
			'next_steps' => array(
				'name'    => __( 'Ready!', 'awebooking' ),
				'view'    => array( $this, 'abkng_setup_ready' ),
				'handler' => '',
			),
		);

		$this->steps = apply_filters( 'awebooking_setup_wizard_steps', $default_steps );
		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );
		$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'select2', AweBooking()->plugin_url() . '/assets/js/select2/select2.full.js', array( 'jquery' ), '4.0.3' );

		wp_enqueue_style( 'awebooking_admin_styles', AweBooking()->plugin_url() . '/assets/css/admin.css', array(), AweBooking::VERSION );
		wp_enqueue_style( 'awebooking-setup', AweBooking()->plugin_url() . '/assets/css/awebooking-setup.css', array( 'dashicons', 'install' ), AweBooking::VERSION );
		wp_enqueue_style( 'select2', AweBooking()->plugin_url() . '/assets/css/select2.css', array( 'install' ), '4.0.3' );

		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	/**
	 * Get the URL for the next step's screen.
	 * @param string step   slug (default: current step)
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 * @since 3.0.0
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys );
		if ( false === $step_index ) {
			return '';
		}

		return add_query_arg( 'step', $keys[ $step_index + 1 ] );
	}

	/**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'AweBooking &rsaquo; Setup Wizard', 'awebooking' ); ?></title>
			<?php wp_print_scripts( 'select2' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="awebooking-setup wp-core-ui">
			<h1 id="awebooking-logo"><a href=""><i class="awebookingf awebookingf-logo"></i></a></h1>
		<?php
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
			<?php if ( 'next_steps' === $this->step ) : ?>

				<div class="awebooking-setup__footer">
					<p class="awebooking-setup-actions step text-right">
						<a href="<?php echo esc_url( admin_url() ); ?>" class="button button-large"><?php esc_html_e( 'Return to the WordPress Dashboard', 'awebooking' ); ?></a>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=room_type&tutorial=true' ) ); ?>" class="button-primary button button-large button-next"><?php esc_html_e( 'Create your first room type!', 'awebooking' ); ?></a>
					</p>
				</div>

			<?php endif; ?>

				<script>
					jQuery(document).ready(function() {
						if (jQuery.fn.select2 == undefined) {
							return;
						}

						jQuery('#currency').select2();
						jQuery('#currency_position').select2({
							minimumResultsForSearch: -1
						});
					});
				</script>
			</body>
		</html>
		<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$ouput_steps = $this->steps;
		array_shift( $ouput_steps );
		if ( 'introduction' !== $this->step ) :
			$step_number = 1;
		?>
		<ol class="awebooking-setup-steps">
			<?php foreach ( $ouput_steps as $step_key => $step ) : ?>
				<li class="awebooking-setup-steps__item <?php
					if ( $step_key === $this->step ) {
						echo 'active';
					} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
						echo 'done';
					}
				?>">
					<div class="awebooking-setup-steps__number">
						<span class="awebooking-setup-steps__number-effect"></span>
						<i><?php echo esc_html( $step_number ); ?></i>
					</div>
					<div class="awebooking-setup-steps__title"><?php echo esc_html( $step['name'] ); ?></div>
				</li>
			<?php
				$step_number++;
				endforeach;
			?>
		</ol>
		<?php
		endif;
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="awebooking-setup-content">';
		call_user_func( $this->steps[ $this->step ]['view'], $this );
		echo '</div>';
	}

	/**
	 * Introduction step.
	 */
	public function abkng_setup_introduction() {
		?>
		<div class="awebooking-setup-introduction">
			<h1 class="awebooking-setup-welcome text-center"><?php esc_html_e( 'Welcome to the AweBooking!', 'awebooking' ); ?></h1>
			<p><?php _e( 'Thank you for choosing AweBooking to power your hotel! This quick setup wizard will help you configure the basic settings. <strong>It’s completely optional and shouldn’t take longer than two minutes.</strong>', 'awebooking' ); ?></p>
			<p><?php esc_html_e( 'No time right now? If you don’t want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!', 'awebooking' ); ?></p>
		</div>
		<p class="awebooking-setup-actions step text-right">
			<a href="<?php echo esc_url( admin_url() ); ?>" class="button button-large"><?php esc_html_e( 'Skip', 'awebooking' ); ?></a>
			<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-large button-next"><?php esc_html_e( 'Let\'s go!', 'awebooking' ); ?></a>
		</p>
		<?php
	}

	/**
	 * Page setup.
	 */
	public function abkng_setup_pages() {
		?>
		<h1><?php esc_html_e( 'Page setup', 'awebooking' ); ?></h1>
		<form method="post">
			<p><?php printf( __( 'Your hotel needs a few essential <a href="%s" target="_blank">pages</a>. The following will be created automatically (if they do not already exist):', 'awebooking' ), esc_url( admin_url( 'edit.php?post_type=page' ) ) ); ?></p>
			<table class="awebooking-setup-pages" cellspacing="0">
				<thead>
					<tr>
						<th class="page-name"><?php esc_html_e( 'Page name', 'awebooking' ); ?></th>
						<th class="page-description"><?php esc_html_e( 'Description', 'awebooking' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="page-name"><?php echo _x( 'Check Availability', 'Page title', 'awebooking' ); ?></td>
						<td><?php esc_html_e( 'Selected page to display check availability form.', 'awebooking' ); ?></td>
					</tr>
					<tr>
						<td class="page-name"><?php echo _x( 'Booking Informations', 'Page title', 'awebooking' ); ?></td>
						<td><?php esc_html_e( 'Selected page to display booking informations.', 'awebooking' ); ?></td>
					</tr>
					<tr>
						<td class="page-name"><?php echo _x( 'Confirm Booking', 'Page title', 'awebooking' ); ?></td>
						<td>
							<?php esc_html_e( 'Selected page to display checkout informations.', 'awebooking' ); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<p><?php printf( __( 'Once created, these pages can be managed from your admin dashboard on the <a href="%1$s" target="_blank">Pages screen</a>. You can control which pages are shown on your website via <a href="%2$s" target="_blank">Appearance > Menus</a>.', 'awebooking' ), esc_url( admin_url( 'edit.php?post_type=page' ) ), esc_url( admin_url( 'nav-menus.php' ) ) ); ?></p>

			<p class="awebooking-setup-actions step text-right">
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php esc_html_e( 'Skip this step', 'awebooking' ); ?></a>
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'awebooking' ); ?>" name="save_step" />
				<?php wp_nonce_field( 'awebooking-setup' ); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * Save Page Settings.
	 */
	public function abkng_setup_pages_save() {
		check_admin_referer( 'awebooking-setup' );

		$pages = apply_filters( 'awebooking/create_pages', array(
			'check_availability' => array(
				'name'    => _x( 'check-availability', 'Page slug', 'awebooking' ),
				'title'   => _x( 'Check Availability', 'Page title', 'awebooking' ),
				'content' => '[' . apply_filters( 'awebooking/check_availability_shortcode_tag', 'awebooking_check_availability' ) . ']',
			),
			'booking' => array(
				'name'    => _x( 'booking', 'Page slug', 'awebooking' ),
				'title'   => _x( 'Booking Informations', 'Page title', 'awebooking' ),
				'content' => '[' . apply_filters( 'awebooking/check_availability_shortcode_tag', 'awebooking_booking' ) . ']',
			),
			'checkout' => array(
				'name'    => _x( 'checkout', 'Page slug', 'awebooking' ),
				'title'   => _x( 'Confirm Booking', 'Page title', 'awebooking' ),
				'content' => '[' . apply_filters( 'awebooking/check_availability_shortcode_tag', 'awebooking_checkout' ) . ']',
			)
		) );

		foreach ( $pages as $key => $page ) {
			Admin_Utils::create_page( esc_sql( $page['name'] ), 'page_' . $key, $page['title'], $page['content'], '' );
		}

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Locale settings.
	 */
	public function abkng_setup_locale() {
		$currencies       = awebooking( 'currency_manager' )->get_for_dropdown( '%name (%symbol)' );
		$currency_pos   = awebooking( 'currency_manager' )->get_positions();

		// Defaults
		$currency_default = abkng_config( 'currency' ) ? abkng_config( 'currency' ) : abkng_config()->get_default( 'currency' );
		$currency_pos_default = abkng_config( 'currency_position' ) ? abkng_config( 'currency_position' ) : abkng_config()->get_default( 'currency_position' );
		$thousand_sep   = abkng_config( 'price_thousand_separator' ) ? abkng_config( 'price_thousand_separator' ) : abkng_config()->get_default( 'price_thousand_separator' );
		$decimal_sep   = abkng_config( 'price_decimal_separator' ) ? abkng_config( 'price_decimal_separator' ) : abkng_config()->get_default( 'price_decimal_separator' );
		$num_decimals   = abkng_config( 'price_number_decimals' ) ? abkng_config( 'price_number_decimals' ) : abkng_config()->get_default( 'price_number_decimals' );
		?>
		<h1><?php esc_html_e( 'General setup', 'awebooking' ); ?></h1>
		<form method="post">

			<table class="form-table">
				<tr>
					<th scope="row"><label for="enable_location"><?php esc_html_e( 'Multi-location?', 'awebooking' ); ?></label></th>
					<td>
						<input class="checkbox" type="checkbox" <?php checked( abkng_config( 'enable_location' ), 'on' ); ?> id="enable_location" name="enable_location" />
					</td>
				</tr>
				<tr>

					<th scope="row"><label for="currency"><?php esc_html_e( 'Which currency will your hotel use?', 'awebooking' ); ?></label></th>
					<td>
						<select id="currency" name="currency" style="width:100%;" data-placeholder="<?php esc_attr_e( 'Choose a currency&hellip;', 'awebooking' ); ?>" class="awebooking-enhanced-select">
							<?php foreach ( $currencies as $key => $currency ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php  selected( $currency_default, $key, true ); ?>><?php echo esc_html_e( $currency ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="currency_position"><?php esc_html_e( 'Currency position', 'awebooking' ); ?></label></th>
					<td>
						<select id="currency_position" name="currency_position" class="awebooking-enhanced-select">
							<?php foreach ( $currency_pos as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php  selected( $currency_pos_default, $key, true ); ?>><?php echo esc_html_e( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="price_thousand_separator"><?php esc_html_e( 'Thousand separator', 'awebooking' ); ?></label></th>
					<td>
						<input type="text" id="price_thousand_separator" name="price_thousand_separator" size="2" value="<?php echo esc_attr( $thousand_sep ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="price_decimal_separator"><?php esc_html_e( 'Decimal separator', 'awebooking' ); ?></label></th>
					<td>
						<input type="text" id="price_decimal_separator" name="price_decimal_separator" size="2" value="<?php echo esc_attr( $decimal_sep ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="price_number_decimals"><?php esc_html_e( 'Number of decimals', 'awebooking' ); ?></label></th>
					<td>
						<input type="text" id="price_number_decimals" name="price_number_decimals" size="2" value="<?php echo esc_attr( $num_decimals ); ?>" />
					</td>
				</tr>

			</table>

			<p class="awebooking-setup-actions step text-right">
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php esc_html_e( 'Skip this step', 'awebooking' ); ?></a>
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'awebooking' ); ?>" name="save_step" />
				<?php wp_nonce_field( 'awebooking-setup' ); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * Save Locale Settings.
	 */
	public function abkng_setup_locale_save() {
		check_admin_referer( 'awebooking-setup' );

		$enable_location           = isset( $_POST['enable_location'] ) ? sanitize_text_field( $_POST['enable_location'] ) : '';

		$currency                  = sanitize_text_field( $_POST['currency'] );
		$currency_position         = sanitize_text_field( $_POST['currency_position'] );
		$price_thousand_separator  = sanitize_text_field( $_POST['price_thousand_separator'] );
		$price_decimal_separator   = sanitize_text_field( $_POST['price_decimal_separator'] );
		$price_number_decimals     = sanitize_text_field( $_POST['price_number_decimals'] );

		awebooking( 'wp_option' )->set( 'enable_location', $enable_location );

		awebooking( 'wp_option' )->set( 'currency', $currency );
		awebooking( 'wp_option' )->set( 'currency_position', $currency_position );
		awebooking( 'wp_option' )->set( 'price_thousand_separator', $price_thousand_separator );
		awebooking( 'wp_option' )->set( 'price_decimal_separator', $price_decimal_separator );
		awebooking( 'wp_option' )->set( 'price_number_decimals', $price_number_decimals );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Actions on the final step.
	 */
	private function abkng_setup_ready_actions() {
		add_option( 'awebooking_installed', 'true' );
	}

	/**
	 * Final step.
	 */
	public function abkng_setup_ready() {
		$this->abkng_setup_ready_actions();
		?>
		<h1 class="awebooking-setup-welcome text-center"><?php esc_html_e( 'Your hotel is ready!', 'awebooking' ); ?></h1>

		<p><?php printf( __( 'You will need to <a href="%1$s" target="_blank">create some room types</a>. to start using awebooking plugin for your hotel. Enjoy your plugin! If you\'re not sure how, please check plugin document <a href="%2$s" target="_blank">here</a>.', 'awebooking' ), esc_url( admin_url( 'post-new.php?post_type=room_type&tutorial=true' ) ), esc_url( 'http://docs.awethemes.com/awebooking/' ) ); ?>
		</p>
		<?php
	}
}
