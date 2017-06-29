<?php
namespace Skeleton\Core\Admin;

use Skeleton\Core\Backups;
use Skeleton\Container\Service_Hooks;

class Ajax_Hooks extends Service_Hooks {
	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 */
	public function init() {
		add_action( 'wp_ajax_skeleton/ajax_save', array( $this, 'ajax_save' ) );
		add_action( 'wp_ajax_skeleton/reset_cmb2', array( $this, 'ajax_reset_cmb2' ) );
		add_action( 'wp_ajax_skeleton/backup/export', array( $this, 'ajax_export_backup' ) );
		add_action( 'wp_ajax_skeleton/backup/restore', array( $this, 'ajax_restore_backup' ) );
	}

	public function ajax_save() {
		$cmb2 = $this->get_cmb2_from_request();

		$cmb2->save_fields( 0, '', $this->request( 'data' ) );

		exit( 'true' );
	}

	/**
	 * Handler AJAX export CMB2 backups data.
	 */
	public function ajax_export_backup() {
		$backups = new Backups( $this->get_cmb2_from_request() );

		Utils::header_send_download(
			$backups->backup(),
			$backups->get_backup_id() . '-' . date_i18n( 'mdY' ) . '.txt'
		);
	}

	/**
	 * Handler AJAX restore CMB2 backups data.
	 */
	public function ajax_restore_backup() {
		$backups = new Backups( $this->get_cmb2_from_request() );
		$restored = $backups->restore( $this->request( 'backup_code' ) );

		ob_clean();

		if ( $restored instanceof \WP_Error ) {
			wp_send_json_error( $restored );
		}

		wp_send_json_success( array( 'message' => 'Successfully restored settings.' ) );
	}

	/**
	 * Handler AJAX export CMB2 backups data.
	 */
	public function ajax_reset_cmb2() {
		$cmb2 = $this->get_cmb2_from_request();

		$cmb2->save_fields( 0, '', array() );

		wp_send_json_success( array( 'message' => 'Successfully reset settings.' ) );
	}

	/**
	 * Get CMB2 instance from request.
	 *
	 * @return \CMB2
	 */
	protected function get_cmb2_from_request() {
		$cmb2 = cmb2_get_metabox( $this->request( 'cmb2' ), $this->request( 'object_id' ), $this->request( 'object_type' ) );

		if ( ! $cmb2 instanceof \CMB2 ) {
			wp_send_json_error( new \WP_Error( 'error', esc_html__( 'Invalid CMB2 ID.', 'skeleton' ) ) );
		}

		return $cmb2;
	}

	/**
	 * Get request value by key name with default value support.
	 *
	 * @param  string $key     Request key name from $_POST, $_GET, $_REQUEST.
	 * @param  mixed  $default A default value will be return if key name is not exists.
	 * @return mixed
	 */
	protected function request( $key, $default = null ) {
		return isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : $default;
	}
}
