(function($, plugin) {
  'use strict';

  /**
   * Create inline select dates.
   *
   * @param  {HTMLElement} el
   * @return {void}
   */
  function createInlineDates(el) {
    const fp = flatpickr;

    // Handle dates picked.
    const onPickedDates = function(dates, str, instance) {
      const $form = $(this.element).closest('form');

      $form.find('input[name="check-in"]').val(
        dates[0] ? fp.formatDate(dates[0], instance.config.dateFormat) : ''
      ).trigger('change');

      $form.find('input[name="check-out"]').val(
        dates[1] ? fp.formatDate(dates[1], instance.config.dateFormat) : ''
      ).trigger('change');

      if (dates.length === 2) {
        $form.trigger('ready');
      } else {
        $form.trigger('notReady');
      }
    };

    // Create the datepicker.
    const datepicker = plugin.datepicker(el, {
      inline: true,
      static: true,
      altInput: false,
      onChange: onPickedDates,
    });

    $('body').on('keydown', (e) => {
      (e.keyCode === 27 && datepicker.clear());
    });

    // Handle form state.
    const $form = $(datepicker.element).closest('form');
    $form.find('[type="submit"]').prop('disabled', true);

    $form.on('ready', () => {
      $form.find('[type="submit"]').prop('disabled', false);
    }).on('notReady', () => {
      $form.find('[type="submit"]').prop('disabled', true);
    });
  }

  $(function() {

    // Create inline select dates.
    const $inlineDates = $('#js-inline-dates');
    if ($inlineDates.length) {
      createInlineDates($inlineDates[0]);
    }

  });

})(jQuery, window.awebooking);
