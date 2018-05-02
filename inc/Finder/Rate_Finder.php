<?php
namespace AweBooking\Finder;

use AweBooking\Support\Period;

class Rate_Finder extends Finder {
	/**
	 * {@inheritdoc}
	 */
	public function find( Period $period ) {
		$response = new Response( $period, $this->resources );

		foreach ( $this->resources as $resource ) {
			// Add match by default, we'll use constraints to perform the rejection.
			$response->add_match( $resource, 'no_reason' );

			// Applly the resource constraints.
			$response->apply_constraints( $resource->get_constraints() );
		}

		// Apply the global finder constraints.
		$response->apply_constraints( $this->constraints );

		return $response;
	}
}
