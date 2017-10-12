const $ = window.jQuery;
const awebooking = window.TheAweBooking;

$(function() {
  'use strict';

  const $dialog = awebooking.Popup.setup('#awebooking-set-price-popup');

  const showComments = function(calendar) {
    var nights = calendar.getNights();

    var text = '';
    var toNight = calendar.endDay;

    if (nights === 1) {
      text = 'One night: ' + toNight.format(calendar.format);
    } else {
      text = `<b>${nights}</b> nights` + ' nights, from <b>' + calendar.startDay.format(calendar.format) + '</b> to <b>' + toNight.format(calendar.format) + '</b>';
    }

    return text;
  };

  const onApplyCalendar = function() {
    let calendar = this;

    calendar.room_type = this.$el.closest('.abkngcal-container').find('h2').text();
    calendar.unit_name = this.$el.find('.abkngcal__month-heading').text();
    calendar.data_id   = this.$el.closest('[data-unit]').data('unit');
    calendar.comments  = showComments(calendar);

    const formTemplate = wp.template('pricing-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));

    calendar.keepRange = true;
    $dialog.dialog('open');
  };

  $('.abkngcal--pricing-calendar > tbody > tr', document).each(function(index, el) {
    let calendar = new PricingCalendar(el);

    calendar.on('apply', onApplyCalendar);

    $dialog.on('dialogclose', function() {
      calendar.keepRange = false;
    });
  });

  const rangepicker = new awebooking.RangeDatepicker('input[name="datepicker-start"]',  'input[name="datepicker-end"]');
  rangepicker.init();
});
