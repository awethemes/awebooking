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
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ 2:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("oNlh");


/***/ }),

/***/ "Fv1B":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.MODIFIER_KEY_NAMES = exports.DEFAULT_VERTICAL_SPACING = exports.FANG_HEIGHT_PX = exports.FANG_WIDTH_PX = exports.WEEKDAYS = exports.BLOCKED_MODIFIER = exports.DAY_SIZE = exports.OPEN_UP = exports.OPEN_DOWN = exports.ANCHOR_RIGHT = exports.ANCHOR_LEFT = exports.INFO_POSITION_AFTER = exports.INFO_POSITION_BEFORE = exports.INFO_POSITION_BOTTOM = exports.INFO_POSITION_TOP = exports.ICON_AFTER_POSITION = exports.ICON_BEFORE_POSITION = exports.VERTICAL_SCROLLABLE = exports.VERTICAL_ORIENTATION = exports.HORIZONTAL_ORIENTATION = exports.END_DATE = exports.START_DATE = exports.ISO_MONTH_FORMAT = exports.ISO_FORMAT = exports.DISPLAY_FORMAT = void 0;
var DISPLAY_FORMAT = 'L';
exports.DISPLAY_FORMAT = DISPLAY_FORMAT;
var ISO_FORMAT = 'YYYY-MM-DD';
exports.ISO_FORMAT = ISO_FORMAT;
var ISO_MONTH_FORMAT = 'YYYY-MM';
exports.ISO_MONTH_FORMAT = ISO_MONTH_FORMAT;
var START_DATE = 'startDate';
exports.START_DATE = START_DATE;
var END_DATE = 'endDate';
exports.END_DATE = END_DATE;
var HORIZONTAL_ORIENTATION = 'horizontal';
exports.HORIZONTAL_ORIENTATION = HORIZONTAL_ORIENTATION;
var VERTICAL_ORIENTATION = 'vertical';
exports.VERTICAL_ORIENTATION = VERTICAL_ORIENTATION;
var VERTICAL_SCROLLABLE = 'verticalScrollable';
exports.VERTICAL_SCROLLABLE = VERTICAL_SCROLLABLE;
var ICON_BEFORE_POSITION = 'before';
exports.ICON_BEFORE_POSITION = ICON_BEFORE_POSITION;
var ICON_AFTER_POSITION = 'after';
exports.ICON_AFTER_POSITION = ICON_AFTER_POSITION;
var INFO_POSITION_TOP = 'top';
exports.INFO_POSITION_TOP = INFO_POSITION_TOP;
var INFO_POSITION_BOTTOM = 'bottom';
exports.INFO_POSITION_BOTTOM = INFO_POSITION_BOTTOM;
var INFO_POSITION_BEFORE = 'before';
exports.INFO_POSITION_BEFORE = INFO_POSITION_BEFORE;
var INFO_POSITION_AFTER = 'after';
exports.INFO_POSITION_AFTER = INFO_POSITION_AFTER;
var ANCHOR_LEFT = 'left';
exports.ANCHOR_LEFT = ANCHOR_LEFT;
var ANCHOR_RIGHT = 'right';
exports.ANCHOR_RIGHT = ANCHOR_RIGHT;
var OPEN_DOWN = 'down';
exports.OPEN_DOWN = OPEN_DOWN;
var OPEN_UP = 'up';
exports.OPEN_UP = OPEN_UP;
var DAY_SIZE = 39;
exports.DAY_SIZE = DAY_SIZE;
var BLOCKED_MODIFIER = 'blocked';
exports.BLOCKED_MODIFIER = BLOCKED_MODIFIER;
var WEEKDAYS = [0, 1, 2, 3, 4, 5, 6];
exports.WEEKDAYS = WEEKDAYS;
var FANG_WIDTH_PX = 20;
exports.FANG_WIDTH_PX = FANG_WIDTH_PX;
var FANG_HEIGHT_PX = 10;
exports.FANG_HEIGHT_PX = FANG_HEIGHT_PX;
var DEFAULT_VERTICAL_SPACING = 22;
exports.DEFAULT_VERTICAL_SPACING = DEFAULT_VERTICAL_SPACING;
var MODIFIER_KEY_NAMES = new Set(['Shift', 'Control', 'Alt', 'Meta']);
exports.MODIFIER_KEY_NAMES = MODIFIER_KEY_NAMES;

/***/ }),

