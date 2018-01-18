<?php
namespace AweBooking\ICalendar\Writer;

use AweBooking\Factory;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Booking;
use AweBooking\Booking\Items\Line_Item;

use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Collection;
use AweBooking\Support\Utils as U;

abstract class Abstract_Writer {
	/**
	 * The room-type to export.
	 *
	 * @var Room_Type
	 */
	protected $room_type;

	/**
	 * Create a writer.
	 *
	 * @param Room_Type $room_type The room-type instance.
	 */
	public function __construct( Room_Type $room_type ) {
		$this->room_type = $room_type;
	}

	/**
	 * Gets booking items of room-type.
	 *
	 * @return array
	 */
	protected function list_booking_items() {
		global $wpdb;

		$units = [];
		$room_units = $this->room_type->get_room_ids();

		if ( ! empty( $room_units ) ) {
			$ids = implode( "', '", array_map( 'esc_sql', $room_units ) );

			$results = $wpdb->get_results("
				SELECT `item`.* FROM `{$wpdb->prefix}awebooking_booking_items` AS `item`
				INNER JOIN `{$wpdb->prefix}awebooking_booking_itemmeta` AS `room_unit` ON (`item`.`booking_item_id` = `room_unit`.`booking_item_id` AND `room_unit`.`meta_key` = '_room_id')
				WHERE `item`.`booking_item_type` = 'line_item' AND `room_unit`.`meta_value` IN ('{$ids}')" // @codingStandardsIgnoreLine
			);

			$units = $results ?: [];
		}

		return Collection::make( $units )
			->map(function( $item ) {
				return new Line_Item( $item->booking_item_id );
			});
	}

	/**
	 * List unavailable events of current room-type.
	 *
	 * @return array
	 */
	protected function list_unavailable_events() {
		$room_units = $this->room_type->get_rooms();

		$period = Period::createFromDay( Carbonate::yesterday() )
			->moveEndDate( '+ 5 YEAR' );

		$unit_events = Factory::create_availability_calendar( $room_units )
			->getEvents( $period->get_start_date(), $period->get_end_date() );

		foreach ( $unit_events as $unit_id => &$events ) {
			$events = U::collect( $events )
				->reject(function( $event ) {
					return $event->getValue() !== Constants::STATE_UNAVAILABLE;
				})->all();
		}

		return array_filter( $unit_events );
	}

	/**
	 * Get the booking summary.
	 *
	 * @param  Booking $booking The booking instance.
	 * @return string
	 */
	protected function get_booking_summary( Booking $booking ) {
		if ( $booking['customer_id'] ) {
			$username = U::optional( get_userdata( $booking['customer_id'] ) )->display_name;
		} elseif ( $booking['customer_company'] ) {
			$username = trim( $booking->get_customer_company() );
		} else {
			$username = esc_html__( 'Guest', 'awebooking-icalendar' );
		}

		// Append the email address.
		if ( $booking['customer_email'] && is_email( $booking['customer_email'] ) ) {
			$username .= sprintf( ' [%1$s]', $booking->get_customer_email() );
		}

		return sprintf( esc_html__( 'Booking #%1$d by %2$s', 'awebooking-icalendar' ),
			esc_attr( $booking->get_id() ),
			esc_html( $username )
		);
	}

	/**
	 * Get the booking summary.
	 *
	 * @param  Booking $booking The booking instance.
	 * @return string
	 */
	protected function get_booking_description( Booking $booking ) {
		return '';
	}
}
