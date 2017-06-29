<?php
namespace Skeleton\Iconfonts;

use Skeleton\Skeleton;

class Admin_Uploader {
	/**
	 * Skeleton container instance.
	 *
	 * @var Skeleton
	 */
	protected $skeleton;

	/**
	 * Fonticons Manager instance.
	 *
	 * @var Manager
	 */
	protected $manager;

	/**
	 * Fonticons Installer instance.
	 *
	 * @var Installer
	 */
	protected $installer;

	/**
	 * The error messages.
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * The uploader messages.
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * Page slug for this page.
	 *
	 * @var string
	 */
	protected $page_slug = 'awethemes-icons';

	/**
	 * Constructor uploader.
	 *
	 * @param Skeleton  $skeleton
	 * @param Manager   $manager
	 * @param Installer $installer
	 */
	public function __construct( Skeleton $skeleton, Manager $manager, Installer $installer ) {
		$this->manager = $manager;
		$this->skeleton = $skeleton;
		$this->installer = $installer;
	}

	/**
	 * Init hooks fonticons uploader.
	 */
	public function init() {
		// Handler ajax delete.
		add_action( 'wp_ajax_skeleton/iconfonts/delete', array( $this, 'handler_delete_icon' ) );
	}

	/**
	 * Output icon manager.
	 */
	public function output() {
		// Handler upload a icon pack.
		if ( ! empty( $_POST ) && ! empty( $_FILES ) ) {
			check_admin_referer( 'skeleton/upload_iconpack' );
			$this->handler_upload_icon();
		}

		// Enqueue admin scripts.
		add_action( 'skeleton/admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Load the admin header.
		require_once ABSPATH . 'wp-admin/admin-header.php';

		// Load main template.
		include dirname( __FILE__ ) . '/views/main.php';
	}

	/**
	 * Handler install new icon pack.
	 */
	protected function handler_upload_icon() {
		if ( empty( $_FILES['icon-zip'] ) ) {
			return;
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Handler upload archive file.
		add_filter( 'upload_dir', array( $this, 'set_tmp_upload_dir' ) );
		$upload_result = wp_handle_upload( $_FILES['icon-zip'], array( 'test_form' => false ) );

		if ( isset( $upload_result['error'] ) ) {
			return $this->add_error( $upload_result['error'] );
		}

		/**
		 * Hook ac_before_install_icon_pack
		 *
		 * @param array           $upload_result  Upload result array.
		 * @param IconManager $this           Instance of this class.
		 */
		do_action( 'ac_before_install_icon_pack', $upload_result, $this );

		$installed_icon = $this->installer->zip_install( $upload_result['file'], true );
		@unlink( $upload_result['file'] );

		/**
		 * Hook ac_after_install_icon_pack
		 *
		 * @param array                  $upload_result  Upload result array.
		 * @param Icon_Pack|WP_Error $installed_icon Installed icon result.
		 * @param IconManager        $this           Instance of this class.
		 */
		do_action( 'ac_after_install_icon_pack', $upload_result, $installed_icon, $this );

		if ( is_wp_error( $installed_icon ) ) {
			return $this->add_error( $installed_icon->get_error_message() );
		} else {
			$this->add_message( esc_html__( 'Install new icon successfully', 'awecontent' ) );
		}

		/**
		 * Hook ac_installed_icon_pack
		 *
		 * @param Icon_Pack   $installed_icon Installed icon model class.
		 * @param IconManager $this           Instance of this class.
		 */
		do_action( 'ac_installed_icon_pack', $installed_icon, $this );
	}

	/**
	 * Handler delete a icon pack via HTTP request.
	 *
	 * @access private
	 */
	public function handler_delete_icon() {
		if ( ! check_ajax_referer( 'ac_delete_icon', '_ajax_nonce', false ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Hey, are you human?', 'awecontent' ) ) );
		}

		if ( empty( $_REQUEST['icon_slug'] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You missing icon name to delete.', 'awecontent' ) ) );
		}

		// Starting delete icon...
		$icon_name = sanitize_text_field( $_REQUEST['icon_slug'] );

		/**
		 * Hook ac_before_delete_icon.
		 *
		 * @param string             $icon_name Name of icon pack will be delete.
		 * @param Icon_Manager_UI $this      Instance of this class.
		 */
		do_action( 'ac_before_delete_icon', $icon_name, $this );

		$delete_result = $this->icon_manager->delete_upload_icon( $icon_name );

		if ( ! $delete_result ) {

			wp_send_json_error( array( 'message' => esc_html__( 'An error has occurred. please try again.', 'awecontent' ) ) );

		} else {

			/**
			 * Hook ac_deleted_icon.
			 *
			 * @param Icon_Manager_UI $this Instance of this class.
			 */
			do_action( 'ac_deleted_icon', $this );

			wp_send_json_success( array( 'message' => sprintf( esc_html__( 'Icon pack "%s" has been deleted successfully', 'awecontent' ), $icon_name ) ) );
		}

		wp_die();
	}


	/**
	 * Set custom upload directory.
	 *
	 * @access private
	 *
	 * @param  array $upload Upload directory information.
	 * @return array
	 */
	public function set_tmp_upload_dir( $upload ) {
		$upload['url']  = ''; // We don't need this.
		$upload['path'] = rtrim( $this->skeleton['iconfonts_upload_tmp_dir'], '/' );

		return $upload;
	}

	/**
	 * Output partial upload form.
	 */
	protected function output_upload_form() {
		$upload_dir = wp_upload_dir();

		if ( ! empty( $upload_dir['error'] ) ) {
			$error  = sprintf( '<p>%s</p>', esc_html__( 'Before you can upload your icon, you will need to fix the following error:', 'awecontent' ) );
			$error .= sprintf( '<p><strong>%s</strong></p>', $upload_dir['error'] );

			printf( '<div class="error inline">%s</div>', $error ); // WPCS: XSS OK.
			return;
		}

		include dirname( __FILE__ ) . '/views/upload-form.php';
	}

	/**
	 * Add an error.
	 *
	 * @param string $text Message text.
	 */
	protected function add_error( $text ) {
		$this->errors[] = $text;
	}

	/**
	 * Add a message.
	 *
	 * @param string $text Error message.
	 */
	protected function add_message( $text ) {
		$this->messages[] = $text;
	}

	/**
	 * Output messages and errors.
	 */
	protected function show_messages() {
		if ( count( $this->errors ) > 0 ) {
			foreach ( $this->errors as $error ) {
				echo '<div class="error notice notice-error is-dismissible"><p>' . esc_html( $error ) . '</p></div>'; // WPCS: XSS OK.
			}
		} elseif ( count( $this->messages ) > 0 ) {
			foreach ( $this->messages as $message ) {
				echo '<div class="updated notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';  // WPCS: XSS OK.
			}
		}
	}

	/**
	 * Add a icon box.
	 *
	 * @param AC_Icon_Model $icon The icon model.
	 */
	protected function add_icons_box( $icon ) {
		$nonce = wp_create_nonce( 'ac_delete_icon' );
		include dirname( __FILE__ ) . '/views/icon-box.php';
	}

	/**
	 * Enqueue scripts.
	 *
	 * @access private
	 */
	public function enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_style( 'skeleton-iconpack' );
		wp_enqueue_script( 'skeleton-iconpack' );

		wp_localize_script( 'skeleton-iconpack', 'skeletonIcons', $this->skeleton['iconfonts_manager']->get_for_js() );

		foreach ( $this->manager->all() as $iconpack ) {
			$iconpack->enqueue_styles();
		}
	}
}