/***/ "WmS1":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = toMomentObject;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _constants = __webpack_require__("Fv1B");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function toMomentObject(dateString, customFormat) {
  var dateFormats = customFormat ? [customFormat, _constants.DISPLAY_FORMAT, _constants.ISO_FORMAT] : [_constants.DISPLAY_FORMAT, _constants.ISO_FORMAT];
  var date = (0, _moment["default"])(dateString, dateFormats, true);
  return date.isValid() ? date.hour(12) : null;
}

/***/ }),

/***/ "nGjC":
/***/ (function(module, exports) {

module.exports = window.ko;

/***/ }),

/***/ "oNlh":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external "jQuery"
var external_jQuery_ = __webpack_require__("xeH2");
var external_jQuery_default = /*#__PURE__*/__webpack_require__.n(external_jQuery_);

// CONCATENATED MODULE: ./assets/babel/utils/control.js
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}


/**
 * //
 *
 * @type {*}
 */

var Synchronizer = {
  val: {
    update: function update(to) {
      this.element.val(to);
    },
    refresh: function refresh() {
      return this.element.val();
    }
  },
  checkbox: {
    update: function update(to) {
      this.element.prop('checked', to);
    },
    refresh: function refresh() {
      return this.element.prop('checked');
    }
  },
  radio: {
    update: function update(to) {
      this.element.filter(function () {
        return this.value === to;
      }).prop('checked', true);
    },
    refresh: function refresh() {
      return this.element.filter(':checked').val();
    }
  },
  html: {
    update: function update(to) {
      this.element.html(to);
    },
    refresh: function refresh() {
      return this.element.html();
    }
    /**
     * Cast a string to a jQuery collection if it isn't already.
     *
     * @param {string|jQuery} element
     */

  }
};

function ensureElement(element) {
  return typeof element == 'string' ? external_jQuery_default()(element) : element;
}
/**
 * //
 *
 * @param {*} a
 * @param {*} b
 * @return {boolean}
 */


function isEquals(a, b) {
  var underscore = window._ || window.lodash;

  if (typeof underscore !== 'undefined') {
    return underscore.isEqual(a, b);
  } // noinspection EqualityComparisonWithCoercionJS


  return a == b;
}
/**
 * An observable value that syncs with an element
 *
 * Handles inputs, selects, and textareas by default
 */


