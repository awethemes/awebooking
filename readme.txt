=== AweBooking - Hotel Booking System ===
Contributors:      awethemes, anhskohbo, ndoublehwp
Donate link:       http://awethemes.com
Tags:              booking, hotel, hotel booking, reservations, reservation, awebooking
Requires at least: 4.6
Tested up to:      4.8.1
Requires PHP:      5.6
Stable tag:        3.0.0-beta10
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Awebooking helps you to setup hotel booking system quickly, pleasantly and easily.

== Description ==

> Get all Awebooking's [premium add-ons](https://awethemes.com/plugins/awebooking#premiumaddons) and a lot of [extras](https://awethemes.com/themes) by joining [our new Membership program](https://awethemes.com/join).

Awebooking is a well-coded plugin with an excellent user interface, perfect for any hotel, hostel, motel, BnB or any kind of accommodation website. Awebooking brings you easiest way to setup any reservations quickly, pleasantly and easily, rent accommodations with detail services, receive online reservations.

Your customers will be impressed by how easy-to-use, fast and clear to check availability and send a booking request. However, it is not harder to use than any other hotel booking WordPress plugins. Moreover, we believe that it's even much easier! And there's a good reason for that: amount of time and effort that we invested in Awebooking to bring you the best hotel booking Wordpress plugin ever.

You can see [plugin demo here](http://demo.awethemes.com/awebooking/). We also provide WordPress admin demo if you want to take a look.

== Plugin features ==

* Room type and rooms
* Multi locations
* Extra services
* Pricing management
* Room availablity management
* By request booking
* Check available widget
* Email notification
* Shortcodes

You can check [plugin description page here](https://awethemes.com/plugins/awebooking) for detail features.

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
* Search for `awebooking`
* Activate AweBooking from your Plugins page. (You'll be greeted with a Setup page.)
* Visit `Room type > Add new room type` and create some room types. (You can always delete these later.)

== Upgrade Notice ==

The newly-updated AweBooking v3.0-beta4 is **major update**, in which we refactor code API.
It requires PHP 5.6 or higher, changes the way booking item do, etc...

If you use previous version with our premium please do not update to **v3.0-beta4**.
We'll release new premiums shortly and guide how to upgrade in to this.

If you have 2.x version, please do not upgrade to this version.

## Video

<iframe width="560" height="315" src="https://www.youtube.com/embed/XNqn4gEakQA" frameborder="0" allowfullscreen></iframe>

== Changelog ==
### [3.0.0-beta10] - [2017-31-10]
#### Fixed
- Minor bugs fixed

### [3.0.0-beta9] - [2017-19-10]
#### Added
- Added Spanish (Argentina) language, thanks @bicho44.
- Added missing "processing" email template, fixed [#43](https://github.com/awethemes/awebooking/issues/43)
- API: Added logger API.
- API: Added `AweBooking\Support\Utils` class.

#### Changes
- Rename "awebooking/after_register_post_type" to "awebooking/register_post_type"
- API: Class `Period_Collection` now extends the `Collection`, rename `Period_Collection::merge()` to `Period_Collection::collapse()`.
- API: Improve `AweBooking\Support\Abstract_Calendar` API.

#### Fixed
- Fixed issues when create new tables in some server.
- Fixed some strings missing to translate.
- Minor bugs fixed

### [3.0.0-beta8] - 2017-10-05
#### Added
- Initial "cart", allow booking multi-rooms in session time.
- API: Added `Period_Collection::adjacents()` alias of `Period_Collection::is_continuous` method.

#### Changes
- Bump Skeleton to version 1.0.0
- API: Main `AweBooking` now extends `Illuminate\Container\Container`, the `Skeleton\Container\Container` has been removed by Skeleton 1.0.0.
- API: Split WP_Object to standalone package "awethemes/wp-object".
- API: Moving `AweBooking\Model\WP_Object` to `AweBooking\Support\WP_Object`, a old class name still available until next version for compatibility.
- API: Use "awethemes/session" package instead "ericmann/wp-session-manager", `awebooking( 'session' )` now implements of `Awethemes\WP_Session\WP_Session` instead old `WP_Session`.
- API: Code refactoring the Price, rename `to_amount()` to `to_integer()` and `from_amount()` to `from_integer` method, use "bcmath" for calculator if available, unit-tests...
- Templates changes.

#### Fixed
- Fixed "parser error" with :class in old PHP version.
- Minor bugs fixed

### [3.0.0-beta7] - 2017-09-22
#### Changes
- Improve multilanguage support

#### Fixed
- Fixed check availability bugs with multilanguage
- Fixed some issues about email templates
- Fixed loading wrong textdomain directory
- Minor bugs fixed

### [3.0.0-beta6] - 2017-09-14
#### Added
- Added SSP currency.
- Change price after update item in booking.
- API: Added `AWEBOOKING_PLUGIN_FILE_PATH` and `AWEBOOKING_VERSION` constants.

#### Changes
- Refactor code API in admin
- Bootstrap file changes

#### Fixed
- Fixed booking cost not updated after add/change or remove a booking item
- Minor bugs fixed

### [3.0.0-beta5] - 2017-09-06
#### Fixed
- Hotfix: Fixed wrong place `deactivate_plugins` is called
- Hotfix: Fixed crash on admin wizard [@see](https://wordpress.org/support/topic/error-after-activation-15/)
- Fixed missing the localisation files

### [3.0.0-beta4] - 2017-09-01
#### Changes
- Refactor code API, a huge classes has changed
- A room when book will be stored as **booking-item**, prepare for multi booking rooms feature.
- Improve stability.

#### Fixed
- Fixed the session cannot stored sometimes.
- Fixed room-units with multilang.
- Minor bugs fixed

### [3.0.0-beta3] - 2017-08-10
#### Changes
- Improve calendar locale

#### Fixed
- Fixed "Bulk Update" not work on multi language
- Fixed awebooking setup page not show
- Minor bugs fixed

### [3.0.0-beta2] - 2017-08-07
#### Changes
- Booking action now worked
- Polylang support

#### Fixed
- Fixed extra service percent calculator
- Fixed hotel location cannot be deleted
- Minor bugs fixed

### [3.0.0-beta] - 2017-07-20
- Release AweBooking first beta version.

### [3.0.0-alpha-307] - 2017-07-18
#### Changes
- Continue working with multilingual support.

### [3.0.0-alpha-306] - 2017-07-18
#### Added
- WPML compatibility.

### [3.0.0-alpha-305] - 2017-07-13
#### Changes
- Template changes.

#### Fixed
- Fixed email not work.
- Fixed can't set "Available" state in Rooms Management.
- After delete a room-type, AweBooking data related to this room-type will be removed too.
- After delete a booking, room state now restore to "available" and booking event will be clear.

### [3.0.0-alpha-301] - 2017-07-09
#### Fixed
- Fixed the migration notice always show.

### [3.0.0-alpha-300] - 2017-07-09
#### Added
- Added tool allow migration from awebooking v2.

### [3.0.0-alpha-230] - 2017-07-07
#### Added
- Add more core hooks.

#### Changes
- Update latest Skeleton v0.3.
- Widget now using Skeleton Widget.
- Some template changes.

#### Fixed
- Fixed start-day not clear in Calendar.
- Fixed check availability in single room not working.
- Fixed memory and queries issues.
- Minor bugs fixed.
