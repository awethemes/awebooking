<?php
namespace AweBooking\Providers;

use AweBooking\Admin\Admin_Menu;
use AweBooking\Admin\Admin_Settings;
use AweBooking\Admin\Admin_Template;
use AweBooking\Support\Flash_Message;
use AweBooking\Support\Service_Provider;
use Illuminate\Support\Arr;

class Admin_Service_Provider extends Service_Provider {
	/**
	 * The core setting classes.
	 *
	 * @var array
	 */
	protected $settings = [
		\AweBooking\Admin\Settings\General_Setting::class,
		\AweBooking\Admin\Settings\Display_Setting::class,
		\AweBooking\Admin\Settings\Email_Setting::class,
	];

	/**
	 * The initializations classes.
	 *
	 * @var array
	 */
	protected $initializations = [
		// TODO: ...
		\AweBooking\Admin\Admin_Scripts::class,
		\AweBooking\Admin\Permalink_Settings::class,
		\AweBooking\Admin\Controllers\Admin_Ajax::class,
		\AweBooking\Admin\Controllers\Action_Handler::class,
		\AweBooking\Admin\Pages\Admin_Email_Preview::class,
		\AweBooking\Admin\Pages\Admin_Setup_Wizard::class,

		// Metaboxes.
		\AweBooking\Admin\Metaboxes\Room_Type_Metabox::class,
		\AweBooking\Admin\Metaboxes\Booking_Metabox::class,
		\AweBooking\Admin\Metaboxes\Service_Metabox::class,
		\AweBooking\Admin\Metaboxes\Amenity_Metabox::class,

		// List-table.
		\AweBooking\Admin\List_Tables\Booking_List_Table::class,
		\AweBooking\Admin\List_Tables\Room_Type_List_Table::class,
		\AweBooking\Admin\List_Tables\Service_List_Table::class,
	];

	/**
	 * Registers services on the AweBooking.
	 */
	public function register() {
		$this->awebooking->singleton( 'admin_menu', function() {
			return new Admin_Menu;
		});

		$this->awebooking->singleton( 'admin_notices', function( $a ) {
			return new Flash_Message( $a['session'], '_admin_notices' );
		});

		$this->awebooking->singleton( 'admin_template', function() {
			return new Admin_Template;
		});

		$this->awebooking->singleton( 'admin_settings', function( $a ) {
			return new Admin_Settings( $a['setting'] );
		});

		$this->awebooking->alias( 'admin_settings', Admin_Settings::class );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! is_admin() ) {
			return;
		}

		// Register core admin settings.
		$this->register_core_settings();

		// Fire the admin hooks.
		$this->awebooking->make( 'admin_menu' )->init();

		// Initializations core.
		foreach ( $this->initializations as $call_class ) {
			$this->awebooking->make( $call_class );
		}

		add_action( 'admin_init', [ $this, 'handle_admin_init' ] );
		add_action( 'admin_head', [ $this, 'correct_admin_menus' ] );
		add_action( 'admin_notices', [ $this, 'display_admin_notices' ] );

		add_filter( 'admin_title', [ $this, 'correct_admin_title' ], 10, 2 );
		add_filter( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );
	}

	/**
	 * Register the core admin settings.
	 *
	 * @return void
	 */
	protected function register_core_settings() {
		$settings = $this->awebooking->make( 'admin_settings' );

		// Loop all core settings and register them.
		foreach ( $this->settings as $setting ) {
			$settings->register( $setting );
		}

		/**
		 * Here you can register or custom AweBooking settings.
		 *
		 * @param Admin_Settings $settings The Admin_Setting instance.
		 */
		do_action( 'awebooking/register_admin_settings', $settings );
	}

	/**
	 * Handle redirects to setup/welcome page after install and updates.
	 *
	 * @access private
	 */
	public function handle_admin_init() {
		// Setup wizard redirect.
		if ( get_transient( '_awebooking_activation_redirect' ) ) {
			delete_transient( '_awebooking_activation_redirect' );

			// Prevent redirect on some case.
			if ( ( ! empty( $_GET['page'] ) && in_array( $_GET['page'], [ 'awebooking-setup' ] ) ) || is_network_admin() ) {
				return;
			}

			// If the user needs to install, send them to the setup wizard.
			wp_safe_redirect( admin_url( 'index.php?page=awebooking-setup' ) );
			exit;
		}
	}

	/**
	 * Highlights the correct admin-menus.
	 *
	 * @access private
	 */
	public function correct_admin_menus() {
		global $parent_file, $submenu_file;

		$current_screen = get_current_screen();
		if ( ! $current_screen ) {
			return;
		}

		if ( 'awebooking_admin' === $current_screen->base ) {
			// @codingStandardsIgnoreStart
			$parent_file  = 'awebooking';
			$submenu_file = 'admin.php?awebooking=' . awebooking('request')->route_path();
			// @codingStandardsIgnoreEnd
		}
	}

	/**
	 * Correct the title tag content for an admin page.
	 *
	 * @param  string $admin_title The page title, with extra context added.
	 * @param  string $title       The original page title.
	 * @return string
	 *
	 * @access private
	 */
	public function correct_admin_title( $admin_title, $title ) {
		global $submenu;

		$current_screen = get_current_screen();
		if ( 'awebooking_admin' !== $current_screen->base || ! isset( $submenu['awebooking'] ) ) {
			return $admin_title;
		}

		$found_submenu = Arr::first( $submenu['awebooking'], function( $submenu_item ) {
			return 'admin.php?awebooking=' . awebooking( 'request' )->route_path() === $submenu_item[2];
		});

		return $found_submenu ? $found_submenu[3] . $admin_title : $admin_title;
	}

	/**
	 * Setup and display admin notices.
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 *
	 * @access private
	 */
	public function display_admin_notices() {
		$this->awebooking['admin_notices']->setup_message();

		if ( ! $this->awebooking['admin_notices']->has() ) {
			return;
		}

		foreach ( $this->awebooking['admin_notices']->all() as $message ) : ?>
			<div class="notice notice-<?php echo esc_attr( $message['type'] ); ?> is-dismissible">
				<?php echo wp_kses_post( wpautop( $message['message'] ) ); ?>
			</div>
		<?php endforeach;
	}

	/**
	 * Add state for check availability page. TODO: Move to admin page.
	 *
	 * @param  array $post_states The post_states.
	 * @param  void  $post        The post object.
	 * @return array
	 *
	 * @access private
	 */
	public function display_post_states( $post_states, $post ) {
		if ( intval( awebooking_option( 'page_check_availability' ) ) === $post->ID ) {
			$post_states['page_check_availability'] = esc_html_x( 'Check Availability', 'Page states', 'awebooking' );
		}

		if ( intval( awebooking_option( 'page_booking' ) ) === $post->ID ) {
			$post_states['page_booking'] = esc_html_x( 'Booking Confirmation', 'Page states', 'awebooking' );
		}

		if ( intval( awebooking_option( 'page_checkout' ) ) === $post->ID ) {
			$post_states['page_checkout'] = esc_html_x( 'Checkout', 'Page states', 'awebooking' );
		}

		return $post_states;
	}
}
