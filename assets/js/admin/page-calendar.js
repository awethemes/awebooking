"use strict";

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(function ($) {
  'use strict';

  var plugin = window.awebooking || {};
  var DATE_FORMAT = 'YYYY-MM-DD';

  var BookingScheduler =
  /*#__PURE__*/
  function () {
    /**
     * Constructor.
     *
     * @return {void}
     */
    function BookingScheduler() {
      _classCallCheck(this, BookingScheduler);

      this.flatpickr = null;
      this.scheduler = new ScheduleCalendar({
        el: '.scheduler',
        debug: plugin.debug,
        granularity: 'nightly'
      });
      this.scheduler.on('clear', this.handleClearSelected.bind(this));
    }
    /**
     * Handle on clear selected.
     *
     * @return {void}
     */


    _createClass(BookingScheduler, [{
      key: "handleClearSelected",
      value: function handleClearSelected() {
        window.swal && swal.close();
      }
    }]);

    return BookingScheduler;
  }();
  /**
   * Document ready!
   *
   * @return {void}
   */


  $(function () {
    new BookingScheduler();
  });
})(jQuery);
//# sourceMappingURL=page-calendar.js.map
