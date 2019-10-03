import $ from 'jquery';
import accounting from 'accounting';
import Dropdown from './core/dropdown';
import Utils from './core/util';
import { formatDateString } from './utils/date-utils';

let plugin = window.awebooking = {};

// Main objects
plugin.utils = {};
plugin.instances = {};
plugin.i18n = window._awebooking_i18n || {};

plugin.config = Object.assign({}, {
  route: window.location.origin + '?awebooking_route=/',
  ajax_url: window.location.origin + '/wp-admin/admin-ajax.php',
}, window._awebooking);

plugin.utils.dropdown = function(el, config) {
  $(el).each(function() {
    $(this).data('abrs-dropdown', new Dropdown(this, config));
  });
};

plugin.utils.dates = {
  format: formatDateString,
};

/**
 * The admin route.
 *
 * @param  {string} route
 * @return {string}
 */
plugin.route = function(route) {
  return this.config.route + (route || '').replace(/^\//g, '');
};

/**
 * Create new datepicker.
 *
 * @see https://flatpickr.js.org/options/
 *
 * @return {flatpickr}
 */
plugin.datepicker = function(instance, options) {
  const i18n = plugin.i18n;
  const defaults = plugin.config.datepicker;

  let { disableDays, disableDates } = defaults;
  const disable = !Array.isArray(disableDates) ? disableDates.split(/,\s?/) : disableDates;

  if (Array.isArray(disableDays) && disableDays.length > 0) {
    disable.push(function(date) {
      return disableDays.includes(date.getDay());
    });
  }

  let minDate = new Date;
  let maxDate = null;

  // Limit available days from today
  if (Date.prototype.fp_incr && defaults.minDate > 0) {
    minDate = minDate.fp_incr(defaults.minDate || 0);
  }

  // TODO: Disable "maxDate", this doesn't work as maxNights as expected.
  if (Date.prototype.fp_incr && defaults.maxNights > 0) {
    // maxDate = minDate.fp_incr(defaults.maxNights)
  }

  const _defaults = {
    dateFormat: 'Y-m-d',
    ariaDateFormat: i18n.dateFormat,
    minDate: minDate,
    maxDate: maxDate,
    disable: disable,
    showMonths: defaults.showMonths || 1,
    enableTime: false,
    enableSeconds: false,
    disableMobile: false,
    onReady(_, __, fp) {
      fp.calendarContainer.classList.add('awebooking-datepicker');
    },
  };

  if (Utils.isMobile()) {
    _defaults.showMonths = 1;
  }

  return flatpickr(instance, $.extend({}, _defaults, options));
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
$(function() {
  tippy('[data-awebooking="tooltip"]', {
    theme: 'awebooking-tooltip',
  });

  $('[data-init="awebooking-dialog"]').each((e, el) => {
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
