<?php
namespace AweBooking\Reservation\Search;

use AweBooking\Model\Room_Type;
use AweBooking\Reservation\Request;
use AweBooking\Calendar\Finder\Response;
use AweBooking\Calendar\Finder\Constraint;
use AweBooking\Support\Traits\Fluent_Getter;

class Availability {
	use Fluent_Getter;

	/**
	 * The request instance.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $request;

	/**
	 * The resource model (Room_Type or Rate_Plan).
	 *
	 * @var mixed
	 */
	protected $resource;

	/**
	 * The finder response items (rooms or rate plans).
	 *
	 * @var \AweBooking\Calendar\Finder\Response
	 */
	protected $response;

	/**
	 * Constructor.
	 *
	 * @param mixed                                $resource The resource instance.
	 * @param \AweBooking\Reservation\Request      $request  The reservation request.
	 * @param \AweBooking\Calendar\Finder\Response $response The finder response.
	 */
	public function __construct( $resource, Request $request, Response $response ) {
		$this->resource = $resource;
		$this->request  = $request;
		$this->response = $response;
	}

	/**
	 * Get back the reservation request.
	 *
	 * @return \AweBooking\Reservation\Request
	 */
	public function get_request() {
		return $this->request;
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
			return;
		}

		return ( 'first' === $possiton )
			? $remains->first()['item']
			: $remains->last()['item'];
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
		return abrs_collect( $this->response->get_included() )
			->transform( $this->transform_item_callback() );
	}

	/**
	 * Returns the excludes items.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function excludes() {
		return abrs_collect( $this->response->get_excluded() )
			->transform( $this->transform_item_callback() );
	}

	/**
	 * Returns callback to transform the calendar response.
	 *
	 * @return \Closure
	 */
	protected function transform_item_callback() {
		return function ( $matching ) {
			if ( ! $reference = $matching['resource']->get_reference() ) {
				throw new \RuntimeException( 'Invalid resource.' );
			}

			// Build the message.
			$message = Reason::get_message( $matching['reason'] );

			if ( isset( $matching['constraint'] )
				&& $matching['constraint'] instanceof Constraint
				&& method_exists( $matching['constraint'], 'as_string' ) ) {
				$message = $matching['constraint']->as_string();
			}

			return [
				'item'    => $reference,
				'reason'  => $matching['reason'],
				'message' => $message,
			];
		};
	}
}
