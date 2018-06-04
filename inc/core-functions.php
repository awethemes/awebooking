<?php

use AweBooking\Multilingual;
use AweBooking\Gateway\Gateway;
use AweBooking\Bootstrap\Load_Textdomain;
use AweBooking\Component\Currency\Symbol;
use AweBooking\Component\Form\Form_Builder;

// Requires other core functions.
require trailingslashit( __DIR__ ) . 'formatting.php';
require trailingslashit( __DIR__ ) . 'date-functions.php';
require trailingslashit( __DIR__ ) . 'db-functions.php';
require trailingslashit( __DIR__ ) . 'hotel-functions.php';
require trailingslashit( __DIR__ ) . 'booking-functions.php';
require trailingslashit( __DIR__ ) . 'concierge.php';

/**
 * Gets the plugin URL.
 *
 * @param  string $path Optional, extra path to added.
 * @return string
 */
function abrs_plugin_url( $path = null ) {
	return awebooking()->plugin_url( $path );
}

/**
 * Returns the logger.
 *
 * @return \Psr\Log\LoggerInterface
 */
function abrs_logger() {
	return awebooking()->make( 'logger' );
}

/**
 * Report an exception.
 *
 * @param  Exception $e Report the exception.
 * @return void
 *
 * @throws Exception
 */
function abrs_report( $e ) {
	try {
		$logger = awebooking()->make( 'logger' );
	} catch ( \Exception $ex ) {
		throw $e; // Throw the original exception.
	}

	$logger->error( $e->getMessage(), [ 'exception' => $e ] );
}

/**
 * Returns the Http request.
 *
 * @return \Awethemes\Http\Request
 */
function abrs_http_request() {
	return awebooking()->make( 'request' );
}

/**
 * Returns the redirector.
 *
 * @return \AweBooking\Component\Routing\Redirector
 */
function abrs_redirector() {
	return awebooking()->make( 'redirector' );
}

/**
 * Returns the Url_Generator.
 *
 * @return \AweBooking\Component\Routing\Url_Generator
 */
function abrs_url() {
	return awebooking()->make( 'url' );
}

/**
 * Retrieves the route URL.
 *
 * @param  string $path       Optional, the admin route.
 * @param  array  $parameters The additional parameters.
 * @param  bool   $is_ssl     Force the SSL in return URL.
 * @return string
 */
function abrs_route( $path = '/', $parameters = [], $is_ssl = false ) {
	return abrs_url()->route( $path, $parameters, $is_ssl );
}

/**
 * Retrieves the admin route URL.
 *
 * @param  string $path       Optional, the admin route.
 * @param  array  $parameters The additional parameters.
 * @return string
 */
function abrs_admin_route( $path = '/', $parameters = [] ) {
	return abrs_url()->admin_route( $path, $parameters );
}

/**
 * Returns the Mailer.
 *
 * @param  string $email Get specified email.
 * @return \AweBooking\Email\Mailer|\AweBooking\Email\Mailable
 */
function abrs_mailer( $email = null ) {
	return is_null( $email )
		? awebooking()->make( 'mailer' )
		: awebooking( 'mailer' )->driver( $email );
}

/**
 * Returns the WP_Session instance.
 *
 * @param  string|null $key     Optional, get a specified session key.
 * @param  mixed       $default Optional, default value if $key is not exists.
 * @return \Awethemes\WP_Session\WP_Session|mixed
 */
function abrs_session( $key = null, $default = null ) {
	return is_null( $key )
		? awebooking( 'session' )
		: awebooking( 'session' )->get( $key, $default );
}

/**
 * Returns the Flash_Notifier.
 *
 * @param  string $message The notice message.
 * @param  string $level   The notice level.
 * @return \AweBooking\Component\Flash\Flash_Notifier
 */
function abrs_flash( $message = null, $level = 'info' ) {
	$flash = awebooking()->make( 'flash' );

	if ( is_null( $message ) ) {
		return $flash;
	}

	return $flash->add_message( $message, $level );
}

