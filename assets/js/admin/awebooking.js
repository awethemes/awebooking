/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
module.exports = __webpack_require__(5);


/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

var $ = window.jQuery;
var settings = window._awebookingSettings || {};

var Popup = __webpack_require__(2);

var AweBooking = _.extend(settings, {
  /**
   * Init the AweBooking
   */
  init: function init() {
    var self = this;

    // Init the popup, use jquery-ui-popup.
    $('[data-toggle="awebooking-popup"]').each(function () {
      $(this).data('awebooking-popup', new Popup(this));
    });
  },


  /**
   * Get a translator string
   */
  trans: function trans(context) {
    return this.strings[context] ? this.strings[context] : '';
  },


  /**
   * Make form ajax request.
   */
  ajaxSubmit: function ajaxSubmit(form, action) {
    var serialize = __webpack_require__(4);
    var data = serialize(form, { hash: true });

    // Add .ajax-loading class in to the form.
    $(form).addClass('ajax-loading');

    return wp.ajax.post(action, data).always(function () {
      $(form).removeClass('ajax-loading');
    });
  }
});

$(function () {
  AweBooking.init();
});

window.TheAweBooking = AweBooking;

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(3);

var Popup = function () {
  /**
   * Wrapper the jquery-ui-popup.
   */
  function Popup(el) {
    _classCallCheck(this, Popup);

    this.el = el;
    this.target = Utils.getSelectorFromElement(el);

    if (this.target) {
      this.setup();

      $(this.el).on('click', $.proxy(this.open, this));
      $(this.target).on('click', '[data-dismiss="awebooking-popup"]', $.proxy(this.close, this));
    }
  }

  _createClass(Popup, [{
    key: 'open',
    value: function open(e) {
      e && e.preventDefault();
      $(this.target).dialog('open');
    }
  }, {
    key: 'close',
    value: function close(e) {
      e && e.preventDefault();
      $(this.target).dialog('close');
    }
  }, {
    key: 'setup',
    value: function setup() {
      if ($(this.target).dialog('instance')) {
        return;
      }

      $(this.target).dialog({
        modal: true,
        width: 'auto',
        height: 'auto',
        autoOpen: false,
        draggable: false,
        resizable: false,
        closeOnEscape: true,
        dialogClass: 'wp-dialog awebooking-dialog',
        position: { at: 'center top+35%' },
        open: function open() {
          $('body').css({ overflow: 'hidden' });
        },
        beforeClose: function beforeClose(event, ui) {
          $('body').css({ overflow: 'inherit' });
        }
      });
    }
  }]);

  return Popup;
}();

module.exports = Popup;