var control_Control =
/*#__PURE__*/
function () {
  function Control(element) {
    _classCallCheck(this, Control);

    this._value = null;
    this._dirty = false; // Store and manage the callback lists

    this.callbacks = external_jQuery_default.a.Callbacks();
    this.element = ensureElement(element);
    this.events = '';
    /* synchronizer */

    var _synchronizer = Synchronizer.html;

    if (this.element.is('input, select, textarea')) {
      var type = this.element.prop('type');
      this.events += ' change input';
      _synchronizer = Synchronizer.val; // For checkbox and radio inputs.

      if (this.element.is('input') && Synchronizer.hasOwnProperty(type)) {
        _synchronizer = Synchronizer[type];
      }
    } // Set the value from the input.


    external_jQuery_default.a.extend(this, _synchronizer);

    var _initialize = this.initialize || void 0;

    if (typeof _initialize === 'function') {
      _initialize.apply(this, arguments);
    } // Overwire some update & refresh methods.


    var self = this;
    var update = this.update,
        refresh = this.refresh;

    this.update = function (to) {
      if (to !== refresh.call(self)) {
        update.apply(this, arguments);
      }
    };

    this.refresh = function () {
      self.set(refresh.call(self));
    };

    this.get = this.get.bind(this);
    this.set = this.set.bind(this);
    this._setter = this._setter.bind(this); // Enable two-way bindings.

    this.bind(this.update);
    this.element.bind(this.events, this.refresh); // Set the initial value.

    if (null === this._value) {
      this._value = refresh.call(this);
    }
  }

  _createClass(Control, [{
    key: "refresh",
    value: function refresh() {}
  }, {
    key: "update",
    value: function update() {}
    /**
     * Get the value.
     *
     * @return {*}
     */

  }, {
    key: "get",
    value: function get() {
      return this._value;
    }
    /**
     * Set the value and trigger all bound callbacks.
     *
     * @param {*} to New value.
     */

  }, {
    key: "set",
    value: function set(to) {
      var from = this._value;
      to = this._setter.apply(this, arguments);
      to = this.validate(to); // Bail if the sanitized value is null or unchanged.

      if (null === to || isEquals(from, to)) {
        return this;
      }

      this._value = to;
      this._dirty = true;
      this.callbacks.fireWith(this, [to, from]);
      return this;
    }
    /**
     * Validate the value and return the sanitized value.
     *
     * @param {*} value
     * @return {*}
     */

  }, {
    key: "validate",
    value: function validate(value) {
      return value;
    }
  }, {
    key: "_setter",
    value: function _setter(to) {
      return to;
    }
  }, {
    key: "setter",
    value: function setter(callback) {
      var from = this.get();
      this._setter = callback; // Temporarily clear value so setter can decide if it's valid.

      this._value = null;
      this.set(from);
      return this;
    }
  }, {
    key: "resetSetter",
    value: function resetSetter() {
      this._setter = this.constructor.prototype._setter;
      this.set(this.get());
      return this;
    }
    /**
     * Bind a function to be invoked whenever the value changes.
     *
     * @param {function} args A function, or multiple functions, to add to the callback stack.
     */

  }, {
    key: "bind",
    value: function bind() {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      this.callbacks.add.apply(this.callbacks, args);
      return this;
    }
    /**
     * Unbind a previously bound function.
     *
     * @param {function} args A function, or multiple functions, to remove from the callback stack.
     */

  }, {
    key: "unbind",
    value: function unbind() {
      for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        args[_key2] = arguments[_key2];
      }

      this.callbacks.remove.apply(this.callbacks, args);
      return this;
    }
  }, {
    key: "link",
    value: function link() {
      var set = this.set;

      for (var _len3 = arguments.length, values = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
        values[_key3] = arguments[_key3];
      }

      values.forEach(function (value) {
        value.bind(set);
      });
      return this;
    }
  }, {
    key: "unlink",
    value: function unlink() {
      var set = this.set;

      for (var _len4 = arguments.length, values = new Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
        values[_key4] = arguments[_key4];
      }

      values.forEach(function (value) {
        value.unbind(set);
      });
      return this;
    }
  }, {
    key: "sync",
    value: function sync() {
      var that = this;

      for (var _len5 = arguments.length, values = new Array(_len5), _key5 = 0; _key5 < _len5; _key5++) {
        values[_key5] = arguments[_key5];
      }

      values.forEach(function (value) {
        that.link(value);
        value.link(that);
      });
      return this;
    }
  }, {
    key: "unsync",
    value: function unsync() {
      var that = this;

      for (var _len6 = arguments.length, values = new Array(_len6), _key6 = 0; _key6 < _len6; _key6++) {
        values[_key6] = arguments[_key6];
      }

      values.forEach(function (value) {
        that.unlink(value);
        value.unlink(that);
      });
      return this;
    }
  }]);

  return Control;
}();


// CONCATENATED MODULE: ./assets/babel/utils/date-utils.js
function formatDateString(dateString, format) {
  var _ref = window.awebooking || {},
      i18n = _ref.i18n,
      utils = _ref.utils;

  var date = utils.dates.parse(dateString, 'Y-m-d');

  if (!date) {
    return '';
  }

  return utils.dates.format(date, format || i18n.dateFormat);
}
// EXTERNAL MODULE: ./node_modules/react-dates/lib/utils/isSameDay.js
var isSameDay = __webpack_require__("pRvc");
var isSameDay_default = /*#__PURE__*/__webpack_require__.n(isSameDay);

// EXTERNAL MODULE: ./node_modules/react-dates/lib/utils/toMomentObject.js
var toMomentObject = __webpack_require__("WmS1");
var toMomentObject_default = /*#__PURE__*/__webpack_require__.n(toMomentObject);

// CONCATENATED MODULE: ./assets/babel/search-form/SearchForm.js
function SearchForm_classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

function SearchForm_defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function SearchForm_createClass(Constructor, protoProps, staticProps) {
  if (protoProps) SearchForm_defineProperties(Constructor.prototype, protoProps);
  if (staticProps) SearchForm_defineProperties(Constructor, staticProps);
  return Constructor;
}







