<?php
namespace Skeleton\CMB2;

use Skeleton\Container\Service_Hooks;
use Skeleton\Support\Multidimensional;

class CMB2_Hooks extends Service_Hooks {
	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 *
	 * @param Skeleton $skeleton Skeleton instance.
	 */
	public function register( $skeleton ) {
		$skeleton->bind( 'cmb2_manager', function () use ( $skeleton ) {
			return new CMB2_Manager( $skeleton );
		});
	}

	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param Skeleton $skeleton Skeleton instance.
	 */
	public function init( $skeleton ) {
		// Init custom fields.
		$skeleton['cmb2_manager']->hooks_fields();

		// Fixed checkbox issue with default is true.
		add_filter( 'cmb2_sanitize_toggle', array( $this, 'sanitize_checkbox' ), 20, 2 );
		add_filter( 'cmb2_sanitize_checkbox', array( $this, 'sanitize_checkbox' ), 20, 2 );

		add_filter( 'cmb2_types_esc_toggle', array( $this, 'escape_checkbox' ), 20, 4 );
		add_filter( 'cmb2_types_esc_checkbox', array( $this, 'escape_checkbox' ), 20, 4 );

		// Support multidimensional fields.
		add_filter( 'cmb2_override_meta_save', array( $this, 'multidimensional_save_data' ), 10, 4 );
		add_filter( 'cmb2_override_meta_value', array( $this, 'multidimensional_get_data' ), 10, 4 );
		add_filter( 'cmb2_override_meta_remove', array( $this, 'multidimensional_remove_data' ), 10, 4 );
	}

	/**
	 * Fixed checkbox issue with default is true.
	 *
	 * @param  mixed $override_value Sanitization/Validation override value to return.
	 * @param  mixed $value          The value to be saved to this field.
	 * @return mixed
	 */
	public function sanitize_checkbox( $override_value, $value ) {
		// Return 0 instead of false if null value given. This hack for
		// checkbox or checkbox-like can be setting true as default value.
		return is_null( $value ) ? 0 : $value;
	}

	/**
	 * Fixed checkbox issue with default is true.
	 *
	 * @param  mixed      $override_value Override value to return.
	 * @param  mixed      $meta_value     The field meta value.
	 * @param  array      $args           The field arguments.
	 * @param  CMB2_Field $field          CMB2_Field object instance.
	 * @return mixed
	 */
	public function escape_checkbox( $override_value, $meta_value, $args, $field ) {
		$meta_value = $field->val_or_default( $meta_value );
		return ( 'off' === $meta_value || 0 === $meta_value ) ? false : $meta_value;
	}

	/**
	 * Filter whether to override saving of meta value.
	 *
	 * @param null|bool  $check      Whether to allow updating metadata for the given type.
	 * @param array      $args       Array of data about current field including.
	 * @param array      $field_args All field argument.
	 * @param CMB2_Field $field      This field objec.
	 * @return mixed
	 */
	public function multidimensional_save_data( $check, $args, $field_args, $field ) {
		$no_override = null;
		$id_data = Multidimensional::split( $args['field_id'] );

		// Tell CMB2 that we no override value.
		if ( empty( $id_data['keys'] ) ) {
			return $no_override;
		}

		// Options page updating...
		if ( 'options-page' === $args['type'] || empty( $args['id'] ) ) {
			$options = cmb2_options( $args['id'] )->get( $id_data['base'], array() );
			$repace_value = $args['value'];

			if ( ! $args['single'] ) {
				$repace_value = Multidimensional::multidimensional_get( $options, $id_data['keys'], array() );
				$repace_value[] = $args['value'];
				$repace_value = array_unique( $repace_value );
			}

			$update_values = Multidimensional::multidimensional_replace( $options, $id_data['keys'], $repace_value );
			return cmb2_options( $args['id'] )->update( $id_data['base'], $update_values, false, true );
		}

		// Update metadata...
		// NOTE: Maybe have bugs in this case, see: CMB2_Field::update_data().
		$metadata = get_metadata( $args['type'], $args['id'], $id_data['base'], true );

		// Delete meta if we have an empty array.
		if ( is_array( $args['value'] ) && empty( $args['value'] ) ) {
			$update_values = Multidimensional::delete( $metadata, $id_data['keys'] );
		} else {
			$update_values = Multidimensional::multidimensional_replace( $metadata, $id_data['keys'], $args['value'] );
		}

		return update_metadata( $args['type'], $args['id'], $id_data['base'], $update_values );
	}

	/**
	 * Filter whether to override saving of meta value.
	 *
	 * @param null|bool  $delete     Whether to allow metadata deletion of the given type.
	 * @param array      $args       Array of data about current field.
	 * @param int|string $field_args All field arguments.
	 * @param CMB2_Field $field      CMB2_Field object.
	 * @return mixed
	 */
	public function multidimensional_remove_data( $delete, $args, $field_args, $field ) {
		$no_override = null;
		$id_data = Multidimensional::split( $args['field_id'] );

		// Tell CMB2 that we no override value.
		if ( empty( $id_data['keys'] ) ) {
			return $no_override;
		}
		
		// NOTE: Maybe have bugs, just fixed for temp.
		$id_keys = '[' . implode( '][', $id_data['keys'] ) . ']';

		// Handler delete options.
		if ( 'options-page' === $args['type'] || empty( $args['id'] ) ) {
			$options = cmb2_options( $args['id'] )->get( $id_data['base'], array() );

			Multidimensional::delete( $options, $id_data['keys'] );

			return cmb2_options( $args['id'] )->update( $id_data['base'], $options, false, true );
		}

		// Handler delete metadata.
		$metadata = (array) get_metadata( $args['type'], $args['id'], $id_data['base'], true );

		Multidimensional::delete( $metadata, $id_data['keys'] );

		return update_metadata( $args['type'], $args['id'], $id_data['base'], $metadata );
	}

	/**
	 * Filter whether to override saving of meta value.
	 *
	 * @param null|bool  $value      The meta value should return.
	 * @param int|string $object_id  Object ID.
	 * @param array      $args       An array of arguments for retrieving data.
	 * @param CMB2_Field $field      CMB2_Field object.
	 * @return mixed
	 */
	public function multidimensional_get_data( $value, $object_id, $args, $field ) {
		$no_override = 'cmb2_field_no_override_val';
		$id_data = Multidimensional::split( $args['field_id'] );

		// Tell CMB2 that we no override value.
		if ( empty( $id_data['keys'] ) ) {
			return $no_override;
		}

		if ( 'options-page' === $args['type'] ) {
			$metadata = cmb2_options( $args['id'] )->get( $id_data['base'] );
		} else {
			// NOTE: Maybe have bugs in this case, see: CMB2_Field::get_data().
			$metadata = get_metadata( $args['type'], $args['id'], $id_data['base'], true );
		}

		return Multidimensional::multidimensional_get( $metadata, $id_data['keys'] );
	}
}
