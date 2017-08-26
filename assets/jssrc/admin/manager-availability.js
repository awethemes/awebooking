const $ = window.jQuery;
const awebooking = window.TheAweBooking;

$(function() {

  const rangepicker = new awebooking.RangeDatepicker(
    'input[name="datepicker-start"]',
    'input[name="datepicker-end"]'
  );

  rangepicker.init();

  const showComments = function() {
    var $text = this.$el.parent().find('.datepicker-container').find('.write-here');
    var nights = this.endDay.diff(this.startDay, 'days');
    var text = '';

    var thatNight = this.endDay.clone().subtract(1, 'day');

    if (nights === 1) {
      text = 'One night: ' + thatNight.format(this.format);
    } else {
      text = nights + ' nights, from  ' + this.startDay.format(this.format) + ' to ' + thatNight.format(this.format) + ' night.';
    }

    $text.html(text);

    this.$el.parent().find('input[name="state"]').prop('disabled', false);
    this.$el.parent().find('button').prop('disabled', false);
  };

  const ajaxGetCalendar = function(calendar) {
    var $container = calendar.$el.parents('.abkngcal-container');
    $container.find('.abkngcal-ajax-loading').show();

    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'get_awebooking_yearly_calendar',
        year: $container.find('.abkngcal select').val(),
        room: $container.data('room'),
      }
    })
    .done(function(response) {
      calendar.destroy();
      calendar.$el.find('select').off();
      calendar.$el.parent().find('button').off();

      $container.html($(response).html());
      calendar.$el = $container.find('.abkngcal.abkngcal--yearly');

      calendar.initialize();
      calendar.on('apply', showComments);
      calendar.$el.parent().find('button').on('click', ajaxSaveState.bind(calendar));
      calendar.$el.find('select').on('change', function() {
        ajaxGetCalendar(calendar);
      });

    });
  };

  const ajaxSaveState = function(e) {
    e.preventDefault();
    var calendar = this;
    var $container = this.$el.parents('.abkngcal-container');

    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'set_awebooking_availability',
        start: this.startDay.format(this.format),
        end: this.endDay.format(this.format),
        room_id: $container.data('room'),
        state: this.$el.parent().find('input[name="state"]:checked').val(),
      },
    })
    .done(function() {
      ajaxGetCalendar(calendar);
    });
  };

  $('.abkngcal.abkngcal--yearly', document).each(function(index, el) {
    let calendar = new window.AweBookingYearlyCalendar(el);
    let $button = calendar.$el.parent().find('button');

    calendar.on('apply', showComments);
    $button.on('click', ajaxSaveState.bind(calendar));

    calendar.$el.find('select').on('change', function() {
      ajaxGetCalendar(calendar);
    });

    $button.prop('disabled', true);
  });

});
