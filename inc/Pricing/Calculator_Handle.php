<?php
namespace AweBooking\Pricing;

interface Calculator_Handle {
	/**
	 * Handle calculator the price in pipeline.
	 *
	 * @param  Price $price Current price in pipe.
	 * @return Price
	 */
	public function handle( Price $price );
}
