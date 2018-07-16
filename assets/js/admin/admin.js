(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var debounce = require('debounce');
var queryString = require('query-string');

(function ($) {
  'use strict';

  debounce(function () {});

  var awebooking = window.awebooking || {};

  // Create the properties.
  awebooking.utils = {};
  awebooking.instances = {};

  awebooking.utils.flatpickrRangePlugin = require('../core/range-dates.js');

  /**
   * The admin route.
   *
   * @param  {string} route
   * @return {string}
   */
  awebooking.route = function (route) {
    return this.admin_route + route.replace(/^\//g, '');
  };

  /**
   * Show the alert dialog.
   *
   * @return {SweetAlert}
   */
  awebooking.alert = function (message) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'error';

    return swal({
      text: message,
      type: type,
      toast: true,
      buttonsStyling: false,
      showCancelButton: false,
      showConfirmButton: true,
      confirmButtonClass: 'button'
    });
  };

  /**
   * Show the confirm message.
   *
   * @return {SweetAlert}
   */
  awebooking.confirm = function (message, callback) {
    if (!window.swal) {
      return window.confirm(message || this.i18n.warning) && callback();
    }

    var confirm = window.swal({
      toast: true,
      text: message || this.i18n.warning,
      type: 'warning',
      position: 'center',
      reverseButtons: true,
      buttonsStyling: false,
      showCancelButton: true,
      cancelButtonClass: 'button',
      confirmButtonClass: 'button button-primary',
      cancelButtonText: this.i18n.cancel,
      confirmButtonText: this.i18n.ok
    });

    if (callback) {
      return confirm.then(function (result) {
        if (result.value) callback(result);
      });
    }

    return confirm;
  };

  /**
   * Create the dialog.
   *
   * @param  {string} selector
   * @return {Object}
   */
  awebooking.dialog = function (selector) {
    var $dialog = $(selector).dialog({
      modal: true,
      width: 'auto',
      height: 'auto',
      autoOpen: false,
      draggable: false,
      resizable: false,
      closeOnEscape: true,
      dialogClass: 'wp-dialog awebooking-dialog',
      position: { my: 'center', at: 'center center-15%', of: window }
    });

    $(window).resize(debounce(function () {
      $dialog.dialog('option', 'position', { my: 'center', at: 'center center-15%', of: window });
    }, 150));

    return $dialog;
  };

  /**
   * Send a ajax request to a route.
   *
   * @param  {String}   route
   * @param  {Object}   data
   * @param  {Function} callback
   * @return {Object}
   */
  awebooking.ajax = function (method, route, data, callback) {
    return $.ajax({
      url: awebooking.route(route),
      data: data,
      method: method,
      dataType: 'json'
    }).done(function (data) {
      if (callback) callback(data);
    }).fail(function (xhr) {
      var json = xhr.responseJSON;

      if (json && json.message) {
        awebooking.alert(json.message, 'error');
      } else {
        awebooking.alert(awebooking.i18n.error, 'error');
      }
    });
  };

  /**
   * Create a form then append to body.
   *
   * @param  {String} link   The form action.
   * @param  {String} method The form method.
   * @return {Object}
   */
  awebooking.createForm = function (action, method) {
    var $form = $('<form>', { 'method': 'POST', 'action': action });

    var hiddenInput = $('<input>', { 'name': '_method', 'type': 'hidden', 'value': method });

    return $form.append(hiddenInput).appendTo('body');
  };

  /**
   * Format the price.
   *
   * @param amount
   * @returns {string}
   */
  awebooking.formatPrice = function (amount) {
    return require('accounting').formatMoney(amount, {
      format: awebooking.i18n.priceFormat,
      symbol: awebooking.i18n.currencySymbol,
      decimal: awebooking.i18n.decimalSeparator,
      thousand: awebooking.i18n.priceThousandSeparator,
      precision: awebooking.i18n.numberDecimals
    });
  };

  /**
   * Retrieves a modified URL query string.
   *
   * @param {object} args
   * @param {string} url
   */
  awebooking.utils.addQueryArgs = function (args, url) {
    if (typeof url === 'undefined') {
      url = window.location.href;
    }

    var parsed = queryString.parseUrl(url);
    var query = $.extend({}, parsed.query, args);

    return parsed.url + '?' + queryString.stringify(query, { sort: false });
  };

  $(function () {
    // Init tippy.
    if (window.tippy) {
      window.tippy('.tippy', {
        arrow: true,
        animation: 'shift-toward',
        duration: [200, 150]
      });
    }

    // Init the selectize.
    if ($.fn.selectize) {
      require('./utils/search-customer.js')();

      $('select.selectize, .with-selectize .cmb2_select').selectize({
        allowEmptyOption: true,
        searchField: ['value', 'text']
      });
    }

    // Init warning before delete.
    $('[data-method="abrs-delete"]').on('click', function (e) {
      e.preventDefault();

      var link = $(this).attr('href');
      var message = $(this).data('warning');

      awebooking.confirm(message, function () {
        awebooking.createForm(link, 'DELETE').submit();
      });
    });
  });

  module.exports = function () {};
})(jQuery);

},{"../core/range-dates.js":3,"./utils/search-customer.js":2,"accounting":4,"debounce":5,"query-string":7}],2:[function(require,module,exports){
'use strict';

var $ = jQuery;
var plugin = window.awebooking;

var ajaxSearch = function ajaxSearch() {
  var type = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'customers';
  var query = arguments[1];
  var callback = arguments[2];

  $.ajax({
    type: 'GET',
    url: plugin.route('/search/' + type),
    data: { term: encodeURIComponent(query) },
    error: function error() {
      callback();
    },
    success: function success(res) {
      callback(res);
    }
  });
};

var initSelectize = function initSelectize(select) {
  $(select).selectize({
    valueField: 'id',
    labelField: 'display',
    searchField: 'display',
    dropdownParent: 'body',
    placeholder: $(this).data('placeholder'),
    load: function load(query, callback) {
      if (!query.length) {
        return callback();
      } else {
        ajaxSearch('customers', query, callback);
      }
    }
  });
};

var initSelectizeServices = function initSelectizeServices(select) {
  $(select).selectize({
    plugins: ['remove_button', 'drag_drop'],
    valueField: 'id',
    labelField: 'name',
    searchField: ['name', 'id'],
    dropdownParent: 'body',
    placeholder: $(this).data('placeholder'),
    load: function load(query, callback) {
      if (!query.length) {
        return callback();
      } else {
        ajaxSearch('services', query, callback);
      }
    }
  });
};

module.exports = function () {
  $('select.awebooking-search-customer, .selectize-search-customer .cmb2_select').each(function () {
    initSelectize(this);
  });

  $('.selectize-search-services').each(function () {
    initSelectizeServices(this);
  });
};

},{}],3:[function(require,module,exports){
'use strict';

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

module.exports = function rangePlugin() {
  var config = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

  return function (fp) {
    var dateFormat = '',
        secondInput = void 0,
        _firstInputFocused = void 0,
        _secondInputFocused = void 0;

    var createSecondInput = function createSecondInput() {
      if (config.input) {
        secondInput = config.input instanceof Element ? config.input : window.document.querySelector(config.input);
      } else {
        secondInput = fp._input.cloneNode();
        secondInput.removeAttribute('id');
        secondInput._flatpickr = undefined;
      }

      if (secondInput.value) {
        var parsedDate = fp.parseDate(secondInput.value);

        if (parsedDate) {
          fp.selectedDates.push(parsedDate);
        }
      }

      secondInput.setAttribute('data-fp-omit', '');

      fp._bind(secondInput, ['focus', 'click'], function () {
        if (fp.selectedDates[1]) {
          fp.latestSelectedDateObj = fp.selectedDates[1];
          fp._setHoursFromDate(fp.selectedDates[1]);
          fp.jumpToDate(fp.selectedDates[1]);
        }

        _firstInputFocused = false;
        _secondInputFocused = true;


        fp.isOpen = false;
        fp.open(undefined, secondInput);
      });

      fp._bind(fp._input, ['focus', 'click'], function (e) {
        e.preventDefault();
        fp.isOpen = false;
        fp.open();
      });

      if (fp.config.allowInput) {
        fp._bind(secondInput, 'keydown', function (e) {
          if (e.key === 'Enter') {
            fp.setDate([fp.selectedDates[0], secondInput.value], true, dateFormat);
            secondInput.click();
          }
        });
      }

      if (!config.input) {
        fp._input.parentNode && fp._input.parentNode.insertBefore(secondInput, fp._input.nextSibling);
      }
    };

    var plugin = {
      onParseConfig: function onParseConfig() {
        fp.config.mode = 'range';
        dateFormat = fp.config.altInput ? fp.config.altFormat : fp.config.dateFormat;
      },
      onReady: function onReady() {
        createSecondInput();
        fp.config.ignoredFocusElements.push(secondInput);

        if (fp.config.allowInput) {
          fp._input.removeAttribute('readonly');
          secondInput.removeAttribute('readonly');
        } else {
          secondInput.setAttribute('readonly', 'readonly');
        }

        fp._bind(fp._input, 'focus', function () {
          fp.latestSelectedDateObj = fp.selectedDates[0];
          fp._setHoursFromDate(fp.selectedDates[0]);

          // fp.jumpToDate(fp.selectedDates[0]);
          _firstInputFocused = true;
          _secondInputFocused = false;
        });

        if (fp.config.allowInput) {
          fp._bind(fp._input, 'keydown', function (e) {
            if (e.key === 'Enter') {
              fp.setDate([fp._input.value, fp.selectedDates[1]], true, dateFormat);
            }
          });
        }

        fp.setDate(fp.selectedDates, false);
        plugin.onValueUpdate(fp.selectedDates);
      },
      onPreCalendarPosition: function onPreCalendarPosition() {
        if (_secondInputFocused) {
          fp._positionElement = secondInput;
          setTimeout(function () {
            fp._positionElement = fp._input;
          }, 0);
        }
      },
      onValueUpdate: function onValueUpdate() {
        if (!secondInput) {
          return;
        }

        var _fp$selectedDates$map = fp.selectedDates.map(function (d) {
          return fp.formatDate(d, dateFormat);
        });

        var _fp$selectedDates$map2 = _slicedToArray(_fp$selectedDates$map, 2);

        var _fp$selectedDates$map3 = _fp$selectedDates$map2[0];
        fp._input.value = _fp$selectedDates$map3 === undefined ? '' : _fp$selectedDates$map3;
        var _fp$selectedDates$map4 = _fp$selectedDates$map2[1];
        secondInput.value = _fp$selectedDates$map4 === undefined ? '' : _fp$selectedDates$map4;
      },
      onChange: function onChange() {
        if (!fp.selectedDates.length) {
          setTimeout(function () {
            if (fp.selectedDates.length) {
              return;
            }

            secondInput.value = '';
          }, 10);
        }

        if (_secondInputFocused) {
          setTimeout(function () {
            secondInput.focus();
          }, 0);
        }
      },
      onDestroy: function onDestroy() {
        if (!config.input) {
          secondInput.parentNode && secondInput.parentNode.removeChild(secondInput);
        }
      }
    };

    return plugin;
  };
};

},{}],4:[function(require,module,exports){
"use strict";

/*!
 * accounting.js v0.4.1
 * Copyright 2014 Open Exchange Rates
 *
 * Freely distributable under the MIT license.
 * Portions of accounting.js are inspired or borrowed from underscore.js
 *
 * Full details and documentation:
 * http://openexchangerates.github.io/accounting.js/
 */

(function (root, undefined) {

	/* --- Setup --- */

	// Create the local library object, to be exported or referenced globally later
	var lib = {};

	// Current version
	lib.version = '0.4.1';

	/* --- Exposed settings --- */

	// The library's settings configuration object. Contains default parameters for
	// currency and number formatting
	lib.settings = {
		currency: {
			symbol: "$", // default currency symbol is '$'
			format: "%s%v", // controls output: %s = symbol, %v = value (can be object, see docs)
			decimal: ".", // decimal point separator
			thousand: ",", // thousands separator
			precision: 2, // decimal places
			grouping: 3 // digit grouping (not implemented yet)
		},
		number: {
			precision: 0, // default precision on numbers is 0
			grouping: 3, // digit grouping (not implemented yet)
			thousand: ",",
			decimal: "."
		}
	};

	/* --- Internal Helper Methods --- */

	// Store reference to possibly-available ECMAScript 5 methods for later
	var nativeMap = Array.prototype.map,
	    nativeIsArray = Array.isArray,
	    toString = Object.prototype.toString;

	/**
  * Tests whether supplied parameter is a string
  * from underscore.js
  */
	function isString(obj) {
		return !!(obj === '' || obj && obj.charCodeAt && obj.substr);
	}

	/**
  * Tests whether supplied parameter is a string
  * from underscore.js, delegates to ECMA5's native Array.isArray
  */
	function isArray(obj) {
		return nativeIsArray ? nativeIsArray(obj) : toString.call(obj) === '[object Array]';
	}

	/**
  * Tests whether supplied parameter is a true object
  */
	function isObject(obj) {
		return obj && toString.call(obj) === '[object Object]';
	}

	/**
  * Extends an object with a defaults object, similar to underscore's _.defaults
  *
  * Used for abstracting parameter handling from API methods
  */
	function defaults(object, defs) {
		var key;
		object = object || {};
		defs = defs || {};
		// Iterate over object non-prototype properties:
		for (key in defs) {
			if (defs.hasOwnProperty(key)) {
				// Replace values with defaults only if undefined (allow empty/zero values):
				if (object[key] == null) object[key] = defs[key];
			}
		}
		return object;
	}

	/**
  * Implementation of `Array.map()` for iteration loops
  *
  * Returns a new Array as a result of calling `iterator` on each array value.
  * Defers to native Array.map if available
  */
	function map(obj, iterator, context) {
		var results = [],
		    i,
		    j;

		if (!obj) return results;

		// Use native .map method if it exists:
		if (nativeMap && obj.map === nativeMap) return obj.map(iterator, context);

		// Fallback for native .map:
		for (i = 0, j = obj.length; i < j; i++) {
			results[i] = iterator.call(context, obj[i], i, obj);
		}
		return results;
	}

	/**
  * Check and normalise the value of precision (must be positive integer)
  */
	function checkPrecision(val, base) {
		val = Math.round(Math.abs(val));
		return isNaN(val) ? base : val;
	}

	/**
  * Parses a format string or object and returns format obj for use in rendering
  *
  * `format` is either a string with the default (positive) format, or object
  * containing `pos` (required), `neg` and `zero` values (or a function returning
  * either a string or object)
  *
  * Either string or format.pos must contain "%v" (value) to be valid
  */
	function checkCurrencyFormat(format) {
		var defaults = lib.settings.currency.format;

		// Allow function as format parameter (should return string or object):
		if (typeof format === "function") format = format();

		// Format can be a string, in which case `value` ("%v") must be present:
		if (isString(format) && format.match("%v")) {

			// Create and return positive, negative and zero formats:
			return {
				pos: format,
				neg: format.replace("-", "").replace("%v", "-%v"),
				zero: format
			};

			// If no format, or object is missing valid positive value, use defaults:
		} else if (!format || !format.pos || !format.pos.match("%v")) {

			// If defaults is a string, casts it to an object for faster checking next time:
			return !isString(defaults) ? defaults : lib.settings.currency.format = {
				pos: defaults,
				neg: defaults.replace("%v", "-%v"),
				zero: defaults
			};
		}
		// Otherwise, assume format was fine:
		return format;
	}

	/* --- API Methods --- */

	/**
  * Takes a string/array of strings, removes all formatting/cruft and returns the raw float value
  * Alias: `accounting.parse(string)`
  *
  * Decimal must be included in the regular expression to match floats (defaults to
  * accounting.settings.number.decimal), so if the number uses a non-standard decimal 
  * separator, provide it as the second argument.
  *
  * Also matches bracketed negatives (eg. "$ (1.99)" => -1.99)
  *
  * Doesn't throw any errors (`NaN`s become 0) but this may change in future
  */
	var unformat = lib.unformat = lib.parse = function (value, decimal) {
		// Recursively unformat arrays:
		if (isArray(value)) {
			return map(value, function (val) {
				return unformat(val, decimal);
			});
		}

		// Fails silently (need decent errors):
		value = value || 0;

		// Return the value as-is if it's already a number:
		if (typeof value === "number") return value;

		// Default decimal point comes from settings, but could be set to eg. "," in opts:
		decimal = decimal || lib.settings.number.decimal;

		// Build regex to strip out everything except digits, decimal point and minus sign:
		var regex = new RegExp("[^0-9-" + decimal + "]", ["g"]),
		    unformatted = parseFloat(("" + value).replace(/\((.*)\)/, "-$1") // replace bracketed values with negatives
		.replace(regex, '') // strip out any cruft
		.replace(decimal, '.') // make sure decimal point is standard
		);

		// This will fail silently which may cause trouble, let's wait and see:
		return !isNaN(unformatted) ? unformatted : 0;
	};

	/**
  * Implementation of toFixed() that treats floats more like decimals
  *
  * Fixes binary rounding issues (eg. (0.615).toFixed(2) === "0.61") that present
  * problems for accounting- and finance-related software.
  */
	var toFixed = lib.toFixed = function (value, precision) {
		precision = checkPrecision(precision, lib.settings.number.precision);
		var power = Math.pow(10, precision);

		// Multiply up by precision, round accurately, then divide and use native toFixed():
		return (Math.round(lib.unformat(value) * power) / power).toFixed(precision);
	};

	/**
  * Format a number, with comma-separated thousands and custom precision/decimal places
  * Alias: `accounting.format()`
  *
  * Localise by overriding the precision and thousand / decimal separators
  * 2nd parameter `precision` can be an object matching `settings.number`
  */
	var formatNumber = lib.formatNumber = lib.format = function (number, precision, thousand, decimal) {
		// Resursively format arrays:
		if (isArray(number)) {
			return map(number, function (val) {
				return formatNumber(val, precision, thousand, decimal);
			});
		}

		// Clean up number:
		number = unformat(number);

		// Build options object from second param (if object) or all params, extending defaults:
		var opts = defaults(isObject(precision) ? precision : {
			precision: precision,
			thousand: thousand,
			decimal: decimal
		}, lib.settings.number),


		// Clean up precision
		usePrecision = checkPrecision(opts.precision),


		// Do some calc:
		negative = number < 0 ? "-" : "",
		    base = parseInt(toFixed(Math.abs(number || 0), usePrecision), 10) + "",
		    mod = base.length > 3 ? base.length % 3 : 0;

		// Format the number:
		return negative + (mod ? base.substr(0, mod) + opts.thousand : "") + base.substr(mod).replace(/(\d{3})(?=\d)/g, "$1" + opts.thousand) + (usePrecision ? opts.decimal + toFixed(Math.abs(number), usePrecision).split('.')[1] : "");
	};

	/**
  * Format a number into currency
  *
  * Usage: accounting.formatMoney(number, symbol, precision, thousandsSep, decimalSep, format)
  * defaults: (0, "$", 2, ",", ".", "%s%v")
  *
  * Localise by overriding the symbol, precision, thousand / decimal separators and format
  * Second param can be an object matching `settings.currency` which is the easiest way.
  *
  * To do: tidy up the parameters
  */
	var formatMoney = lib.formatMoney = function (number, symbol, precision, thousand, decimal, format) {
		// Resursively format arrays:
		if (isArray(number)) {
			return map(number, function (val) {
				return formatMoney(val, symbol, precision, thousand, decimal, format);
			});
		}

		// Clean up number:
		number = unformat(number);

		// Build options object from second param (if object) or all params, extending defaults:
		var opts = defaults(isObject(symbol) ? symbol : {
			symbol: symbol,
			precision: precision,
			thousand: thousand,
			decimal: decimal,
			format: format
		}, lib.settings.currency),


		// Check format (returns object with pos, neg and zero):
		formats = checkCurrencyFormat(opts.format),


		// Choose which format to use for this value:
		useFormat = number > 0 ? formats.pos : number < 0 ? formats.neg : formats.zero;

		// Return with currency symbol added:
		return useFormat.replace('%s', opts.symbol).replace('%v', formatNumber(Math.abs(number), checkPrecision(opts.precision), opts.thousand, opts.decimal));
	};

	/**
  * Format a list of numbers into an accounting column, padding with whitespace
  * to line up currency symbols, thousand separators and decimals places
  *
  * List should be an array of numbers
  * Second parameter can be an object containing keys that match the params
  *
  * Returns array of accouting-formatted number strings of same length
  *
  * NB: `white-space:pre` CSS rule is required on the list container to prevent
  * browsers from collapsing the whitespace in the output strings.
  */
	lib.formatColumn = function (list, symbol, precision, thousand, decimal, format) {
		if (!list) return [];

		// Build options object from second param (if object) or all params, extending defaults:
		var opts = defaults(isObject(symbol) ? symbol : {
			symbol: symbol,
			precision: precision,
			thousand: thousand,
			decimal: decimal,
			format: format
		}, lib.settings.currency),


		// Check format (returns object with pos, neg and zero), only need pos for now:
		formats = checkCurrencyFormat(opts.format),


		// Whether to pad at start of string or after currency symbol:
		padAfterSymbol = formats.pos.indexOf("%s") < formats.pos.indexOf("%v") ? true : false,


		// Store value for the length of the longest string in the column:
		maxLength = 0,


		// Format the list according to options, store the length of the longest string:
		formatted = map(list, function (val, i) {
			if (isArray(val)) {
				// Recursively format columns if list is a multi-dimensional array:
				return lib.formatColumn(val, opts);
			} else {
				// Clean up the value
				val = unformat(val);

				// Choose which format to use for this value (pos, neg or zero):
				var useFormat = val > 0 ? formats.pos : val < 0 ? formats.neg : formats.zero,


				// Format this value, push into formatted list and save the length:
				fVal = useFormat.replace('%s', opts.symbol).replace('%v', formatNumber(Math.abs(val), checkPrecision(opts.precision), opts.thousand, opts.decimal));

				if (fVal.length > maxLength) maxLength = fVal.length;
				return fVal;
			}
		});

		// Pad each number in the list and send back the column of numbers:
		return map(formatted, function (val, i) {
			// Only if this is a string (not a nested array, which would have already been padded):
			if (isString(val) && val.length < maxLength) {
				// Depending on symbol position, pad after symbol or at index 0:
				return padAfterSymbol ? val.replace(opts.symbol, opts.symbol + new Array(maxLength - val.length + 1).join(" ")) : new Array(maxLength - val.length + 1).join(" ") + val;
			}
			return val;
		});
	};

	/* --- Module Definition --- */

	// Export accounting for CommonJS. If being loaded as an AMD module, define it as such.
	// Otherwise, just add `accounting` to the global object
	if (typeof exports !== 'undefined') {
		if (typeof module !== 'undefined' && module.exports) {
			exports = module.exports = lib;
		}
		exports.accounting = lib;
	} else if (typeof define === 'function' && define.amd) {
		// Return the library as an AMD module:
		define([], function () {
			return lib;
		});
	} else {
		// Use accounting.noConflict to restore `accounting` back to its original value.
		// Returns a reference to the library's `accounting` object;
		// e.g. `var numbers = accounting.noConflict();`
		lib.noConflict = function (oldAccounting) {
			return function () {
				// Reset the value of the root's `accounting` variable:
				root.accounting = oldAccounting;
				// Delete the noConflict method:
				lib.noConflict = undefined;
				// Return reference to the library to re-assign it:
				return lib;
			};
		}(root.accounting);

		// Declare `fx` on the root (global/window) object:
		root['accounting'] = lib;
	}

	// Root will be `window` in browser or `global` on the server:
})(undefined);

},{}],5:[function(require,module,exports){
"use strict";

/**
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing. The function also has a property 'clear' 
 * that is a function which will clear the timer to prevent previously scheduled executions. 
 *
 * @source underscore.js
 * @see http://unscriptable.com/2009/03/20/debouncing-javascript-methods/
 * @param {Function} function to wrap
 * @param {Number} timeout in ms (`100`)
 * @param {Boolean} whether to execute at the beginning (`false`)
 * @api public
 */

module.exports = function debounce(func, wait, immediate) {
  var timeout, args, context, timestamp, result;
  if (null == wait) wait = 100;

  function later() {
    var last = Date.now() - timestamp;

    if (last < wait && last >= 0) {
      timeout = setTimeout(later, wait - last);
    } else {
      timeout = null;
      if (!immediate) {
        result = func.apply(context, args);
        context = args = null;
      }
    }
  };

  var debounced = function debounced() {
    context = this;
    args = arguments;
    timestamp = Date.now();
    var callNow = immediate && !timeout;
    if (!timeout) timeout = setTimeout(later, wait);
    if (callNow) {
      result = func.apply(context, args);
      context = args = null;
    }

    return result;
  };

  debounced.clear = function () {
    if (timeout) {
      clearTimeout(timeout);
      timeout = null;
    }
  };

  debounced.flush = function () {
    if (timeout) {
      result = func.apply(context, args);
      context = args = null;

      clearTimeout(timeout);
      timeout = null;
    }
  };

  return debounced;
};

},{}],6:[function(require,module,exports){
'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var token = '%[a-f0-9]{2}';
var singleMatcher = new RegExp(token, 'gi');
var multiMatcher = new RegExp('(' + token + ')+', 'gi');

function decodeComponents(components, split) {
	try {
		// Try to decode the entire string first
		return decodeURIComponent(components.join(''));
	} catch (err) {
		// Do nothing
	}

	if (components.length === 1) {
		return components;
	}

	split = split || 1;

	// Split the array in 2 parts
	var left = components.slice(0, split);
	var right = components.slice(split);

	return Array.prototype.concat.call([], decodeComponents(left), decodeComponents(right));
}

function decode(input) {
	try {
		return decodeURIComponent(input);
	} catch (err) {
		var tokens = input.match(singleMatcher);

		for (var i = 1; i < tokens.length; i++) {
			input = decodeComponents(tokens, i).join('');

			tokens = input.match(singleMatcher);
		}

		return input;
	}
}

function customDecodeURIComponent(input) {
	// Keep track of all the replacements and prefill the map with the `BOM`
	var replaceMap = {
		'%FE%FF': '\uFFFD\uFFFD',
		'%FF%FE': '\uFFFD\uFFFD'
	};

	var match = multiMatcher.exec(input);
	while (match) {
		try {
			// Decode as big chunks as possible
			replaceMap[match[0]] = decodeURIComponent(match[0]);
		} catch (err) {
			var result = decode(match[0]);

			if (result !== match[0]) {
				replaceMap[match[0]] = result;
			}
		}

		match = multiMatcher.exec(input);
	}

	// Add `%C2` at the end of the map to make sure it does not replace the combinator before everything else
	replaceMap['%C2'] = '\uFFFD';

	var entries = Object.keys(replaceMap);

	for (var i = 0; i < entries.length; i++) {
		// Replace all decoded components
		var key = entries[i];
		input = input.replace(new RegExp(key, 'g'), replaceMap[key]);
	}

	return input;
}

module.exports = function (encodedURI) {
	if (typeof encodedURI !== 'string') {
		throw new TypeError('Expected `encodedURI` to be of type `string`, got `' + (typeof encodedURI === 'undefined' ? 'undefined' : _typeof(encodedURI)) + '`');
	}

	try {
		encodedURI = encodedURI.replace(/\+/g, ' ');

		// Try the built in decoder first
		return decodeURIComponent(encodedURI);
	} catch (err) {
		// Fallback to a more advanced decoder
		return customDecodeURIComponent(encodedURI);
	}
};

},{}],7:[function(require,module,exports){
'use strict';

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var strictUriEncode = require('strict-uri-encode');
var decodeComponent = require('decode-uri-component');

function encoderForArrayFormat(options) {
	switch (options.arrayFormat) {
		case 'index':
			return function (key, value, index) {
				return value === null ? [encode(key, options), '[', index, ']'].join('') : [encode(key, options), '[', encode(index, options), ']=', encode(value, options)].join('');
			};
		case 'bracket':
			return function (key, value) {
				return value === null ? [encode(key, options), '[]'].join('') : [encode(key, options), '[]=', encode(value, options)].join('');
			};
		default:
			return function (key, value) {
				return value === null ? encode(key, options) : [encode(key, options), '=', encode(value, options)].join('');
			};
	}
}

function parserForArrayFormat(options) {
	var result = void 0;

	switch (options.arrayFormat) {
		case 'index':
			return function (key, value, accumulator) {
				result = /\[(\d*)\]$/.exec(key);

				key = key.replace(/\[\d*\]$/, '');

				if (!result) {
					accumulator[key] = value;
					return;
				}

				if (accumulator[key] === undefined) {
					accumulator[key] = {};
				}

				accumulator[key][result[1]] = value;
			};
		case 'bracket':
			return function (key, value, accumulator) {
				result = /(\[\])$/.exec(key);
				key = key.replace(/\[\]$/, '');

				if (!result) {
					accumulator[key] = value;
					return;
				}

				if (accumulator[key] === undefined) {
					accumulator[key] = [value];
					return;
				}

				accumulator[key] = [].concat(accumulator[key], value);
			};
		default:
			return function (key, value, accumulator) {
				if (accumulator[key] === undefined) {
					accumulator[key] = value;
					return;
				}

				accumulator[key] = [].concat(accumulator[key], value);
			};
	}
}

function encode(value, options) {
	if (options.encode) {
		return options.strict ? strictUriEncode(value) : encodeURIComponent(value);
	}

	return value;
}

function decode(value, options) {
	if (options.decode) {
		return decodeComponent(value);
	}

	return value;
}

function keysSorter(input) {
	if (Array.isArray(input)) {
		return input.sort();
	}

	if ((typeof input === 'undefined' ? 'undefined' : _typeof(input)) === 'object') {
		return keysSorter(Object.keys(input)).sort(function (a, b) {
			return Number(a) - Number(b);
		}).map(function (key) {
			return input[key];
		});
	}

	return input;
}

function extract(input) {
	var queryStart = input.indexOf('?');
	if (queryStart === -1) {
		return '';
	}
	return input.slice(queryStart + 1);
}

function parse(input, options) {
	options = Object.assign({ decode: true, arrayFormat: 'none' }, options);

	var formatter = parserForArrayFormat(options);

	// Create an object with no prototype
	var ret = Object.create(null);

	if (typeof input !== 'string') {
		return ret;
	}

	input = input.trim().replace(/^[?#&]/, '');

	if (!input) {
		return ret;
	}

	var _iteratorNormalCompletion = true;
	var _didIteratorError = false;
	var _iteratorError = undefined;

	try {
		for (var _iterator = input.split('&')[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
			var param = _step.value;

			var _param$replace$split = param.replace(/\+/g, ' ').split('='),
			    _param$replace$split2 = _slicedToArray(_param$replace$split, 2),
			    key = _param$replace$split2[0],
			    value = _param$replace$split2[1];

			// Missing `=` should be `null`:
			// http://w3.org/TR/2012/WD-url-20120524/#collect-url-parameters


			value = value === undefined ? null : decode(value, options);

			formatter(decode(key, options), value, ret);
		}
	} catch (err) {
		_didIteratorError = true;
		_iteratorError = err;
	} finally {
		try {
			if (!_iteratorNormalCompletion && _iterator.return) {
				_iterator.return();
			}
		} finally {
			if (_didIteratorError) {
				throw _iteratorError;
			}
		}
	}

	return Object.keys(ret).sort().reduce(function (result, key) {
		var value = ret[key];
		if (Boolean(value) && (typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && !Array.isArray(value)) {
			// Sort object keys, not values
			result[key] = keysSorter(value);
		} else {
			result[key] = value;
		}

		return result;
	}, Object.create(null));
}

exports.extract = extract;
exports.parse = parse;

exports.stringify = function (obj, options) {
	var defaults = {
		encode: true,
		strict: true,
		arrayFormat: 'none'
	};

	options = Object.assign(defaults, options);

	if (options.sort === false) {
		options.sort = function () {};
	}

	var formatter = encoderForArrayFormat(options);

	return obj ? Object.keys(obj).sort(options.sort).map(function (key) {
		var value = obj[key];

		if (value === undefined) {
			return '';
		}

		if (value === null) {
			return encode(key, options);
		}

		if (Array.isArray(value)) {
			var result = [];

			var _iteratorNormalCompletion2 = true;
			var _didIteratorError2 = false;
			var _iteratorError2 = undefined;

			try {
				for (var _iterator2 = value.slice()[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
					var value2 = _step2.value;

					if (value2 === undefined) {
						continue;
					}

					result.push(formatter(key, value2, result.length));
				}
			} catch (err) {
				_didIteratorError2 = true;
				_iteratorError2 = err;
			} finally {
				try {
					if (!_iteratorNormalCompletion2 && _iterator2.return) {
						_iterator2.return();
					}
				} finally {
					if (_didIteratorError2) {
						throw _iteratorError2;
					}
				}
			}

			return result.join('&');
		}

		return encode(key, options) + '=' + encode(value, options);
	}).filter(function (x) {
		return x.length > 0;
	}).join('&') : '';
};

exports.parseUrl = function (input, options) {
	return {
		url: input.split('?')[0] || '',
		query: parse(extract(input), options)
	};
};

},{"decode-uri-component":6,"strict-uri-encode":8}],8:[function(require,module,exports){
'use strict';

module.exports = function (str) {
  return encodeURIComponent(str).replace(/[!'()*]/g, function (x) {
    return '%' + x.charCodeAt(0).toString(16).toUpperCase();
  });
};

},{}]},{},[1]);

//# sourceMappingURL=admin.js.map
