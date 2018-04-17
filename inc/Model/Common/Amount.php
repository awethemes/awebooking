<?php
namespace AweBooking\Model\Common;

class Amount {
	const FIXED = 'fixed';
	const PERCENTAGE = 'percentage';

	/**
	 * The type (fixed or percentage).
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The amount.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $amount;

	/**
	 * Constructor.
	 *
	 * @param mixed  $amount The amount.
	 * @param string $type   The amount type, default: 'percentage'.
	 */
	public function __construct( $amount, $type = 'percentage' ) {
		$this->set_type( $type );

		$this->set_amount( $amount );
	}

	/**
	 * Calculate amount by given a total amount.
	 *
	 * @param  \AweBooking\Support\Decimal $total The total amount.
	 * @return \AweBooking\Support\Decimal
	 */
	public function of( $total ) {
		$amount = $this->get_amount();

		if ( static::FIXED === $this->get_type() ) {
			return $amount;
		}

		return Decimal::create( $total )->to_percentage( $amount );
	}

	/**
	 * Get the amount type.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set the amount type.
	 *
	 * @param string $type The amount type.
	 */
	public function set_type( $type ) {
		$this->type = ( static::FIXED === strtolower( $type ) ) ? static::FIXED : static::PERCENTAGE;
	}

	/**
	 * Get the amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Set the new amount.
	 *
	 * @param \AweBooking\Support\Decimal $amount The amount.
	 */
	public function set_amount( $amount ) {
		$this->amount = Decimal::create( $amount );
	}

	/**
	 * Get the label to display.
	 *
	 * @return string
	 */
	public function get_label() {
		$amount = $this->get_amount();

		if ( static::FIXED === $this->get_type() ) {
			return F::money( $amount, true );
		}

		return F::number( $amount->as_string(), true ) . '%';
	}

	/**
	 * {@inheritdoc}
	 */
	public function to_array() {
		return [
			'type'   => $this->get_type(),
			'amount' => $this->get_amount()->as_numeric(),
			'label'  => $this->get_label(),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function as_string() {
		return $this->get_label();
	}
}
