webpackJsonp([4],{

/***/ 25:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(26);


/***/ }),

/***/ 26:
/***/ (function(module, exports) {

var $ = window.jQuery;
var AweBooking = window.TheAweBooking;

$(function () {

  var $dialog = AweBooking.Popup.setup('#awebooking-set-availability-popup');

  var showComments = function showComments(calendar) {
    var nights = calendar.getNights();
    var toNight = calendar.endDay;

    var text = '';
    if (nights === 1) {
      text = 'One night: ' + toNight.format(calendar.format);
    } else {
      text = '<b>' + nights + '</b> nights' + ' nights, from <b>' + calendar.startDay.format(calendar.format) + '</b> to <b>' + toNight.format(calendar.format) + '</b>';
    }

    return text;
  };

  var onApplyCalendar = function onApplyCalendar() {
    var calendar = this;

    calendar.room_name = this.$el.closest('tr').find('span').text();
    calendar.data_id = this.$el.closest('[data-unit]').data('unit');
    calendar.comments = showComments(calendar);

    var formTemplate = wp.template('availability-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));

    calendar.keepRange = true;
    $dialog.dialog('open');
  };

  $('.abkngcal--availability-calendar tbody > tr', document).each(function (index, el) {
    var calendar = new window.AweBookingYearlyCalendar(el);
    $(el).data('availability-calendar', calendar);

    calendar.on('apply', onApplyCalendar);

    $dialog.on('dialogclose', function () {
      calendar.keepRange = false;
    });
  });

  new AweBooking.RangeDatepicker('input[name="datepicker-start"]', 'input[name="datepicker-end"]').init();

  new AweBooking.ToggleCheckboxes('.abkngcal--availability-calendar > table');
});

/***/ })

},[25]);