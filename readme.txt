=== AweBooking - Hotel Booking System ===
Contributors:      awethemes, anhskohbo, ndoublehwp
Donate link:       http://awethemes.com
Tags:              booking, hotel, hotel booking, reservations, reservation, awebooking
Requires at least: 4.6
Tested up to:      4.8.1
Requires PHP:      5.6
Stable tag:        3.0.0-beta4
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Awebooking helps you to setup hotel booking system quickly, pleasantly and easily.

== Description ==

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