/**
 * Create a new form builder.
 *
 * @param  string      $form_id The form ID.
 * @param  object|null $data    Optional, form data.
 * @return \AweBooking\Component\Form\Form_Builder
 */
function abrs_create_form( $form_id = '', $data = null ) {
	return new Form_Builder( $form_id, $data ?: 0, 'static' );
}

/**
 * Gets the gateway manager.
 *
 * @return \AweBooking\Gateway\Gateways
 */
function abrs_payment_gateways() {
	return awebooking()->make( 'gateways' );
}

/**
 * Get list payment methods.
 *
 * @return array
 */
function abrs_list_payment_methods() {
	$methods = apply_filters( 'awebooking/base_payment_methods', [
		'cash' => esc_html__( 'Cash', 'awebooking' ),
	]);

	$gateways = abrs_payment_gateways()
		->enabled()->map( function( Gateway $gateway ) {
			return $gateway->get_method_title();
		})->all();

	return array_merge( $methods, $gateways );
}

/**
 * Returns plugin current currency.
 *
 * @return string
 */
function abrs_current_currency() {
	return abrs_get_option( 'currency', 'USD' );
}

/**
 * Returns list of currencies.
 *
 * @return array[]
 */
function abrs_list_currencies() {
	return abrs_collect( awebooking( 'currencies' )->all() )
		->pluck( 'name', 'alpha3' )
		->all();
}

/**
 * Get the currency symbol by code.
 *
 * @param  string $currency The currency code.
 * @return string
 */
function abrs_currency_symbol( $currency = null ) {
	if ( is_null( $currency ) ) {
		$currency = abrs_current_currency();
	}

	$symbols = apply_filters( 'awebooking/currency_symbols', Symbol::$symbols );

	$symbol = array_key_exists( $currency, $symbols )
		? $symbols[ $currency ]
		: '';

	return apply_filters( 'awebooking/currency_symbol', $symbol, $currency );
}

/**
 * Get the currency name by code.
 *
 * @param  string $currency The currency code.
 * @return string
 */
function abrs_currency_name( $currency = null ) {
	if ( is_null( $currency ) ) {
		$currency = abrs_current_currency();
	}

	$name = abrs_rescue( function() use ( $currency ) {
		return awebooking( 'currencies' )->find( $currency )['name'];
	});

	return apply_filters( 'awebooking/currency_name', (string) $name, $currency );
}

/**
 * Returns list of countries indexed by alpha2 code.
 *
 * @return array[]
 */
function abrs_list_countries() {
	return abrs_collect( awebooking( 'countries' )->all() )
		->pluck( 'name', 'alpha2' )
		->all();
}

/**
 * Returns list dropdown of currencies.
 *
 * @return array[]
 */
function abrs_list_dropdown_currencies() {
	return abrs_collect( abrs_list_currencies() )
		->transform( function( $name, $code ) {
			return $name . ' (' . abrs_currency_symbol( $code ) . ')';
		})->all();
}

/**
 * Retrieves an option by key-name.
 *
 * @param  string $key     The key name.
 * @param  mixed  $default The default value.
 * @return mixed
 */
function abrs_get_option( $key, $default = null ) {
	return awebooking()->get_option( $key, $default );
}

/**
 * Sanitises various option values based on the nature of the option.
 *
 * @param  string $key   The name of the option.
 * @param  string $value The unsanitised value.
 * @return string
 */
function abrs_sanitize_option( $key, $value ) {
	// Pre-sanitize option by key name.
	switch ( $key ) {
		case 'enable_location':
		case 'children_bookable':
		case 'infants_bookable':
			$value = abrs_sanitize_checkbox( $value );
			break;

		case 'star_rating':
		case 'price_number_decimals':
		case 'page_booking':
		case 'page_checkout':
		case 'page_check_availability':
		case 'scheduler_display_duration':
			$value = absint( $value );
			break;
	}

	/**
	 * Allow custom sanitize a specified option value.
	 *
	 * @param mixed $value Mixed option value.
	 * @var   mixed
	 */
	$value = apply_filters( "awebooking/sanitize_option_{$key}", $value );

	/**
	 * Allow custom sanitize option values.
	 *
	 * @param mixed  $value The option value.
	 * @param string $key   The option key name.
	 * @var   mixed
	 */
	return apply_filters( 'awebooking/sanitize_option', $value, $key );
}

