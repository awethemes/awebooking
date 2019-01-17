<?php

namespace AweBooking\Availability;

use WP_Error;
use AweBooking\Model\Hotel;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;
use WPLibs\Http\Request as Http_Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use AweBooking\Availability\Search\Search_Form;
use Illuminate\Support\Arr;

class Request implements \ArrayAccess, \JsonSerializable {
	use Deprecated\Deprecated;

	/**
	 * The http request instance.
	 *
	 * @var \WPLibs\Http\Request
	 */
	protected $http_request;

	/**
	 * The form field parameters.
	 *
	 * @var \Symfony\Component\HttpFoundation\ParameterBag
	 */
	protected $parameters;

	/**
	 * The parameters will be lock.
	 *
	 * @var array
	 */
	protected $locks = [];

	/**
	 * Store the constraints.
	 *
	 * @var array
	 */
	protected $constraints = [];

	/**
	 * Store the errors.
	 *
	 * @var \WP_Error
	 */
	protected $errors;

	/**
	 * Store the request hash.
	 *
	 * @var string
	 */
	protected $hash;

	/**
	 * Store current instance number.
	 *
	 * @var int
	 */
	protected $instance = 0;

	/**
	 * Store count instance number.
	 *
	 * @var int
	 */
	protected static $instances = 0;

	/**
	 * Create new instance from Http request.
	 *
	 * @param Http_Request $request
	 * @return static
	 */
	public static function create_from_request( Http_Request $request ) {
		return new static( null, null, $request );
	}

	/**
	 * Constructor.
	 *
	 * @param Timespan|null     $timespan
	 * @param Guest_Counts|null $guest_counts
	 * @param Http_Request|null $http_request
	 */
	public function __construct(
		Timespan $timespan = null,
		Guest_Counts $guest_counts = null,
		Http_Request $http_request = null
	) {
		$this->instance = ++static::$instances;

		$this->errors       = new WP_Error;
		$this->http_request = $http_request;

		$this->initialize( compact( 'timespan', 'guest_counts' ) );
	}

	/**
	 * Initialize search form with default parameters
	 *
	 * @param  array $parameters
	 * @return $this
	 */
	public function initialize( array $parameters = [] ) {
		$this->parameters = new ParameterBag;

		// Sets default parameters.
		foreach ( $this->get_default_parameters() as $key => $value ) {
			$this->parameters->set( $key, $value );
		}

		// TODO: Consider to remove in next version.
		if ( isset( $parameters['timespan'] ) || isset( $parameters['guest_counts'] ) ) {
			$this->initialize_from_objects( $parameters );
		}

		// Initialize the parameters.
		if ( $this->http_request ) {
			$parameters += $this->http_request->only( $this->parameters->keys() );
		}

		$this->merge_parameters( $parameters );

		if ( abrs_multilingual() && ! $this->get_parameter( 'lang' ) ) {
			$this->set_parameter( 'lang', abrs_multilingual()->get_current_language() );
		}

		if ( abrs_multiple_hotels() && ! $this->get_parameter( 'hotel' ) ) {
			$this->set_parameter( 'hotel', abrs_get_page_id( 'primary_hotel' ) );
		}

		return $this;
	}

	/**
	 * Validate the request.
	 *
	 * @return \WP_Error|bool
	 */
	public function validate() {
		$errors = new WP_Error;

		try {
			$timespan = $this->get_timespan();
			$timespan->requires_minimum_nights( 1 );
		} catch ( \Exception $e ) {
			$errors->add( 'timespan', $e->getMessage() );
		}

		return count( $errors->errors ) > 0 ? $errors : true;
	}

	/**
	 * Search the available rooms and rates.
	 *
	 * @return \AweBooking\Availability\Query_Results
	 */
	public function search() {
		return ( new Query( $this ) )->search();
	}

	/**
	 * Display the search form template.
	 *
	 * @param \AweBooking\Availability\Search\Search_Form $search_form
	 * @return string
	 */
	public function display( Search_Form $search_form ) {
		$search_form->set_request( $this );

		if ( $this->http_request ) {
			$search_form->set_http_request( $this->http_request );
		}

		return $search_form->render();
	}

	/**
	 * Gets the request parameter.
	 *
	 * @param  string $name The parameter key.
	 * @return mixed
	 */
	public function get( $name ) {
		$name = strtolower( $name );

		if ( method_exists( $this, $method = "get_{$name}" ) ) {
			return $this->{$method}();
		}

		return $this->get_parameter( $name );
	}

	/**
	 * Returns all parameters.
	 *
	 * @return array
	 */
	public function get_parameters() {
		return $this->parameters->all();
	}

	/**
	 * Return parameter value by given key.
	 *
	 * @param  string $key The parameter key name.
	 * @return mixed
	 */
	public function get_parameter( $key ) {
		return $this->parameters->get( $key );
	}

