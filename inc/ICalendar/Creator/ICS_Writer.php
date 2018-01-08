<?php
namespace AweBooking\ICalendar\Writer;

use AweBooking\Model\Room_Type;
use AweBooking\Model\Booking;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Support\Carbonate;
use Roomify\Bat\Event\Event;
use Sabre\VObject\Component\VCalendar;

class ICS_Writer extends Abstract_Writer implements Writer {
	/**
	 * Create the Calendar.
	 *
	 * @return VCalendar
	 */
	public function create() {
		$vcalendar = $this->create_vcalendar();

		$this->list_booking_items()
			->each(function( $item ) use ( $vcalendar ) {
				$this->create_event( $vcalendar, $item );
			});

		foreach ( $this->list_unavailable_events() as $unit_id => $events ) {
			array_walk( $events, function( $event ) use ( $vcalendar, $unit_id ) {
				$this->create_blocked_event( $vcalendar, $event, $unit_id );
			});
		}

		return $vcalendar->serialize();
	}

	/**
	 * Create the VCalendar object.
	 *
	 * @return VCalendar
	 */
	protected function create_vcalendar() {
		return new VCalendar([
			'PRODID'         => '-//AweThemes//AweBooking ' . awebooking()->version() . '//EN',
			'X-WR-CALNAME'   => $this->sanitize_string( get_option( 'blogname' ) ),
			'X-WR-CALDESC'   => $this->sanitize_string( sprintf( esc_html__( 'Bookings from %s', 'awebooking-icalendar' ), $this->room_type->get_title() ) ),
			'X-ORIGINAL-URL' => $this->sanitize_string( home_url( '/' ) ),
		]);
	}

	/**
	 * Create event in the calendar.
	 *
	 * @param  VCalendar $vcalendar VCalendar instance.
	 * @param  Line_Item $item      The booking line item.
	 * @return $this
	 */
	protected function create_event( VCalendar $vcalendar, Line_Item $item ) {
		$booking = $item->get_booking();

		// Prevent create no-exists booking item.
		if ( ! $booking->exists() || $item->get_nights_stayed() < 1 ) {
			return;
		}

		// Get period of the booking item.
		$booking_period = $item->get_period();

		/* @see https://tools.ietf.org/html/rfc5545#section-3.8.1.11 */
		$vstatus = 'CONFIRMED';
		if ( Booking::CANCELLED === $booking->get_state_status() ) {
			$vstatus = 'CANCELLED';
		}

		// Attach event into vcalendar.
		$vcalendar->add('VEVENT', [
			'UID'           => 'awebooking-item-' . $item->get_id(),
			'STATUS'        => $this->sanitize_string( $vstatus ),
			'SUMMARY'       => $this->sanitize_string( $this->get_booking_summary( $booking ) ),
			'DESCRIPTION'   => $this->sanitize_string( $this->get_booking_description( $booking ) ),
			'DTSTART'       => $booking_period->get_start_date()->hour( 14 )->minute( 0 ),
			'DTEND'         => $booking_period->get_end_date()->hour( 12 )->minute( 0 ),
			'CREATED'       => $booking->get_booking_date(),
			'LAST-MODIFIED' => $booking->get_date_modified(),
		]);

		return $this;
	}

	/**
	 * Create blocked event in the calendar.
	 *
	 * @param  VCalendar $vcalendar VCalendar instance.
	 * @param  Event     $event     Bat event.
	 * @param  int       $unit_id   The room-unit ID.
	 * @return $this
	 */
	protected function create_blocked_event( VCalendar $vcalendar, Event $event, $unit_id ) {
		$vcalendar->add('VEVENT', [
			'UID'           => 'awebooking-blocked-' . $unit_id,
			'STATUS'        => 'CONFIRMED',
			'SUMMARY'       => esc_html__( 'Blocked Date', 'awebooking-icalendar' ),
			'DESCRIPTION'   => esc_html__( 'Blocked Date', 'awebooking-icalendar' ),
			'DTSTART'       => Carbonate::create_date( $event->getStartDate() )->hour( 14 )->minute( 0 ),
			'DTEND'         => Carbonate::create_date( $event->getEndDate() )->addDay()->hour( 12 )->minute( 0 ),
		]);

		return $this;
	}

	/**
	 * Sanitize strings for .ics
	 *
	 * @param  string $string String to sanitize.
	 * @return string
	 */
	protected function sanitize_string( $string ) {
		$string = preg_replace( '/([\,;])/', '\\\$1', $string );
		$string = str_replace( "\n", '\n', $string );

		return sanitize_text_field( $string );
	}
}
