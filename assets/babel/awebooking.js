window.awebooking = {};

const accounting = require('accounting');
const Dropdown = require('./core/dropdown');

(function ($, plugin) {
  'use strict';

  // Main objects
  plugin.utils = plugin.instances = {};

  plugin.utils.dropdown = function (el, config) {
    $(el).each(function () {
      $(this).data('abrs-dropdown', new Dropdown(this, config));
    });
  };

  plugin.i18n = window._awebooking_i18n || {};

  plugin.config = Object.assign( {}, window._awebooking, {
    route: window.location.origin + '?awebooking_route=/',
    ajax_url: window.location.origin + '/wp-admin/admin-ajax.php',
  });

  /**
   * The admin route.
   *
   * @param  {string} route
   * @return {string}
   */
  plugin.route = function (route) {
    return this.config.route + (route || '').replace(/^\//g, '');
  };

  /**
   * Create new datepicker.
   *
   * @see https://flatpickr.js.org/options/
   *
   * @return {flatpickr}
   */
  plugin.datepicker = function (instance, options) {
    const i18n = plugin.i18n;
    const defaults = plugin.config.datepicker;
    const disable = Array.isArray(defaults.disable) ? defaults.disable : [];

    if (Array.isArray(defaults.disableDays)) {
      disable.push(function (date) {
        return defaults.disableDays.indexOf(date.getDay()) !== -1;
      });
    }

    // const minDate = new Date().fp_incr(defaults.min_date);
    // const maxDate = (defaults.max_date && defaults.max_date !== 0) ? new Date().fp_incr(defaults.max_date) : '';

    const fp = flatpickr(instance, Object.assign({}, options, {
      dateFormat: 'Y-m-d',
      ariaDateFormat: i18n.dateFormat,
      minDate: 'today',
      // maxDate: max_date,
      // disable: disable,
      showMonths: defaults.showMonths || 1,
      enableTime: false,
      enableSeconds: false,
      onReady(_, __, fp) {
        fp.calendarContainer.classList.add('awebooking-datepicker');
      }
    }));

    return fp;
  };

  /**
   * Format the price.
   *
   * @param amount
   * @returns {string}
   */
  plugin.formatPrice = function(amount) {
    return accounting.formatMoney(amount, {
      format: plugin.i18n.priceFormat,
      symbol: plugin.i18n.currencySymbol,
      decimal: plugin.i18n.decimalSeparator,
      thousand: plugin.i18n.priceThousandSeparator,
      precision: plugin.i18n.numberDecimals,
    });
  };

  /**
   * Document ready.
   *
   * @return {void}
   */
  $(function () {
    window.tippy('[data-awebooking="tooltip"]', {
      theme: 'awebooking-tooltip'
    });

    $('[data-init="awebooking-dialog"]').each( (e, el) => {
      const dialog = new window.A11yDialog(el);

      dialog.on('show', () => {
        el.classList.add('open');
        el.removeAttribute('aria-hidden');
      });

      dialog.on('hide', () => {
        el.classList.remove('open');
        el.setAttribute('aria-hidden', true);
      });
    });
  });

})(jQuery, window.awebooking);
