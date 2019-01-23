<?php

namespace AweBooking\Availability;

use AweBooking\Calendar\Finder\Response;
use AweBooking\Availability\Constraints\Reason;

class Availability {
	/**
	 * The resource model (Room_Type or Rate).
	 *
	 * @var mixed
	 */
	protected $resource;

	/**
	 * The finder response items (rooms or rates).
	 *
	 * @var \AweBooking\Calendar\Finder\Response
	 */
	protected $response;

	/**
	 * Constructor.
	 *
	 * @param mixed                                $resource The resource instance.
	 * @param \AweBooking\Calendar\Finder\Response $response The finder response.
	 */
	public function __construct( $resource, Response $response ) {
		$this->resource = $resource;
		$this->response = $response;
	}

	/**
	 * Gets the resource.
	 *
	 * @return mixed
	 */
	public function get_resource() {
		return $this->resource;
	}

	/**
	 * Get the response.
	 *
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	public function get_response() {
		return $this->response;
	}

	/**
	 * Select first (or last) remain item.
	 *
	 * @param  string $possiton The possiton to select, first or last.
	 * @return mixed|null
	 */
	public function select( $possiton = 'first' ) {
		$remains = $this->remains();

		if ( count( $remains ) === 0 ) {
			return null;
		}

		return ( 'first' === $possiton )
			? $remains->first()['resource']
			: $remains->last()['resource'];
	}

	/**
	 * Determines if a item still remain.
	 *
	 * @param  int $item The item ID.
	 * @return bool
	 */
	public function remain( $item ) {
		return $this->response->remain( $item );
	}

	/**
	 * Returns the remains left.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function remains() {
		return $this->response->get_included()->map( $this->transform_item_callback() );
	}

	/**
	 * Returns the excludes items.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function excludes() {
		return $this->response->get_excluded()->map( $this->transform_item_callback() );
	}

	/**
	 * Returns callback to transform the calendar response.
	 *
	 * @return \Closure
	 */
	protected function transform_item_callback() {
		return function ( $matching ) {
			/* @var \AweBooking\Calendar\Resource\Resource $matching['resource'] */
			if ( ! $reference = $matching['resource']->get_reference() ) {
				throw new \RuntimeException( 'Invalid resource.' );
			}

			// Build the message.
			$message = Reason::get_message( $matching['reason'] );

			if ( isset( $matching['constraint'] ) && method_exists( $matching['constraint'], 'as_string' ) ) {
				$message = $matching['constraint']->as_string();
			}

			return [
				'resource' => $reference,
				'reason'   => $matching['reason'],
				'message'  => $message,
			];
		};
	}
}