var SearchForm_SearchForm =
/*#__PURE__*/
function () {
  function SearchForm(root, instance) {
    SearchForm_classCallCheck(this, SearchForm);

    this.root = external_jQuery_default()(root);
    this.instance = instance; // Store the input elements

    this.elements = {};
    this.linkElements();

    this._registerBindings();

    if (window.createReactDatePicker && this.root.find('.abrs-searchbox__dates').length > 0) {
      this._createDatePicker();
    }
  }

  SearchForm_createClass(SearchForm, [{
    key: "getFormData",
    value: function getFormData() {
      var elements = this.elements;
      var data = {};
      Object.keys(elements).forEach(function (index) {
        data[index] = elements[index].get();
      });
      return data;
    }
  }, {
    key: "getRootElement",
    value: function getRootElement() {
      return this.root[0];
    }
  }, {
    key: "_createDatePicker",
    value: function _createDatePicker() {
      var datepicker = window.awebooking.config.datepicker;
      var disableDays = datepicker.disableDays,
          disableDates = datepicker.disableDates;
      disableDates = disableDates.split(/,\s?/).map(function (day) {
        return toMomentObject_default()(day);
      });

      var isDayBlocked = function isDayBlocked(day) {
        var disabled = false;

        if (Array.isArray(disableDays) && disableDays.length > 0) {
          disabled = disableDays.includes(parseInt(day.format('d'), 10));
        }

        if (!disabled && disableDates.length > 0) {
          disabled = disableDates.some(function (test) {
            return isSameDay_default()(day, test);
          });
        }

        return disabled;
      };

      window.createReactDatePicker(this, {
        isDayBlocked: isDayBlocked,
        minimumNights: datepicker.minNights || 1,
        maximumNights: datepicker.maxNights || 0,
        minimumDateRange: datepicker.minDate || 0,
        maximumDateRange: datepicker.maxDate || 0,
        numberOfMonths: datepicker.showMonths || 1
      });
    }
  }, {
    key: "_registerBindings",
    value: function _registerBindings() {
      var _this = this;

      var binding = function binding(bind) {
        return function (value) {
          _this.elements[bind].set(value ? formatDateString(value) : '');
        };
      };

      if (this.elements.hasOwnProperty('check_in_alt')) {
        this.elements['check_in'].bind(binding('check_in_alt'));
      }

      if (this.elements.hasOwnProperty('check_out_alt')) {
        this.elements['check_out'].bind(binding('check_out_alt'));
      }
    }
    /**
     * Link elements between settings and inputs
     */

  }, {
    key: "linkElements",
    value: function linkElements() {
      var control = this;
      var nodes = control.root.find('select, input, textarea');
      var radios = {};
      nodes.each(function (index, element) {
        var node = external_jQuery_default()(element);

        if (node.data('_elementLinked')) {
          return;
        } // Prevent re-linking element.


        node.data('_elementLinked', true);
        var name = node.prop('name');

        if (node.is(':radio')) {
          if (radios[name]) {
            return;
          }

          radios[name] = true;
          node = nodes.filter('[name="' + name + '"]');
        }

        index = name || index;

        if (node.data('element')) {
          index = node.data('element');
        }

        control.elements[index] = new control_Control(node);
      });
    }
  }]);

  return SearchForm;
}();


// EXTERNAL MODULE: external "window.ko"
var external_window_ko_ = __webpack_require__("nGjC");
var external_window_ko_default = /*#__PURE__*/__webpack_require__.n(external_window_ko_);

// CONCATENATED MODULE: ./assets/babel/search-form/old.js
function old_classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

function old_defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function old_createClass(Constructor, protoProps, staticProps) {
  if (protoProps) old_defineProperties(Constructor.prototype, protoProps);
  if (staticProps) old_defineProperties(Constructor, staticProps);
  return Constructor;
}





var old_SearchFormModel =
/*#__PURE__*/
function () {
  function SearchFormModel(data) {
    var _this = this;

    old_classCallCheck(this, SearchFormModel);

    this.hotel = external_window_ko_default.a.observable(data.adults || 0);
    this.adults = external_window_ko_default.a.observable(data.adults || 1);
    this.children = external_window_ko_default.a.observable(data.children || 0);
    this.infants = external_window_ko_default.a.observable(data.infants || 0);
    this.checkIn = external_window_ko_default.a.observable(data.check_in || '');
    this.checkOut = external_window_ko_default.a.observable(data.check_out || '');
    this.checkInDate = external_window_ko_default.a.computed(function () {
      return formatDateString(_this.checkIn(), 'Y-m-d');
    });
    this.checkOutDate = external_window_ko_default.a.computed(function () {
      return formatDateString(_this.checkOut(), 'Y-m-d');
    });
  }

  old_createClass(SearchFormModel, [{
    key: "checkInFormatted",
    value: function checkInFormatted(format) {
      return formatDateString(this.checkIn(), format);
    }
  }, {
    key: "checkOutFormatted",
    value: function checkOutFormatted(format) {
      return formatDateString(this.checkOut(), format);
    }
  }]);

  return SearchFormModel;
}();

