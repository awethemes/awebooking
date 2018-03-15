<?php
namespace AweBooking\Model\Common;

class Tax {
	/**
	 * @SerializedName("Percent")
	 * @XmlAttribute
	 * @Type("string")
	 * @var string
	 */
	private $percent;

	/**
	 * @SerializedName("Amount")
	 * @XmlAttribute
	 * @Type("double")
	 * @var double
	 */
	private $amount;

	/**
	 * @SerializedName("Type")
	 * @XmlAttribute
	 * @Type("string")
	 * @var string
	 */
	private $type = 'Inclusive';

	/**
	 * @XmlList(inline=true, entry="TaxDescription")
	 * @Type("array<C2is\OTA\Model\Common\Taxes\TaxDescription>")
	 * @var array
	 */
	private $description;

	/**
	 * @param string $percent
	 */
	public function setPercent($percent)
	{
		$this->percent = $percent;
	}

	/**
	 * @return string
	 */
	public function getPercent()
	{
		return $this->percent;
	}

	/**
	 * @param double $amount
	 */
	public function setAmount($amount)
	{
		$this->amount = $amount;

		return $this;
	}

	/**
	 * @return double
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @param array $tax
	 */
	public function setTax($tax)
	{
		$this->tax = $tax;

		return $this;
	}

	/**
	 * @param Tax $tax
	 */
	public function addTax(Tax $tax)
	{
		$this->tax[] = $tax;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getTax()
	{
		return $this->tax;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param array $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @param TaxDescription $description
	 */
	public function addDescription(TaxDescription $description)
	{
		$this->description[] = $description;
	}

	/**
	 * @return array
	 */
	public function getDescription()
	{
		return $this->description;
	}
}
