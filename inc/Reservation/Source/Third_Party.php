<?php
namespace AweBooking\Reservation\Source;

use AweBooking\Model\Fee;
use AweBooking\Model\Commission;

class Third_Party_Source extends Source_Abstract implements Have_Commission {
	/* Constants */
	const OTA = 'OTA';
	const WHOLESALER = 'WHOLESALER';
	const TRAVEL_AGENT = 'TRAVEL_AGENT';
	const CORPORATE_CLIENT = 'CORPORATE_CLIENT';

	/**
	 * The commission for the source.
	 *
	 * @var Commission
	 */
	protected $commission;

	/**
	 * Constructor.
	 *
	 * @param string          $uid        The source UID.
	 * @param string          $name       The source name.
	 * @param Fee|null        $surcharge  The surcharge: tax or fee (optional).
	 * @param Commission|null $commission The commission (optional).
	 */
	public function __construct( $uid, $name, Fee $surcharge = null, Commission $commission = null ) {
		$this->uid  = $uid;
		$this->name = $name;

		$this->surcharge  = $surcharge;
		$this->commission = $commission;
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
