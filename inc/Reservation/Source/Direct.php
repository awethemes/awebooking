<?php
namespace AweBooking\Reservation\Source;

use AweBooking\Model\Fee;

class Direct extends Source_Abstract {
	/**
	 * Constructor.
	 *
	 * @param string         $uid       The source UID.
	 * @param string         $name      The source name.
	 * @param Surcharge|null $surcharge The surcharge: tax or fee (optional).
	 */
	public function __construct( $uid, $name, Fee $surcharge = null ) {
		$this->uid  = $uid;
		$this->name = $name;
		$this->surcharge = $surcharge;
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
}
