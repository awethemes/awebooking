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
 * @param {mixed} a
 * @param {mixed} b
 * @return {bool}
 */


function isEquals(a, b) {
  var underscore = window._ || window.lodash;

  if (underscore) {
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

    if (typeof this.initialize === 'function') {
      this.initialize.apply(this, arguments);
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
     * @return {mixed}
     */

  }, {
    key: "get",
    value: function get() {
      return this._value;
    }
    /**
     * Set the value and trigger all bound callbacks.
     *
     * @param {mixed} to New value.
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
     * @param {mixed} value
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


// CONCATENATED MODULE: ./assets/babel/search-form.js
function search_form_classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

function search_form_defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function search_form_createClass(Constructor, protoProps, staticProps) {
  if (protoProps) search_form_defineProperties(Constructor.prototype, protoProps);
  if (staticProps) search_form_defineProperties(Constructor, staticProps);
  return Constructor;
}




var search_form_SearchForm =
/*#__PURE__*/
function () {
  function SearchForm(root, instance) {
    search_form_classCallCheck(this, SearchForm);

    this.root = external_jQuery_default()(root);
    this.instance = instance; // Store the input elements

    this.elements = {};
    this.linkElements();
    var self = this;
    window.createDatePicker(this, {
      onChange: function onChange(props) {
        var startDate = props.startDate,
            endDate = props.endDate;
        var elements = self.elements;
        elements['check_in'].set(startDate ? startDate.format('YYYY-MM-DD') : '');
        elements['check_out'].set(endDate ? endDate.format('YYYY-MM-DD') : '');
      }
    });

    if (this.elements.hasOwnProperty('check_in_alt')) {
      this.elements['check_in_alt'].sync(this.elements['check_in']);
    }

    if (this.elements.hasOwnProperty('check_out')) {
      this.elements['check_out_alt'].sync(this.elements['check_out']);
    }
  }

  search_form_createClass(SearchForm, [{
    key: "getRootElement",
    value: function getRootElement() {
      return this.root[0];
    }
    /**
     * Link elements between settings and inputs
     */

  }, {
    key: "linkElements",
    value: function linkElements() {
      var control = this;
      var nodes = control.root.find('[data-element]');
      var radios = {};
      nodes.each(function (index, element) {
        var node = external_jQuery_default()(element);

        if (node.data('_controlSettingLinked')) {
          return;
        } // Prevent re-linking element.


        node.data('_controlSettingLinked', true);

        if (node.is(':radio')) {
          var name = node.prop('name');

          if (radios[name]) {
            return;
          }

          radios[name] = true;
          node = nodes.filter('[name="' + name + '"]');
        }

        if (node.data('element')) {
          index = node.data('element');
        }

        control.elements[index] = new control_Control(node);
      });
    }
  }]);

  return SearchForm;
}();

external_jQuery_default()(function () {
  external_jQuery_default()('.awebooking .searchbox, .awebooking-block .searchbox').each(function (i, el) {
    new search_form_SearchForm(el, i);
  });
});

/***/ }),

/***/ "xeH2":
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });