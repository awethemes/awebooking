<?php
namespace AweBooking\Calculator;

use AweBooking\Hotel\Service;
use AweBooking\Pricing\Price;
use AweBooking\Booking\Request;
use AweBooking\Pricing\Calculator_Handle;

class Service_Calculator implements Calculator_Handle {
	/**
	 * Extra-service instance.
	 *
	 * @var Service
	 */
	protected $service;

	/**
	 * Booking request instance.
	 *
	 * @var Request
	 */
	protected $request;

	/**
	 * Base price, use for increase and decrease by percent.
	 *
	 * @var Price
	 */
	protected $base_price;

	/**
	 * Service_Calculator constructor.
	 *
	 * @param Service $service    Extra-service instance.
	 * @param Request $request    Booking request instance.
	 * @param Price   $base_price Base price.
	 */
	public function __construct( Service $service, Request $request, Price $base_price ) {
		$this->service    = $service;
		$this->request    = $request;
		$this->base_price = $base_price;
	}

	/**
	 * Handle calculator the price in pipeline.
	 *
	 * @param  Price $price Current price in pipe.
	 * @return Price
	 */
	public function handle( Price $price ) {
		$service_price = $this->service->get_price();

		switch ( $this->service->get_operation() ) {
			case Service::OP_ADD:
				$price = $price->add( $service_price );
				break;

			case Service::OP_ADD_DAILY:
				$nights = $this->request->get_nights();
				$price  = $price->add( $service_price->multiply( $nights ) );
				break;

			case Service::OP_ADD_PERSON:
				$persons = $this->request->get_people();
				$price   = $price->add( $service_price->multiply( $persons ) );
				break;

			case Service::OP_ADD_PERSON_DAILY:
				$nights  = $this->request->get_nights();
				$persons = $this->request->get_people();

				$price = $price->add(
					$service_price->multiply( $persons )->multiply( $nights )
				);
				break;

			case Service::OP_SUB:
				$price = $price->subtract( $service_price );
				break;

			case Service::OP_SUB_DAILY:
				$nights = $this->request->get_nights();
				$price  = $price->subtract( $service_price->multiply( $nights ) );
				break;

			case Service::OP_INCREASE:
				$percent_price = $this->base_price->multiply( $this->service->get_value() / 100 );
				$price = $price->add( $percent_price );
				break;

			case Service::OP_DECREASE:
				$percent_price = $this->base_price->multiply( $this->service->get_value() / 100 );
				$price = $price->subtract( $percent_price );
				break;
		} // End switch().

		return $price;
	}
}
