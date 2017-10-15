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

    calendar.room_name = this.$el.closest('.abkngcal-container').find('h2').text();
    calendar.data_id   = this.$el.closest('[data-room]').data('room');
    calendar.comments  = showComments(calendar);

    const formTemplate = wp.template('availability-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));

    $dialog.find('form').data('calendar', calendar);
    $dialog.dialog('open');
  };

  const ajaxGetCalendar = function(calendar) {
    const $container = calendar.$el.parents('.abkngcal-container');
    $container.find('.abkngcal-ajax-loading').show();

    wp.ajax.post( 'get_awebooking_yearly_calendar', {
      year: $container.find('.abkngcal select').val(),
      room: $container.data('room'),
    })
    .done(function(data) {
      calendar.destroy();
      calendar.$el.find('select').off();

      $container.html($(data.html).html());
      calendar.$el = $container.find('.abkngcal.abkngcal--yearly');

      calendar.initialize();
      calendar.on('apply', onApplyCalendar);
      calendar.$el.find('select').on('change', ajaxGetCalendar.bind(null, calendar));
    });
  };

  $('.abkngcal.abkngcal--yearly', document).each(function(index, el) {
    let calendar = new window.AweBookingYearlyCalendar(el);
    $(el).data('availability-calendar', calendar);

    calendar.on('apply', onApplyCalendar);
    calendar.$el.find('select').on('change', ajaxGetCalendar.bind(null, calendar));
  });

  $dialog.find('form').on('submit', function(e) {
    e.preventDefault();

    const $el = $(this);
    const calendar = $el.data('calendar');

    $el.addClass('ajax-loading');
    AweBooking.ajaxSubmit(this, 'set_awebooking_availability')
      .always(function() {
        $el.removeClass('ajax-loading');
      })
      .done(function(data) {
        ajaxGetCalendar(calendar);
        $dialog.dialog('close');
      })
      .fail(function(e) {
        if (e.error && typeof e.error === 'string') {
          alert(e.error);
        }
      });
  });

  (new AweBooking.RangeDatepicker(
    'input[name="datepicker-start"]',
    'input[name="datepicker-end"]'
  )).init();

  (new AweBooking.ToggleCheckboxes(
    'table.awebooking_page_manager-awebooking'
  ));
});
