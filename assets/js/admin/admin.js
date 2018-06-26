(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

(function ($) {
  'use strict';

  var awebooking = window.awebooking || {};

  // Create the properties.
  awebooking.utils = {};
  awebooking.instances = {};

  awebooking.utils.flatpickrRangePlugin = require('flatpickr/dist/plugins/rangePlugin.js');

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
    var debounce = require('debounce');

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
   * Create a form then append to body.
   *
   * @param  {string} link   The form action.
   * @param  {string} method The form method.
   * @return {Object}
   */
  awebooking.createForm = function (action, method) {
    var $form = $('<form>', { 'method': 'POST', 'action': action });

    var hiddenInput = $('<input>', { 'name': '_method', 'type': 'hidden', 'value': method });

    return $form.append(hiddenInput).appendTo('body');
  };

  /**
   * Retrieves a modified URL query string.
   *
   * @param {object} args
   * @param {string} url
   */
  awebooking.utils.addQueryArgs = function (args, url) {
    var queryString = require('query-string');

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
})(jQuery);

},{"./utils/search-customer.js":2,"debounce":3,"flatpickr/dist/plugins/rangePlugin.js":5,"query-string":6}],2:[function(require,module,exports){
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
    valueField: 'id',
    labelField: 'name',
    searchField: 'name',
    dropdownParent: 'body',
    placeholder: $(this).data('placeholder'),
    load: function load(query, callback) {
      if (!query.length) {
        return callback();
      } else {
        ajaxSearch('services', query, callback);
      }
    }
    // render: {
    //   item: function(item, escape) {
    //     return '<div>' +
    //         (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
    //     '</div>';
    //   },
    //   option: function(item, escape) {
    //     var label = item.label ? item.label : '';
    //     return '<div>' + label + '</div>';
    //   }
    // },
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

module.exports = function debounce(func, wait, immediate){
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

  var debounced = function(){
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

  debounced.clear = function() {
    if (timeout) {
      clearTimeout(timeout);
      timeout = null;
    }
  };
  
  debounced.flush = function() {
    if (timeout) {
      result = func.apply(context, args);
      context = args = null;
      
      clearTimeout(timeout);
      timeout = null;
    }
  };

  return debounced;
};

},{}],4:[function(require,module,exports){
'use strict';
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
		throw new TypeError('Expected `encodedURI` to be of type `string`, got `' + typeof encodedURI + '`');
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

},{}],5:[function(require,module,exports){
/* flatpickr v4.5.0, @license MIT */
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (global.rangePlugin = factory());
}(this, (function () { 'use strict';

    function rangePlugin(config) {
      if (config === void 0) {
        config = {};
      }

      return function (fp) {
        var dateFormat = "",
            secondInput,
            _secondInputFocused,
            _prevDates;

        var createSecondInput = function createSecondInput() {
          if (config.input) {
            secondInput = config.input instanceof Element ? config.input : window.document.querySelector(config.input);
          } else {
            secondInput = fp._input.cloneNode();
            secondInput.removeAttribute("id");
            secondInput._flatpickr = undefined;
          }

          if (secondInput.value) {
            var parsedDate = fp.parseDate(secondInput.value);
            if (parsedDate) fp.selectedDates.push(parsedDate);
          }

          secondInput.setAttribute("data-fp-omit", "");

          fp._bind(secondInput, ["focus", "click"], function () {
            if (fp.selectedDates[1]) {
              fp.latestSelectedDateObj = fp.selectedDates[1];

              fp._setHoursFromDate(fp.selectedDates[1]);

              fp.jumpToDate(fp.selectedDates[1]);
            }
            _secondInputFocused = true;
            fp.isOpen = false;
            fp.open(undefined, secondInput);
          });

          fp._bind(fp._input, ["focus", "click"], function (e) {
            e.preventDefault();
            fp.isOpen = false;
            fp.open();
          });

          if (fp.config.allowInput) fp._bind(secondInput, "keydown", function (e) {
            if (e.key === "Enter") {
              fp.setDate([fp.selectedDates[0], secondInput.value], true, dateFormat);
              secondInput.click();
            }
          });
          if (!config.input) fp._input.parentNode && fp._input.parentNode.insertBefore(secondInput, fp._input.nextSibling);
        };

        var plugin = {
          onParseConfig: function onParseConfig() {
            fp.config.mode = "range";
            dateFormat = fp.config.altInput ? fp.config.altFormat : fp.config.dateFormat;
          },
          onReady: function onReady() {
            createSecondInput();
            fp.config.ignoredFocusElements.push(secondInput);

            if (fp.config.allowInput) {
              fp._input.removeAttribute("readonly");

              secondInput.removeAttribute("readonly");
            } else {
              secondInput.setAttribute("readonly", "readonly");
            }

            fp._bind(fp._input, "focus", function () {
              fp.latestSelectedDateObj = fp.selectedDates[0];

              fp._setHoursFromDate(fp.selectedDates[0]);
              _secondInputFocused = false;
              fp.jumpToDate(fp.selectedDates[0]);
            });

            if (fp.config.allowInput) fp._bind(fp._input, "keydown", function (e) {
              if (e.key === "Enter") fp.setDate([fp._input.value, fp.selectedDates[1]], true, dateFormat);
            });
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
          onChange: function onChange() {
            if (!fp.selectedDates.length) {
              setTimeout(function () {
                if (fp.selectedDates.length) return;
                secondInput.value = "";
                _prevDates = [];
              }, 10);
            }

            if (_secondInputFocused) {
              setTimeout(function () {
                secondInput.focus();
              }, 0);
            }
          },
          onDestroy: function onDestroy() {
            if (!config.input) secondInput.parentNode && secondInput.parentNode.removeChild(secondInput);
          },
          onValueUpdate: function onValueUpdate(selDates) {
            if (!secondInput) return;
            _prevDates = !_prevDates || selDates.length >= _prevDates.length ? selDates.concat() : _prevDates;

            if (_prevDates.length > selDates.length) {
              var newSelectedDate = selDates[0];
              var newDates = _secondInputFocused ? [_prevDates[0], newSelectedDate] : [newSelectedDate, _prevDates[1]];
              fp.setDate(newDates, false);
              _prevDates = newDates.concat();
            }

            var _fp$selectedDates$map = fp.selectedDates.map(function (d) {
              return fp.formatDate(d, dateFormat);
            });

            var _fp$selectedDates$map2 = _fp$selectedDates$map[0];
            fp._input.value = _fp$selectedDates$map2 === void 0 ? "" : _fp$selectedDates$map2;
            var _fp$selectedDates$map3 = _fp$selectedDates$map[1];
            secondInput.value = _fp$selectedDates$map3 === void 0 ? "" : _fp$selectedDates$map3;
          }
        };
        return plugin;
      };
    }

    return rangePlugin;

})));

},{}],6:[function(require,module,exports){
'use strict';
const strictUriEncode = require('strict-uri-encode');
const decodeComponent = require('decode-uri-component');

function encoderForArrayFormat(options) {
	switch (options.arrayFormat) {
		case 'index':
			return (key, value, index) => {
				return value === null ? [
					encode(key, options),
					'[',
					index,
					']'
				].join('') : [
					encode(key, options),
					'[',
					encode(index, options),
					']=',
					encode(value, options)
				].join('');
			};
		case 'bracket':
			return (key, value) => {
				return value === null ? [encode(key, options), '[]'].join('') : [
					encode(key, options),
					'[]=',
					encode(value, options)
				].join('');
			};
		default:
			return (key, value) => {
				return value === null ? encode(key, options) : [
					encode(key, options),
					'=',
					encode(value, options)
				].join('');
			};
	}
}

function parserForArrayFormat(options) {
	let result;

	switch (options.arrayFormat) {
		case 'index':
			return (key, value, accumulator) => {
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
			return (key, value, accumulator) => {
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
			return (key, value, accumulator) => {
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

	if (typeof input === 'object') {
		return keysSorter(Object.keys(input))
			.sort((a, b) => Number(a) - Number(b))
			.map(key => input[key]);
	}

	return input;
}

function extract(input) {
	const queryStart = input.indexOf('?');
	if (queryStart === -1) {
		return '';
	}
	return input.slice(queryStart + 1);
}

function parse(input, options) {
	options = Object.assign({decode: true, arrayFormat: 'none'}, options);

	const formatter = parserForArrayFormat(options);

	// Create an object with no prototype
	const ret = Object.create(null);

	if (typeof input !== 'string') {
		return ret;
	}

	input = input.trim().replace(/^[?#&]/, '');

	if (!input) {
		return ret;
	}

	for (const param of input.split('&')) {
		let [key, value] = param.replace(/\+/g, ' ').split('=');

		// Missing `=` should be `null`:
		// http://w3.org/TR/2012/WD-url-20120524/#collect-url-parameters
		value = value === undefined ? null : decode(value, options);

		formatter(decode(key, options), value, ret);
	}

	return Object.keys(ret).sort().reduce((result, key) => {
		const value = ret[key];
		if (Boolean(value) && typeof value === 'object' && !Array.isArray(value)) {
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

exports.stringify = (obj, options) => {
	const defaults = {
		encode: true,
		strict: true,
		arrayFormat: 'none'
	};

	options = Object.assign(defaults, options);

	if (options.sort === false) {
		options.sort = () => {};
	}

	const formatter = encoderForArrayFormat(options);

	return obj ? Object.keys(obj).sort(options.sort).map(key => {
		const value = obj[key];

		if (value === undefined) {
			return '';
		}

		if (value === null) {
			return encode(key, options);
		}

		if (Array.isArray(value)) {
			const result = [];

			for (const value2 of value.slice()) {
				if (value2 === undefined) {
					continue;
				}

				result.push(formatter(key, value2, result.length));
			}

			return result.join('&');
		}

		return encode(key, options) + '=' + encode(value, options);
	}).filter(x => x.length > 0).join('&') : '';
};

exports.parseUrl = (input, options) => {
	return {
		url: input.split('?')[0] || '',
		query: parse(extract(input), options)
	};
};

},{"decode-uri-component":4,"strict-uri-encode":7}],7:[function(require,module,exports){
'use strict';
module.exports = str => encodeURIComponent(str).replace(/[!'()*]/g, x => `%${x.charCodeAt(0).toString(16).toUpperCase()}`);

},{}]},{},[1]);

//# sourceMappingURL=admin.js.map
