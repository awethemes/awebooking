<?php
namespace AweBooking\Model;

use AweBooking\Model\Tax;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class Source implements Jsonable, Arrayable {
	/* Constants */
	const DIRECT = 'direct';
	const THIRD_PARTY = 'third_party';

	const OTA = 'ota';
	const WHOLESALER = 'wholesaler';
	const TRAVEL_AGENT = 'travel_agent';
	const CORPORATE_CLIENT = 'corporate_client';

	/**
	 * The source UID.
	 *
	 * @var string
	 */
	protected $uid;

	/**
	 * The source name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The source surcharge.
	 *
	 * @var Surcharge|null
	 */
	protected $surcharge;

	/**
	 * The commission for the third_party source.
	 *
	 * @var Commission
	 */
	protected $commission;

	/**
	 * Constructor.
	 *
	 * @param string         $uid       The source UID.
	 * @param string         $name      The source name.
	 * @param Surcharge|null $surcharge The surcharge: tax or fee (optional).
	 */
	public function __construct( $uid, $name, Tax $surcharge = null ) {
		$this->uid  = $uid;
		$this->name = $name;
		$this->surcharge = $surcharge;
	}

	/**
	 * Get the source unique ID.
	 *
	 * @return string
	 */
	public function get_uid() {
		return $this->uid;
	}

	/**
	 * Set the source unique ID.
	 *
	 * @param  string $uid The source unique ID.
	 * @return $this
	 */
	public function set_uid( $uid ) {
		$this->uid = $uid;

		return $this;
	}

	/**
	 * Get the source name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the source name.
	 *
	 * @param  string $name The source display name.
	 * @return $this
	 */
	public function set_name( $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Determines if this source have surcharge.
	 *
	 * @return boolean
	 */
	public function has_surcharge() {
		return ! is_null( $this->surcharge );
	}

	/**
	 * Get the source surcharge (tax or fee).
	 *
	 * @return \AweBooking\Mode\Tax|null
	 */
	public function get_surcharge() {
		return $this->surcharge;
	}

	/**
	 * Set the source surcharge.
	 *
	 * @param  Tax $surcharge Surcharge tax or fee.
	 * @return $this
	 */
	public function set_surcharge( Tax $surcharge ) {
		$this->surcharge = $surcharge;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_commission() {
		return $this->commission;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_commission( Commission $commission ) {
		$this->commission = $commission;

		return $this;
	}

	/**
	 * Get the source label.
	 *
	 * @return string
	 */
	public function get_label() {
		return sprintf( '%s', $this->get_name() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function toJson( $options = 0 ) {
		return json_encode( $this->toArray(), $options );
	}

	/**
	 * {@inheritdoc}
	 */
	public function toArray() {
		return [
			'uid'   => $this->get_uid(),
			'name'  => $this->get_name(),
			'label' => $this->get_label(),
		];
	}

	/**
	 * Magic isset method.
	 *
	 * @param  string $property The property name.
	 * @return bool
	 */
	public function __isset( $property ) {
		return null !== $this->__get( $property );
	}

	/**
	 * Magic getter method.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		if ( method_exists( $this, $method = "get_{$property}" ) ) {
			return $this->{$method}();
		}
	}
}
