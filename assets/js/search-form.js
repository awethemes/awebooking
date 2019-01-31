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

/***/ "./assets/babel/search-form.js":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _search_form_SearchForm__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("./assets/babel/search-form/SearchForm.js");
/* harmony import */ var _search_form_old__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__("./assets/babel/search-form/old.js");



jquery__WEBPACK_IMPORTED_MODULE_0___default()(function () {
  jquery__WEBPACK_IMPORTED_MODULE_0___default()('.searchbox, .abrs-searchbox').each(function (index, element) {
    var form = new _search_form_SearchForm__WEBPACK_IMPORTED_MODULE_1__["default"](element, index);

    if (!element.classList.contains('searchbox--experiment-style')) {
      new _search_form_old__WEBPACK_IMPORTED_MODULE_2__["default"](element, form);
    }
  });
});

/***/ }),

/***/ "./assets/babel/search-form/SearchForm.js":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SearchForm; });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_control__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("./assets/babel/utils/control.js");
/* harmony import */ var _utils_date_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__("./assets/babel/utils/date-utils.js");
/* harmony import */ var react_dates_lib_utils_isSameDay__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__("./node_modules/react-dates/lib/utils/isSameDay.js");
/* harmony import */ var react_dates_lib_utils_isSameDay__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react_dates_lib_utils_isSameDay__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var react_dates_lib_utils_toMomentObject__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__("./node_modules/react-dates/lib/utils/toMomentObject.js");
/* harmony import */ var react_dates_lib_utils_toMomentObject__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_dates_lib_utils_toMomentObject__WEBPACK_IMPORTED_MODULE_4__);
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







var SearchForm =
/*#__PURE__*/
function () {
  function SearchForm(root, instance) {
    _classCallCheck(this, SearchForm);

    this.root = jquery__WEBPACK_IMPORTED_MODULE_0___default()(root);
    this.instance = instance; // Store the input elements

    this.elements = {};
    this.linkElements();

    this._registerBindings();

    if (window.createReactDatePicker && this.root.find('.abrs-searchbox__dates').length > 0) {
      this._createDatePicker();
    }
  }

  _createClass(SearchForm, [{
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
        return react_dates_lib_utils_toMomentObject__WEBPACK_IMPORTED_MODULE_4___default()(day);
      });

      var isDayBlocked = function isDayBlocked(day) {
        var disabled = false;

        if (Array.isArray(disableDays) && disableDays.length > 0) {
          disabled = disableDays.includes(parseInt(day.format('d'), 10));
        }

        if (!disabled && disableDates.length > 0) {
          disabled = disableDates.some(function (test) {
            return react_dates_lib_utils_isSameDay__WEBPACK_IMPORTED_MODULE_3___default()(day, test);
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
          _this.elements[bind].set(value ? Object(_utils_date_utils__WEBPACK_IMPORTED_MODULE_2__["formatDateString"])(value) : '');
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
        var node = jquery__WEBPACK_IMPORTED_MODULE_0___default()(element);

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

        control.elements[index] = new _utils_control__WEBPACK_IMPORTED_MODULE_1__["default"](node);
      });
    }
  }]);

  return SearchForm;
}();



/***/ }),

