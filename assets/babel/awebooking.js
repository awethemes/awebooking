(function($) {
  'use strict';

  const _defaults = require('lodash.defaults');

  // Polyfill location.origin in IE, @see https://stackoverflow.com/a/25495161
  if (! window.location.origin) {
    window.location.origin = window.location.protocol + "//" + window.location.hostname  + (window.location.port ? ':' + window.location.port : '');
  }

  /**
   * The awebooking main object.
   *
   * @type {Object}
   */
  window.awebooking = {};

  // Alias of awebooking.
  const self = awebooking;

  // Sub objects
  awebooking.utils = {};
  awebooking.instances = {};

  awebooking.utils.flatpickrRangePlugin = require('./core/flatpickr-range-plugin.js');

  /**
   * Configure.
   *
   * @type {Object}
   */
  awebooking.config = _defaults(window._awebooking, {
    route:    window.location.origin + '?awebooking_route=/',
    ajax_url: window.location.origin + '/wp-admin/admin-ajax.php',
    i18n: {
      date_format: 'F j, Y',
      time_format: 'H:i:s',
    }
  });

  /**
   * The admin route.
   *
   * @param  {string} route
   * @return {string}
   */
  awebooking.route = function(route) {
    return this.config.route + (route || '').replace(/^\//g, '');
  };

  /**
   * Create new datepicker.
   *
   * @see https://flatpickr.js.org/options/
   *
   * @return {flatpickr}
   */
  awebooking.datepicker = function(instance, options) {
    const i18n = self.config.i18n;
    const defaults = self.config.datepicker;
    const disable = Array.isArray(defaults.disable) ? defaults.disable : [];

    if (Array.isArray(defaults.disable_days)) {
      disable.push(function(date) {
        return defaults.disable_days.indexOf(date.getDay()) !== -1;
      });
    }

    const min_date = new Date().fp_incr(defaults.min_date);
    const max_date = (defaults.max_date && defaults.max_date !== 0) ? new Date().fp_incr(defaults.max_date) : '';

    const fp = flatpickr(instance, _defaults(options, {
      mode: 'range',
      dateFormat: 'Y-m-d',
      ariaDateFormat: i18n.date_format,
      minDate: 'today',
      // maxDate: max_date,
      // disable: disable,
      showMonths: defaults.show_months,
      enableTime: false,
      enableSeconds: false,
      onReady (_, __, fp) {
        fp.calendarContainer.classList.add('awebooking-datepicker');
      }
    }));

    return fp;
  };

  /**
   * Document ready.
   *
   * @return {void}
   */
  $(function() {

    $('.searchbox').each( () => {
      const $el = $(this);

      const $checkin = $el.find('input[name="check-in"]');
      const $checkout = $el.find('input[name="check-out"]');
      const $rangepicker = $el.find('[data-hotel="rangepicker"]');

      const fp = awebooking.datepicker($rangepicker[0], {
        // inline: true,
        // clickOpens: false,
        onChange: (dates, str, fp) => {
          const dateFormat = fp.config.dateFormat;

          $checkin.val('');
          $checkout.val('');

          if (dates[0]) {
            $checkin.val(fp.formatDate(dates[0], dateFormat)).trigger('change');
          }

          if (dates[1]) {
            $checkout.val(fp.formatDate(dates[1], dateFormat)).trigger('change');
          }
        }
      });

      $checkin.on('click focus', (e) => {
        e.preventDefault();

        fp.isOpen = false;
        fp.open(undefined, $checkin[0]);
      });

      $checkout.on('click focus', (e) => {
        e.preventDefault();

        fp.isOpen = false;
        fp.open(undefined, $checkout[0]);
      });

      console.log(fp);
    });

  });

})(jQuery);
