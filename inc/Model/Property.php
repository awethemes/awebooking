<?php
namespace AweBooking\Model;

class Property {
	// Resort
	// Hotel
	// Apartment

	/**
	 * The property name.
	 *
	 * @var string
	 */
	protected $property_name;

	protected $accommodation_type;

	/**
	 * The hotel start, maximium Five stars.
	 *
	 * @var int
	 */
	protected $stars;

	/**
	 * The property address.
	 *
	 * @var string
	 */
	protected $address;

	/**
	 * The property address supplement.
	 *
	 * @var string
	 */
	protected $address_supplement;

	/**
	 * The country name.
	 *
	 * @var string
	 */
	protected $country;

	/**
	 * The city of the country.
	 *
	 * @var string
	 */
	protected $city;

	/**
	 * The ZIP code.
	 *
	 * @var string
	 */
	protected $zipcode;

	/**
	 * The phone number.
	 *
	 * @var string
	 */
	protected $phone;

	/**
	 * The phone number 2.
	 *
	 * @var string
	 */
	protected $phone2;

	/**
	 * The map latitude.
	 *
	 * @var float
	 */
	protected $map_latitude;

	/**
	 * The map longitude.
	 *
	 * @var float
	 */
	protected $map_longitude;

	/**
	 * Check-in start time (H:i).
	 *
	 * @var string
	 */
	protected $checkin_start;

	/**
	 * Check-in end time (H:i).
	 *
	 * @var string
	 */
	protected $checkin_end;

	/**
	 * Check-out start time (H:i).
	 *
	 * @var string
	 */
	protected $checkout_start;

	/**
	 * Check-out end time (H:i)
	 *
	 * @var string
	 */
	protected $checkout_end;

	/**
	 * Is allowed children?
	 *
	 * @var bool
	 */
	protected $children_allowed;

	/**
	 * Is allowed infans?
	 *
	 * @var bool
	 */
	protected $infans_allowed;

	/**
	 * Is pets allowed?
	 *
	 * @var bool
	 */
	protected $pets_allowed;
}
