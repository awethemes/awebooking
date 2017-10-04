<?php
namespace AweBooking\Support;

use Awethemes\Support\Plugin_Updater;

abstract class Addon {
	/**
	 * The container instance.
	 *
	 * @var Container
	 */
	public $awebooking;

	/**
	 * Addon namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'awethemes';

	/**
	 * Addon unique name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Addon file path.
	 *
	 * @var string
	 */
	protected $file_path;

	/**
	 * Log addon errors messages.
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Notify if any update available, only for awethemes addons.
	 *
	 * @var boolean
	 */
	protected $notify_update = true;

	/**
	 * //
	 *
	 * @var boolean
	 */
	protected $updater = false;

	/**
	 * Constructor addon.
	 *
	 * @param string $addon_name Addon unique name.
	 * @param string $addon_path Addon (plugin) file path.
	 */
	public function __construct( $addon_name, $addon_path = null ) {
		$this->name = $addon_name;
		$this->file_path = $addon_path;
	}

	/**
	 * Registers services on the awebooking.
	 *
	 * @return void
	 */
	public function register() {}

	/**
	 * Init the addon.
	 *
	 * @return void
	 */
	public function init() {}

	/**
	 * Requires minimum AweBooking version.
	 *
	 * @return string
	 */
	public function requires() {
		return 'any';
	}

	/**
	 * TODO: ...
	 *
	 * @return array
	 */
	public function conflicts() {
		return [];
	}

	/**
	 * Returns addon name.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->namespace . '.' . sanitize_key( $this->name );
	}

	/**
	 * Returns addon name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Returns addon version.
	 *
	 * @return string|null
	 */
	public function get_version() {
		return defined( 'static::VERSION' ) ? static::VERSION : null;
	}

	/**
	 * Returns addon directory url.
	 *
	 * @return string|null
	 */
	public function get_dir_url() {
		return $this->file_path ? plugin_dir_url( $this->file_path ) : null;
	}

	/**
	 * Returns addon directory path.
	 *
	 * @return string|null
	 */
	public function get_dir_path() {
		return $this->file_path ? plugin_dir_path( $this->file_path ) : null;
	}

	/**
	 * Returns addon basename.
	 *
	 * @return string|null
	 */
	public function get_basename() {
		return $this->file_path ? plugin_basename( $this->file_path ) : null;
	}

	/**
	 * If this addon is a WordPress plugin.
	 *
	 * @return boolean|null
	 */
	public function is_wp_plugin() {
		if ( $this->file_path && function_exists( 'is_plugin_active' ) ) {
			return is_plugin_active( $this->get_basename() );
		}
	}

	/**
	 * Determines this plugin has any errors.
	 *
	 * @return boolean
	 */
	public function has_errors() {
		return count( $this->get_errors() ) !== 0;
	}

	/**
	 * Returns errors messages.
	 *
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Get first error in errors messages.
	 *
	 * @return string
	 */
	public function get_error() {
		return isset( $this->errors[0] ) ? $this->errors[0] : '';
	}

	/**
	 * Log an error message.
	 *
	 * @param  string $message The error message.
	 * @return $this
	 */
	public function log_error( $message ) {
		$this->errors[] = $message;

		return $this;
	}

	/**
	 * Clear the errors messages.
	 *
	 * @return $this
	 */
	public function clear_errors() {
		$this->errors = [];

		return $this;
	}

	/**
	 * If this addon allow notify update.
	 *
	 * @return boolean
	 */
	public function is_notify_update() {
		return $this->notify_update;
	}

	/**
	 * Setup addon updater.
	 *
	 * @return void
	 */
	public function setup_addon_updater() {
		if ( ! class_exists( Plugin_Updater::class ) ) {
			return;
		}

		if ( ! $this->notify_update || 'awethemes' !== $this->namespace ) {
			return;
		}

		if ( ! $this->get_version() || ! $this->get_basename() ) {
			return;
		}

		(new Plugin_Updater(
			$this->get_name(), $this->get_basename(), $this->get_version()
		))->hooks();

		$this->updater = true;
	}
}