	/**
	 * Set a parameter by key/value.
	 *
	 * @param  string $key   The key name.
	 * @param  mixed  $value The key value.
	 * @param  bool   $force Force to set even lock.
	 * @return $this
	 */
	public function set_parameter( $key, $value, $force = false ) {
		if ( $force || ! $this->is_locked( $key ) ) {
			$this->parameters->set( $key, $value );
		}

		return $this;
	}

	/**
	 * Set the parameters from raw values.
	 *
	 * @param array $parameters
	 */
	public function merge_parameters( array $parameters ) {
		foreach ( $parameters as $key => $value ) {
			if ( is_null( $value ) || ! $this->parameters->has( $key ) ) {
				continue;
			}

			if ( $this->is_locked( $key ) ) {
				continue;
			}

			$value = abrs_clean( $value );

			if ( method_exists( $this, $method = 'set_' . strtolower( $key ) ) ) {
				$this->{$method}( $value );
			} else {
				$this->parameters->set( $key, $value );
			}
		}
	}

	/**
	 * Returns default parameters.
	 *
	 * @return array
	 */
	public function get_default_parameters() {
		return [
			'check_in'     => null,
			'check_out'    => null,
			'adults'       => 1,
			'children'     => 0,
			'infants'      => 0,
			'number_rooms' => 1, // Number of rooms.
			'promo_code'   => '',
			'only'         => null, // Search only specific rooms.
			'hotel'        => null,
			'lang'         => null,
		];
	}

	/**
	 * Gets the constraints.
	 *
	 * @return array
	 */
	public function get_constraints() {
		return $this->constraints;
	}

	/**
	 * Sets the constraints.
	 *
	 * @param  array $constraints Array of constraints.
	 * @return $this
	 */
	public function set_constraints( $constraints ) {
		$this->constraints = $constraints;

		return $this;
	}

	/**
	 * Add one or more constraints.
	 *
	 * @param  array $constraints Array of constraints.
	 * @return $this
	 */
	public function add_contraints( $constraints ) {
		foreach ( (array) $constraints as $constraint ) {
			$this->constraints[] = $constraint;
		}

		return $this;
	}

	/**
	 * Returns length of stay.
	 *
	 * @return int
	 */
	public function get_los() {
		return $this->get_timespan()->get_nights();
	}

	/**
	 * Return the current hotel.
	 *
	 * @return int|null
	 */
	public function get_hotel() {
		return $this->get_parameter( 'hotel' );
	}

	/**
	 * Set the current hotel ID.
	 *
	 * @param \AweBooking\Model\Hotel|int $hotel The hotel ID.
	 * @return $this
	 */
	public function set_hotel( $hotel ) {
		if ( $hotel instanceof Hotel ) {
			$hotel = $hotel->get_id();
		}

		return $this->set_parameter( 'hotel', (int) $hotel );
	}

	/**
	 * Return the "check_in" date string.
	 *
	 * @return string|null
	 */
	public function get_check_in() {
		return $this->get_parameter( 'check_in' );
	}

	/**
	 * Set the check_in date.
	 *
	 * @param  string|\DateTimeInterface $date
	 * @return void
	 */
	public function set_check_in( $date ) {
		if ( ! $date instanceof \DateTimeInterface ) {
			$date = abrs_date( $date );
		}

		if ( is_null( $date ) ) {
			$this->errors->add( 'check_in', esc_html__( 'Please enter a valid arrival date.', 'awebooking' ) );

			return;
		}

		if ( $check_out = $this->parameters->has( 'check_out' ) ) {
			$this->validate_timespan( $date, $check_out );
		}

		$this->set_parameter( 'check_in', $date->format( 'Y-m-d' ) );
	}

	protected function validate_timespan() {
		$timespan = $this->get_timespan();
	}

	/**
	 * Return the number of adults.
	 *
	 * @return int
	 */
	public function get_adults() {
		return $this->get_parameter( 'adults' );
	}

	/**
	 * Sets the number of adults.
	 *
	 * @param  int $adults The number of adults.
	 * @return $this
	 */
	public function set_adults( $adults ) {
		if ( is_numeric( $adults ) && $adults > 0 ) {
			$adults = min( $adults, (int) abrs_get_option( 'search_form_max_adults', 6 ) );

			$this->set_parameter( 'adults', max( 1, $adults ) );
		}

		return $this;
	}

	/**
	 * Return the number of children.
	 *
	 * @return int
	 */
	public function get_children() {
		return $this->get_parameter( 'children' );
	}

	/**
	 * Sets the number of children.
	 *
	 * @param  int $children The number of children.
	 * @return $this
	 */
	public function set_children( $children ) {
		if ( is_numeric( $children ) && $children > 0 ) {
			$this->set_parameter( 'children', min( $children, (int) abrs_get_option( 'search_form_max_children', 6 ) ) );
		}

		return $this;
	}

