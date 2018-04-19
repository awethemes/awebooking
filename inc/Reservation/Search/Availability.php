<?php
namespace AweBooking\Reservation\Search;

use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Reservation\Request;
use AweBooking\Calendar\Finder\Response;
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
	 * The Room_Type model.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The response of rooms.
	 *
	 * @var \AweBooking\Calendar\Finder\Response
	 */
	protected $response_rooms;

	/**
	 * The response of rate plans.
	 *
	 * @var \AweBooking\Calendar\Finder\Response
	 */
	protected $response_plans;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Request      $request The reservation request.
	 * @param \AweBooking\Model\Room_Type          $room_type      The room_type.
	 * @param \AweBooking\Calendar\Finder\Response $response_rooms The response_rooms.
	 * @param \AweBooking\Calendar\Finder\Response $response_plans The response_plans.
	 */
	public function __construct( Request $request, Room_Type $room_type, Response $response_rooms, Response $response_plans ) {
		$this->request        = $request;
		$this->room_type      = $room_type;
		$this->response_rooms = $response_rooms;
		$this->response_plans = $response_plans;
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
	 * Gets the room_type.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Get the response of rooms.
	 *
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	public function get_response_rooms() {
		return $this->response_rooms;
	}

	/**
	 * Get the response of rate plans.
	 *
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	public function get_response_plans() {
		return $this->response_plans;
	}

	/**
	 * Determines if room still remain.
	 *
	 * @param  \AweBooking\Model\Room $room The room_unit instance.
	 * @return bool
	 */
	public function remain( $room ) {
		$room_id = ( $room instanceof Room ) ? $room->get_id() : absint( $room );

		return $this->response_rooms->remain( $room_id );
	}

	/**
	 * Returns the remain rooms left.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function remain_rooms() {
		return abrs_collect( $this->response_rooms->get_included() )
			->transform( $this->transform_callback() );
	}

	/**
	 * Returns the excluded rooms.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function excluded_rooms() {
		return abrs_collect( $this->response_rooms->get_excluded() )
			->transform( $this->transform_callback() );
	}

	/**
	 * Returns callback to transform the calendar response.
	 *
	 * @return \Closure
	 */
	protected function transform_callback() {
		return function ( $matching ) {
			return [
				'room'           => abrs_get_room( $matching['resource']->get_id() ),
				'reason'         => $matching['reason'],
				'reason_message' => Reason::get_message( $matching['reason'] ),
			];
		};
	}
}
