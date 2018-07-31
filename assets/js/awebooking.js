(function ($) {
	'use strict';

	$ = $ && $.hasOwnProperty('default') ? $['default'] : $;

	var commonjsGlobal = typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

	function createCommonjsModule(fn, module) {
		return module = { exports: {} }, fn(module, module.exports), module.exports;
	}

	var accounting = createCommonjsModule(function (module, exports) {
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
			{
				if (module.exports) {
					exports = module.exports = lib;
				}
				exports.accounting = lib;
			}

			// Root will be `window` in browser or `global` on the server:
		})(commonjsGlobal);
	});
	var accounting_1 = accounting.accounting;

	var util = function ($$$1) {

	  function getTransitionEndEvent() {
	    var transitionEndEvent = '';

	    var transitionEndEvents = {
	      'WebkitTransition': 'webkitTransitionEnd',
	      'MozTransition': 'transitionend',
	      'OTransition': 'otransitionend',
	      'transition': 'transitionend'
	    };

	    for (var name in transitionEndEvents) {
	      if ({}.hasOwnProperty.call(transitionEndEvents, name)) {
	        var tempEl = document.createElement('p');
	        if (typeof tempEl.style[name] !== 'undefined') {
	          transitionEndEvent = transitionEndEvents[name];
	        }
	      }
	    }

	    return transitionEndEvent;
	  }

	  return {
	    TRANSITION_END: getTransitionEndEvent(),

	    onTransitionEnd: function onTransitionEnd(el, callback) {
	      var _this = this;

	      var called = false;

	      $$$1(el).one(this.TRANSITION_END, function () {
	        callback();
	        called = true;
	      });

	      setTimeout(function () {
	        if (!called) $$$1(el).trigger(_this.TRANSITION_END);
	      }, this.getTransitionDurationFromElement(el));
	    },
	    getTransitionDurationFromElement: function getTransitionDurationFromElement(element) {
	      if (!element) {
	        return 0;
	      }

	      // Get transition-duration of the element.
	      var transitionDuration = $$$1(element).css('transition-duration');
	      var floatTransitionDuration = parseFloat(transitionDuration);

	      // Return 0 if element or transition duration is not found.
	      if (!floatTransitionDuration) {
	        return 0;
	      }

	      // If multiple durations are defined, take the first.
	      transitionDuration = transitionDuration.split(',')[0];

	      return parseFloat(transitionDuration) * 1000;
	    },
	    getTargetFromElement: function getTargetFromElement(element) {
	      var selector = element.getAttribute('data-target');

	      if (!selector || selector === '#') {
	        selector = element.getAttribute('href') || '';
	      }

	      try {
	        return document.querySelector(selector) ? selector : null;
	      } catch (err) {
	        return null;
	      }
	    }
	  };
	}(jQuery);

	var classCallCheck = function (instance, Constructor) {
	  if (!(instance instanceof Constructor)) {
	    throw new TypeError("Cannot call a class as a function");
	  }
	};

	var createClass = function () {
	  function defineProperties(target, props) {
	    for (var i = 0; i < props.length; i++) {
	      var descriptor = props[i];
	      descriptor.enumerable = descriptor.enumerable || false;
	      descriptor.configurable = true;
	      if ("value" in descriptor) descriptor.writable = true;
	      Object.defineProperty(target, descriptor.key, descriptor);
	    }
	  }

	  return function (Constructor, protoProps, staticProps) {
	    if (protoProps) defineProperties(Constructor.prototype, protoProps);
	    if (staticProps) defineProperties(Constructor, staticProps);
	    return Constructor;
	  };
	}();

	var Dropdown = function ($$$1, Popper) {

	  var Dropdown = function () {
	    function Dropdown(element, options) {
	      classCallCheck(this, Dropdown);

	      this.element = element;
	      this.options = Object.assign({}, Dropdown.defaults, options);
	      this.drop = this._getDropElement();
	      this.popper = null;

	      if (!this.drop || typeof this.drop === 'undefined') {
	        throw new Error('Drop Error: Cannot find the drop element.');
	      }

	      if (typeof Popper !== 'undefined' && !this.popper) {
	        var referenceElement = this.element;
	        this.popper = new Popper(referenceElement, this.drop, this._getPopperConfig());
	      }

	      this._addEventListeners();
	      Dropdown.allDrops.push(this);
	    }

	    createClass(Dropdown, [{
	      key: 'isOpened',
	      value: function isOpened() {
	        return this.drop.classList.contains('open');
	      }
	    }, {
	      key: 'isDisabled',
	      value: function isDisabled() {
	        return this.element.disabled || this.element.classList.contains('disabled');
	      }
	    }, {
	      key: 'toggle',
	      value: function toggle() {
	        if (this.isOpened()) {
	          this.close();
	        } else {
	          this.open();
	        }
	      }
	    }, {
	      key: 'open',
	      value: function open() {
	        var _this = this;

	        if (this.isDisabled() || this.isOpened()) {
	          return;
	        }

	        this.element.focus();
	        this.element.setAttribute('aria-expanded', true);

	        // If this is a touch-enabled device we add extra
	        // empty mouseover listeners to the body's immediate children;
	        // only needed because of broken event delegation on iOS
	        // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html
	        if ('ontouchstart' in document.documentElement) {
	          $$$1(document.body).children().on('mouseover', null, $$$1.noop);
	        }

	        this.drop.classList.add('open');
	        this.drop.setAttribute('aria-hidden', true);

	        if (this.popper) {
	          this.popper.update();
	        }

	        setTimeout(function () {
	          _this.drop.classList.add('open--transition');
	        });
	      }
	    }, {
	      key: 'close',
	      value: function close() {
	        var _this2 = this;

	        if (this.isDisabled() || !this.isOpened()) {
	          return;
	        }

	        // If this is a touch-enabled device we remove the extra
	        // empty mouseover listeners we added for iOS support
	        if ('ontouchstart' in document.documentElement) {
	          $$$1(document.body).children().off('mouseover', null, $$$1.noop);
	        }

	        this.element.setAttribute('aria-expanded', false);
	        this.drop.removeAttribute('aria-hidden');
	        this.drop.classList.remove('open--transition');

	        util.onTransitionEnd(this.drop, function () {
	          _this2.drop.classList.remove('open');
	        });
	      }
	    }, {
	      key: '_addEventListeners',
	      value: function _addEventListeners() {
	        var _this3 = this;

	        if (!this.options.openOn) {
	          return;
	        }

	        if (this.options.openOn === 'always') {
	          setTimeout(this.open.bind(this));
	          return;
	        }

	        var events = this.options.openOn.split(' ');

	        if (events.indexOf('click') >= 0) {
	          $$$1(this.element).on('click', function (e) {
	            e.preventDefault();
	            // e.stopPropagation();

	            _this3.toggle();
	          });

	          $$$1(document).on('click', function (e) {
	            if (!_this3.isOpened()) {
	              return;
	            }

	            // Clicking inside dropdown
	            if (e.target === _this3.drop || _this3.drop.contains(e.target)) {
	              return;
	            }

	            // Clicking target
	            if (e.target === _this3.element || _this3.element.contains(e.target)) {
	              return;
	            }

	            _this3.close(e);
	          });
	        }

	        if (events.indexOf('hover') >= 0) ;

	        if (events.indexOf('focus') >= 0) ;
	      }
	    }, {
	      key: '_getDropElement',
	      value: function _getDropElement() {
	        if (!this.drop) {
	          var parent = this.element.parentNode;
	          var target = util.getTargetFromElement(this.element);

	          if (target) {
	            this.drop = document.querySelector(target);
	          } else {
	            this.drop = parent ? parent.querySelector(this.options.drop) : null;
	          }
	        }

	        return this.drop;
	      }
	    }, {
	      key: '_getPopperConfig',
	      value: function _getPopperConfig() {
	        var _this4 = this;

	        var offset = {};

	        if (typeof this.options.offset === 'function') {
	          offset.fn = function (data) {
	            data.offsets = Object.assign({}, data.offsets, _this4.options.offset(data.offsets) || {});
	            return data;
	          };
	        } else {
	          offset.offset = this.options.offset;
	        }

	        var config = {
	          placement: this._getPlacement(),
	          modifiers: {
	            offset: offset,
	            flip: { enabled: this.options.flip },
	            preventOverflow: { boundariesElement: this.options.boundary }
	          }
	        };

	        // Disable Popper.js if we have a static display.
	        if (this.options.display === 'static') {
	          config.modifiers.applyStyle = {
	            enabled: false
	          };
	        }

	        return config;
	      }
	    }, {
	      key: '_getPlacement',
	      value: function _getPlacement() {
	        return 'bottom-start';
	      }
	    }]);
	    return Dropdown;
	  }();

	  // Store dropdown instances.


	  Dropdown.allDrops = [];

	  Dropdown.defaults = {
	    drop: '[data-drop]',
	    offset: 0,
	    flip: true,
	    openOn: 'click',
	    boundary: 'scrollParent',
	    reference: 'toggle',
	    display: 'dynamic'
	  };

	  return Dropdown;
	}(jQuery, window.Popper);

	// Export the module.
	var dropdown = Dropdown;

	var dateUtils = function () {
	  var pad = function pad(number) {
	    return ('0' + number).slice(-2);
	  };
	  var int = function int(bool) {
	    return bool === true ? 1 : 0;
	  };
	  var monthToStr = function monthToStr(monthNumber, shorthand, locale) {
	    return locale.months[shorthand ? 'shorthand' : 'longhand'][monthNumber];
	  };

	  var tokenRegex = {
	    D: "(\\w+)",
	    F: "(\\w+)",
	    G: "(\\d\\d|\\d)",
	    H: "(\\d\\d|\\d)",
	    J: "(\\d\\d|\\d)\\w+",
	    K: "",
	    M: "(\\w+)",
	    S: "(\\d\\d|\\d)",
	    U: "(.+)",
	    W: "(\\d\\d|\\d)",
	    Y: "(\\d{4})",
	    Z: "(.+)",
	    d: "(\\d\\d|\\d)",
	    h: "(\\d\\d|\\d)",
	    i: "(\\d\\d|\\d)",
	    j: "(\\d\\d|\\d)",
	    l: "(\\w+)",
	    m: "(\\d\\d|\\d)",
	    n: "(\\d\\d|\\d)",
	    s: "(\\d\\d|\\d)",
	    w: "(\\d\\d|\\d)",
	    y: "(\\d{2})"
	  };

	  var revFormat = {
	    D: function D() {
	      return undefined;
	    },

	    F: function F(date, monthName, locale) {
	      date.setMonth(locale.months.longhand.indexOf(monthName));
	    },

	    G: function G(date, hour) {
	      date.setHours(parseFloat(hour));
	    },

	    H: function H(date, hour) {
	      date.setHours(parseFloat(hour));
	    },

	    J: function J(date, day) {
	      date.setDate(parseFloat(day));
	    },

	    K: function K(date, amPM, locale) {
	      date.setHours(date.getHours() % 12 + 12 * int(new RegExp(locale.amPM[1], 'i').test(amPM)));
	    },

	    M: function M(date, shortMonth, locale) {
	      date.setMonth(locale.months.shorthand.indexOf(shortMonth));
	    },

	    S: function S(date, seconds) {
	      date.setSeconds(parseFloat(seconds));
	    },

	    U: function U(_, unixSeconds) {
	      return new Date(parseFloat(unixSeconds) * 1000);
	    },

	    W: function W(date, weekNum) {
	      var weekNumber = parseInt(weekNum);
	      return new Date(date.getFullYear(), 0, 2 + (weekNumber - 1) * 7, 0, 0, 0, 0);
	    },

	    Y: function Y(date, year) {
	      date.setFullYear(parseFloat(year));
	    },

	    Z: function Z(_, ISODate) {
	      return new Date(ISODate);
	    },

	    d: function d(date, day) {
	      date.setDate(parseFloat(day));
	    },

	    h: function h(date, hour) {
	      date.setHours(parseFloat(hour));
	    },

	    i: function i(date, minutes) {
	      date.setMinutes(parseFloat(minutes));
	    },

	    j: function j(date, day) {
	      date.setDate(parseFloat(day));
	    },

	    l: function l() {
	      return undefined;
	    },

	    m: function m(date, month) {
	      date.setMonth(parseFloat(month) - 1);
	    },

	    n: function n(date, month) {
	      date.setMonth(parseFloat(month) - 1);
	    },

	    s: function s(date, seconds) {
	      date.setSeconds(parseFloat(seconds));
	    },

	    w: function w() {
	      return undefined;
	    },

	    y: function y(date, year) {
	      date.setFullYear(2000 + parseFloat(year));
	    }
	  };

	  var formats = {
	    // Get the date in UTC
	    Z: function Z(date) {
	      return date.toISOString();
	    },

	    // Weekday name, short, e.g. Thu
	    D: function D(date, locale) {
	      return locale.weekdays.shorthand[formats.w(date, locale)];
	    },

	    // Full month name e.g. January
	    F: function F(date, locale) {
	      return monthToStr(formats.n(date, locale) - 1, false, locale);
	    },

	    // Padded hour 1-12
	    G: function G(date, locale) {
	      return pad(formats.h(date, locale));
	    },

	    // Hours with leading zero e.g. 03
	    H: function H(date) {
	      return pad(date.getHours());
	    },

	    // Day (1-30) with ordinal suffix e.g. 1st, 2nd
	    J: function J(date, locale) {
	      return locale.ordinal !== undefined ? date.getDate() + locale.ordinal(date.getDate()) : date.getDate();
	    },

	    // AM/PM
	    K: function K(date, locale) {
	      return locale.amPM[int(date.getHours() > 11)];
	    },

	    // Shorthand month e.g. Jan, Sep, Oct, etc
	    M: function M(date, locale) {
	      return monthToStr(date.getMonth(), true, locale);
	    },

	    // Seconds 00-59
	    S: function S(date) {
	      return pad(date.getSeconds());
	    },

	    // Unix timestamp
	    U: function U(date) {
	      return date.getTime() / 1000;
	    },

	    // ISO-8601 week number of year
	    W: function W(date) {
	      return DateUtils.getWeek(date);
	    },

	    // Full year e.g. 2016
	    Y: function Y(date) {
	      return date.getFullYear();
	    },

	    // Day in month, padded (01-30)
	    d: function d(date) {
	      return pad(date.getDate());
	    },

	    // Hour from 1-12 (am/pm)
	    h: function h(date) {
	      return date.getHours() % 12 ? date.getHours() % 12 : 12;
	    },

	    // Minutes, padded with leading zero e.g. 09
	    i: function i(date) {
	      return pad(date.getMinutes());
	    },

	    // Day in month (1-30)
	    j: function j(date) {
	      return date.getDate();
	    },

	    // Weekday name, full, e.g. Thursday
	    l: function l(date, locale) {
	      return locale.weekdays.longhand[date.getDay()];
	    },

	    // Padded month number (01-12)
	    m: function m(date) {
	      return pad(date.getMonth() + 1);
	    },

	    // The month number (1-12)
	    n: function n(date) {
	      return date.getMonth() + 1;
	    },

	    // Seconds 0-59
	    s: function s(date) {
	      return date.getSeconds();
	    },

	    // Number of the day of the week
	    w: function w(date) {
	      return date.getDay();
	    },

	    // Last two digits of year e.g. 16 for 2016
	    y: function y(date) {
	      return String(date.getFullYear()).substring(2);
	    }
	  };

	  var DateUtils = {
	    l10n: {
	      amPM: ['AM', 'PM'],
	      weekdays: {
	        shorthand: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
	        longhand: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
	      },
	      months: {
	        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	        longhand: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
	      }
	    },

	    getWeek: function getWeek(givenDate) {
	      var date = new Date(givenDate.getTime());
	      date.setHours(0, 0, 0, 0);

	      // Thursday in current week decides the year.
	      date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);

	      // January 4 is always in week 1.
	      var week1 = new Date(date.getFullYear(), 0, 4);

	      // Adjust to Thursday in week 1 and count number of weeks from date to week1.
	      return 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
	    },
	    format: function format(date, _format, locale) {
	      locale = locale || this.l10n;

	      return _format.split('').map(function (c, i, arr) {
	        return formats[c] && arr[i - 1] !== '\\' ? formats[c](date, locale) : c !== '\\' ? c : '';
	      }).join('');
	    },
	    parse: function parse(date, format, timeless, locale) {
	      locale = locale || this.l10n;

	      if (date !== 0 && !date) {
	        return undefined;
	      }

	      var parsedDate = void 0;

	      if (date instanceof Date) {
	        parsedDate = new Date(date.getTime());
	      } else if (typeof date !== 'string' && date.toFixed !== undefined) {
	        parsedDate = new Date(date);
	      } else if (typeof date === 'string') {
	        var datestr = String(date).trim();

	        if (datestr === 'today') {
	          parsedDate = new Date();
	          timeless = true;
	        } else if (/Z$/.test(datestr) || /GMT$/.test(datestr)) {
	          parsedDate = new Date(date);
	        } else {
	          parsedDate = new Date(new Date().getFullYear(), 0, 1, 0, 0, 0, 0);

	          var matched = void 0,
	              ops = [];
	          for (var i = 0, matchIndex = 0, regexStr = ''; i < format.length; i++) {
	            var token = format[i];
	            var isBackSlash = token === '\\';
	            var escaped = format[i - 1] === '\\' || isBackSlash;

	            if (tokenRegex[token] && !escaped) {
	              regexStr += tokenRegex[token];
	              var match = new RegExp(regexStr).exec(date);

	              if (match && (matched = true)) {
	                ops[token !== 'Y' ? 'push' : 'unshift']({
	                  fn: revFormat[token],
	                  val: match[++matchIndex]
	                });
	              }
	            } else if (!isBackSlash) {
	              regexStr += '.'; // don't really care
	            }

	            ops.forEach(function (_ref) {
	              var fn = _ref.fn,
	                  val = _ref.val;
	              return parsedDate = fn(parsedDate, val, locale) || parsedDate;
	            });
	          }

	          parsedDate = matched ? parsedDate : undefined;
	        }
	      }

	      /* istanbul ignore next */
	      if (!(parsedDate instanceof Date && !isNaN(parsedDate.getTime()))) {
	        // config.errorHandler(new Error(`Invalid date provided: ${dateOrig}`))
	        return undefined;
	      }

	      if (timeless === true) {
	        parsedDate.setHours(0, 0, 0, 0);
	      }

	      return parsedDate;
	    }
	  };

	  return DateUtils;
	}();

	var plugin = window.awebooking = {};

	// Main objects
	plugin.utils = {};
	plugin.instances = {};
	plugin.i18n = window._awebooking_i18n || {};

	plugin.config = Object.assign({}, window._awebooking, {
	  route: window.location.origin + '?awebooking_route=/',
	  ajax_url: window.location.origin + '/wp-admin/admin-ajax.php'
	});

	plugin.utils.dates = dateUtils;
	if (typeof window.flatpickr !== 'undefined') {
	  plugin.utils.dates.l10n = flatpickr.l10ns.default;
	}

	plugin.utils.dropdown = function (el, config) {
	  $(el).each(function () {
	    $(this).data('abrs-dropdown', new dropdown(this, config));
	  });
	};

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
	  var i18n = plugin.i18n;
	  var defaults = plugin.config.datepicker;
	  var disable = Array.isArray(defaults.disable) ? defaults.disable : [];

	  if (Array.isArray(defaults.disableDays)) {
	    disable.push(function (date) {
	      return defaults.disableDays.indexOf(date.getDay()) !== -1;
	    });
	  }

	  // const minDate = new Date().fp_incr(defaults.min_date);
	  // const maxDate = (defaults.max_date && defaults.max_date !== 0) ? new Date().fp_incr(defaults.max_date) : '';

	  var _defaults = {
	    dateFormat: 'Y-m-d',
	    ariaDateFormat: i18n.dateFormat,
	    minDate: 'today',
	    // maxDate: max_date,
	    // disable: disable,
	    showMonths: 1,
	    enableTime: false,
	    enableSeconds: false,
	    disableMobile: false
	  };

	  var fp = flatpickr(instance, $.extend({}, _defaults, options));

	  fp.config.onReady.push(function (_, __, fp) {
	    fp.calendarContainer.classList.add('awebooking-datepicker');
	  });

	  return fp;
	};

	/**
	 * Format the price.
	 *
	 * @param amount
	 * @returns {string}
	 */
	plugin.formatPrice = function (amount) {
	  return accounting.formatMoney(amount, {
	    format: plugin.i18n.priceFormat,
	    symbol: plugin.i18n.currencySymbol,
	    decimal: plugin.i18n.decimalSeparator,
	    thousand: plugin.i18n.priceThousandSeparator,
	    precision: plugin.i18n.numberDecimals
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

	  $('[data-init="awebooking-dialog"]').each(function (e, el) {
	    var dialog = new window.A11yDialog(el);

	    dialog.on('show', function () {
	      el.classList.add('open');
	      el.removeAttribute('aria-hidden');
	    });

	    dialog.on('hide', function () {
	      el.classList.remove('open');
	      el.setAttribute('aria-hidden', true);
	    });
	  });
	});

}(jQuery));

//# sourceMappingURL=awebooking.js.map