/**
 * Is current WordPress is running on multi-languages.
 *
 * @return bool
 */
function abrs_running_on_multilanguage() {
	return apply_filters( 'awebooking/is_running_multilanguage', Multilingual::is_polylang() || Multilingual::is_wpml() );
}

/**
 * Determines if plugin enable multiple hotels.
 *
 * @return bool
 */
function abrs_multiple_hotels() {
	return apply_filters( 'awebooking/is_multiple_hotels', abrs_get_option( 'enable_location', false ) );
}

/**
 * Determines if plugin allow children in reservation.
 *
 * @return bool
 */
function abrs_children_bookable() {
	return apply_filters( 'awebooking/is_children_bookable', abrs_get_option( 'children_bookable', true ) );
}

/**
 * Determines if plugin allow infants in reservation.
 *
 * @return bool
 */
function abrs_infants_bookable() {
	return apply_filters( 'awebooking/is_infants_bookable', abrs_get_option( 'infants_bookable', true ) );
}

/**
 * Returns the maximum rooms allowed in the scaffold.
 *
 * @return int
 */
function abrs_maximum_scaffold_rooms() {
	return (int) apply_filters( 'awebooking/maximum_scaffold_rooms', 25 );
}

/**
 * Return a list of common titles.
 *
 * @return array
 */
function abrs_list_common_titles() {
	return apply_filters( 'awebooking/list_customer_titles', [
		'mr'   => esc_html__( 'Mr.', 'awebooking' ),
		'ms'   => esc_html__( 'Ms.', 'awebooking' ),
		'mrs'  => esc_html__( 'Mrs.', 'awebooking' ),
		'miss' => esc_html__( 'Miss.', 'awebooking' ),
		'dr'   => esc_html__( 'Dr.', 'awebooking' ),
		'prof' => esc_html__( 'Prof.', 'awebooking' ),
	]);
}

/**
 * Switch AweBooking to site language.
 *
 * @return void
 */
function abrs_switch_to_site_locale() {
	if ( function_exists( 'switch_to_locale' ) ) {
		switch_to_locale( get_locale() );

		// Filter on plugin_locale so load_plugin_textdomain loads the correct locale.
		add_filter( 'plugin_locale', 'get_locale' );

		// Init AweBooking locale.
		( new Load_Textdomain )->bootstrap( awebooking() );
	}
}

/**
 * Switch AweBooking language to original.
 *
 * @return void
 */
function abrs_restore_locale() {
	if ( function_exists( 'restore_previous_locale' ) ) {
		restore_previous_locale();

		// Remove filter.
		remove_filter( 'plugin_locale', 'get_locale' );

		// Init AweBooking locale.
		( new Load_Textdomain )->bootstrap( awebooking() );
	}
}

/**
 * Locate a template and return the path for inclusion.
 *
 * @param  string $template_name The template name.
 * @return string
 */
function abrs_locate_template( $template_name ) {
	// Locate in your {theme}/awebooking.
	$template = locate_template([
		trailingslashit( awebooking()->template_path() ) . $template_name,
	]);

	// Fallback to default template in the plugin.
	if ( ! $template || ABRS_TEMPLATE_DEBUG ) {
		$template = awebooking()->plugin_path( 'templates/' ) . $template_name;
	}

	// Return what we found.
	return apply_filters( 'awebooking/locate_template', $template, $template_name );
}

/**
 * Include a template by given template name.
 *
 * @param  string $template_name Template name.
 * @param  array  $vars          Optional, the data send to template.
 * @return void
 */
