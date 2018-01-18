<?php
namespace AweBooking\Reservation\Source;

use AweBooking\Model\Fee;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

abstract class Source_Abstract implements Source, Jsonable, Arrayable {
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
	 * Get the source surcharge (tax or fee).
	 *
	 * @return \AweBooking\Mode\Fee
	 */
	public function get_surcharge() {
		return $this->surcharge;
	}

	/**
	 * Set the source surcharge.
	 *
	 * @param  Fee $surcharge Surcharge tax or fee.
	 * @return $this
	 */
	public function set_surcharge( Fee $surcharge ) {
		$this->surcharge = $surcharge;

		return $this;
	}

	/**
	 * Get the label.
	 *
	 * @return string
	 */
	public function get_label() {
		return sprintf( '%s', $this->get_name() );
	}

	/**
	 * Determines if current source is Direct source.
	 *
	 * @return boolean
	 */
	public function is_direct() {
		return $this instanceof Direct;
	}

	/**
	 * Determines if current source is Third_Party source.
	 *
	 * @return boolean
	 */
	public function is_third_party() {
		return $this instanceof Third_Party_Source;
	}

	/**
	 * {@inheritdoc}
	 */
	abstract public function toArray();

	/**
	 * {@inheritdoc}
	 */
	public function toJson( $options = 0 ) {
		return json_encode( $this->toArray(), $options );
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
