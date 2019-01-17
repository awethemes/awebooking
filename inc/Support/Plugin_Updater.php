<?php

namespace AweBooking\Support;

use AweBooking\Premium;

class Plugin_Updater {
	/* Constants */
	const LATEST_URI   = 'https://update.awethemes.com/latest/{plugin}.json';
	const DOWNLOAD_URI = 'https://update.awethemes.com/download/{plugin}.zip';

	/**
	 * The plugin name (slug name, eg: awebooking).
	 *
	 * @var string
	 */
	protected $plugin;

	/**
	 * The plugin base name (eg: awebooking/awebooking.php).
	 *
	 * @var string
	 */
	protected $basename;

	/**
	 * Constructor.
	 *
	 * @param string $plugin   Plugin name/slug.
	 * @param string $basename Plugin basename, @see plugin_basename().
	 */
	public function __construct( $plugin, $basename ) {
		$this->plugin   = $plugin;
		$this->basename = $basename;
	}

	/**
	 * Hooks in to WP.
	 *
	 * @return void
	 */
	public function hooks() {
		add_filter( 'plugins_api', [ $this, 'get_infomation' ], 10, 3 );
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );
	}

	/**
	 * Gets the plugin file.
	 *
	 * @return string|null
	 */
	public function get_plugin_file() {
		if ( file_exists( trailingslashit( WPMU_PLUGIN_DIR ) . $this->basename ) ) {
			return trailingslashit( WPMU_PLUGIN_DIR ) . $this->basename;
		}

		if ( file_exists( trailingslashit( WP_PLUGIN_DIR ) . $this->basename ) ) {
			return trailingslashit( WP_PLUGIN_DIR ) . $this->basename;
		}

		return null;
	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 272 for the original wp_update_plugins() function.
	 *
	 * @param  array $transient Update array build by WordPress.
	 * @return array
	 */
	public function check_update( $transient ) {
		// Prevent if transient checked is empty.
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		if ( ! $plugin_file = $this->get_plugin_file() ) {
			return $transient;
		}

		$plugin          = get_plugin_data( $plugin_file, false, false );
		$current_version = $plugin['Version'];

		// Get latest version of awebooking.
		$remote_version = $this->request(
			str_replace( '{plugin}', $this->plugin, static::LATEST_URI )
		);

		if ( isset( $remote_version['latest'] ) && version_compare( $current_version, $remote_version['latest'], '<' ) ) {
			$transient->response[ $this->basename ] = (object) [
				'slug'        => $this->plugin,
				'plugin'      => $this->basename,
				'new_version' => $remote_version['latest'],
				'package'     => $this->generate_download_link(),
			];
		}

		return $transient;
	}

	/**
	 * Add awebooking response for infomation request.
	 *
	 * @param false|object $result The result data.
	 * @param string       $action The type of information being requested from the Plugin Install API.
	 * @param object       $args   Plugin API arguments.
	 * @return string
	 */
	public function get_infomation( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		if ( $args->slug !== $this->basename ) {
			return $result;
		}

		// TODO: ...
		return $result;
	}

	/**
	 * Request to remote and retrieve json response.
	 *
	 * @param  string $url URL to retrieve.
	 * @return array|false
	 */
	protected function request( $url ) {
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Returns the download link.
	 *
	 * @return string
	 */
	protected function generate_download_link() {
		global $wp_version;

		$download_link = str_replace( '{plugin}', $this->plugin, static::DOWNLOAD_URI );

		return add_query_arg([
			'api'                => Premium::get_api_code(),
			'domain'             => parse_url( home_url( '' ), PHP_URL_HOST ),
			'wordpress_version'  => $wp_version,
			'awebooking_version' => awebooking()->version(),
		], $download_link );
	}
}
