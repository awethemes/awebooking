webpackJsonp([3],{

/***/ 23:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(24);


/***/ }),

/***/ 24:
/***/ (function(module, exports) {

var $ = window.jQuery;
var awebooking = window.TheAweBooking;

$(function () {
  'use strict';

  var $dialog = awebooking.Popup.setup('#awebooking-set-price-popup');

  var showComments = function showComments(calendar) {
    var nights = calendar.getNights();

    var text = '';
    var toNight = calendar.endDay;

    if (nights === 1) {
      text = 'One night: ' + toNight.format(calendar.format);
    } else {
      text = '<b>' + nights + '</b> nights' + ' nights, from <b>' + calendar.startDay.format(calendar.format) + '</b> to <b>' + toNight.format(calendar.format) + '</b>';
    }

    return text;
  };

  var onApplyCalendar = function onApplyCalendar() {
    var calendar = this;

    calendar.room_type = this.$el.closest('.abkngcal-container').find('h2').text();
    // calendar.unit_name = this.$el.find('.abkngcal__month-heading').text();
    calendar.unit_name = '';
    calendar.data_id = this.$el.closest('[data-unit]').data('unit');
    calendar.comments = showComments(calendar);

    var formTemplate = wp.template('pricing-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));

    calendar.keepRange = true;
    $dialog.dialog('open');
  };

  var createCalendar = function createCalendar(el) {
    var calendar = new PricingCalendar(el);

    calendar.on('apply', onApplyCalendar);

    $dialog.on('dialogclose', function () {
      calendar.keepRange = false;
    });
  };

  $('.abkngcal--pricing-calendar .abkngcal__table').each(function (index, el) {
    if ($(el).hasClass('abkngcal__table--scheduler')) {
      $(el).find('tbody > tr').each(function (i, subel) {
        createCalendar(subel);
      });
    } else {
      createCalendar(el);
    }
  });

  new awebooking.RangeDatepicker('input[name="datepicker-start"]', 'input[name="datepicker-end"]').init();

  new awebooking.ToggleCheckboxes('table.pricing_management');
});

/***/ })

},[23]);