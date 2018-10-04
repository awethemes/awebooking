<?php

use AweBooking\Constants;
use AweBooking\Model\Service;

/**
 * Retrieves the service object.
 *
 * @param  mixed $service The post object or post ID of the service.
 * @return \AweBooking\Model\Service|false|null
 */
function abrs_get_service( $service ) {
	return abrs_rescue( function() use ( $service ) {
		$service = new Service( $service );

		return $service->exists() ? $service : null;
	}, false );
}

/**
 * Gets all services.
 *
 * @param  array $args Optional, the WP_Query args.
 * @return \AweBooking\Support\Collection
 */
function abrs_list_services( $args = [] ) {
	$args = wp_parse_args( $args, apply_filters( 'abrs_query_services_args', [
		'post_type'      => Constants::HOTEL_SERVICE,
		'post_status'    => 'publish',
		'posts_per_page' => 500, // Limit max 500.
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
	]));

	$transform = [
		'include' => 'post__in',
		'exclude' => 'post__not_in',
	];

	foreach ( $transform as $key => $key1 ) {
		if ( isset( $args[ $key ] ) ) {
			$args[ $key1 ] = $args[ $key ];
			unset( $args[ $key ] );
		}
	}

	$wp_query = new WP_Query( $args );

	return abrs_collect( $wp_query->posts )
		->map_into( Service::class );
}

/**
 * Get all service operations.
 *
 * @return array
 */
function abrs_get_service_operations() {
	return apply_filters( 'abrs_get_service_operations', [
		'add'       => esc_html__( 'Add to price', 'awebooking' ),
		'add_daily' => esc_html__( 'Add to price per night', 'awebooking' ),
		'increase'  => esc_html__( 'Increase price by % amount of room prices', 'awebooking' ),
	]);
}

/**
 * Gets the services with price for selection.
 *
 * @param array $query_args The query arguments.
 * @param array $includes   The services with price included.
 * @param array $context    The context to calculate price, see abrs_calc_service_price().
 * @return \AweBooking\Support\Collection
 */
function abrs_services_for_reservation( array $query_args, array $includes, array $context ) {
	$services = abrs_list_services( $query_args ); // TODO:...

	$includes = abrs_collect( $includes )->transform( function( $s ) {
		return $s instanceof Service ? $s->get_id() : (int) $s;
	})->all();

	return $services->transform( function( $service ) use ( $context, $includes ) {
		$included = in_array( $service->get_id(), $includes );

		return [
			'service'  => $service,
			'included' => $included,
			'price'    => $included ? 0 : abrs_calc_service_price( $service, $context ),
		];
	});
}

/**
 * Calculate the service price based on the context.
 *
 * @param  Service|int $service The service ID.
 * @param  array       $context The context data.
 * @return float
 */
function abrs_calc_service_price( $service, array $context ) {
	$context = wp_parse_args( $context, [
		'nights'     => 0,
		'base_price' => 0,
	]);

	if ( ! $service instanceof Service ) {
		$service = abrs_get_service( $service );
	}

	// By default price same with amount.
	$amount = $service->get( 'amount' );

	// Calculate cost on specials case.
	switch ( $service->get( 'operation' ) ) {
		case 'add_daily':
			$amount = $amount * absint( $context['nights'] );
			break;

		case 'increase':
			$amount = abrs_decimal( $context['base_price'] )->to_percentage( $amount )->as_numeric();
			break;
	}

	return apply_filters( 'abrs_calc_service_price', $amount, $service, $context );
}
