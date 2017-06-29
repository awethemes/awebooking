<?php
namespace Skeleton\CMB2;

use Skeleton\Page_Settings;
use Skeleton\Support\Encrypter;

class Backups {
	/**
	 * CMB2 instance object.
	 *
	 * @var \CMB2
	 */
	protected $cmb_instance;

	/**
	 * CMB2 Object ID.
	 *
	 * @var string|int
	 */
	protected $object_id;

	/**
	 * Type of object being saved.
	 *
	 * @var string
	 */
	protected $object_type;

	/**
	 * Current backup data of this CMB2.
	 *
	 * @var array
	 */
	protected $backup_data = array();

	/**
	 * Create A CMB2 Backup Manager.
	 *
	 * @param \CMB2      $cmb_instance MB2 instance object.
	 * @param int|string $object_id    CMB2 Object ID.
	 * @param string     $object_type  Type of object being saved. (e.g., post, user, or comment).
	 */
	public function __construct( \CMB2 $cmb_instance, $object_id = 0, $object_type = '' ) {
		$this->cmb_instance = $cmb_instance;

		$this->object_id = $cmb_instance->object_id( $object_id );
		$this->object_type = $cmb_instance->object_type( $object_type );

		$this->fetch_backup_data();
	}

	/**
	 * Return encrypt backup payload.
	 *
	 * @return string
	 */
	public function backup() {
		return Encrypter::encrypt( array(
			'cmb_id'      => $this->cmb_instance->cmb_id,
			'object_id'   => (string) $this->object_id,
			'object_type' => $this->object_type,
			'data'        => $this->backup_data,
		));
	}

	/**
	 * Restore a backup code.
	 *
	 * @param  string $payload A payload backup code.
	 * @return true|WP_Error
	 */
	public function restore( $payload ) {
		$payload = trim( $payload );

		// If we have invalid payload or same with current backup code, do nothing.
		if ( empty( $payload ) || $payload === $this->backup() ) {
			return new \WP_Error( 'error', esc_html__( 'Nothing to backup.', 'skeleton' ) );
		}

		// Decrypt payload code and check valid payload header.
		$decrypt = @Encrypter::decrypt( $payload );
		if ( ! $this->is_valid_payload( $decrypt ) ) {
			return new \WP_Error( 'error', esc_html__( 'Invalid payload received.', 'skeleton' ) );
		}

		// If everything is OK, run save fields data.
		$this->cmb_instance->save_fields( $this->object_id, $this->object_type, (array) $decrypt['data'] );
		return true;
	}

	/**
	 * Check valid payload.
	 *
	 * @param  array $payload Payload decrypt.
	 * @return boolean
	 */
	public function is_valid_payload( $payload ) {
		if ( empty( $payload ) || ! is_array( $payload ) ) {
			return false;
		}

		// Checking entirety of payload.
		foreach ( array( 'cmb_id', 'object_id', 'object_type', 'data' ) as $key ) {
			if ( empty( $payload[ $key ] ) ) {
				return false;
			}
		}

		if ( $this->cmb_instance->cmb_id !== $payload['cmb_id'] ) {
			return false;
		}

		if ( $this->cmb_instance->object_id != $payload['object_id'] ) {
			return false;
		}

		if ( $this->cmb_instance->object_type !== $payload['object_type'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Return WP Ajax export URL.
	 *
	 * @see \Skeleton\Core\Admin_Ajax::ajax_export_backup()
	 *
	 * @return string
	 */
	public function get_export_url() {
		$link = sprintf(
			'admin-ajax.php?action=skeleton/backup/export&cmb2=%1$s&object_id=%2$s&object_type=%3$s',
			$this->cmb_instance->cmb_id,
			$this->object_id,
			$this->object_type
		);

		return admin_url( $link );
	}

	/**
	 * Return backup ID.
	 *
	 * @return string
	 */
	public function get_backup_id() {
		if ( $this->cmb_instance instanceof Page_Settings ) {
			$id = $this->cmb_instance->cmb_id;
		} else {
			$id = $this->cmb_instance->cmb_id . '-' . $this->object_id . '-' . $this->object_type;
		}

		$id = apply_filters( 'skeleton/backups/backup_id', $id, $this );

		return str_replace( array( '\\', '/' ), '-', $id );
	}

	/**
	 * Get CMB2 instance.
	 */
	public function get_cmb() {
		return $this->cmb_instance;
	}

	/**
	 * Get CMB2 object_id.
	 */
	public function get_object_id() {
		return $this->object_id;
	}

	/**
	 * Get CMB2 get_object_type.
	 */
	public function get_object_type() {
		return $this->object_type;
	}

	/**
	 * Get backup data.
	 */
	public function get_data() {
		return $this->backup_data;
	}

	/**
	 * Loop through CMB2 fields and set backup data.
	 */
	protected function fetch_backup_data() {
		$ignore_types = array( 'title', 'backup' );

		foreach ( $this->cmb_instance->prop( 'fields' ) as $field ) {
			// Ignore some field types.
			if ( in_array( $field['type'], $ignore_types ) ) {
				continue;
			}

			// Get field value.
			$field = $this->cmb_instance->get_field( $field['id'] );
			$field_value = $field->value();

			// Ignore empty value.
			if ( \CMB2_Utils::isempty( $field_value ) ) {
				continue;
			}

			$this->backup_data[ $field->_id() ] = $field_value;
		}
	}
}