function abrs_get_template( $template_name, $vars = [] ) {
	$located = abrs_locate_template( $template_name );

	if ( ! file_exists( $located ) ) {
		/* translators: %s template */
		_doing_it_wrong( __FUNCTION__, sprintf( wp_kses_post( __( '%s does not exist.', 'awebooking' ) ), '<code>' . esc_html( $located ) . '</code>' ), '3.1' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'awebooking/get_template', $located, $template_name, $vars );

	do_action( 'awebooking/before_template_part', $template_name, $located, $vars );

	// Extract $vars to variables.
	if ( ! empty( $vars ) && is_array( $vars ) ) {
		extract( $vars, EXTR_SKIP ); // @codingStandardsIgnoreLine
	}

	// Include the located file.
	include $located;

	do_action( 'awebooking/after_template_part', $template_name, $located, $vars );
}

/**
 * Returns a template content by given template name.
 *
 * @see abrs_get_template()
 *
 * @param  string $template_name Template name.
 * @param  array  $vars          Optional, the data send to template.
 * @return string
 */
function abrs_get_template_content( $template_name, $vars = [] ) {
	$level = ob_get_level();

	ob_start();

	try {
		abrs_get_template( $template_name, $vars );
	} catch ( Exception $e ) {
		awebooking()->handle_buffering_exception( $e, $level );
	} catch ( Throwable $e ) {
		awebooking()->handle_buffering_exception( $e, $level );
	}

	return trim( ob_get_clean() );
}

/**
 * Loads a template part into a template.
 *
 * @param mixed  $slug The slug name for the generic template.
 * @param string $name The name of the specified template.
 */
function abrs_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Try locate {$slug}-{$name}.php first.
	if ( '' !== $name ) {
		$template = abrs_locate_template( "{$slug}-{$name}.php" );
	}

	// Then try locate in {$slug}.php.
	if ( ! $template ) {
		$template = abrs_locate_template( "{$slug}.php" );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'awebooking/get_template_part', $template, $slug, $name );

	if ( $template && file_exists( $template ) ) {
		load_template( $template, false );
	}
}

/**
 * Retrieve the page ID.
 *
 * @param  string $page The page slug: search_results, booking, checkout.
 * @return int
 */
function abrs_get_page_id( $page ) {
	$page_alias = [ // Back-compat, we changed name but still keep ID.
		'search'         => 'check_availability',
		'search_results' => 'check_availability',
	];

	if ( array_key_exists( $page, $page_alias ) ) {
		$page = $page_alias[ $page ];
	}

	$page = apply_filters( "awebooking/get_{$page}_page_id", abrs_get_option( 'page_' . $page ) );

	return $page ? absint( $page ) : 0;
}

/**
 * Retrieve page permalink.
 *
 * @param  string $page The retrieve page.
 * @return string
 */
function abrs_get_page_permalink( $page ) {
	$page_id = abrs_get_page_id( $page );

	$permalink = 0 <= $page_id ? get_permalink( $page_id ) : get_home_url();

	return apply_filters( "awebooking/get_{$page}_page_permalink", $permalink );
}

/**
 * Get an image size. TODO: ...
 *
 * @param array|string $image_size Image size.
 * @return array
 */
function abrs_get_image_size( $image_size ) {
	if ( is_array( $image_size ) ) {
		$width  = isset( $image_size[0] ) ? $image_size[0] : '300';
		$height = isset( $image_size[1] ) ? $image_size[1] : '300';
		$crop   = isset( $image_size[2] ) ? $image_size[2] : 1;

		$size = compact( 'width', 'height', 'crop' );

		$image_size = $width . '_' . $height;

	} elseif ( in_array( $image_size, array( 'thumbnail', 'archive', 'single' ) ) ) {
		$size           = abrs_get_option( $image_size . '_image_size', [] );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 0;
	} else {
		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1,
		);
	}

	return apply_filters( 'awebooking/get_image_size_' . $image_size, $size );
}
