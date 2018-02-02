<?php
namespace AweBooking\Model;

use AweBooking\Constants;
use Roomify\Bat\Unit\UnitInterface;
use AweBooking\Booking\BAT\Unit_Trait;

class Tax extends WP_Object implements UnitInterface {
	use Unit_Trait;

	/**
	 * Type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'awebooking_taxes';

	/**
	 * WordPress type for object.
	 *
	 * @var string
	 */
	protected $wp_type = 'awebooking_taxes';

	/**
	 * This object does not support metadata.
	 *
	 * @var false
	 */
	protected $meta_type = false;

	/**
	 * The attributes for this object.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'        => '',
		'code'        => '',
		'type'        => 'tax',
		'category'    => 'exclusive',
		'amount_type' => Constants::TAX_AMOUNT_PERCENTAGE,
		'amount'      => 0,
		'created_date' => null,
		'modified_date' => null,
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'amount'      => 'float',
	];

	public function __construct( $id = 0, $type = null ) {
		parent::__construct( $id );

		if ( ! is_null( $type ) ) {
			$this->type = $type;
		}
	}

	/**
	 * Return an array of IDs for for a specific tax / fee code.
	 * Can return multiple to check for existence.
	 *
	 * @param string $code code.
	 * @param string $exclude exclude.
	 *
	 * @return array Array of IDs.
	 */
	public static function get_by_code( $code, $exclude = 0 ) {
		global $wpdb;

		$ids = wp_cache_get( 'awebooking/tax/get_by_code/' . $code, 'awebooking_tax' );

		if ( false === $ids ) {
			$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}awebooking_tax_rates WHERE code = %s ORDER BY created_date DESC;", $code ) );

			if ( $ids ) {
				wp_cache_get( 'awebooking/tax/get_by_code/' . $code, 'awebooking_tax' );
			}
		}

		$ids = array_diff( array_filter( array_map( 'absint', (array) $ids ) ), array( $exclude ) );
		$id = apply_filters( 'awebooking/get_tax_id_from_code', absint( current( $ids ) ), $code, $exclude );

		return new static( $id );
	}

	/**
	 * The tax rates query.
	 *
	 * @param  array $args args.
	 * @return WP_Query
	 */
	public static function query() {
		global $wpdb;

		$query = "SELECT * FROM {$wpdb->prefix}awebooking_tax_rates AS taxes ORDER BY created_date DESC";

		return $wpdb->get_results( $query );
	}

	/**
	 * Get the tax name.
	 *
	 * @return string
	 */
	public function get_name() {
		return apply_filters( $this->prefix( 'get_name' ), $this['name'], $this );
	}

	/**
	 * Get the tax code.
	 *
	 * @return string
	 */
	public function get_code() {
		return apply_filters( $this->prefix( 'get_code' ), $this['code'], $this );
	}

	/**
	 * Get the tax type.
	 *
	 * @return string
	 */
	public function get_type() {
		return apply_filters( $this->prefix( 'get_type' ), $this['type'], $this );
	}

	/**
	 * Get the tax type.
	 *
	 * @return string
	 */
	public function get_category() {
		return apply_filters( $this->prefix( 'get_category' ), $this['category'], $this );
	}

	/**
	 * Get the tax amount type.
	 *
	 * @return string
	 */
	public function get_amount_type() {
		return apply_filters( $this->prefix( 'get_amount_type' ), $this['amount_type'], $this );
	}

	/**
	 * Get the tax amount.
	 *
	 * @return string
	 */
	public function get_amount() {
		return apply_filters( $this->prefix( 'get_amount' ), $this['amount'], $this );
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['name']          = $this->instance['name'];
		$this['code']          = $this->instance['code'];
		$this['type']          = $this->instance['type'];
		$this['category']      = $this->instance['category'];
		$this['amount_type']   = $this->instance['amount_type'];
		$this['amount']        = $this->instance['amount'];
		$this['created_date']  = $this->instance['created_date'];
		$this['modified_date'] = $this->instance['modified_date'];
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {
		wp_cache_delete( $this->get_id(), Constants::CACHE_RAW_TAX_RATE );
	}

	/**
	 * Run perform insert object into database.
	 *
	 * @return int|void
	 */
	protected function perform_insert() {
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . 'awebooking_tax_rates',
			$this->only( 'name', 'code', 'type', 'category', 'amount_type', 'amount' ),
			[ '%s', '%s', '%s', '%s', '%s', '%d' ]
		);

		return absint( $wpdb->insert_id );
	}

	/**
	 * Run perform update object.
	 *
	 * @param  array $dirty The attributes has been modified.
	 * @return bool|void
	 */
	protected function perform_update( array $dirty ) {
		global $wpdb;

		$updated = $wpdb->update( $wpdb->prefix . 'awebooking_tax_rates',
			$this->only( 'name', 'code', 'type', 'category', 'amount_type', 'amount' ),
			[ 'id' => $this->get_id() ]
		);

		return false !== $updated;
	}

	/**
	 * Perform delete object.
	 *
	 * @param  bool $force Force delete or not.
	 * @return bool
	 */
	protected function perform_delete( $force ) {
		global $wpdb;

		$deleted = $wpdb->delete( $wpdb->prefix . 'awebooking_tax_rates', [ 'id' => $this->get_id() ], '%d' );

		return false !== $deleted;
	}

	/**
	 * Setup WP Core Object based on ID and object-type.
	 *
	 * @return void
	 */
	protected function setup_instance() {
		global $wpdb;

		// Try get in the cache.
		$the_tax_rate = wp_cache_get( $this->get_id(), Constants::CACHE_RAW_TAX_RATE );

		if ( false === $the_tax_rate ) {
			// Get the room in database.
			$the_tax_rate = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_tax_rates` WHERE `id` = %d LIMIT 1", $this->get_id() ),
				ARRAY_A
			);

			// Do nothing if not found the room.
			if ( is_null( $the_tax_rate ) ) {
				return;
			}

			wp_cache_add( (int) $the_tax_rate['id'], $the_tax_rate, Constants::CACHE_RAW_TAX_RATE );
		}

		$this->set_instance( $the_tax_rate );
	}
}
