<?php

namespace AweBooking\Admin;

class Notices {
	/**
	 * Stores notices.
	 *
	 * @var array
	 */
	protected static $notices = [];

	/**
	 * Array of notices.
	 *
	 * @var array
	 */
	protected static $core_notices = [
		'install'          => 'print_install_notice',
		'update'           => 'print_update_notice',
		'plugins_requires' => 'print_plugins_requires_notice',
	];

	/**
	 * Constructor.
	 */
	public static function init() {
		static::$notices = (array) get_option( 'awebooking_admin_notices', [] );

		add_action( 'wp_loaded', [ __CLASS__, 'dismiss_notices' ] );
		add_action( 'admin_notices', [ __CLASS__, 'print_notices' ] );
		add_action( 'shutdown', [ __CLASS__, 'store' ] );
	}

	/**
	 * Gets all notices.
	 *
	 * @return array
	 */
	public static function all() {
		return self::$notices;
	}

	/**
	 * Show a notice.
	 *
	 * @param string $name Notice name.
	 */
	public static function show( $name ) {
		self::$notices = array_unique( array_merge( static::$notices, (array) $name ) );
	}

	/**
	 * Remove a notice from being displayed.
	 *
	 * @param string $name Notice name.
	 */
	public static function remove( $name ) {
		static::$notices = array_diff( static::$notices, (array) $name );
	}

	/**
	 * Flush all notices.
	 *
	 * @return void
	 */
	public static function flush() {
		self::$notices = [];
	}

	/**
	 * Store notices to DB.
	 *
	 * @return void
	 */
	public static function store() {
		update_option( 'awebooking_admin_notices', static::$notices );
	}

	/**
	 * Reset notices for themes when switched or a new version of AweBooking is installed.
	 *
	 * @return void
	 */
	public static function reset_notices() {
		static::flush();

		static::show( 'plugins_requires' );
	}

	/**
	 * Has a notice been dismissed by current user?
	 *
	 * @param  string $notice Notice name.
	 * @return bool
	 */
	public static function is_dismissed( $notice ) {
		return (bool) get_user_meta( get_current_user_id(), 'awebooking_dismissed_' . $notice . '_notice', true );
	}

	/**
	 * Handler dismiss a notice if requested.
	 *
	 * @return void
	 */
	public static function dismiss_notices() {
		if ( isset( $_GET['abrs-hide-notice'], $_GET['notice_nonce'] ) ) {
			$nonce = sanitize_key( wp_unslash( $_GET['notice_nonce'] ) );

			if ( ! wp_verify_nonce( $nonce, 'abrs_hide_notices_nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'awebooking' ) );
			}

			if ( ! current_user_can( 'manage_awebooking' ) ) {
				wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'awebooking' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['abrs-hide-notice'] ) );

			static::remove( $hide_notice );

			update_user_meta( get_current_user_id(), 'awebooking_dismissed_' . $hide_notice . '_notice', true );
		}
	}

	/**
	 * Print the notices.
	 *
	 * @return void
	 */
	public static function print_notices() {
		if ( ! $notices = static::$notices ) {
			return;
		}

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		// Notices should only show on AweBooking screens,
		// the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, [ 'dashboard', 'plugins' ] ) && ! abrs_is_awebooking_screen() ) {
			return;
		}

		foreach ( $notices as $notice ) {
			if ( ! array_key_exists( $notice, static::$core_notices ) ) {
				continue;
			}

			if ( apply_filters( 'abrs_show_admin_notice', true, $notice ) ) {
				call_user_func( [ __CLASS__, static::$core_notices[ $notice ] ] );
			}
		}
	}

	/**
	 * If we have just installed, show a message with the install pages button.
	 *
	 * @return void
	 */
	public static function print_install_notice() {
		// ...
	}

	/**
	 * If we need to update, include a message with the update button.
	 *
	 * @return void
	 */
	public static function print_update_notice() {
		// ...
	}

	/**
	 * Print list plugins requires.
	 *
	 * @return void
	 */
	public static function print_plugins_requires_notice() {
		if ( static::is_dismissed( 'plugins_requires' ) ) {
			return;
		}

		if ( ! function_exists( 'wp_simple_iconfonts' ) ) {
			include __DIR__ . '/views/notices/html-notice-plugins-requires.php';
		}
	}
}
