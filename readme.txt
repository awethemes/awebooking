=== AweBooking - Hotel Booking System ===
Contributors:      awethemes, anhskohbo, ndoublehwp
Donate link:       http://awethemes.com
Tags:              booking, hotel, hotel booking, reservations, reservation, awebooking
Requires at least: 4.6
Tested up to:      5.0
Requires PHP:      5.6
Stable tag:        3.2.6
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Awebooking helps you to setup hotel booking system quickly, pleasantly and easily.

== Description ==

> Extend AweBooking's features with [premium add-ons](https://awethemes.com/awebooking) and high quality [themes](https://awethemes.com/themes) by joining [our Membership program](https://awethemes.com/join).

Awebooking is a well-coded plugin with an excellent user interface, perfect for any hotel, hostel, motel, BnB or any kind of accommodation website. Awebooking brings you easiest way to setup any reservations quickly, pleasantly and easily, rent accommodations with detail services, receive online reservations.

Your customers will be impressed by how easy-to-use, fast and clear to check availability and send a booking request. However, it is not harder to use than any other hotel booking WordPress plugins. Moreover, we believe that it's even much easier! And there's a good reason for that: amount of time and effort that we invested in Awebooking to bring you the best hotel booking Wordpress plugin ever.

You can see [plugin demo here](http://demo.awethemes.com/awebooking/). We also provide WordPress admin demo if you want to take a look.

== Plugin features ==

* Room type and rooms
* Multi locations
* Extra services
* Amentities
* Pricing management
* Room availablity management
* Block dates
* Multiple Rooms Booking
* Booking Management
* Booking Note
* Check available widget
* Email notification
* Minimum/Maximum Nights
* Tax
* Shortcodes
* Multilingual Ready
* Fit With Your Theme
* Developer Friendly
* More features are on the way!

== Premium features ==

* [Online payment](https://awethemes.com/awebooking/addon/online-payment)
* [Booking form builder](https://awethemes.com/awebooking/addon/booking-form-builder)
* [Price breakdown](https://awethemes.com/awebooking/addon/price-breakdown)
* [Enhanced calendar](https://awethemes.com/awebooking/addon/enhanced-calendar)
* [Image gallery](https://awethemes.com/awebooking/addon/image-gallery)
* [iCalendar](https://awethemes.com/awebooking/addon/icalendar)
* [Simple reservation](https://awethemes.com/awebooking/addon/simple-reservation)
* [reCAPTCHA](https://awethemes.com/awebooking/addon/recaptcha)
* [Fast book](https://awethemes.com/awebooking/addon/fast-book)
* [User profile](https://awethemes.com/awebooking/addon/user-profile)
* [MailChimp](https://awethemes.com/awebooking/addon/mailchimp)
* [Fees](https://awethemes.com/awebooking/addon/fees)
* [Rules](https://awethemes.com/awebooking/addon/rules)
* [Elementor integration](https://wordpress.org/plugins/awebooking-elementor-integration/)

You can check [plugin description page here](https://awethemes.com/plugins/awebooking) for detail features.

== Premium themes ==

* [The Chains](https://awethemes.com/themes/the-chains)
* [Awemotel](https://awethemes.com/themes/awemotel)
* [Rosewood](https://awethemes.com/themes/rosewood)


== Screenshots ==

1. Room type list
2. Room type detail settings
3. Services list
4. Availablity rooms management
5. Pricing management
6. Booking list
7. Plugin settings

== Installation ==

* From your WordPress dashboard, Visit `Plugins > Add New`
* Query for `awebooking`
* Activate AweBooking from your Plugins page. (You'll be greeted with a Setup page.)
* Visit `Room type > Add new room type` and create some room types. (You can always delete these later.)

## Video

<iframe width="560" height="315" src="https://www.youtube.com/embed/UqRMIl9ISLw?rel=0" frameborder="0" allowfullscreen></iframe>

== Changelog ==
### [v3.2.6] - [2019-02-11]
- Added: Calendar now pagination 15 room types per page to better performance.
- Fixed: Guests count not work correctly since v3.2
- Minor bug fixes

### [v3.2.5] - [2019-02-04]
- Happy Lunar New Year!
- Fixed: Failed to check reference when booking room in admin area.

### [v3.2.4] - [2019-02-01]
- Minor bug fixes

### [v3.2.3] - [2019-01-30]
- Added: Now you can swap a room unit to another in same room type.
- Added: Auto recalculates room price when editing a booking room.
- Add some back-compat API with v3.1 prevent some fatal errors.

### [v3.2.2] - [2019-01-29]
- Fixed the search rooms did not work when multi-hotel enabled
- and from single room-type.

### [v3.2.1] - [2019-01-24]
- Minor tweak about style to make search form better.
- Fixed fatal error about `Service_List_Table`.

### [v3.2.0] - [2019-01-23]
- Improve frontend style, switch from "rem" unit to "em".
- Intro new "Experiment Form Style", you can enable and testing it from: "Settings > Appearance > Search Form".
- The settings in the "Appearance > Date Picker" now worked but only in "Experiment Form Style"
- Minor bug fixes and improvements.
- API: Switch from "nesbot/carbon" to "cakephp/chronos" for DateTime API.

### [v3.1.21] - [2019-01-10]
- Fixed: Missing `wp_reset_postdata` after call the WP_Query that confusing to Elementor.
- Fixed: Calculate booking costs.

### [v3.1.20] - [2018-12-24]
- Merry Christmas!
- Correct some warning in PHP 7.3
- Fixed: Bookings not showing in "All" tab in the list table.
- Fixed: Wrong price in reservation when timezone is not UTC.

### [v3.1.19] - [2018-12-14]
- WordPress 5.0 with Gutenberg compatibility
- Frontend: Force to showing single month in the datepicker in mobile
- Auto delete "pending" bookings after 30 minutes from time created
- Fixed add new booking in the backend when multi-hotels enabled
- Minor bug fixes

### [v3.1.18] - [2018-12-01]
- Minor bug fixes

### [v3.1.17] - [2018-11-24]
- Fixed: Booking not store the hotel ID
- Fixed: Some search form bugs

### [v3.1.16] - [2018-11-20]
- Imporove admin Calendar
- Minor bug fixes

### [v3.1.15] - [2018-11-05]
- Added: Auto update checked-out status in booking
- Fixed the gateway instruction in email and thankyou page
- Prepare for v3.2
- Minor bug fixes

### [v3.1.13] - [2018-11-01]
- Fixed some issues about reservation.
- Fixed display wrong price in email.
- Minor bug fixes.

### [v3.1.12] - [2018-10-10]
- Added: Validation on checkout.
- Fixed: Bulk update availability calendar not work with `only_days`.
- API Added: New class `AweBooking\Component\Validation\Validator` for the validation.

### [v3.1.11] - [2018-10-04]
- Fixed: Minor issues in the reservation.
- Added: Intro `[awebooking_rooms]` and `[awebooking_single_room]` shortcode.

### [v3.1.10] - [2018-09-26]
- API: Added relationships API.

### [v3.1.9] - [2018-09-19]
- Change: Cancelled email now send to customer instead to admin.
- New style for confirm dialog in admin area.
- Continue improve multiple hotels.
- Minor bug fixes.

### [v3.1.8] - [2018-09-13]
- Added: Show terms on checkout.
- Improve multiple hotels.
- Minor bug fixes.

### [v3.1.7] - [2018-09-01]
- Fixed tax issues.
- Minor bug fixes.

### [v3.1.6] - [2018-08-21]
- Fixed some template issues.
- Fixed send email issue.
- Minor bug fixes.

### [v3.1.5] - [2018-08-07]
- Added: Remove selected room.
- Fixed: Cannot send email when have multiple recipients.
- Fixed: Trim zeros when "Number of Decimals" iz zero.
- Fixed: Database upgrade issues.
- Minor bug fixes.

### [v3.1.4] - [2018-08-02]
- Fixed: Premium addons update not work.
- Minor bug fixes.

### [v3.1.3] - [2018-07-31]
- Added: Widget Check Availability.
- Fixed: The prices include tax not work as expected.
- Fixed: Datepicker not working on mobile devices.
- Minor bug fixes

### [v3.1.2] - [2018-07-25]
- Fixed datepicker not working when Elementor activate.
- Minor bug fixes

### [v3.1.1] - [2018-07-20]
- Minor bug fixes

### [v3.1.0] - [2018-07-19]
- Release v3.1.0
