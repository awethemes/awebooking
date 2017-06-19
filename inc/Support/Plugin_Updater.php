<?php
namespace AweBooking\Support;

use AweBooking\AweBooking;

/**
 * Creates the AweThemes API connection.
 *
 * @link https://code.tutsplus.com/tutorials/a-guide-to-the-wordpress-http-api-automatic-plugin-updates--wp-25181
 */
class Plugin_Updater {
	/**
	 * API URL checking latest version.
	 *
	 * @var string
	 */
	const LATEST_URL = 'https://update.awethemes.com/latest/awebooking.json';

	/**
	 * The plugin name.
	 *
	 * NOTE: Not sure when we hard 'awebooking' here!
	 *
	 * @var string
	 */
	protected $plugin_name = 'awebooking';

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * Current plugin version.
	 *
	 * @var string
	 */
	protected $current_version;

	/**
	 * Plugin_Updater Constructor.
	 */
	public function __construct() {
		$this->plugin_slug = awebooking()->plugin_basename() . '/awebooking.php';
		$this->current_version = AweBooking::VERSION;

		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );
		add_filter( 'plugins_api', [ $this, 'get_infomation' ], 10, 3 );
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

		// Get latest version of awebooking.
		$remote_version = $this->request( static::LATEST_URL );

		if ( $remote_version && version_compare( $this->current_version, $remote_version['latest'], '<' ) ) {
			$transient->response[ $this->plugin_slug ] = (object) [
				'slug'        => $this->plugin_name,
				'package'     => $remote_version['download_url'],
				'new_version' => $remote_version['latest'],
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

		if ( $args->slug !== $this->plugin_name ) {
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
}
