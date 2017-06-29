<?php
namespace Skeleton\CMB2\Fields;

use Skeleton\CMB2\Backups;

class Backups_Field extends Field_Abstract {
	/**
	 * Render custom field type callback.
	 *
	 * @param \CMB2_Field $field              The passed in `CMB2_Field` object.
	 * @param mixed       $escaped_value      The value of this field escaped.
	 * @param int|string  $object_id          The ID of the current object.
	 * @param string      $object_type        The type of object you are working with.
	 * @param \CMB2_Types $field_type_object  The `CMB2_Types` object.
	 */
	public function output( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$cmb2 = $field->get_cmb();

		if ( is_wp_error( $cmb2 ) ) {
			return;
		}

		$backups = new Backups( $cmb2 ); ?>

		<div class="cmb2-form-backup">
			<h4><?php esc_html_e( 'Backup', 'skeleton' ); ?></h4>
			<p><?php printf( wp_kses_post( __( 'Here you can copy/<a href="%s" target="_blank">download</a> current settings. Keep this safe as you can use it as a backup should anything go wrong.', 'skeleton' ) ), esc_url( $backups->get_export_url() ) ); ?></p>
			<textarea class="cmb2-backup-code" readonly rows="10" cols="60"><?php print $backups->backup(); // WPCS: XSS OK. ?></textarea>
		</div>

		<hr>

		<div class="cmb2-form-restore" data-cmb2="<?php echo esc_attr( $cmb2->cmb_id ); ?>" data-object-id="<?php echo esc_attr( $backups->get_object_id() ); ?>" data-object-type="<?php echo esc_attr( $backups->get_object_type() ); ?>">
			<h4><?php esc_html_e( 'Restore a backup', 'skeleton' ); ?></h4>
			<p><?php esc_html_e( 'Copy-paste your backup string here:', 'skeleton' ); ?></p>
			<textarea class="cmb2-restore-code" rows="10" cols="60"></textarea>

			<p>
				<button type="button" class="button cmb2-import-backup"><?php esc_html_e( 'Backup Now', 'skeleton' ); ?></button>
				<span><?php printf( wp_kses_post( __( 'or <a href="%s" class="cmb2-reset-backup">reset</a> to defaults.', 'skeleton' ) ), '#s' ); ?></span>
			</p>

			<div class="cmb2-warning"><p><?php esc_html_e( 'Warning! This will overwrite all existing options, please proceed with caution!', 'skeleton' ); ?></p></div>
		</div><?php
	}
}
