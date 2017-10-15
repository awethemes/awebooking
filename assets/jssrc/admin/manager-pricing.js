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
    // calendar.unit_name = this.$el.find('.abkngcal__month-heading').text();
    calendar.unit_name = '';
    calendar.data_id   = this.$el.closest('[data-unit]').data('unit');
    calendar.comments  = showComments(calendar);

    const formTemplate = wp.template('pricing-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));

    calendar.keepRange = true;
    $dialog.dialog('open');
  };

  const createCalendar = function(el) {
    let calendar = new PricingCalendar(el);

    calendar.on('apply', onApplyCalendar);

    $dialog.on('dialogclose', function() {
      calendar.keepRange = false;
    });
  }

  $('.abkngcal--pricing-calendar .abkngcal__table').each(function(index, el) {
    if ($(el).hasClass('abkngcal__table--scheduler')) {
      $(el).find('tbody > tr').each(function(i, subel) {
        createCalendar(subel);
      });
    } else {
      createCalendar(el);
    }
  });

  (new awebooking.RangeDatepicker(
    'input[name="datepicker-start"]',  'input[name="datepicker-end"]'
  )).init();

  (new awebooking.ToggleCheckboxes(
    'table.pricing_management'
  ));
});
