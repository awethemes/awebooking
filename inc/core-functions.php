<?php

use AweBooking\Multilingual;
use AweBooking\Component\Currency\Symbol;
use AweBooking\Component\Form\Form_Builder;
use AweBooking\Gateway\Manager as Gateway_Manager;

/* Constants */
if ( ! defined( 'ABRS_TEMPLATE_DEBUG' ) ) {
	define( 'ABRS_TEMPLATE_DEBUG', false );
}

if ( ! defined( 'ABRS_ASSET_URL' ) ) {
	define( 'ABRS_ASSET_URL', awebooking()->plugin_url( 'assets/' ) );
}

// Requires other core functions.
require trailingslashit( __DIR__ ) . 'sanitizer.php';
require trailingslashit( __DIR__ ) . 'formatting.php';
require trailingslashit( __DIR__ ) . 'date-functions.php';
require trailingslashit( __DIR__ ) . 'db-functions.php';
require trailingslashit( __DIR__ ) . 'room-functions.php';
require trailingslashit( __DIR__ ) . 'booking-functions.php';
require trailingslashit( __DIR__ ) . 'calendar.php';
require trailingslashit( __DIR__ ) . 'concierge.php';
require trailingslashit( __DIR__ ) . 'reservation.php';

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
 * Returns the Http request.
 *
 * @return \Awethemes\Http\Request
 */
function abrs_request() {
	return awebooking()->make( 'request' );
}

/**
 * Returns the Url_Generator.
 *
 * @return \AweBooking\Http\Routing\Url_Generator
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
 * Gets the gateway manager.
 *
 * @return array
 */
function abrs_payment_gateways() {
	return awebooking()->make( Gateway_Manager::class );
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

	$gateways = awebooking()->make( 'gateways' )->enabled()
		->map( function( $m ) {
			return $m->get_method_title();
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
	 * Allow custom sanitize a special option value.
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
 * @return string
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
 * @param string $name The name of the specialised template.
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
 * @param  string $page The page slug: check_availability, booking, checkout.
 * @return int
 */
function abrs_get_page_id( $page ) {
	$page_alias = [ // Back-compat, we changed name but still keep ID.
		'search_results' => 'check_availability',
	];

	if ( array_key_exists( $page, $page_alias ) ) {
		$page = $page_alias[ $page ];
	}

	$page = apply_filters( "awebooking/get_{$page}_page_id", abrs_get_option( 'page_' . $page ) );

	return $page ? absint( $page ) : -1;
}

/**
 * Retrieve page permalink.
 *
 * @see awebooking_get_page_id()
 *
 * @param  string $page The retrieve page.
 * @return string
 */
function abrs_page_permalink( $page ) {
	$page_id = abrs_get_page_id( $page );

	$permalink = 0 < $page_id ? get_permalink( $page_id ) : get_home_url();

	return apply_filters( "awebooking/get_{$page}_page_permalink", $permalink );
}

/**
 * Create a new form builder.
 *
 * @param  string     $form_id The form ID.
 * @param  Model|null $model   Optional, the model data.
 * @return \AweBooking\Component\Form\Form_Builder
 */
function abrs_create_form( $form_id = '', $model = null ) {
	return new Form_Builder( $form_id, $model ?: 0, 'static' );
}

/**
 * Returns the date format.
 *
 * @return string
 */
function abrs_date_format() {
	return apply_filters( 'awebooking/date_format', get_option( 'date_format' ) );
}

/**
 * Returns the time format.
 *
 * @return string
 */
function abrs_time_format() {
	return apply_filters( 'awebooking/time_format', get_option( 'time_format' ) );
}

/**
 * Returns the date time format.
 *
 * @return string
 */
function abrs_datetime_format() {
	/* translators: 1 -Date format, 2 - Time format */
	return apply_filters( 'awebooking/date_time_format', sprintf( esc_html_x( '%1$s %2$s', 'DateTime Format', 'awebooking' ), abrs_date_format(), abrs_time_format() ) );
}
