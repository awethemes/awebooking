<?php
namespace AweBooking\Deprecated;

trait Plugin_Deprecated {
	protected $loaded_addons = [];

	/**
	 * Register an addon with the AweBooking.
	 *
	 * @param  Addon $addon The addon instance.
	 * @return Addon|false
	 */
	public function register_addon( $addon ) {
		// The unique addon ID, normally same as plugin name.
		$addon_id = $addon->get_id();

		// If already registered, just leave.
		if ( isset( $this->loaded_addons[ $addon_id ] ) ) {
			return $addon;
		}

		// Binding the awebooking into the addon.
		$addon->set_awebooking( $this );

		// Validate the addon before register.
		$addon->validate();

		if ( $addon->has_errors() ) {
			$this->failed_addons[ $addon_id ] = $addon;
			return false;
		}

		try {
			$this->register( $addon );
		} catch ( \Exception $e ) {
			$addon->log_error( $e->getMessage() );
		} catch ( \Throwable $e ) {
			$addon->log_error( $e->getMessage() );
		}

		$this->loaded_addons[ $addon_id ] = get_class( $addon );

		return $addon;
	}

	/**
	 * Get addon instance by addon ID.
	 *
	 * @param  string $addon_id The addon ID.
	 * @return Addon
	 */
	public function get_addon( $addon_id ) {
		if ( isset( $this->loaded_addons[ $addon_id ] ) ) {
			return $this->get_provider( $this->loaded_addons[ $addon_id ] );
		}
	}

	/**
	 * Get the loaded addons.
	 *
	 * @return array
	 */
	public function get_addons() {
		return array_map( function( $addon_class ) {
			return $this->get_provider( $addon_class );
		}, $this->loaded_addons );
	}
}
