<?php
namespace AweBooking\Deprecated\Model;

use AweBooking\Concierge;
use AweBooking\Booking\Request;
use AweBooking\Pricing\Price_Calculator;
use AweBooking\Model\Service;
use AweBooking\Calculator\Service_Calculator;

trait Room_Type_Deprecated {
	public function get_max_adults() {
		return 0;
	}

	public function get_max_children() {
		return 0;
	}

	public function get_max_infants() {
		return 0;
	}

	public function get_allowed_adults() {
		return $this->get_maximum_occupancy();
	}

	public function get_allowed_children() {
		return $this->get_maximum_occupancy();
	}

	public function get_allowed_infants() {
		return $this->get_maximum_occupancy();
	}

	public function get_buyable_identifier( $options ) {
		return $this->get_id();
	}

	public function get_buyable_price( $options ) {
		$options['room-type'] = $this->get_id();
		$request = Request::from_array( $options->to_array() );

		// Price by nights.
		$price = Concierge::get_room_price( $this, $request );
		$pipes = apply_filters( $this->prefix( 'get_buyable_price' ), [], $this, $request );

		if ( $request->has_request( 'extra_services' ) ) {
			foreach ( $request->get_services() as $service_id => $quantity ) {
				$pipes[] = new Service_Calculator( new Service( $service_id ), $request, $price );
			}
		}

		return (new Price_Calculator( $price ))
			->through( $pipes )
			->process();
	}

	public function is_purchasable( $options ) {
		if ( $this->get_base_price()->is_zero() ) {
			return false;
		}

		try {
			$request = Request::from_array( $options->to_array() );
			$availability = Concierge::check_room_type_availability( $this, $request );

			return $availability->available();
		} catch ( \Exception $e ) {
			//
		}
	}
}