/***/ }),
/* 3 */
/***/ (function(module, exports) {

var $ = window.jQuery;

var Utils = {
  getSelectorFromElement: function getSelectorFromElement(el) {
    var selector = el.getAttribute('data-target');

    if (!selector || selector === '#') {
      selector = el.getAttribute('href') || '';
    }

    try {
      var $selector = $(selector);
      return $selector.length > 0 ? selector : null;
    } catch (error) {
      return null;
    }
  }
};

module.exports = Utils;

/***/ }),
/* 4 */
/***/ (function(module, exports) {

// get successful control from form and assemble into object
// http://www.w3.org/TR/html401/interact/forms.html#h-17.13.2

// types which indicate a submit action and are not successful controls
// these will be ignored
var k_r_submitter = /^(?:submit|button|image|reset|file)$/i;

// node names which could be successful controls
var k_r_success_contrls = /^(?:input|select|textarea|keygen)/i;

// Matches bracket notation.
var brackets = /(\[[^\[\]]*\])/g;

// serializes form fields
// @param form MUST be an HTMLForm element
// @param options is an optional argument to configure the serialization. Default output
// with no options specified is a url encoded string
//    - hash: [true | false] Configure the output type. If true, the output will
//    be a js object.
//    - serializer: [function] Optional serializer function to override the default one.
//    The function takes 3 arguments (result, key, value) and should return new result
//    hash and url encoded str serializers are provided with this module
//    - disabled: [true | false]. If true serialize disabled fields.
//    - empty: [true | false]. If true serialize empty fields
function serialize(form, options) {
    if (typeof options != 'object') {
        options = { hash: !!options };
    }
    else if (options.hash === undefined) {
        options.hash = true;
    }

    var result = (options.hash) ? {} : '';
    var serializer = options.serializer || ((options.hash) ? hash_serializer : str_serialize);

    var elements = form && form.elements ? form.elements : [];

    //Object store each radio and set if it's empty or not
    var radio_store = Object.create(null);

    for (var i=0 ; i<elements.length ; ++i) {
        var element = elements[i];

        // ingore disabled fields
        if ((!options.disabled && element.disabled) || !element.name) {
            continue;
        }
        // ignore anyhting that is not considered a success field
        if (!k_r_success_contrls.test(element.nodeName) ||
            k_r_submitter.test(element.type)) {
            continue;
        }

        var key = element.name;
        var val = element.value;

        // we can't just use element.value for checkboxes cause some browsers lie to us
        // they say "on" for value when the box isn't checked
        if ((element.type === 'checkbox' || element.type === 'radio') && !element.checked) {
            val = undefined;
        }

        // If we want empty elements
        if (options.empty) {
            // for checkbox
            if (element.type === 'checkbox' && !element.checked) {
                val = '';
            }

            // for radio
            if (element.type === 'radio') {
                if (!radio_store[element.name] && !element.checked) {
                    radio_store[element.name] = false;
                }
                else if (element.checked) {
                    radio_store[element.name] = true;
                }
            }

            // if options empty is true, continue only if its radio
            if (val == undefined && element.type == 'radio') {
                continue;
            }
        }
        else {
            // value-less fields are ignored unless options.empty is true
            if (!val) {
                continue;
            }
        }

        // multi select boxes
        if (element.type === 'select-multiple') {
            val = [];

            var selectOptions = element.options;
            var isSelectedOptions = false;
            for (var j=0 ; j<selectOptions.length ; ++j) {
                var option = selectOptions[j];
                var allowedEmpty = options.empty && !option.value;
                var hasValue = (option.value || allowedEmpty);
                if (option.selected && hasValue) {
                    isSelectedOptions = true;

                    // If using a hash serializer be sure to add the
                    // correct notation for an array in the multi-select
                    // context. Here the name attribute on the select element
                    // might be missing the trailing bracket pair. Both names
                    // "foo" and "foo[]" should be arrays.
                    if (options.hash && key.slice(key.length - 2) !== '[]') {
                        result = serializer(result, key + '[]', option.value);
                    }
                    else {
                        result = serializer(result, key, option.value);
                    }
                }
            }

            // Serialize if no selected options and options.empty is true
            if (!isSelectedOptions && options.empty) {
                result = serializer(result, key, '');
            }

            continue;
        }

        result = serializer(result, key, val);
    }

    // Check for all empty radio buttons and serialize them with key=""
    if (options.empty) {
        for (var key in radio_store) {
            if (!radio_store[key]) {
                result = serializer(result, key, '');
            }
        }
    }

    return result;
}

function parse_keys(string) {
    var keys = [];
    var prefix = /^([^\[\]]*)/;
    var children = new RegExp(brackets);
    var match = prefix.exec(string);

    if (match[1]) {
        keys.push(match[1]);
    }

    while ((match = children.exec(string)) !== null) {
        keys.push(match[1]);
    }

    return keys;
}

function hash_assign(result, keys, value) {
    if (keys.length === 0) {
        result = value;
        return result;
    }

    var key = keys.shift();
    var between = key.match(/^\[(.+?)\]$/);

    if (key === '[]') {
        result = result || [];

        if (Array.isArray(result)) {
            result.push(hash_assign(null, keys, value));
        }
        else {
            // This might be the result of bad name attributes like "[][foo]",
            // in this case the original `result` object will already be
            // assigned to an object literal. Rather than coerce the object to
            // an array, or cause an exception the attribute "_values" is
            // assigned as an array.
            result._values = result._values || [];
            result._values.push(hash_assign(null, keys, value));
        }

        return result;
    }

    // Key is an attribute name and can be assigned directly.
    if (!between) {
        result[key] = hash_assign(result[key], keys, value);
    }
    else {
        var string = between[1];
        // +var converts the variable into a number
        // better than parseInt because it doesn't truncate away trailing
        // letters and actually fails if whole thing is not a number
        var index = +string;

        // If the characters between the brackets is not a number it is an
        // attribute name and can be assigned directly.
        if (isNaN(index)) {
            result = result || {};
            result[string] = hash_assign(result[string], keys, value);
        }
        else {
            result = result || [];
            result[index] = hash_assign(result[index], keys, value);
        }
    }

    return result;
}

// Object/hash encoding serializer.
function hash_serializer(result, key, value) {
    var matches = key.match(brackets);

    // Has brackets? Use the recursive assignment function to walk the keys,
    // construct any missing objects in the result tree and make the assignment
    // at the end of the chain.
    if (matches) {
        var keys = parse_keys(key);
        hash_assign(result, keys, value);
    }
    else {
        // Non bracket notation can make assignments directly.
        var existing = result[key];

        // If the value has been assigned already (for instance when a radio and
        // a checkbox have the same name attribute) convert the previous value
        // into an array before pushing into it.
        //
        // NOTE: If this requirement were removed all hash creation and
        // assignment could go through `hash_assign`.
        if (existing) {
            if (!Array.isArray(existing)) {
                result[key] = [ existing ];
            }

            result[key].push(value);
        }
        else {
            result[key] = value;
        }
    }

    return result;
}

// urlform encoding serializer
function str_serialize(result, key, value) {
    // encode newlines as \r\n cause the html spec says so
    value = value.replace(/(\r)?\n/g, '\r\n');
    value = encodeURIComponent(value);

    // spaces should be '+' rather than '%20'.
    value = value.replace(/%20/g, '+');
    return result + (result ? '&' : '') + encodeURIComponent(key) + '=' + value;
}

module.exports = serialize;


/***/ }),
/* 5 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgZTdmMThmM2QxMDU3MzgzNDNjYzQiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2F3ZWJvb2tpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3BvcHVwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy91dGlscy5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvZm9ybS1zZXJpYWxpemUvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3Nhc3MvYWRtaW4uc2Nzcz9kOWU2Il0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJqUXVlcnkiLCJzZXR0aW5ncyIsIl9hd2Vib29raW5nU2V0dGluZ3MiLCJQb3B1cCIsInJlcXVpcmUiLCJBd2VCb29raW5nIiwiXyIsImV4dGVuZCIsImluaXQiLCJzZWxmIiwiZWFjaCIsImRhdGEiLCJ0cmFucyIsImNvbnRleHQiLCJzdHJpbmdzIiwiYWpheFN1Ym1pdCIsImZvcm0iLCJhY3Rpb24iLCJzZXJpYWxpemUiLCJoYXNoIiwiYWRkQ2xhc3MiLCJ3cCIsImFqYXgiLCJwb3N0IiwiYWx3YXlzIiwicmVtb3ZlQ2xhc3MiLCJUaGVBd2VCb29raW5nIiwiVXRpbHMiLCJlbCIsInRhcmdldCIsImdldFNlbGVjdG9yRnJvbUVsZW1lbnQiLCJzZXR1cCIsIm9uIiwicHJveHkiLCJvcGVuIiwiY2xvc2UiLCJlIiwicHJldmVudERlZmF1bHQiLCJkaWFsb2ciLCJtb2RhbCIsIndpZHRoIiwiaGVpZ2h0IiwiYXV0b09wZW4iLCJkcmFnZ2FibGUiLCJyZXNpemFibGUiLCJjbG9zZU9uRXNjYXBlIiwiZGlhbG9nQ2xhc3MiLCJwb3NpdGlvbiIsImF0IiwiY3NzIiwib3ZlcmZsb3ciLCJiZWZvcmVDbG9zZSIsImV2ZW50IiwidWkiLCJtb2R1bGUiLCJleHBvcnRzIiwic2VsZWN0b3IiLCJnZXRBdHRy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O0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7OztBQ25RQSx5QyIsImZpbGUiOiIvanMvYWRtaW4vYXdlYm9va2luZy5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDApO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIGU3ZjE4ZjNkMTA1NzM4MzQzY2M0IiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBzZXR0aW5ncyA9IHdpbmRvdy5fYXdlYm9va2luZ1NldHRpbmdzIHx8IHt9O1xuXG5jb25zdCBQb3B1cCA9IHJlcXVpcmUoJy4vdXRpbHMvcG9wdXAuanMnKTtcblxuY29uc3QgQXdlQm9va2luZyA9IF8uZXh0ZW5kKHNldHRpbmdzLCB7XG4gIC8qKlxuICAgKiBJbml0IHRoZSBBd2VCb29raW5nXG4gICAqL1xuICBpbml0KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgLy8gSW5pdCB0aGUgcG9wdXAsIHVzZSBqcXVlcnktdWktcG9wdXAuXG4gICAgJCgnW2RhdGEtdG9nZ2xlPVwiYXdlYm9va2luZy1wb3B1cFwiXScpLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAkKHRoaXMpLmRhdGEoJ2F3ZWJvb2tpbmctcG9wdXAnLCBuZXcgUG9wdXAodGhpcykpO1xuICAgIH0pO1xuICB9LFxuXG4gIC8qKlxuICAgKiBHZXQgYSB0cmFuc2xhdG9yIHN0cmluZ1xuICAgKi9cbiAgdHJhbnMoY29udGV4dCkge1xuICAgIHJldHVybiB0aGlzLnN0cmluZ3NbY29udGV4dF0gPyB0aGlzLnN0cmluZ3NbY29udGV4dF0gOiAnJztcbiAgfSxcblxuICAvKipcbiAgICogTWFrZSBmb3JtIGFqYXggcmVxdWVzdC5cbiAgICovXG4gIGFqYXhTdWJtaXQoZm9ybSwgYWN0aW9uKSB7XG4gICAgY29uc3Qgc2VyaWFsaXplID0gcmVxdWlyZSgnZm9ybS1zZXJpYWxpemUnKTtcbiAgICBjb25zdCBkYXRhID0gc2VyaWFsaXplKGZvcm0sIHsgaGFzaDogdHJ1ZSB9KTtcblxuICAgIC8vIEFkZCAuYWpheC1sb2FkaW5nIGNsYXNzIGluIHRvIHRoZSBmb3JtLlxuICAgICQoZm9ybSkuYWRkQ2xhc3MoJ2FqYXgtbG9hZGluZycpO1xuXG4gICAgcmV0dXJuIHdwLmFqYXgucG9zdChhY3Rpb24sIGRhdGEpXG4gICAgICAuYWx3YXlzKGZ1bmN0aW9uKCkge1xuICAgICAgICAkKGZvcm0pLnJlbW92ZUNsYXNzKCdhamF4LWxvYWRpbmcnKTtcbiAgICAgIH0pO1xuICB9LFxufSk7XG5cbiQoZnVuY3Rpb24oKSB7XG4gIEF3ZUJvb2tpbmcuaW5pdCgpO1xufSk7XG5cbndpbmRvdy5UaGVBd2VCb29raW5nID0gQXdlQm9va2luZztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi9hd2Vib29raW5nLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBVdGlscyA9IHJlcXVpcmUoJy4vdXRpbHMuanMnKTtcblxuY2xhc3MgUG9wdXAge1xuICAvKipcbiAgICogV3JhcHBlciB0aGUganF1ZXJ5LXVpLXBvcHVwLlxuICAgKi9cbiAgY29uc3RydWN0b3IoZWwpIHtcbiAgICB0aGlzLmVsID0gZWw7XG4gICAgdGhpcy50YXJnZXQgPSBVdGlscy5nZXRTZWxlY3RvckZyb21FbGVtZW50KGVsKTtcblxuICAgIGlmICh0aGlzLnRhcmdldCkge1xuICAgICAgdGhpcy5zZXR1cCgpO1xuXG4gICAgICAkKHRoaXMuZWwpLm9uKCdjbGljaycsICQucHJveHkodGhpcy5vcGVuLCB0aGlzKSk7XG4gICAgICAkKHRoaXMudGFyZ2V0KS5vbignY2xpY2snLCAnW2RhdGEtZGlzbWlzcz1cImF3ZWJvb2tpbmctcG9wdXBcIl0nLCAkLnByb3h5KHRoaXMuY2xvc2UsIHRoaXMpKTtcbiAgICB9XG4gIH1cblxuICBvcGVuKGUpIHtcbiAgICBlICYmIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAkKHRoaXMudGFyZ2V0KS5kaWFsb2coJ29wZW4nKTtcbiAgfVxuXG4gIGNsb3NlKGUpIHtcbiAgICBlICYmIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAkKHRoaXMudGFyZ2V0KS5kaWFsb2coJ2Nsb3NlJyk7XG4gIH1cblxuICBzZXR1cCgpIHtcbiAgICBpZiAoJCh0aGlzLnRhcmdldCkuZGlhbG9nKCdpbnN0YW5jZScpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgJCh0aGlzLnRhcmdldCkuZGlhbG9nKHtcbiAgICAgIG1vZGFsOiB0cnVlLFxuICAgICAgd2lkdGg6ICdhdXRvJyxcbiAgICAgIGhlaWdodDogJ2F1dG8nLFxuICAgICAgYXV0b09wZW46IGZhbHNlLFxuICAgICAgZHJhZ2dhYmxlOiBmYWxzZSxcbiAgICAgIHJlc2l6YWJsZTogZmFsc2UsXG4gICAgICBjbG9zZU9uRXNjYXBlOiB0cnVlLFxuICAgICAgZGlhbG9nQ2xhc3M6ICd3cC1kaWFsb2cgYXdlYm9va2luZy1kaWFsb2cnLFxuICAgICAgcG9zaXRpb246IHsgYXQ6ICdjZW50ZXIgdG9wKzM1JScgfSxcbiAgICAgIG9wZW46IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgJCgnYm9keScpLmNzcyh7IG92ZXJmbG93OiAnaGlkZGVuJyB9KTtcbiAgICAgIH0sXG4gICAgICBiZWZvcmVDbG9zZTogZnVuY3Rpb24oZXZlbnQsIHVpKSB7XG4gICAgICAgICQoJ2JvZHknKS5jc3MoeyBvdmVyZmxvdzogJ2luaGVyaXQnIH0pO1xuICAgICB9XG4gICAgfSk7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBQb3B1cDtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9wb3B1cC5qcyIsInZhciAkID0gd2luZG93LmpRdWVyeTtcblxuY29uc3QgVXRpbHMgPSB7XG5cbiAgZ2V0U2VsZWN0b3JGcm9tRWxlbWVudChlbCkge1xuICAgIGxldCBzZWxlY3RvciA9IGVsLmdldEF0dHJpYnV0ZSgnZGF0YS10YXJnZXQnKTtcblxuICAgIGlmICghc2VsZWN0b3IgfHwgc2VsZWN0b3IgPT09ICcjJykge1xuICAgICAgc2VsZWN0b3IgPSBlbC5nZXRBdHRyaWJ1dGUoJ2hyZWYnKSB8fCAnJztcbiAgICB9XG5cbiAgICB0cnkge1xuICAgICAgY29uc3QgJHNlbGVjdG9yID0gJChzZWxlY3Rvcik7XG4gICAgICByZXR1cm4gJHNlbGVjdG9yLmxlbmd0aCA+IDAgPyBzZWxlY3RvciA6IG51bGw7XG4gICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgIHJldHVybiBudWxsO1xuICAgIH1cbiAgfSxcblxufTtcblxubW9kdWxlLmV4cG9ydHMgPSBVdGlscztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy91dGlscy5qcyIsIi8vIGdldCBzdWNjZXNzZnVsIGNvbnRyb2wgZnJvbSBmb3JtIGFuZCBhc3NlbWJsZSBpbnRvIG9iamVjdFxuLy8gaHR0cDovL3d3dy53My5vcmcvVFIvaHRtbDQwMS9pbnRlcmFjdC9mb3Jtcy5odG1sI2gtMTcuMTMuMlxuXG4vLyB0eXBlcyB3aGljaCBpbmRpY2F0ZSBhIHN1Ym1pdCBhY3Rpb24gYW5kIGFyZSBub3Qgc3VjY2Vzc2Z1bCBjb250cm9sc1xuLy8gdGhlc2Ugd2lsbCBiZSBpZ25vcmVkXG52YXIga19yX3N1Ym1pdHRlciA9IC9eKD86c3VibWl0fGJ1dHRvbnxpbWFnZXxyZXNldHxmaWxlKSQvaTtcblxuLy8gbm9kZSBuYW1lcyB3aGljaCBjb3VsZCBiZSBzdWNjZXNzZnVsIGNvbnRyb2xzXG52YXIga19yX3N1Y2Nlc3NfY29udHJscyA9IC9eKD86aW5wdXR8c2VsZWN0fHRleHRhcmVhfGtleWdlbikvaTtcblxuLy8gTWF0Y2hlcyBicmFja2V0IG5vdGF0aW9uLlxudmFyIGJyYWNrZXRzID0gLyhcXFtbXlxcW1xcXV0qXFxdKS9nO1xuXG4vLyBzZXJpYWxpemVzIGZvcm0gZmllbGRzXG4vLyBAcGFyYW0gZm9ybSBNVVNUIGJlIGFuIEhUTUxGb3JtIGVsZW1lbnRcbi8vIEBwYXJhbSBvcHRpb25zIGlzIGFuIG9wdGlvbmFsIGFyZ3VtZW50IHRvIGNvbmZpZ3VyZSB0aGUgc2VyaWFsaXphdGlvbi4gRGVmYXVsdCBvdXRwdXRcbi8vIHdpdGggbm8gb3B0aW9ucyBzcGVjaWZpZWQgaXMgYSB1cmwgZW5jb2RlZCBzdHJpbmdcbi8vICAgIC0gaGFzaDogW3RydWUgfCBmYWxzZV0gQ29uZmlndXJlIHRoZSBvdXRwdXQgdHlwZS4gSWYgdHJ1ZSwgdGhlIG91dHB1dCB3aWxsXG4vLyAgICBiZSBhIGpzIG9iamVjdC5cbi8vICAgIC0gc2VyaWFsaXplcjogW2Z1bmN0aW9uXSBPcHRpb25hbCBzZXJpYWxpemVyIGZ1bmN0aW9uIHRvIG92ZXJyaWRlIHRoZSBkZWZhdWx0IG9uZS5cbi8vICAgIFRoZSBmdW5jdGlvbiB0YWtlcyAzIGFyZ3VtZW50cyAocmVzdWx0LCBrZXksIHZhbHVlKSBhbmQgc2hvdWxkIHJldHVybiBuZXcgcmVzdWx0XG4vLyAgICBoYXNoIGFuZCB1cmwgZW5jb2RlZCBzdHIgc2VyaWFsaXplcnMgYXJlIHByb3ZpZGVkIHdpdGggdGhpcyBtb2R1bGVcbi8vICAgIC0gZGlzYWJsZWQ6IFt0cnVlIHwgZmFsc2VdLiBJZiB0cnVlIHNlcmlhbGl6ZSBkaXNhYmxlZCBmaWVsZHMuXG4vLyAgICAtIGVtcHR5OiBbdHJ1ZSB8IGZhbHNlXS4gSWYgdHJ1ZSBzZXJpYWxpemUgZW1wdHkgZmllbGRzXG5mdW5jdGlvbiBzZXJpYWxpemUoZm9ybSwgb3B0aW9ucykge1xuICAgIGlmICh0eXBlb2Ygb3B0aW9ucyAhPSAnb2JqZWN0Jykge1xuICAgICAgICBvcHRpb25zID0geyBoYXNoOiAhIW9wdGlvbnMgfTtcbiAgICB9XG4gICAgZWxzZSBpZiAob3B0aW9ucy5oYXNoID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgb3B0aW9ucy5oYXNoID0gdHJ1ZTtcbiAgICB9XG5cbiAgICB2YXIgcmVzdWx0ID0gKG9wdGlvbnMuaGFzaCkgPyB7fSA6ICcnO1xuICAgIHZhciBzZXJpYWxpemVyID0gb3B0aW9ucy5zZXJpYWxpemVyIHx8ICgob3B0aW9ucy5oYXNoKSA/IGhhc2hfc2VyaWFsaXplciA6IHN0cl9zZXJpYWxpemUpO1xuXG4gICAgdmFyIGVsZW1lbnRzID0gZm9ybSAmJiBmb3JtLmVsZW1lbnRzID8gZm9ybS5lbGVtZW50cyA6IFtdO1xuXG4gICAgLy9PYmplY3Qgc3RvcmUgZWFjaCByYWRpbyBhbmQgc2V0IGlmIGl0J3MgZW1wdHkgb3Igbm90XG4gICAgdmFyIHJhZGlvX3N0b3JlID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcblxuICAgIGZvciAodmFyIGk9MCA7IGk8ZWxlbWVudHMubGVuZ3RoIDsgKytpKSB7XG4gICAgICAgIHZhciBlbGVtZW50ID0gZWxlbWVudHNbaV07XG5cbiAgICAgICAgLy8gaW5nb3JlIGRpc2FibGVkIGZpZWxkc1xuICAgICAgICBpZiAoKCFvcHRpb25zLmRpc2FibGVkICYmIGVsZW1lbnQuZGlzYWJsZWQpIHx8ICFlbGVtZW50Lm5hbWUpIHtcbiAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICB9XG4gICAgICAgIC8vIGlnbm9yZSBhbnlodGluZyB0aGF0IGlzIG5vdCBjb25zaWRlcmVkIGEgc3VjY2VzcyBmaWVsZFxuICAgICAgICBpZiAoIWtfcl9zdWNjZXNzX2NvbnRybHMudGVzdChlbGVtZW50Lm5vZGVOYW1lKSB8fFxuICAgICAgICAgICAga19yX3N1Ym1pdHRlci50ZXN0KGVsZW1lbnQudHlwZSkpIHtcbiAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICB9XG5cbiAgICAgICAgdmFyIGtleSA9IGVsZW1lbnQubmFtZTtcbiAgICAgICAgdmFyIHZhbCA9IGVsZW1lbnQudmFsdWU7XG5cbiAgICAgICAgLy8gd2UgY2FuJ3QganVzdCB1c2UgZWxlbWVudC52YWx1ZSBmb3IgY2hlY2tib3hlcyBjYXVzZSBzb21lIGJyb3dzZXJzIGxpZSB0byB1c1xuICAgICAgICAvLyB0aGV5IHNheSBcIm9uXCIgZm9yIHZhbHVlIHdoZW4gdGhlIGJveCBpc24ndCBjaGVja2VkXG4gICAgICAgIGlmICgoZWxlbWVudC50eXBlID09PSAnY2hlY2tib3gnIHx8IGVsZW1lbnQudHlwZSA9PT0gJ3JhZGlvJykgJiYgIWVsZW1lbnQuY2hlY2tlZCkge1xuICAgICAgICAgICAgdmFsID0gdW5kZWZpbmVkO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSWYgd2Ugd2FudCBlbXB0eSBlbGVtZW50c1xuICAgICAgICBpZiAob3B0aW9ucy5lbXB0eSkge1xuICAgICAgICAgICAgLy8gZm9yIGNoZWNrYm94XG4gICAgICAgICAgICBpZiAoZWxlbWVudC50eXBlID09PSAnY2hlY2tib3gnICYmICFlbGVtZW50LmNoZWNrZWQpIHtcbiAgICAgICAgICAgICAgICB2YWwgPSAnJztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gZm9yIHJhZGlvXG4gICAgICAgICAgICBpZiAoZWxlbWVudC50eXBlID09PSAncmFkaW8nKSB7XG4gICAgICAgICAgICAgICAgaWYgKCFyYWRpb19zdG9yZVtlbGVtZW50Lm5hbWVdICYmICFlbGVtZW50LmNoZWNrZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgcmFkaW9fc3RvcmVbZWxlbWVudC5uYW1lXSA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBlbHNlIGlmIChlbGVtZW50LmNoZWNrZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgcmFkaW9fc3RvcmVbZWxlbWVudC5uYW1lXSA9IHRydWU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBpZiBvcHRpb25zIGVtcHR5IGlzIHRydWUsIGNvbnRpbnVlIG9ubHkgaWYgaXRzIHJhZGlvXG4gICAgICAgICAgICBpZiAodmFsID09IHVuZGVmaW5lZCAmJiBlbGVtZW50LnR5cGUgPT0gJ3JhZGlvJykge1xuICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgLy8gdmFsdWUtbGVzcyBmaWVsZHMgYXJlIGlnbm9yZWQgdW5sZXNzIG9wdGlvbnMuZW1wdHkgaXMgdHJ1ZVxuICAgICAgICAgICAgaWYgKCF2YWwpIHtcbiAgICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIC8vIG11bHRpIHNlbGVjdCBib3hlc1xuICAgICAgICBpZiAoZWxlbWVudC50eXBlID09PSAnc2VsZWN0LW11bHRpcGxlJykge1xuICAgICAgICAgICAgdmFsID0gW107XG5cbiAgICAgICAgICAgIHZhciBzZWxlY3RPcHRpb25zID0gZWxlbWVudC5vcHRpb25zO1xuICAgICAgICAgICAgdmFyIGlzU2VsZWN0ZWRPcHRpb25zID0gZmFsc2U7XG4gICAgICAgICAgICBmb3IgKHZhciBqPTAgOyBqPHNlbGVjdE9wdGlvbnMubGVuZ3RoIDsgKytqKSB7XG4gICAgICAgICAgICAgICAgdmFyIG9wdGlvbiA9IHNlbGVjdE9wdGlvbnNbal07XG4gICAgICAgICAgICAgICAgdmFyIGFsbG93ZWRFbXB0eSA9IG9wdGlvbnMuZW1wdHkgJiYgIW9wdGlvbi52YWx1ZTtcbiAgICAgICAgICAgICAgICB2YXIgaGFzVmFsdWUgPSAob3B0aW9uLnZhbHVlIHx8IGFsbG93ZWRFbXB0eSk7XG4gICAgICAgICAgICAgICAgaWYgKG9wdGlvbi5zZWxlY3RlZCAmJiBoYXNWYWx1ZSkge1xuICAgICAgICAgICAgICAgICAgICBpc1NlbGVjdGVkT3B0aW9ucyA9IHRydWU7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gSWYgdXNpbmcgYSBoYXNoIHNlcmlhbGl6ZXIgYmUgc3VyZSB0byBhZGQgdGhlXG4gICAgICAgICAgICAgICAgICAgIC8vIGNvcnJlY3Qgbm90YXRpb24gZm9yIGFuIGFycmF5IGluIHRoZSBtdWx0aS1zZWxlY3RcbiAgICAgICAgICAgICAgICAgICAgLy8gY29udGV4dC4gSGVyZSB0aGUgbmFtZSBhdHRyaWJ1dGUgb24gdGhlIHNlbGVjdCBlbGVtZW50XG4gICAgICAgICAgICAgICAgICAgIC8vIG1pZ2h0IGJlIG1pc3NpbmcgdGhlIHRyYWlsaW5nIGJyYWNrZXQgcGFpci4gQm90aCBuYW1lc1xuICAgICAgICAgICAgICAgICAgICAvLyBcImZvb1wiIGFuZCBcImZvb1tdXCIgc2hvdWxkIGJlIGFycmF5cy5cbiAgICAgICAgICAgICAgICAgICAgaWYgKG9wdGlvbnMuaGFzaCAmJiBrZXkuc2xpY2Uoa2V5Lmxlbmd0aCAtIDIpICE9PSAnW10nKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSBzZXJpYWxpemVyKHJlc3VsdCwga2V5ICsgJ1tdJywgb3B0aW9uLnZhbHVlKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCA9IHNlcmlhbGl6ZXIocmVzdWx0LCBrZXksIG9wdGlvbi52YWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIFNlcmlhbGl6ZSBpZiBubyBzZWxlY3RlZCBvcHRpb25zIGFuZCBvcHRpb25zLmVtcHR5IGlzIHRydWVcbiAgICAgICAgICAgIGlmICghaXNTZWxlY3RlZE9wdGlvbnMgJiYgb3B0aW9ucy5lbXB0eSkge1xuICAgICAgICAgICAgICAgIHJlc3VsdCA9IHNlcmlhbGl6ZXIocmVzdWx0LCBrZXksICcnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgIH1cblxuICAgICAgICByZXN1bHQgPSBzZXJpYWxpemVyKHJlc3VsdCwga2V5LCB2YWwpO1xuICAgIH1cblxuICAgIC8vIENoZWNrIGZvciBhbGwgZW1wdHkgcmFkaW8gYnV0dG9ucyBhbmQgc2VyaWFsaXplIHRoZW0gd2l0aCBrZXk9XCJcIlxuICAgIGlmIChvcHRpb25zLmVtcHR5KSB7XG4gICAgICAgIGZvciAodmFyIGtleSBpbiByYWRpb19zdG9yZSkge1xuICAgICAgICAgICAgaWYgKCFyYWRpb19zdG9yZVtrZXldKSB7XG4gICAgICAgICAgICAgICAgcmVzdWx0ID0gc2VyaWFsaXplcihyZXN1bHQsIGtleSwgJycpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIHJlc3VsdDtcbn1cblxuZnVuY3Rpb24gcGFyc2Vfa2V5cyhzdHJpbmcpIHtcbiAgICB2YXIga2V5cyA9IFtdO1xuICAgIHZhciBwcmVmaXggPSAvXihbXlxcW1xcXV0qKS87XG4gICAgdmFyIGNoaWxkcmVuID0gbmV3IFJlZ0V4cChicmFja2V0cyk7XG4gICAgdmFyIG1hdGNoID0gcHJlZml4LmV4ZWMoc3RyaW5nKTtcblxuICAgIGlmIChtYXRjaFsxXSkge1xuICAgICAgICBrZXlzLnB1c2gobWF0Y2hbMV0pO1xuICAgIH1cblxuICAgIHdoaWxlICgobWF0Y2ggPSBjaGlsZHJlbi5leGVjKHN0cmluZykpICE9PSBudWxsKSB7XG4gICAgICAgIGtleXMucHVzaChtYXRjaFsxXSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIGtleXM7XG59XG5cbmZ1bmN0aW9uIGhhc2hfYXNzaWduKHJlc3VsdCwga2V5cywgdmFsdWUpIHtcbiAgICBpZiAoa2V5cy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgcmVzdWx0ID0gdmFsdWU7XG4gICAgICAgIHJldHVybiByZXN1bHQ7XG4gICAgfVxuXG4gICAgdmFyIGtleSA9IGtleXMuc2hpZnQoKTtcbiAgICB2YXIgYmV0d2VlbiA9IGtleS5tYXRjaCgvXlxcWyguKz8pXFxdJC8pO1xuXG4gICAgaWYgKGtleSA9PT0gJ1tdJykge1xuICAgICAgICByZXN1bHQgPSByZXN1bHQgfHwgW107XG5cbiAgICAgICAgaWYgKEFycmF5LmlzQXJyYXkocmVzdWx0KSkge1xuICAgICAgICAgICAgcmVzdWx0LnB1c2goaGFzaF9hc3NpZ24obnVsbCwga2V5cywgdmFsdWUpKTtcbiAgICAgICAgfVxuICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgIC8vIFRoaXMgbWlnaHQgYmUgdGhlIHJlc3VsdCBvZiBiYWQgbmFtZSBhdHRyaWJ1dGVzIGxpa2UgXCJbXVtmb29dXCIsXG4gICAgICAgICAgICAvLyBpbiB0aGlzIGNhc2UgdGhlIG9yaWdpbmFsIGByZXN1bHRgIG9iamVjdCB3aWxsIGFscmVhZHkgYmVcbiAgICAgICAgICAgIC8vIGFzc2lnbmVkIHRvIGFuIG9iamVjdCBsaXRlcmFsLiBSYXRoZXIgdGhhbiBjb2VyY2UgdGhlIG9iamVjdCB0b1xuICAgICAgICAgICAgLy8gYW4gYXJyYXksIG9yIGNhdXNlIGFuIGV4Y2VwdGlvbiB0aGUgYXR0cmlidXRlIFwiX3ZhbHVlc1wiIGlzXG4gICAgICAgICAgICAvLyBhc3NpZ25lZCBhcyBhbiBhcnJheS5cbiAgICAgICAgICAgIHJlc3VsdC5fdmFsdWVzID0gcmVzdWx0Ll92YWx1ZXMgfHwgW107XG4gICAgICAgICAgICByZXN1bHQuX3ZhbHVlcy5wdXNoKGhhc2hfYXNzaWduKG51bGwsIGtleXMsIHZhbHVlKSk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gcmVzdWx0O1xuICAgIH1cblxuICAgIC8vIEtleSBpcyBhbiBhdHRyaWJ1dGUgbmFtZSBhbmQgY2FuIGJlIGFzc2lnbmVkIGRpcmVjdGx5LlxuICAgIGlmICghYmV0d2Vlbikge1xuICAgICAgICByZXN1bHRba2V5XSA9IGhhc2hfYXNzaWduKHJlc3VsdFtrZXldLCBrZXlzLCB2YWx1ZSk7XG4gICAgfVxuICAgIGVsc2Uge1xuICAgICAgICB2YXIgc3RyaW5nID0gYmV0d2VlblsxXTtcbiAgICAgICAgLy8gK3ZhciBjb252ZXJ0cyB0aGUgdmFyaWFibGUgaW50byBhIG51bWJlclxuICAgICAgICAvLyBiZXR0ZXIgdGhhbiBwYXJzZUludCBiZWNhdXNlIGl0IGRvZXNuJ3QgdHJ1bmNhdGUgYXdheSB0cmFpbGluZ1xuICAgICAgICAvLyBsZXR0ZXJzIGFuZCBhY3R1YWxseSBmYWlscyBpZiB3aG9sZSB0aGluZyBpcyBub3QgYSBudW1iZXJcbiAgICAgICAgdmFyIGluZGV4ID0gK3N0cmluZztcblxuICAgICAgICAvLyBJZiB0aGUgY2hhcmFjdGVycyBiZXR3ZWVuIHRoZSBicmFja2V0cyBpcyBub3QgYSBudW1iZXIgaXQgaXMgYW5cbiAgICAgICAgLy8gYXR0cmlidXRlIG5hbWUgYW5kIGNhbiBiZSBhc3NpZ25lZCBkaXJlY3RseS5cbiAgICAgICAgaWYgKGlzTmFOKGluZGV4KSkge1xuICAgICAgICAgICAgcmVzdWx0ID0gcmVzdWx0IHx8IHt9O1xuICAgICAgICAgICAgcmVzdWx0W3N0cmluZ10gPSBoYXNoX2Fzc2lnbihyZXN1bHRbc3RyaW5nXSwga2V5cywgdmFsdWUpO1xuICAgICAgICB9XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgcmVzdWx0ID0gcmVzdWx0IHx8IFtdO1xuICAgICAgICAgICAgcmVzdWx0W2luZGV4XSA9IGhhc2hfYXNzaWduKHJlc3VsdFtpbmRleF0sIGtleXMsIHZhbHVlKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiByZXN1bHQ7XG59XG5cbi8vIE9iamVjdC9oYXNoIGVuY29kaW5nIHNlcmlhbGl6ZXIuXG5mdW5jdGlvbiBoYXNoX3NlcmlhbGl6ZXIocmVzdWx0LCBrZXksIHZhbHVlKSB7XG4gICAgdmFyIG1hdGNoZXMgPSBrZXkubWF0Y2goYnJhY2tldHMpO1xuXG4gICAgLy8gSGFzIGJyYWNrZXRzPyBVc2UgdGhlIHJlY3Vyc2l2ZSBhc3NpZ25tZW50IGZ1bmN0aW9uIHRvIHdhbGsgdGhlIGtleXMsXG4gICAgLy8gY29uc3RydWN0IGFueSBtaXNzaW5nIG9iamVjdHMgaW4gdGhlIHJlc3VsdCB0cmVlIGFuZCBtYWtlIHRoZSBhc3NpZ25tZW50XG4gICAgLy8gYXQgdGhlIGVuZCBvZiB0aGUgY2hhaW4uXG4gICAgaWYgKG1hdGNoZXMpIHtcbiAgICAgICAgdmFyIGtleXMgPSBwYXJzZV9rZXlzKGtleSk7XG4gICAgICAgIGhhc2hfYXNzaWduKHJlc3VsdCwga2V5cywgdmFsdWUpO1xuICAgIH1cbiAgICBlbHNlIHtcbiAgICAgICAgLy8gTm9uIGJyYWNrZXQgbm90YXRpb24gY2FuIG1ha2UgYXNzaWdubWVudHMgZGlyZWN0bHkuXG4gICAgICAgIHZhciBleGlzdGluZyA9IHJlc3VsdFtrZXldO1xuXG4gICAgICAgIC8vIElmIHRoZSB2YWx1ZSBoYXMgYmVlbiBhc3NpZ25lZCBhbHJlYWR5IChmb3IgaW5zdGFuY2Ugd2hlbiBhIHJhZGlvIGFuZFxuICAgICAgICAvLyBhIGNoZWNrYm94IGhhdmUgdGhlIHNhbWUgbmFtZSBhdHRyaWJ1dGUpIGNvbnZlcnQgdGhlIHByZXZpb3VzIHZhbHVlXG4gICAgICAgIC8vIGludG8gYW4gYXJyYXkgYmVmb3JlIHB1c2hpbmcgaW50byBpdC5cbiAgICAgICAgLy9cbiAgICAgICAgLy8gTk9URTogSWYgdGhpcyByZXF1aXJlbWVudCB3ZXJlIHJlbW92ZWQgYWxsIGhhc2ggY3JlYXRpb24gYW5kXG4gICAgICAgIC8vIGFzc2lnbm1lbnQgY291bGQgZ28gdGhyb3VnaCBgaGFzaF9hc3NpZ25gLlxuICAgICAgICBpZiAoZXhpc3RpbmcpIHtcbiAgICAgICAgICAgIGlmICghQXJyYXkuaXNBcnJheShleGlzdGluZykpIHtcbiAgICAgICAgICAgICAgICByZXN1bHRba2V5XSA9IFsgZXhpc3RpbmcgXTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmVzdWx0W2tleV0ucHVzaCh2YWx1ZSk7XG4gICAgICAgIH1cbiAgICAgICAgZWxzZSB7XG4gICAgICAgICAgICByZXN1bHRba2V5XSA9IHZhbHVlO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIHJlc3VsdDtcbn1cblxuLy8gdXJsZm9ybSBlbmNvZGluZyBzZXJpYWxpemVyXG5mdW5jdGlvbiBzdHJfc2VyaWFsaXplKHJlc3VsdCwga2V5LCB2YWx1ZSkge1xuICAgIC8vIGVuY29kZSBuZXdsaW5lcyBhcyBcXHJcXG4gY2F1c2UgdGhlIGh0bWwgc3BlYyBzYXlzIHNvXG4gICAgdmFsdWUgPSB2YWx1ZS5yZXBsYWNlKC8oXFxyKT9cXG4vZywgJ1xcclxcbicpO1xuICAgIHZhbHVlID0gZW5jb2RlVVJJQ29tcG9uZW50KHZhbHVlKTtcblxuICAgIC8vIHNwYWNlcyBzaG91bGQgYmUgJysnIHJhdGhlciB0aGFuICclMjAnLlxuICAgIHZhbHVlID0gdmFsdWUucmVwbGFjZSgvJTIwL2csICcrJyk7XG4gICAgcmV0dXJuIHJlc3VsdCArIChyZXN1bHQgPyAnJicgOiAnJykgKyBlbmNvZGVVUklDb21wb25lbnQoa2V5KSArICc9JyArIHZhbHVlO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IHNlcmlhbGl6ZTtcblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vbm9kZV9tb2R1bGVzL2Zvcm0tc2VyaWFsaXplL2luZGV4LmpzXG4vLyBtb2R1bGUgaWQgPSA0XG4vLyBtb2R1bGUgY2h1bmtzID0gMCIsIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9hc3NldHMvc2Fzcy9hZG1pbi5zY3NzXG4vLyBtb2R1bGUgaWQgPSA1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCJdLCJzb3VyY2VSb290IjoiIn0=