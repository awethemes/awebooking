<?php
namespace AweBooking\Model\Common;

use AweBooking\Support\Decimal;
use AweBooking\Support\Collection;

class Total {
	/**
	 * [$currency description]
	 *
	 * @var [type]
	 */
	protected $currency;

	/**
	 * [$taxes description]
	 *
	 * @var [type]
	 */
	protected $taxes;

	/**
	 * The subtotal (before taxes, fees, etc.)
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $subtotal;

	/**
	 * The total of taxes.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $total_tax;

	protected $total;

	/**
	 * [__construct description]
	 */
	public function __construct() {
		$this->taxes = new Collection;
	}

	/**
	 * @param double $afterTax
	 */
	public function setAfterTax( $afterTax ) {
		$this->afterTax = $afterTax;
	}

	/**
	 * @return double
	 */
	public function getAfterTax() {
		return $this->afterTax;
	}

	/**
	 * @param double $beforeTax
	 */
	public function setBeforeTax( $beforeTax ) {
		$this->beforeTax = $beforeTax;
	}

	/**
	 * @return double
	 */
	public function getBeforeTax() {
		return $this->beforeTax;
	}

	/**
	 * @param string $currency
	 */
	public function set_currency( $currency ) {
		$this->currency = $currency;
	}

	/**
	 * @return string
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @param \C2is\OTA\Model\Common\Taxes\Taxes $taxes
	 */
	public function setTaxes( Taxes $taxes ) {
		$this->taxes = $taxes;
	}

	/**
	 * @param \C2is\OTA\Model\Common\Taxes\Tax $tax
	 */
	public function addTax( Tax $tax ) {
		$this->taxes->addTax( $tax );
	}

	/**
	 * @return \C2is\OTA\Model\Common\Taxes\Taxes
	 */
	public function getTaxes() {
		return $this->taxes;
	}
}
