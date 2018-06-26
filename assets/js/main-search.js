(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

(function ($, plugin) {
  'use strict';

  /**
   * Create inline select dates.
   *
   * @param  {HTMLElement} el
   * @return {void}
   */

  function createInlineDates(el) {
    var fp = flatpickr;

    // Handle dates picked.
    var onPickedDates = function onPickedDates(dates, str, instance) {
      var $form = $(this.element).closest('form');

      $form.find('input[name="check-in"]').val(dates[0] ? fp.formatDate(dates[0], instance.config.dateFormat) : '').trigger('change');

      $form.find('input[name="check-out"]').val(dates[1] ? fp.formatDate(dates[1], instance.config.dateFormat) : '').trigger('change');

      if (dates.length === 2) {
        $form.trigger('ready');
      } else {
        $form.trigger('notReady');
      }
    };

    // Create the datepicker.
    var datepicker = plugin.datepicker(el, {
      inline: true,
      static: true,
      altInput: false,
      onChange: onPickedDates
    });

    $('body').on('keydown', function (e) {
      e.keyCode === 27 && datepicker.clear();
    });

    // Handle form state.
    var $form = $(datepicker.element).closest('form');
    $form.find('[type="submit"]').prop('disabled', true);

    $form.on('ready', function () {
      $form.find('[type="submit"]').prop('disabled', false);
    }).on('notReady', function () {
      $form.find('[type="submit"]').prop('disabled', true);
    });
  }

  $(function () {

    // Create inline select dates.
    var $inlineDates = $('#js-inline-dates');
    if ($inlineDates.length) {
      createInlineDates($inlineDates[0]);
    }
  });
})(jQuery, window.awebooking);

},{}]},{},[1]);

//# sourceMappingURL=main-search.js.map
