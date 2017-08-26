const $ = window.jQuery;
const awebooking = window.TheAweBooking;

$(function() {

  const rangepicker = new awebooking.RangeDatepicker(
    'input[name="datepicker-start"]',
    'input[name="datepicker-end"]'
  );

  rangepicker.init();
  const $dialog = awebooking.Popup.setup('#awebooking-set-price-popup');

  const onApplyCalendar = function() {
    const calendar = this;
    calendar.data_id = this.$el.closest('[data-unit]').data('unit');

    const formTemplate = wp.template('pricing-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));
    $dialog.dialog('open');
  };

  $('.abkngcal--pricing-calendar', document).each(function(index, el) {
    let calendar = new PricingCalendar(el);
    calendar.on('apply', onApplyCalendar);
  });

});