var old_OldSearchForm =
/*#__PURE__*/
function () {
  function OldSearchForm(el, form) {
    var _this2 = this;

    old_classCallCheck(this, OldSearchForm);

    this.$el = external_jQuery_default()(el);
    this.form = form;

    this._setupDatePicker();

    this.$el.find('.searchbox__box').each(function (i, box) {
      external_jQuery_default()(box).data('popup', _this2._setuptPopper(box));
    });
    this.$el.find('[data-trigger="spinner"]').on('changed.spinner', function () {
      external_jQuery_default()(this).find('[data-spin="spinner"]').trigger('change');
    });
    this.model = new old_SearchFormModel(form.getFormData());
    external_window_ko_default.a.applyBindings(this.model, el);
  }

  old_createClass(OldSearchForm, [{
    key: "_setupDatePicker",
    value: function _setupDatePicker() {
      var _this3 = this;

      var self = this;
      var plugin = window.awebooking;
      var $rangepicker = this.$el.find('[data-hotel="rangepicker"]');

      if ($rangepicker.length === 0) {
        $rangepicker = external_jQuery_default()('<input type="text" data-hotel="rangepicker"/>').appendTo(this.$el);
      }

      var fp = plugin.datepicker($rangepicker[0], {
        mode: 'range',
        altInput: false,
        clickOpens: false,
        closeOnSelect: true,
        onReady: function onReady(_, __, fp) {
          fp.calendarContainer.classList.add('awebooking-datepicker');
          this.config.ignoredFocusElements.push(external_jQuery_default()('.searchbox__box--checkin', self.$el)[0]);
          this.config.ignoredFocusElements.push(external_jQuery_default()('.searchbox__box--checkout', self.$el)[0]);
        },
        onChange: function onChange(dates) {
          if (dates.length === 0) {
            _this3.model.checkIn(null);

            _this3.model.checkIn(null);
          } else if (dates.length === 1) {
            _this3.model.checkIn(dates[0]);

            _this3.model.checkOut(null);
          } else {
            _this3.model.checkIn(dates[0]);

            _this3.model.checkOut(dates[1]);
          }
        },
        onPreCalendarPosition: function onPreCalendarPosition(_, __, fp) {
          var _this4 = this;

          fp._positionElement = external_jQuery_default()('.searchbox__box--checkout', self.$el)[0];
          setTimeout(function () {
            _this4._positionElement = _this4._input;
          }, 0);
        }
      });
      external_jQuery_default()(this.$el).on('click', '.searchbox__box--checkin, .searchbox__box--checkout', function (e) {
        e.preventDefault();
        fp.isOpen = false;
        fp.open(undefined, e.currentTarget);
      });
    }
  }, {
    key: "_setuptPopper",
    value: function _setuptPopper(el) {
      var plugin = window.awebooking;
      var $html = external_jQuery_default()(el).find('.searchbox__popup');

      if ($html.length === 0) {
        return;
      }

      plugin.utils.dropdown(external_jQuery_default()(el).find('.searchbox__box-wrap'), {
        drop: '.searchbox__popup',
        display: 'static'
      });
    }
  }]);

  return OldSearchForm;
}();


// CONCATENATED MODULE: ./assets/babel/search-form.js



external_jQuery_default()(function () {
  external_jQuery_default()('.searchbox, .abrs-searchbox').each(function (index, element) {
    var form = new SearchForm_SearchForm(element, index);

    if (!element.classList.contains('searchbox--experiment-style')) {
      new old_OldSearchForm(element, form);
    }
  });
});

/***/ }),

/***/ "pRvc":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isSameDay;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function isSameDay(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false; // Compare least significant, most likely to change units first
  // Moment's isSame clones moment inputs and is a tad slow

  return a.date() === b.date() && a.month() === b.month() && a.year() === b.year();
}

/***/ }),

/***/ "wy2R":
/***/ (function(module, exports) {

module.exports = moment;

/***/ }),

/***/ "xeH2":
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });