webpackJsonp([3],{

/***/ 25:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(26);


/***/ }),

/***/ 26:
/***/ (function(module, exports) {

(function ($) {
  'use strict';

  $(function () {
    var awebooking = window.TheAweBooking;

    // Create the scheduler.
    var scheduler = new awebooking.ScheduleCalendar({
      el: '.scheduler'
    });

    var selectCalendar = null;
    var enableSelectCalendar = function enableSelectCalendar() {
      awebooking.Flatpickr($('.booking-dates__picker.checkin > input')[0], {
        altInput: false,
        altFormat: 'F j, Y',
        dateFormat: 'Y-m-d',
        plugins: [new awebooking.FlatpickrRange({ input: $('.booking-dates__picker.checkout > input')[0] })]
      });
    };

    var compileHtmlControls = function compileHtmlControls(action) {
      var template = wp.template('scheduler-pricing-controls');
      var data = scheduler.model.toJSON();

      data.action = action;
      $('#js-scheduler-form-controls').html(template(data));

      enableSelectCalendar();
    };

    // Setup popup
    var $popup = awebooking.Popup.setup($('#scheduler-form-dialog')[0]);
    $popup.dialog('close');

    var $schedulerForm = $('#scheduler-form');

    scheduler.on('clear', function () {
      window.swal && swal.close();

      $popup.dialog('close');
    });

    scheduler.on('action:set-price', function (e, model) {
      window.swal && swal.close();

      compileHtmlControls();

      $popup.dialog('open');
    });

    scheduler.on('action:reset-price', function (e, model) {
      awebooking.confirm(function () {
        compileHtmlControls('reset_price');

        $schedulerForm.submit();

        model.clear();
      });
    });
  });
})(jQuery);

/***/ })

},[25]);