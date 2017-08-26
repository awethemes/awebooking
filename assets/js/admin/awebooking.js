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
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
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
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(2);
module.exports = __webpack_require__(7);


/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

var $ = window.jQuery;
var settings = window._awebookingSettings || {};

var AweBooking = _.extend(settings, {
  Popup: __webpack_require__(3),
  ToggleClass: __webpack_require__(4),
  RangeDatepicker: __webpack_require__(5),

  /**
   * Init the AweBooking
   */
  init: function init() {
    var self = this;

    // Init the popup, use jquery-ui-popup.
    $('[data-toggle="awebooking-popup"]').each(function () {
      $(this).data('awebooking-popup', new self.Popup(this));
    });

    $('[data-init="awebooking-toggle"]').each(function () {
      $(this).data('awebooking-toggle', new self.ToggleClass(this));
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
    var serialize = __webpack_require__(6);
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
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(0);

var Popup = function () {
  /**
   * Wrapper the jquery-ui-popup.
   */
  function Popup(el) {
    _classCallCheck(this, Popup);

    this.el = el;
    this.target = Utils.getSelectorFromElement(el);

    if (this.target) {
      Popup.setup(this.target);

      $(this.el).on('click', this.open.bind(this));
      $(this.target).on('click', '[data-dismiss="awebooking-popup"]', this.close.bind(this));
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
  }], [{
    key: 'setup',
    value: function setup(target) {
      var $target = $(target);
      if (!$target.length) {
        return;
      }

      if ($target.dialog('instance')) {
        return;
      }

      var _triggerResize = function _triggerResize() {
        if ($target.dialog('isOpen')) {
          $target.dialog('option', 'position', { my: 'center', at: 'center top+25%', of: window });
        }
      };

      var dialog = $target.dialog({
        modal: true,
        width: 'auto',
        height: 'auto',
        autoOpen: false,
        draggable: false,
        resizable: false,
        closeOnEscape: true,
        dialogClass: 'wp-dialog awebooking-dialog',
        position: { my: 'center', at: 'center top+25%', of: window },
        open: function open() {
          $('body').css({ overflow: 'hidden' });
        },
        beforeClose: function beforeClose(event, ui) {
          $('body').css({ overflow: 'inherit' });
        }
      });

      $(window).on('resize', _.debounce(_triggerResize, 250));

      return dialog;
    }
  }]);

  return Popup;
}();

module.exports = Popup;

/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(0);

var ToggleClass = function () {
  function ToggleClass(el) {
    _classCallCheck(this, ToggleClass);

    this.el = el;
    this.target = Utils.getSelectorFromElement(el);

    if (!this.target) {
      this.target = $(el).parent().children('.awebooking-main-toggle')[0];
    }

    if (this.target) {
      $(this.el).on('click', this.toggleClass.bind(this));
      $(document).on('click', this.removeClass.bind(this));
    }
  }

  _createClass(ToggleClass, [{
    key: 'toggleClass',
    value: function toggleClass(e) {
      e && e.preventDefault();
      $(this.target).parent().toggleClass('active');
    }
  }, {
    key: 'removeClass',
    value: function removeClass(e) {
      if (e && $.contains($(this.target).parent()[0], e.target)) {
        return;
      }

      $(this.target).parent().removeClass('active');
    }
  }]);

  return ToggleClass;
}();

module.exports = ToggleClass;

/***/ }),
/* 5 */
/***/ (function(module, exports) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var DATE_FORMAT = 'yy-mm-dd';

var RangeDatepicker = function () {
  function RangeDatepicker(fromDate, toDate) {
    _classCallCheck(this, RangeDatepicker);

    this.toDate = toDate;
    this.fromDate = fromDate;
  }

  _createClass(RangeDatepicker, [{
    key: 'init',
    value: function init() {
      var beforeShowCallback = function beforeShowCallback() {
        $('#ui-datepicker-div').addClass('cmb2-element');
      };

      var closeCallback = function closeCallback() {
        $('#ui-datepicker-div').addClass('cmb2-element');
      };

      $(this.fromDate).datepicker({
        onClose: closeCallback,
        beforeShow: beforeShowCallback,
        dateFormat: DATE_FORMAT
      }).on('change', this.applyFromChange.bind(this));

      $(this.toDate).datepicker({
        onClose: closeCallback,
        beforeShow: beforeShowCallback,
        dateFormat: DATE_FORMAT
      }).on('change', this.applyToChange.bind(this));

      this.applyToChange();
      this.applyFromChange();
    }
  }, {
    key: 'applyFromChange',
    value: function applyFromChange() {
      try {
        var minDate = $.datepicker.parseDate(DATE_FORMAT, $(this.fromDate).val());
        minDate.setDate(minDate.getDate() + 1);

        $(this.toDate).datepicker('option', 'minDate', minDate);
      } catch (e) {}
    }
  }, {
    key: 'applyToChange',
    value: function applyToChange() {
      try {
        var maxDate = $.datepicker.parseDate(DATE_FORMAT, $(this.toDate).val());
        $(this.fromDate).datepicker('option', 'maxDate', maxDate);
      } catch (e) {}
    }
  }]);

  return RangeDatepicker;
}();

module.exports = RangeDatepicker;

/***/ }),
/* 6 */
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
/* 7 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMmQzOWIxYzM5NGE3YTBiMjBhN2MiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3V0aWxzLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi9hd2Vib29raW5nLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9wb3B1cC5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvdG9nZ2xlLWNsYXNzLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9yYW5nZS1kYXRlcGlja2VyLmpzIiwid2VicGFjazovLy8uL25vZGVfbW9kdWxlcy9mb3JtLXNlcmlhbGl6ZS9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvc2Fzcy9hZG1pbi5zY3NzP2Q5ZTYiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsImpRdWVyeSIsIlV0aWxzIiwiZ2V0U2VsZWN0b3JGcm9tRWxlbWVudCIsImVsIiwic2VsZWN0b3IiLCJnZXRBdHRyaWJ1dGUiLCIkc2VsZWN0b3IiLCJsZW5ndGgiLCJlcnJvciIsIm1vZHVsZSIsImV4cG9ydHMiLCJzZXR0aW5ncyIsIl9hd2Vib29raW5nU2V0dGluZ3MiLCJBd2VCb29raW5nIiwiXyIsImV4dGVuZCIsIlBvcHVwIiwicmVxdWlyZSIsIlRvZ2dsZUNsYXNzIiwiUmFuZ2VEYXRlcGlja2VyIiwiaW5pdCIsInNlbGYiLCJlYWNoIiwiZGF0YSIsInRyYW5zIiwiY29udGV4dCIsInN0cmluZ3MiLCJhamF4U3VibWl0IiwiZm9ybSIsImFjdGlvbiIsInNlcmlhbGl6ZSIsImhhc2giLCJhZGRDbGFzcyIsIndwIiwiYWpheCIsInBvc3QiLCJhbHdheXMiLCJyZW1vdmVDbGFzcyIsIlRoZUF3ZUJvb2tpbmciLCJ0YXJnZXQiLCJzZXR1cCIsIm9uIiwib3BlbiIsImJpbmQiLCJjbG9zZSIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImRpYWxvZyIsIiR0YXJnZXQiLCJfdHJpZ2dlclJlc2l6ZSIsIm15IiwiYXQiLCJvZiIsIm1vZGFsIiwid2lkdGgiLCJoZWlnaHQiLCJhdXRvT3BlbiIsImRyYWdnYWJsZSIsInJlc2l6YWJsZSIsImNsb3NlT25Fc2NhcGUiLCJkaWFsb2dDbGFzcyIsInBvc2l0aW9uIiwiY3NzIiwib3ZlcmZsb3ciLCJiZWZvcmVDbG9zZSIsImV2ZW50IiwidWkiLCJkZWJvdW5jZSIsInBhcmVudCIsImNoaWxkcmVuIiwidG9nZ2xlQ2xhc3MiLCJkb2N1bWVudCIsImNvbnRhaW5zIiwiREFURV9GT1JNQVQiLCJmcm9tRGF0ZSIsInRvRGF0ZSIsImJlZm9yZVNob3dDYWxsYmFjayIsImNsb3NlQ2FsbGJhY2siLCJkYXRlcGlja2VyIiwib25DbG9zZSIsImJlZm9yZVNob3ciLCJkYXRlRm9ybWF0IiwiYXBwbHlGcm9tQ2hhbmdlIiwiYXBwbHlUb0NoYW5nZSIsIm1pbkRhdGUiLCJwYXJzZURhdGUiLCJ2YWwiLCJzZXREYXRlIiwiZ2V0RGF0ZSIsIm1heERhdGUiXSwibWFwcGluZ3MiOiI7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7OztBQzdEQSxJQUFJQSxJQUFJQyxPQUFPQyxNQUFmOztBQUVBLElBQU1DLFFBQVE7QUFFWkMsd0JBRlksa0NBRVdDLEVBRlgs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QUNwREE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsbUJBQW1CO0FBQ25CO0FBQ0E7QUFDQTtBQUNBOztBQUVBLG9DQUFvQztBQUNwQzs7QUFFQTs7QUFFQTtBQUNBOztBQUVBLGtCQUFrQixvQkFBb0I7QUFDdEM7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSwwQkFBMEIseUJBQXlCO0FBQ25EO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7Ozs7QUNuUUEseUMiLCJmaWxlIjoiL2pzL2FkbWluL2F3ZWJvb2tpbmcuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAxKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAyZDM5YjFjMzk0YTdhMGIyMGE3YyIsInZhciAkID0gd2luZG93LmpRdWVyeTtcblxuY29uc3QgVXRpbHMgPSB7XG5cbiAgZ2V0U2VsZWN0b3JGcm9tRWxlbWVudChlbCkge1xuICAgIGxldCBzZWxlY3RvciA9IGVsLmdldEF0dHJpYnV0ZSgnZGF0YS10YXJnZXQnKTtcblxuICAgIGlmICghc2VsZWN0b3IgfHwgc2VsZWN0b3IgPT09ICcjJykge1xuICAgICAgc2VsZWN0b3IgPSBlbC5nZXRBdHRyaWJ1dGUoJ2hyZWYnKSB8fCAnJztcbiAgICB9XG5cbiAgICB0cnkge1xuICAgICAgY29uc3QgJHNlbGVjdG9yID0gJChzZWxlY3Rvcik7XG4gICAgICByZXR1cm4gJHNlbGVjdG9yLmxlbmd0aCA+IDAgPyBzZWxlY3RvciA6IG51bGw7XG4gICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgIHJldHVybiBudWxsO1xuICAgIH1cbiAgfSxcblxufTtcblxubW9kdWxlLmV4cG9ydHMgPSBVdGlscztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy91dGlscy5qcyIsImNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xuY29uc3Qgc2V0dGluZ3MgPSB3aW5kb3cuX2F3ZWJvb2tpbmdTZXR0aW5ncyB8fCB7fTtcblxuY29uc3QgQXdlQm9va2luZyA9IF8uZXh0ZW5kKHNldHRpbmdzLCB7XG4gIFBvcHVwOiByZXF1aXJlKCcuL3V0aWxzL3BvcHVwLmpzJyksXG4gIFRvZ2dsZUNsYXNzOiByZXF1aXJlKCcuL3V0aWxzL3RvZ2dsZS1jbGFzcy5qcycpLFxuICBSYW5nZURhdGVwaWNrZXI6IHJlcXVpcmUoJy4vdXRpbHMvcmFuZ2UtZGF0ZXBpY2tlci5qcycpLFxuXG4gIC8qKlxuICAgKiBJbml0IHRoZSBBd2VCb29raW5nXG4gICAqL1xuICBpbml0KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgLy8gSW5pdCB0aGUgcG9wdXAsIHVzZSBqcXVlcnktdWktcG9wdXAuXG4gICAgJCgnW2RhdGEtdG9nZ2xlPVwiYXdlYm9va2luZy1wb3B1cFwiXScpLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAkKHRoaXMpLmRhdGEoJ2F3ZWJvb2tpbmctcG9wdXAnLCBuZXcgc2VsZi5Qb3B1cCh0aGlzKSk7XG4gICAgfSk7XG5cbiAgICAkKCdbZGF0YS1pbml0PVwiYXdlYm9va2luZy10b2dnbGVcIl0nKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgJCh0aGlzKS5kYXRhKCdhd2Vib29raW5nLXRvZ2dsZScsIG5ldyBzZWxmLlRvZ2dsZUNsYXNzKHRoaXMpKTtcbiAgICB9KTtcbiAgfSxcblxuICAvKipcbiAgICogR2V0IGEgdHJhbnNsYXRvciBzdHJpbmdcbiAgICovXG4gIHRyYW5zKGNvbnRleHQpIHtcbiAgICByZXR1cm4gdGhpcy5zdHJpbmdzW2NvbnRleHRdID8gdGhpcy5zdHJpbmdzW2NvbnRleHRdIDogJyc7XG4gIH0sXG5cbiAgLyoqXG4gICAqIE1ha2UgZm9ybSBhamF4IHJlcXVlc3QuXG4gICAqL1xuICBhamF4U3VibWl0KGZvcm0sIGFjdGlvbikge1xuICAgIGNvbnN0IHNlcmlhbGl6ZSA9IHJlcXVpcmUoJ2Zvcm0tc2VyaWFsaXplJyk7XG4gICAgY29uc3QgZGF0YSA9IHNlcmlhbGl6ZShmb3JtLCB7IGhhc2g6IHRydWUgfSk7XG5cbiAgICAvLyBBZGQgLmFqYXgtbG9hZGluZyBjbGFzcyBpbiB0byB0aGUgZm9ybS5cbiAgICAkKGZvcm0pLmFkZENsYXNzKCdhamF4LWxvYWRpbmcnKTtcblxuICAgIHJldHVybiB3cC5hamF4LnBvc3QoYWN0aW9uLCBkYXRhKVxuICAgICAgLmFsd2F5cyhmdW5jdGlvbigpIHtcbiAgICAgICAgJChmb3JtKS5yZW1vdmVDbGFzcygnYWpheC1sb2FkaW5nJyk7XG4gICAgICB9KTtcbiAgfSxcbn0pO1xuXG4kKGZ1bmN0aW9uKCkge1xuICBBd2VCb29raW5nLmluaXQoKTtcbn0pO1xuXG53aW5kb3cuVGhlQXdlQm9va2luZyA9IEF3ZUJvb2tpbmc7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vYXdlYm9va2luZy5qcyIsImNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xuY29uc3QgVXRpbHMgPSByZXF1aXJlKCcuL3V0aWxzLmpzJyk7XG5cbmNsYXNzIFBvcHVwIHtcbiAgLyoqXG4gICAqIFdyYXBwZXIgdGhlIGpxdWVyeS11aS1wb3B1cC5cbiAgICovXG4gIGNvbnN0cnVjdG9yKGVsKSB7XG4gICAgdGhpcy5lbCA9IGVsO1xuICAgIHRoaXMudGFyZ2V0ID0gVXRpbHMuZ2V0U2VsZWN0b3JGcm9tRWxlbWVudChlbCk7XG5cbiAgICBpZiAodGhpcy50YXJnZXQpIHtcbiAgICAgIFBvcHVwLnNldHVwKHRoaXMudGFyZ2V0KTtcblxuICAgICAgJCh0aGlzLmVsKS5vbignY2xpY2snLCB0aGlzLm9wZW4uYmluZCh0aGlzKSk7XG4gICAgICAkKHRoaXMudGFyZ2V0KS5vbignY2xpY2snLCAnW2RhdGEtZGlzbWlzcz1cImF3ZWJvb2tpbmctcG9wdXBcIl0nLCB0aGlzLmNsb3NlLmJpbmQodGhpcykpO1xuICAgIH1cbiAgfVxuXG4gIG9wZW4oZSkge1xuICAgIGUgJiYgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICQodGhpcy50YXJnZXQpLmRpYWxvZygnb3BlbicpO1xuICB9XG5cbiAgY2xvc2UoZSkge1xuICAgIGUgJiYgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICQodGhpcy50YXJnZXQpLmRpYWxvZygnY2xvc2UnKTtcbiAgfVxuXG4gIHN0YXRpYyBzZXR1cCh0YXJnZXQpIHtcbiAgICBjb25zdCAkdGFyZ2V0ID0gJCh0YXJnZXQpO1xuICAgIGlmICghICR0YXJnZXQubGVuZ3RoKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKCR0YXJnZXQuZGlhbG9nKCdpbnN0YW5jZScpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgbGV0IF90cmlnZ2VyUmVzaXplID0gZnVuY3Rpb24oKSB7XG4gICAgICBpZiAoJHRhcmdldC5kaWFsb2coJ2lzT3BlbicpKSB7XG4gICAgICAgICR0YXJnZXQuZGlhbG9nKCdvcHRpb24nLCAncG9zaXRpb24nLCB7IG15OiAnY2VudGVyJywgYXQ6ICdjZW50ZXIgdG9wKzI1JScsIG9mOiB3aW5kb3cgfSk7XG4gICAgICB9XG4gICAgfVxuXG4gICAgbGV0IGRpYWxvZyA9ICR0YXJnZXQuZGlhbG9nKHtcbiAgICAgIG1vZGFsOiB0cnVlLFxuICAgICAgd2lkdGg6ICdhdXRvJyxcbiAgICAgIGhlaWdodDogJ2F1dG8nLFxuICAgICAgYXV0b09wZW46IGZhbHNlLFxuICAgICAgZHJhZ2dhYmxlOiBmYWxzZSxcbiAgICAgIHJlc2l6YWJsZTogZmFsc2UsXG4gICAgICBjbG9zZU9uRXNjYXBlOiB0cnVlLFxuICAgICAgZGlhbG9nQ2xhc3M6ICd3cC1kaWFsb2cgYXdlYm9va2luZy1kaWFsb2cnLFxuICAgICAgcG9zaXRpb246IHsgbXk6ICdjZW50ZXInLCBhdDogJ2NlbnRlciB0b3ArMjUlJywgb2Y6IHdpbmRvdyB9LFxuICAgICAgb3BlbjogZnVuY3Rpb24gKCkge1xuICAgICAgICAkKCdib2R5JykuY3NzKHsgb3ZlcmZsb3c6ICdoaWRkZW4nIH0pO1xuICAgICAgfSxcbiAgICAgIGJlZm9yZUNsb3NlOiBmdW5jdGlvbihldmVudCwgdWkpIHtcbiAgICAgICAgJCgnYm9keScpLmNzcyh7IG92ZXJmbG93OiAnaW5oZXJpdCcgfSk7XG4gICAgIH1cbiAgICB9KTtcblxuICAgICQod2luZG93KS5vbigncmVzaXplJywgXy5kZWJvdW5jZShfdHJpZ2dlclJlc2l6ZSwgMjUwKSk7XG5cbiAgICByZXR1cm4gZGlhbG9nO1xuICB9XG59XG5cbm1vZHVsZS5leHBvcnRzID0gUG9wdXA7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvcG9wdXAuanMiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IFV0aWxzID0gcmVxdWlyZSgnLi91dGlscy5qcycpO1xuXG5jbGFzcyBUb2dnbGVDbGFzcyB7XG5cbiAgY29uc3RydWN0b3IoZWwpIHtcbiAgICB0aGlzLmVsID0gZWw7XG4gICAgdGhpcy50YXJnZXQgPSBVdGlscy5nZXRTZWxlY3RvckZyb21FbGVtZW50KGVsKTtcblxuICAgIGlmICghdGhpcy50YXJnZXQpIHtcbiAgICAgIHRoaXMudGFyZ2V0ID0gJChlbCkucGFyZW50KCkuY2hpbGRyZW4oJy5hd2Vib29raW5nLW1haW4tdG9nZ2xlJylbMF07XG4gICAgfVxuXG4gICAgaWYgKHRoaXMudGFyZ2V0KSB7XG4gICAgICAkKHRoaXMuZWwpLm9uKCdjbGljaycsIHRoaXMudG9nZ2xlQ2xhc3MuYmluZCh0aGlzKSk7XG4gICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLnJlbW92ZUNsYXNzLmJpbmQodGhpcykpO1xuICAgIH1cbiAgfVxuXG4gIHRvZ2dsZUNsYXNzKGUpIHtcbiAgICBlICYmIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAkKHRoaXMudGFyZ2V0KS5wYXJlbnQoKS50b2dnbGVDbGFzcygnYWN0aXZlJyk7XG4gIH1cblxuICByZW1vdmVDbGFzcyhlKSB7XG4gICAgaWYgKGUgJiYgJC5jb250YWlucygkKHRoaXMudGFyZ2V0KS5wYXJlbnQoKVswXSwgZS50YXJnZXQpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgJCh0aGlzLnRhcmdldCkucGFyZW50KCkucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xuICB9XG59XG5cbm1vZHVsZS5leHBvcnRzID0gVG9nZ2xlQ2xhc3M7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvdG9nZ2xlLWNsYXNzLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBEQVRFX0ZPUk1BVCA9ICd5eS1tbS1kZCc7XG5cbmNsYXNzIFJhbmdlRGF0ZXBpY2tlciB7XG5cbiAgY29uc3RydWN0b3IoZnJvbURhdGUsIHRvRGF0ZSkge1xuICAgIHRoaXMudG9EYXRlID0gdG9EYXRlO1xuICAgIHRoaXMuZnJvbURhdGUgPSBmcm9tRGF0ZTtcbiAgfVxuXG4gIGluaXQoKSB7XG4gICAgbGV0IGJlZm9yZVNob3dDYWxsYmFjayA9IGZ1bmN0aW9uKCkge1xuICAgICAgJCgnI3VpLWRhdGVwaWNrZXItZGl2JykuYWRkQ2xhc3MoJ2NtYjItZWxlbWVudCcpO1xuICAgIH07XG5cbiAgICBsZXQgY2xvc2VDYWxsYmFjayA9IGZ1bmN0aW9uKCkge1xuICAgICAgJCgnI3VpLWRhdGVwaWNrZXItZGl2JykuYWRkQ2xhc3MoJ2NtYjItZWxlbWVudCcpO1xuICAgIH07XG5cbiAgICAkKHRoaXMuZnJvbURhdGUpLmRhdGVwaWNrZXIoe1xuICAgICAgb25DbG9zZTogY2xvc2VDYWxsYmFjayxcbiAgICAgIGJlZm9yZVNob3c6IGJlZm9yZVNob3dDYWxsYmFjayxcbiAgICAgIGRhdGVGb3JtYXQ6IERBVEVfRk9STUFULFxuICAgIH0pLm9uKCdjaGFuZ2UnLCB0aGlzLmFwcGx5RnJvbUNoYW5nZS5iaW5kKHRoaXMpKTtcblxuICAgICQodGhpcy50b0RhdGUpLmRhdGVwaWNrZXIoe1xuICAgICAgb25DbG9zZTogY2xvc2VDYWxsYmFjayxcbiAgICAgIGJlZm9yZVNob3c6IGJlZm9yZVNob3dDYWxsYmFjayxcbiAgICAgIGRhdGVGb3JtYXQ6IERBVEVfRk9STUFULFxuICAgIH0pLm9uKCdjaGFuZ2UnLCB0aGlzLmFwcGx5VG9DaGFuZ2UuYmluZCh0aGlzKSk7XG5cbiAgICB0aGlzLmFwcGx5VG9DaGFuZ2UoKTtcbiAgICB0aGlzLmFwcGx5RnJvbUNoYW5nZSgpO1xuICB9XG5cbiAgYXBwbHlGcm9tQ2hhbmdlKCkge1xuICAgIHRyeSB7XG4gICAgICB2YXIgbWluRGF0ZSA9ICQuZGF0ZXBpY2tlci5wYXJzZURhdGUoREFURV9GT1JNQVQsICQodGhpcy5mcm9tRGF0ZSkudmFsKCkpO1xuICAgICAgbWluRGF0ZS5zZXREYXRlKG1pbkRhdGUuZ2V0RGF0ZSgpICsgMSk7XG5cbiAgICAgICQodGhpcy50b0RhdGUpLmRhdGVwaWNrZXIoJ29wdGlvbicsICdtaW5EYXRlJywgbWluRGF0ZSk7XG4gICAgfSBjYXRjaChlKSB7fVxuICB9XG5cbiAgYXBwbHlUb0NoYW5nZSgpIHtcbiAgICB0cnkge1xuICAgICAgdmFyIG1heERhdGUgPSAkLmRhdGVwaWNrZXIucGFyc2VEYXRlKERBVEVfRk9STUFULCAkKHRoaXMudG9EYXRlKS52YWwoKSk7XG4gICAgICAkKHRoaXMuZnJvbURhdGUpLmRhdGVwaWNrZXIoJ29wdGlvbicsICdtYXhEYXRlJywgbWF4RGF0ZSk7XG4gICAgfSBjYXRjaChlKSB7fVxuICB9XG59XG5cbm1vZHVsZS5leHBvcnRzID0gUmFuZ2VEYXRlcGlja2VyO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3JhbmdlLWRhdGVwaWNrZXIuanMiLCIvLyBnZXQgc3VjY2Vzc2Z1bCBjb250cm9sIGZyb20gZm9ybSBhbmQgYXNzZW1ibGUgaW50byBvYmplY3Rcbi8vIGh0dHA6Ly93d3cudzMub3JnL1RSL2h0bWw0MDEvaW50ZXJhY3QvZm9ybXMuaHRtbCNoLTE3LjEzLjJcblxuLy8gdHlwZXMgd2hpY2ggaW5kaWNhdGUgYSBzdWJtaXQgYWN0aW9uIGFuZCBhcmUgbm90IHN1Y2Nlc3NmdWwgY29udHJvbHNcbi8vIHRoZXNlIHdpbGwgYmUgaWdub3JlZFxudmFyIGtfcl9zdWJtaXR0ZXIgPSAvXig/OnN1Ym1pdHxidXR0b258aW1hZ2V8cmVzZXR8ZmlsZSkkL2k7XG5cbi8vIG5vZGUgbmFtZXMgd2hpY2ggY291bGQgYmUgc3VjY2Vzc2Z1bCBjb250cm9sc1xudmFyIGtfcl9zdWNjZXNzX2NvbnRybHMgPSAvXig/OmlucHV0fHNlbGVjdHx0ZXh0YXJlYXxrZXlnZW4pL2k7XG5cbi8vIE1hdGNoZXMgYnJhY2tldCBub3RhdGlvbi5cbnZhciBicmFja2V0cyA9IC8oXFxbW15cXFtcXF1dKlxcXSkvZztcblxuLy8gc2VyaWFsaXplcyBmb3JtIGZpZWxkc1xuLy8gQHBhcmFtIGZvcm0gTVVTVCBiZSBhbiBIVE1MRm9ybSBlbGVtZW50XG4vLyBAcGFyYW0gb3B0aW9ucyBpcyBhbiBvcHRpb25hbCBhcmd1bWVudCB0byBjb25maWd1cmUgdGhlIHNlcmlhbGl6YXRpb24uIERlZmF1bHQgb3V0cHV0XG4vLyB3aXRoIG5vIG9wdGlvbnMgc3BlY2lmaWVkIGlzIGEgdXJsIGVuY29kZWQgc3RyaW5nXG4vLyAgICAtIGhhc2g6IFt0cnVlIHwgZmFsc2VdIENvbmZpZ3VyZSB0aGUgb3V0cHV0IHR5cGUuIElmIHRydWUsIHRoZSBvdXRwdXQgd2lsbFxuLy8gICAgYmUgYSBqcyBvYmplY3QuXG4vLyAgICAtIHNlcmlhbGl6ZXI6IFtmdW5jdGlvbl0gT3B0aW9uYWwgc2VyaWFsaXplciBmdW5jdGlvbiB0byBvdmVycmlkZSB0aGUgZGVmYXVsdCBvbmUuXG4vLyAgICBUaGUgZnVuY3Rpb24gdGFrZXMgMyBhcmd1bWVudHMgKHJlc3VsdCwga2V5LCB2YWx1ZSkgYW5kIHNob3VsZCByZXR1cm4gbmV3IHJlc3VsdFxuLy8gICAgaGFzaCBhbmQgdXJsIGVuY29kZWQgc3RyIHNlcmlhbGl6ZXJzIGFyZSBwcm92aWRlZCB3aXRoIHRoaXMgbW9kdWxlXG4vLyAgICAtIGRpc2FibGVkOiBbdHJ1ZSB8IGZhbHNlXS4gSWYgdHJ1ZSBzZXJpYWxpemUgZGlzYWJsZWQgZmllbGRzLlxuLy8gICAgLSBlbXB0eTogW3RydWUgfCBmYWxzZV0uIElmIHRydWUgc2VyaWFsaXplIGVtcHR5IGZpZWxkc1xuZnVuY3Rpb24gc2VyaWFsaXplKGZvcm0sIG9wdGlvbnMpIHtcbiAgICBpZiAodHlwZW9mIG9wdGlvbnMgIT0gJ29iamVjdCcpIHtcbiAgICAgICAgb3B0aW9ucyA9IHsgaGFzaDogISFvcHRpb25zIH07XG4gICAgfVxuICAgIGVsc2UgaWYgKG9wdGlvbnMuaGFzaCA9PT0gdW5kZWZpbmVkKSB7XG4gICAgICAgIG9wdGlvbnMuaGFzaCA9IHRydWU7XG4gICAgfVxuXG4gICAgdmFyIHJlc3VsdCA9IChvcHRpb25zLmhhc2gpID8ge30gOiAnJztcbiAgICB2YXIgc2VyaWFsaXplciA9IG9wdGlvbnMuc2VyaWFsaXplciB8fCAoKG9wdGlvbnMuaGFzaCkgPyBoYXNoX3NlcmlhbGl6ZXIgOiBzdHJfc2VyaWFsaXplKTtcblxuICAgIHZhciBlbGVtZW50cyA9IGZvcm0gJiYgZm9ybS5lbGVtZW50cyA/IGZvcm0uZWxlbWVudHMgOiBbXTtcblxuICAgIC8vT2JqZWN0IHN0b3JlIGVhY2ggcmFkaW8gYW5kIHNldCBpZiBpdCdzIGVtcHR5IG9yIG5vdFxuICAgIHZhciByYWRpb19zdG9yZSA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG5cbiAgICBmb3IgKHZhciBpPTAgOyBpPGVsZW1lbnRzLmxlbmd0aCA7ICsraSkge1xuICAgICAgICB2YXIgZWxlbWVudCA9IGVsZW1lbnRzW2ldO1xuXG4gICAgICAgIC8vIGluZ29yZSBkaXNhYmxlZCBmaWVsZHNcbiAgICAgICAgaWYgKCghb3B0aW9ucy5kaXNhYmxlZCAmJiBlbGVtZW50LmRpc2FibGVkKSB8fCAhZWxlbWVudC5uYW1lKSB7XG4gICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgfVxuICAgICAgICAvLyBpZ25vcmUgYW55aHRpbmcgdGhhdCBpcyBub3QgY29uc2lkZXJlZCBhIHN1Y2Nlc3MgZmllbGRcbiAgICAgICAgaWYgKCFrX3Jfc3VjY2Vzc19jb250cmxzLnRlc3QoZWxlbWVudC5ub2RlTmFtZSkgfHxcbiAgICAgICAgICAgIGtfcl9zdWJtaXR0ZXIudGVzdChlbGVtZW50LnR5cGUpKSB7XG4gICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHZhciBrZXkgPSBlbGVtZW50Lm5hbWU7XG4gICAgICAgIHZhciB2YWwgPSBlbGVtZW50LnZhbHVlO1xuXG4gICAgICAgIC8vIHdlIGNhbid0IGp1c3QgdXNlIGVsZW1lbnQudmFsdWUgZm9yIGNoZWNrYm94ZXMgY2F1c2Ugc29tZSBicm93c2VycyBsaWUgdG8gdXNcbiAgICAgICAgLy8gdGhleSBzYXkgXCJvblwiIGZvciB2YWx1ZSB3aGVuIHRoZSBib3ggaXNuJ3QgY2hlY2tlZFxuICAgICAgICBpZiAoKGVsZW1lbnQudHlwZSA9PT0gJ2NoZWNrYm94JyB8fCBlbGVtZW50LnR5cGUgPT09ICdyYWRpbycpICYmICFlbGVtZW50LmNoZWNrZWQpIHtcbiAgICAgICAgICAgIHZhbCA9IHVuZGVmaW5lZDtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIElmIHdlIHdhbnQgZW1wdHkgZWxlbWVudHNcbiAgICAgICAgaWYgKG9wdGlvbnMuZW1wdHkpIHtcbiAgICAgICAgICAgIC8vIGZvciBjaGVja2JveFxuICAgICAgICAgICAgaWYgKGVsZW1lbnQudHlwZSA9PT0gJ2NoZWNrYm94JyAmJiAhZWxlbWVudC5jaGVja2VkKSB7XG4gICAgICAgICAgICAgICAgdmFsID0gJyc7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIGZvciByYWRpb1xuICAgICAgICAgICAgaWYgKGVsZW1lbnQudHlwZSA9PT0gJ3JhZGlvJykge1xuICAgICAgICAgICAgICAgIGlmICghcmFkaW9fc3RvcmVbZWxlbWVudC5uYW1lXSAmJiAhZWxlbWVudC5jaGVja2VkKSB7XG4gICAgICAgICAgICAgICAgICAgIHJhZGlvX3N0b3JlW2VsZW1lbnQubmFtZV0gPSBmYWxzZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZSBpZiAoZWxlbWVudC5jaGVja2VkKSB7XG4gICAgICAgICAgICAgICAgICAgIHJhZGlvX3N0b3JlW2VsZW1lbnQubmFtZV0gPSB0cnVlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gaWYgb3B0aW9ucyBlbXB0eSBpcyB0cnVlLCBjb250aW51ZSBvbmx5IGlmIGl0cyByYWRpb1xuICAgICAgICAgICAgaWYgKHZhbCA9PSB1bmRlZmluZWQgJiYgZWxlbWVudC50eXBlID09ICdyYWRpbycpIHtcbiAgICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgIC8vIHZhbHVlLWxlc3MgZmllbGRzIGFyZSBpZ25vcmVkIHVubGVzcyBvcHRpb25zLmVtcHR5IGlzIHRydWVcbiAgICAgICAgICAgIGlmICghdmFsKSB7XG4gICAgICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvLyBtdWx0aSBzZWxlY3QgYm94ZXNcbiAgICAgICAgaWYgKGVsZW1lbnQudHlwZSA9PT0gJ3NlbGVjdC1tdWx0aXBsZScpIHtcbiAgICAgICAgICAgIHZhbCA9IFtdO1xuXG4gICAgICAgICAgICB2YXIgc2VsZWN0T3B0aW9ucyA9IGVsZW1lbnQub3B0aW9ucztcbiAgICAgICAgICAgIHZhciBpc1NlbGVjdGVkT3B0aW9ucyA9IGZhbHNlO1xuICAgICAgICAgICAgZm9yICh2YXIgaj0wIDsgajxzZWxlY3RPcHRpb25zLmxlbmd0aCA7ICsraikge1xuICAgICAgICAgICAgICAgIHZhciBvcHRpb24gPSBzZWxlY3RPcHRpb25zW2pdO1xuICAgICAgICAgICAgICAgIHZhciBhbGxvd2VkRW1wdHkgPSBvcHRpb25zLmVtcHR5ICYmICFvcHRpb24udmFsdWU7XG4gICAgICAgICAgICAgICAgdmFyIGhhc1ZhbHVlID0gKG9wdGlvbi52YWx1ZSB8fCBhbGxvd2VkRW1wdHkpO1xuICAgICAgICAgICAgICAgIGlmIChvcHRpb24uc2VsZWN0ZWQgJiYgaGFzVmFsdWUpIHtcbiAgICAgICAgICAgICAgICAgICAgaXNTZWxlY3RlZE9wdGlvbnMgPSB0cnVlO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIElmIHVzaW5nIGEgaGFzaCBzZXJpYWxpemVyIGJlIHN1cmUgdG8gYWRkIHRoZVxuICAgICAgICAgICAgICAgICAgICAvLyBjb3JyZWN0IG5vdGF0aW9uIGZvciBhbiBhcnJheSBpbiB0aGUgbXVsdGktc2VsZWN0XG4gICAgICAgICAgICAgICAgICAgIC8vIGNvbnRleHQuIEhlcmUgdGhlIG5hbWUgYXR0cmlidXRlIG9uIHRoZSBzZWxlY3QgZWxlbWVudFxuICAgICAgICAgICAgICAgICAgICAvLyBtaWdodCBiZSBtaXNzaW5nIHRoZSB0cmFpbGluZyBicmFja2V0IHBhaXIuIEJvdGggbmFtZXNcbiAgICAgICAgICAgICAgICAgICAgLy8gXCJmb29cIiBhbmQgXCJmb29bXVwiIHNob3VsZCBiZSBhcnJheXMuXG4gICAgICAgICAgICAgICAgICAgIGlmIChvcHRpb25zLmhhc2ggJiYga2V5LnNsaWNlKGtleS5sZW5ndGggLSAyKSAhPT0gJ1tdJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ID0gc2VyaWFsaXplcihyZXN1bHQsIGtleSArICdbXScsIG9wdGlvbi52YWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSBzZXJpYWxpemVyKHJlc3VsdCwga2V5LCBvcHRpb24udmFsdWUpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBTZXJpYWxpemUgaWYgbm8gc2VsZWN0ZWQgb3B0aW9ucyBhbmQgb3B0aW9ucy5lbXB0eSBpcyB0cnVlXG4gICAgICAgICAgICBpZiAoIWlzU2VsZWN0ZWRPcHRpb25zICYmIG9wdGlvbnMuZW1wdHkpIHtcbiAgICAgICAgICAgICAgICByZXN1bHQgPSBzZXJpYWxpemVyKHJlc3VsdCwga2V5LCAnJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmVzdWx0ID0gc2VyaWFsaXplcihyZXN1bHQsIGtleSwgdmFsKTtcbiAgICB9XG5cbiAgICAvLyBDaGVjayBmb3IgYWxsIGVtcHR5IHJhZGlvIGJ1dHRvbnMgYW5kIHNlcmlhbGl6ZSB0aGVtIHdpdGgga2V5PVwiXCJcbiAgICBpZiAob3B0aW9ucy5lbXB0eSkge1xuICAgICAgICBmb3IgKHZhciBrZXkgaW4gcmFkaW9fc3RvcmUpIHtcbiAgICAgICAgICAgIGlmICghcmFkaW9fc3RvcmVba2V5XSkge1xuICAgICAgICAgICAgICAgIHJlc3VsdCA9IHNlcmlhbGl6ZXIocmVzdWx0LCBrZXksICcnKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiByZXN1bHQ7XG59XG5cbmZ1bmN0aW9uIHBhcnNlX2tleXMoc3RyaW5nKSB7XG4gICAgdmFyIGtleXMgPSBbXTtcbiAgICB2YXIgcHJlZml4ID0gL14oW15cXFtcXF1dKikvO1xuICAgIHZhciBjaGlsZHJlbiA9IG5ldyBSZWdFeHAoYnJhY2tldHMpO1xuICAgIHZhciBtYXRjaCA9IHByZWZpeC5leGVjKHN0cmluZyk7XG5cbiAgICBpZiAobWF0Y2hbMV0pIHtcbiAgICAgICAga2V5cy5wdXNoKG1hdGNoWzFdKTtcbiAgICB9XG5cbiAgICB3aGlsZSAoKG1hdGNoID0gY2hpbGRyZW4uZXhlYyhzdHJpbmcpKSAhPT0gbnVsbCkge1xuICAgICAgICBrZXlzLnB1c2gobWF0Y2hbMV0pO1xuICAgIH1cblxuICAgIHJldHVybiBrZXlzO1xufVxuXG5mdW5jdGlvbiBoYXNoX2Fzc2lnbihyZXN1bHQsIGtleXMsIHZhbHVlKSB7XG4gICAgaWYgKGtleXMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIHJlc3VsdCA9IHZhbHVlO1xuICAgICAgICByZXR1cm4gcmVzdWx0O1xuICAgIH1cblxuICAgIHZhciBrZXkgPSBrZXlzLnNoaWZ0KCk7XG4gICAgdmFyIGJldHdlZW4gPSBrZXkubWF0Y2goL15cXFsoLis/KVxcXSQvKTtcblxuICAgIGlmIChrZXkgPT09ICdbXScpIHtcbiAgICAgICAgcmVzdWx0ID0gcmVzdWx0IHx8IFtdO1xuXG4gICAgICAgIGlmIChBcnJheS5pc0FycmF5KHJlc3VsdCkpIHtcbiAgICAgICAgICAgIHJlc3VsdC5wdXNoKGhhc2hfYXNzaWduKG51bGwsIGtleXMsIHZhbHVlKSk7XG4gICAgICAgIH1cbiAgICAgICAgZWxzZSB7XG4gICAgICAgICAgICAvLyBUaGlzIG1pZ2h0IGJlIHRoZSByZXN1bHQgb2YgYmFkIG5hbWUgYXR0cmlidXRlcyBsaWtlIFwiW11bZm9vXVwiLFxuICAgICAgICAgICAgLy8gaW4gdGhpcyBjYXNlIHRoZSBvcmlnaW5hbCBgcmVzdWx0YCBvYmplY3Qgd2lsbCBhbHJlYWR5IGJlXG4gICAgICAgICAgICAvLyBhc3NpZ25lZCB0byBhbiBvYmplY3QgbGl0ZXJhbC4gUmF0aGVyIHRoYW4gY29lcmNlIHRoZSBvYmplY3QgdG9cbiAgICAgICAgICAgIC8vIGFuIGFycmF5LCBvciBjYXVzZSBhbiBleGNlcHRpb24gdGhlIGF0dHJpYnV0ZSBcIl92YWx1ZXNcIiBpc1xuICAgICAgICAgICAgLy8gYXNzaWduZWQgYXMgYW4gYXJyYXkuXG4gICAgICAgICAgICByZXN1bHQuX3ZhbHVlcyA9IHJlc3VsdC5fdmFsdWVzIHx8IFtdO1xuICAgICAgICAgICAgcmVzdWx0Ll92YWx1ZXMucHVzaChoYXNoX2Fzc2lnbihudWxsLCBrZXlzLCB2YWx1ZSkpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHJlc3VsdDtcbiAgICB9XG5cbiAgICAvLyBLZXkgaXMgYW4gYXR0cmlidXRlIG5hbWUgYW5kIGNhbiBiZSBhc3NpZ25lZCBkaXJlY3RseS5cbiAgICBpZiAoIWJldHdlZW4pIHtcbiAgICAgICAgcmVzdWx0W2tleV0gPSBoYXNoX2Fzc2lnbihyZXN1bHRba2V5XSwga2V5cywgdmFsdWUpO1xuICAgIH1cbiAgICBlbHNlIHtcbiAgICAgICAgdmFyIHN0cmluZyA9IGJldHdlZW5bMV07XG4gICAgICAgIC8vICt2YXIgY29udmVydHMgdGhlIHZhcmlhYmxlIGludG8gYSBudW1iZXJcbiAgICAgICAgLy8gYmV0dGVyIHRoYW4gcGFyc2VJbnQgYmVjYXVzZSBpdCBkb2Vzbid0IHRydW5jYXRlIGF3YXkgdHJhaWxpbmdcbiAgICAgICAgLy8gbGV0dGVycyBhbmQgYWN0dWFsbHkgZmFpbHMgaWYgd2hvbGUgdGhpbmcgaXMgbm90IGEgbnVtYmVyXG4gICAgICAgIHZhciBpbmRleCA9ICtzdHJpbmc7XG5cbiAgICAgICAgLy8gSWYgdGhlIGNoYXJhY3RlcnMgYmV0d2VlbiB0aGUgYnJhY2tldHMgaXMgbm90IGEgbnVtYmVyIGl0IGlzIGFuXG4gICAgICAgIC8vIGF0dHJpYnV0ZSBuYW1lIGFuZCBjYW4gYmUgYXNzaWduZWQgZGlyZWN0bHkuXG4gICAgICAgIGlmIChpc05hTihpbmRleCkpIHtcbiAgICAgICAgICAgIHJlc3VsdCA9IHJlc3VsdCB8fCB7fTtcbiAgICAgICAgICAgIHJlc3VsdFtzdHJpbmddID0gaGFzaF9hc3NpZ24ocmVzdWx0W3N0cmluZ10sIGtleXMsIHZhbHVlKTtcbiAgICAgICAgfVxuICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgIHJlc3VsdCA9IHJlc3VsdCB8fCBbXTtcbiAgICAgICAgICAgIHJlc3VsdFtpbmRleF0gPSBoYXNoX2Fzc2lnbihyZXN1bHRbaW5kZXhdLCBrZXlzLCB2YWx1ZSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gcmVzdWx0O1xufVxuXG4vLyBPYmplY3QvaGFzaCBlbmNvZGluZyBzZXJpYWxpemVyLlxuZnVuY3Rpb24gaGFzaF9zZXJpYWxpemVyKHJlc3VsdCwga2V5LCB2YWx1ZSkge1xuICAgIHZhciBtYXRjaGVzID0ga2V5Lm1hdGNoKGJyYWNrZXRzKTtcblxuICAgIC8vIEhhcyBicmFja2V0cz8gVXNlIHRoZSByZWN1cnNpdmUgYXNzaWdubWVudCBmdW5jdGlvbiB0byB3YWxrIHRoZSBrZXlzLFxuICAgIC8vIGNvbnN0cnVjdCBhbnkgbWlzc2luZyBvYmplY3RzIGluIHRoZSByZXN1bHQgdHJlZSBhbmQgbWFrZSB0aGUgYXNzaWdubWVudFxuICAgIC8vIGF0IHRoZSBlbmQgb2YgdGhlIGNoYWluLlxuICAgIGlmIChtYXRjaGVzKSB7XG4gICAgICAgIHZhciBrZXlzID0gcGFyc2Vfa2V5cyhrZXkpO1xuICAgICAgICBoYXNoX2Fzc2lnbihyZXN1bHQsIGtleXMsIHZhbHVlKTtcbiAgICB9XG4gICAgZWxzZSB7XG4gICAgICAgIC8vIE5vbiBicmFja2V0IG5vdGF0aW9uIGNhbiBtYWtlIGFzc2lnbm1lbnRzIGRpcmVjdGx5LlxuICAgICAgICB2YXIgZXhpc3RpbmcgPSByZXN1bHRba2V5XTtcblxuICAgICAgICAvLyBJZiB0aGUgdmFsdWUgaGFzIGJlZW4gYXNzaWduZWQgYWxyZWFkeSAoZm9yIGluc3RhbmNlIHdoZW4gYSByYWRpbyBhbmRcbiAgICAgICAgLy8gYSBjaGVja2JveCBoYXZlIHRoZSBzYW1lIG5hbWUgYXR0cmlidXRlKSBjb252ZXJ0IHRoZSBwcmV2aW91cyB2YWx1ZVxuICAgICAgICAvLyBpbnRvIGFuIGFycmF5IGJlZm9yZSBwdXNoaW5nIGludG8gaXQuXG4gICAgICAgIC8vXG4gICAgICAgIC8vIE5PVEU6IElmIHRoaXMgcmVxdWlyZW1lbnQgd2VyZSByZW1vdmVkIGFsbCBoYXNoIGNyZWF0aW9uIGFuZFxuICAgICAgICAvLyBhc3NpZ25tZW50IGNvdWxkIGdvIHRocm91Z2ggYGhhc2hfYXNzaWduYC5cbiAgICAgICAgaWYgKGV4aXN0aW5nKSB7XG4gICAgICAgICAgICBpZiAoIUFycmF5LmlzQXJyYXkoZXhpc3RpbmcpKSB7XG4gICAgICAgICAgICAgICAgcmVzdWx0W2tleV0gPSBbIGV4aXN0aW5nIF07XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJlc3VsdFtrZXldLnB1c2godmFsdWUpO1xuICAgICAgICB9XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgcmVzdWx0W2tleV0gPSB2YWx1ZTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiByZXN1bHQ7XG59XG5cbi8vIHVybGZvcm0gZW5jb2Rpbmcgc2VyaWFsaXplclxuZnVuY3Rpb24gc3RyX3NlcmlhbGl6ZShyZXN1bHQsIGtleSwgdmFsdWUpIHtcbiAgICAvLyBlbmNvZGUgbmV3bGluZXMgYXMgXFxyXFxuIGNhdXNlIHRoZSBodG1sIHNwZWMgc2F5cyBzb1xuICAgIHZhbHVlID0gdmFsdWUucmVwbGFjZSgvKFxccik/XFxuL2csICdcXHJcXG4nKTtcbiAgICB2YWx1ZSA9IGVuY29kZVVSSUNvbXBvbmVudCh2YWx1ZSk7XG5cbiAgICAvLyBzcGFjZXMgc2hvdWxkIGJlICcrJyByYXRoZXIgdGhhbiAnJTIwJy5cbiAgICB2YWx1ZSA9IHZhbHVlLnJlcGxhY2UoLyUyMC9nLCAnKycpO1xuICAgIHJldHVybiByZXN1bHQgKyAocmVzdWx0ID8gJyYnIDogJycpICsgZW5jb2RlVVJJQ29tcG9uZW50KGtleSkgKyAnPScgKyB2YWx1ZTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBzZXJpYWxpemU7XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL25vZGVfbW9kdWxlcy9mb3JtLXNlcmlhbGl6ZS9pbmRleC5qc1xuLy8gbW9kdWxlIGlkID0gNlxuLy8gbW9kdWxlIGNodW5rcyA9IDAiLCIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vYXNzZXRzL3Nhc3MvYWRtaW4uc2Nzc1xuLy8gbW9kdWxlIGlkID0gN1xuLy8gbW9kdWxlIGNodW5rcyA9IDAiXSwic291cmNlUm9vdCI6IiJ9