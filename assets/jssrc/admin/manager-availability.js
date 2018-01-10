const $ = window.jQuery;
const AweBooking = window.TheAweBooking;

$(function() {

  const $dialog = AweBooking.Popup.setup('#awebooking-set-availability-popup');

  const showComments = function(calendar) {
    const nights = calendar.getNights();
    const toNight = calendar.endDay;

    let text = '';
    if (nights === 1) {
      text = 'One night: ' + toNight.format(calendar.format);
    } else {
      text = `<b>${nights}</b> nights` + ' nights, from <b>' + calendar.startDay.format(calendar.format) + '</b> to <b>' + toNight.format(calendar.format) + '</b>';
    }

    return text;
  };

  const onApplyCalendar = function() {
    let calendar = this;

    calendar.room_name = this.$el.closest('tr').find('span').text();
    calendar.data_id   = this.$el.closest('[data-unit]').data('unit');
    calendar.comments  = showComments(calendar);

    const formTemplate = wp.template('availability-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));

    calendar.keepRange = true;
    $dialog.dialog('open');
  };

  $('.abkngcal--availability-calendar tbody > tr', document).each(function(index, el) {
    let calendar = new window.AweBookingYearlyCalendar(el);
    $(el).data('availability-calendar', calendar);

    calendar.on('apply', onApplyCalendar);

    $dialog.on('dialogclose', function() {
      calendar.keepRange = false;
    });
  });

  (new AweBooking.RangeDatepicker(
    'input[name="datepicker-start"]',
    'input[name="datepicker-end"]'
  )).init();

  new AweBooking.ToggleCheckboxes('.abkngcal--availability-calendar > table');
});
