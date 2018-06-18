<?php
namespace AweBooking\Gateway;

use AweBooking\Plugin;
use AweBooking\Support\Manager;
use AweBooking\Support\Collection;

class Gateways extends Manager {
	/**
	 * Core gateways will be registers.
	 *
	 * @var array
	 */
	protected $core_gateways = [
		\AweBooking\Gateway\Direct_Payment_Gateway::class,
		\AweBooking\Gateway\BACS_Gateway::class,
	];

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->drivers = new Collection;

		parent::__construct( $plugin );
	}

	/**
	 * Init the mailer (trigger in `init`).
	 *
	 * @return void
	 */
	public function init() {
		$gateways = $this->get_sorted_gateways();

		foreach ( $gateways as $gateway ) {
			$this->register( is_string( $gateway ) ? $this->plugin->make( $gateway ) : $gateway );
		}

		do_action( 'abrs_setup_gateways', $this );
	}

	/**
	 * Returns the list gateways for register.
	 *
	 * @return array
	 */
	protected function get_sorted_gateways() {
		$ordering = (array) $this->plugin->get_option( 'list_gateway_order', [] );

		// Filters the payment gateways classmap.
		$load_gateways = apply_filters( 'abrs_payment_gateways', $this->core_gateways );

		$sorted = [];
		$bottom = 1001;

		foreach ( $load_gateways as $gateway ) {
			$gateway = is_string( $gateway ) ? $this->plugin->make( $gateway ) : $gateway;

			// Found the possiton in $ordering.
			$index = array_search( $gateway->get_method(), $ordering );

			// Found in $ordering, just add by that position,
			// otherwise we will add to end of the $sorted.
			if ( false !== $index ) {
				$sorted[ $index ] = $gateway;
			} else {
				$sorted[ $bottom ] = $gateway;
				$bottom++;
			}
		}

		// Sort by index.
		ksort( $sorted );

		return array_values( $sorted );
	}

	/**
	 * Get a gateway (enable only).
	 *
	 * @param  string $gateway The gateway ID.
	 * @return \AweBooking\Gateway\Gateway|null
	 */
	public function get( $gateway ) {
		return $this->enabled()->get( $gateway );
	}

	/**
	 * Returns gateways enable only.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function enabled() {
		return $this->all()->filter( function( $gateway ) {
			return $gateway->is_enabled();
		});
	}

	/**
	 * Add a new reservation gateway.
	 *
	 * @param  \AweBooking\Gateway\Gateway $gateway The gateway implementation.
	 * @return bool
	 *
	 * @throws \InvalidArgumentException
	 */
	public function register( $gateway ) {
		if ( ! $gateway instanceof Gateway ) {
			throw new \InvalidArgumentException( 'Gateway must be a class instance of AweBooking\Gateway\Gateway.' );
		}

		$key = $gateway->get_method();
		if ( empty( $key ) || $this->registered( $key ) ) {
			return false;
		}

		$gateway->setup();
		$this->drivers[ $key ] = $gateway;

		return true;
	}
}