	/**
	 * Return the number of infants.
	 *
	 * @return int
	 */
	public function get_infants() {
		return $this->get_parameter( 'infants' );
	}

	/**
	 * Sets the number of infants.
	 *
	 * @param  int $infants The number of infants.
	 * @return $this
	 */
	public function set_infants( $infants ) {
		if ( is_numeric( $infants ) && $infants > 0 ) {
			$this->set_parameter( 'infants', min( $infants, (int) abrs_get_option( 'search_form_max_infants', 6 ) ) );
		}

		return $this;
	}

	/**
	 * Lock one or more parameters.
	 *
	 * @param  array|mixed $parameters The parameter(s) to lock.
	 * @return void
	 */
	public function lock( $parameters ) {
		$parameters = is_array( $parameters ) ? $parameters : func_get_args();

		$this->locks = array_unique(
			array_merge( $this->locks, $parameters )
		);
	}

	/**
	 * Unlock one or more parameters.
	 *
	 * @param  array|mixed $parameters The parameter(s) to unlock.
	 * @return void
	 */
	public function unlock( $parameters ) {
		$parameters = is_array( $parameters ) ? $parameters : func_get_args();

		$this->locks = array_diff( $this->locks, $parameters );
	}

	/**
	 * Determines if given parameter(s) is locked.
	 *
	 * @param  array|mixed $parameters The parameter(s) to check.
	 * @return bool
	 */
	public function is_locked( $parameters ) {
		$parameters = is_array( $parameters ) ? $parameters : func_get_args();

		foreach ( $parameters as $parameter ) {
			if ( ! in_array( $parameter, $this->locks ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns the HTTP request.
	 *
	 * @return \WPLibs\Http\Request|null
	 */
	public function get_http_request() {
		return $this->http_request;
	}

	/**
	 * Sets the HTTP request.
	 *
	 * @param  Http_Request $http_request
	 * @param  bool         $reinitialize
	 * @return void
	 */
	public function use_http_request( Http_Request $http_request, $reinitialize = true ) {
		$this->http_request = $http_request;

		if ( $reinitialize ) {
			$this->initialize();
		}
	}

	/**
	 * Get the request hash.
	 *
	 * @return string
	 */
	public function get_hash() {
		return $this->hash ?: $this->generate_hash();
	}

	/**
	 * Set the request hash.
	 *
	 * @param  string $hash The hash string.
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function set_hash( $hash ) {
		if ( strlen( $hash ) !== 40 ) {
			throw new \InvalidArgumentException( esc_html__( 'The hash must be have 40 characters of length.', 'awebooking' ) );
		}

		$this->hash = $hash;

		return $this;
	}

	/**
	 * Checks if the request is sane with other request.
	 *
	 * @param  self $another Another request.
	 * @return bool
	 */
	public function same_with( self $another ) {
		return hash_equals( $this->get_hash(), $another->get_hash() );
	}

	/**
	 * Generate the res_request hash.
	 *
	 * @return string
	 */
	public function generate_hash() {
		return sha1( serialize(
			Arr::only( $this->parameters->all(), [ 'lang', 'hotel', 'check_in', 'check_out' ] )
		) );
	}

	/**
	 * Returns the instance number.
	 *
	 * @return int
	 */
	public function get_instance_number() {
		return $this->instance;
	}

	/**
	 * Reset the instance number for the testing purpose.
	 *
	 * @access private
	 */
	public static function reset_instance_number() {
		static::$instances = 0;
	}

	/**
	 * Returns the errors instance.
	 *
	 * @return \WP_Error
	 */
	public function errors() {
		return $this->errors;
	}

	/**
	 * Export the object as array.
	 *
	 * @return array
	 */
	public function to_array() {
		return $this->get_parameters();
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->to_array();
	}

	/**
	 * Whether the given offset exists.
	 *
	 * @param  string $offset The offset name.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return $this->parameters->has( $offset );
	}

	/**
	 * Fetch the offset.
	 *
	 * @param  string $offset The offset name.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->get_parameter( $offset );
	}

	/**
	 * Assign the offset.
	 *
	 * @param  string $offset The offset name.
	 * @param  mixed  $value  The offset value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->set_parameter( $offset, $value );
	}

	/**
	 * Unset the offset.
	 *
	 * @param  mixed $offset The offset name.
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		// ...
	}

	/**
	 * Getter a property.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		return $this->get( $property );
	}

	/**
	 * Check a property exists.
	 *
	 * @param  string $property The property name.
	 * @return bool
	 */
	public function __isset( $property ) {
		return $this->offsetExists( $property );
	}

	/**
	 * Increment instance number when clone object.
	 *
	 * @return void
	 */
	public function __clone() {
		$this->instance   = ++static::$instances;
		$this->parameters = clone $this->parameters;
		$this->errors     = clone $this->errors;
	}
}