/***/ "./assets/babel/search-form/old.js":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return OldSearchForm; });
/* harmony import */ var ko__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ko");
/* harmony import */ var ko__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(ko__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_date_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__("./assets/babel/utils/date-utils.js");
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





var SearchFormModel =
/*#__PURE__*/
function () {
  function SearchFormModel(data) {
    var _this = this;

    _classCallCheck(this, SearchFormModel);

    this.hotel = ko__WEBPACK_IMPORTED_MODULE_0___default.a.observable(data.adults || 0);
    this.adults = ko__WEBPACK_IMPORTED_MODULE_0___default.a.observable(data.adults || 1);
    this.children = ko__WEBPACK_IMPORTED_MODULE_0___default.a.observable(data.children || 0);
    this.infants = ko__WEBPACK_IMPORTED_MODULE_0___default.a.observable(data.infants || 0);
    this.checkIn = ko__WEBPACK_IMPORTED_MODULE_0___default.a.observable(data.check_in || '');
    this.checkOut = ko__WEBPACK_IMPORTED_MODULE_0___default.a.observable(data.check_out || '');
    this.checkInDate = ko__WEBPACK_IMPORTED_MODULE_0___default.a.computed(function () {
      return Object(_utils_date_utils__WEBPACK_IMPORTED_MODULE_2__["formatDateString"])(_this.checkIn(), 'Y-m-d');
    });
    this.checkOutDate = ko__WEBPACK_IMPORTED_MODULE_0___default.a.computed(function () {
      return Object(_utils_date_utils__WEBPACK_IMPORTED_MODULE_2__["formatDateString"])(_this.checkOut(), 'Y-m-d');
    });
    this.checkInFormatted = ko__WEBPACK_IMPORTED_MODULE_0___default.a.computed(this.checkInFormatted.bind(this));
    this.checkOutFormatted = ko__WEBPACK_IMPORTED_MODULE_0___default.a.computed(this.checkOutFormatted.bind(this));
  }

  _createClass(SearchFormModel, [{
    key: "checkInFormatted",
    value: function checkInFormatted(format) {
      return Object(_utils_date_utils__WEBPACK_IMPORTED_MODULE_2__["formatDateString"])(this.checkIn(), format);
    }
  }, {
    key: "checkOutFormatted",
    value: function checkOutFormatted(format) {
      return Object(_utils_date_utils__WEBPACK_IMPORTED_MODULE_2__["formatDateString"])(this.checkOut(), format);
    }
  }]);

  return SearchFormModel;
}();

var OldSearchForm =
/*#__PURE__*/
function () {
  function OldSearchForm(el, form) {
    var _this2 = this;

    _classCallCheck(this, OldSearchForm);

    this.$el = jquery__WEBPACK_IMPORTED_MODULE_1___default()(el);
    this.form = form;

    this._setupDatePicker();

    this.$el.find('.searchbox__box').each(function (i, box) {
      jquery__WEBPACK_IMPORTED_MODULE_1___default()(box).data('popup', _this2._setuptPopper(box));
    });
    this.$el.find('[data-trigger="spinner"]').on('changed.spinner', function () {
      jquery__WEBPACK_IMPORTED_MODULE_1___default()(this).find('[data-spin="spinner"]').trigger('change');
    });
    this.model = new SearchFormModel(form.getFormData());
    ko__WEBPACK_IMPORTED_MODULE_0___default.a.applyBindings(this.model, el);
  }

  _createClass(OldSearchForm, [{
    key: "_setupDatePicker",
    value: function _setupDatePicker() {
      var _this3 = this;

      var self = this;
      var plugin = window.awebooking;
      var $rangepicker = this.$el.find('[data-hotel="rangepicker"]');

      if ($rangepicker.length === 0) {
        $rangepicker = jquery__WEBPACK_IMPORTED_MODULE_1___default()('<input type="text" data-hotel="rangepicker"/>').appendTo(this.$el);
      }

      var fp = plugin.datepicker($rangepicker[0], {
        mode: 'range',
        altInput: false,
        clickOpens: false,
        closeOnSelect: true,
        onReady: function onReady(_, __, fp) {
          fp.calendarContainer.classList.add('awebooking-datepicker');
          this.config.ignoredFocusElements.push(jquery__WEBPACK_IMPORTED_MODULE_1___default()('.searchbox__box--checkin', self.$el)[0]);
          this.config.ignoredFocusElements.push(jquery__WEBPACK_IMPORTED_MODULE_1___default()('.searchbox__box--checkout', self.$el)[0]);
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

          fp._positionElement = jquery__WEBPACK_IMPORTED_MODULE_1___default()('.searchbox__box--checkout', self.$el)[0];
          setTimeout(function () {
            _this4._positionElement = _this4._input;
          }, 0);
        }
      });
      jquery__WEBPACK_IMPORTED_MODULE_1___default()(this.$el).on('click', '.searchbox__box--checkin, .searchbox__box--checkout', function (e) {
        e.preventDefault();
        fp.isOpen = false;
        fp.open(undefined, e.currentTarget);
      });
    }
  }, {
    key: "_setuptPopper",
    value: function _setuptPopper(el) {
      var plugin = window.awebooking;
      var $html = jquery__WEBPACK_IMPORTED_MODULE_1___default()(el).find('.searchbox__popup');

      if ($html.length === 0) {
        return;
      }

      plugin.utils.dropdown(jquery__WEBPACK_IMPORTED_MODULE_1___default()(el).find('.searchbox__box-wrap'), {
        drop: '.searchbox__popup',
        display: 'static'
      });
    }
  }]);

  return OldSearchForm;
}();



/***/ }),

/***/ "./assets/babel/utils/control.js":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Control; });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
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
  return typeof element == 'string' ? jquery__WEBPACK_IMPORTED_MODULE_0___default()(element) : element;
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


var Control =
/*#__PURE__*/
function () {
  function Control(element) {
    _classCallCheck(this, Control);

    this._value = null;
    this._dirty = false; // Store and manage the callback lists

    this.callbacks = jquery__WEBPACK_IMPORTED_MODULE_0___default.a.Callbacks();
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


    jquery__WEBPACK_IMPORTED_MODULE_0___default.a.extend(this, _synchronizer);

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



/***/ }),

/***/ "./assets/babel/utils/date-utils.js":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "formatDateString", function() { return formatDateString; });
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

/***/ }),

/***/ "./node_modules/react-dates/lib/constants.js":
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

/***/ "./node_modules/react-dates/lib/utils/isSameDay.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isSameDay;

var _moment = _interopRequireDefault(__webpack_require__("moment"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function isSameDay(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false; // Compare least significant, most likely to change units first
  // Moment's isSame clones moment inputs and is a tad slow

  return a.date() === b.date() && a.month() === b.month() && a.year() === b.year();
}

/***/ }),

/***/ "./node_modules/react-dates/lib/utils/toMomentObject.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = toMomentObject;

var _moment = _interopRequireDefault(__webpack_require__("moment"));

var _constants = __webpack_require__("./node_modules/react-dates/lib/constants.js");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function toMomentObject(dateString, customFormat) {
  var dateFormats = customFormat ? [customFormat, _constants.DISPLAY_FORMAT, _constants.ISO_FORMAT] : [_constants.DISPLAY_FORMAT, _constants.ISO_FORMAT];
  var date = (0, _moment["default"])(dateString, dateFormats, true);
  return date.isValid() ? date.hour(12) : null;
}

/***/ }),

/***/ 2:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("./assets/babel/search-form.js");


/***/ }),

/***/ "jquery":
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),

/***/ "ko":
/***/ (function(module, exports) {

module.exports = window.ko;

/***/ }),

/***/ "moment":
/***/ (function(module, exports) {

module.exports = moment;

/***/ })

/******/ });
//# sourceMappingURL=search-form.js.map