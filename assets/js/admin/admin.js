(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

(function ($) {
  'use strict';

  var awebooking = window.awebooking || {};

  // Create the properties.
  awebooking.utils = {};
  awebooking.instances = {};

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
   * Show the confirm message.
   *
   * @return {SweetAlert}
   */
  awebooking.confirm = function (message, callback) {
    if (!window.swal) {
      return window.confirm(message || this.i18n.warning) && callback();
    }

    var confirm = swal({
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
  },

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
  },

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

  /**
   * Init the search customers.
   *
   * @return {void}
   */
  awebooking.utils.initSearchCustomer = function () {
    var $selectors = $('select.awebooking-search-customer, .selectize-search-customer .cmb2_select');

    var ajaxSearch = function ajaxSearch(query, callback) {
      $.ajax({
        type: 'GET',
        url: awebooking.route('/search/customers'),
        data: { term: encodeURIComponent(query) },
        error: function error() {
          callback();
        },
        success: function success(res) {
          callback(res);
        }
      });
    };

    $selectors.each(function () {
      $(this).selectize({
        valueField: 'id',
        labelField: 'display',
        searchField: 'display',
        dropdownParent: 'body',
        placeholder: $(this).data('placeholder'),
        load: function load(query, callback) {
          if (!query.length) {
            return callback();
          } else {
            ajaxSearch(query, callback);
          }
        }
      });
    });
  };

  $(function () {
    // Init tippy.
    if (window.tippy) {
      tippy('.tippy', {
        arrow: true,
        animation: 'shift-toward',
        duration: [200, 150]
      });
    }

    // Init the selectize.
    if ($.fn.selectize) {
      $('select.selectize, .with-selectize .cmb2_select').selectize({
        allowEmptyOption: true,
        searchField: ['value', 'text']
      });

      awebooking.utils.initSearchCustomer();
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

},{"debounce":2,"query-string":4}],2:[function(require,module,exports){
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

},{}],3:[function(require,module,exports){
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

},{}],4:[function(require,module,exports){
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
				return value === null ? encode(key, options) : [
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
	options = Object.assign({arrayFormat: 'none'}, options);

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
		value = value === undefined ? null : decodeComponent(value);

		formatter(decodeComponent(key), value, ret);
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

},{"decode-uri-component":3,"strict-uri-encode":5}],5:[function(require,module,exports){
'use strict';
module.exports = str => encodeURIComponent(str).replace(/[!'()*]/g, x => `%${x.charCodeAt(0).toString(16).toUpperCase()}`);

},{}]},{},[1]);

//# sourceMappingURL=admin.js.map
