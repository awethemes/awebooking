const $ = window.jQuery;
const awebooking = window.TheAweBooking;

$(function() {
  'use strict';

  const $dialog = awebooking.Popup.setup('#awebooking-set-price-popup');

  const rangepicker = new awebooking.RangeDatepicker(
    'input[name="datepicker-start"]',
    'input[name="datepicker-end"]'
  );

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
    const calendar = this;
    calendar.room_type = this.$el.closest('.abkngcal-container').find('h2').text();
    calendar.data_id   = this.$el.closest('[data-unit]').data('unit');
    calendar.comments  = showComments(calendar);

    const formTemplate = wp.template('pricing-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));

    $dialog.dialog('open');
  };

  $('.abkngcal--pricing-calendar', document).each(function(index, el) {
    let calendar = new PricingCalendar(el);
    $(el).data('pricing-calendar', calendar);

    calendar.on('apply', onApplyCalendar);
  });

  rangepicker.init();

});
