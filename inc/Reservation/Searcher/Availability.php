<?php
namespace AweBooking\Reservation\Searcher;

use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Common\Timespan;
use AweBooking\Calendar\Finder\Response;
use AweBooking\Support\Traits\Fluent_Getter;

class Availability {
	use Fluent_Getter;

	/**
	 * The Room_Type model.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The Timespan model.
	 *
	 * @var \AweBooking\Model\Common\Timespan
	 */
	protected $timespan;

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
	protected $rate_plans;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Common\Timespan    $timespan       The timespan.
	 * @param \AweBooking\Model\Room_Type          $room_type      The room_type.
	 * @param \AweBooking\Calendar\Finder\Response $response_rooms The response_rooms.
	 */
	public function __construct( Timespan $timespan, Room_Type $room_type, Response $response_rooms ) {
		$this->timespan = $timespan;
		$this->room_type = $room_type;
		$this->response_rooms = $response_rooms;
	}

	/**
	 * Gets the timespan.
	 *
	 * @return \AweBooking\Model\Common\Timespan
	 */
	public function get_timespan() {
		return $this->timespan;
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
				'room'           => $matching['resource']->get_reference(),
				'reason'         => $matching['reason'],
				'reason_message' => Reason::get_message( $matching['reason'] ),
			];
		};
	}
}
