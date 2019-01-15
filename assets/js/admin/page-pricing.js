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
/******/ 	return __webpack_require__(__webpack_require__.s = 7);
/******/ })
/************************************************************************/
/******/ ({

/***/ 7:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("EUu/");


/***/ }),

/***/ "EUu/":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("xeH2");
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


var plugin = window.awebooking;
var DATE_FORMAT = 'YYYY-MM-DD';

var PricingScheduler =
/*#__PURE__*/
function () {
  /**
   * Constructor.
   *
   * @return {void}
   */
  function PricingScheduler() {
    _classCallCheck(this, PricingScheduler);

    this.flatpickr = null;
    this.scheduler = new ScheduleCalendar({
      el: '.scheduler',
      debug: plugin.debug,
      granularity: 'daily'
    });
    this.$dialog = plugin.dialog('#scheduler-form-dialog');
    this.scheduler.on('clear', this.handleClearSelected.bind(this));
    this.scheduler.on('action:set-price', this.handleSetPrice.bind(this));
    this.scheduler.on('action:reset-price', this.handleResetPrice.bind(this));
    this.initBulkUpdate();
  }
  /**
   * Handle on clear selected.
   *
   * @return {void}
   */


  _createClass(PricingScheduler, [{
    key: "handleClearSelected",
    value: function handleClearSelected() {
      window.swal && swal.close();

      if (this.flatpickr) {
        this.flatpickr.destroy();
      }

      this.$dialog.dialog('close');
      jquery__WEBPACK_IMPORTED_MODULE_0___default()('#js-scheduler-form-controls').html('');
    }
    /**
     * Handle set price action.
     *
     * @param  {Event}  e
     * @param  {Object} model
     * @return {void}
     */

  }, {
    key: "handleSetPrice",
    value: function handleSetPrice(e, model) {
      window.swal && swal.close();
      this.compileHtmlControls('set_price', 0);
      this.$dialog.dialog('open');
    }
    /**
     * Handle reset price action.
     *
     * @param  {Event}  e
     * @param  {Object} model
     * @return {void}
     */

  }, {
    key: "handleResetPrice",
    value: function handleResetPrice(e, model) {
      var _this = this;

      plugin.confirm(plugin.i18n.warning, function () {
        var $controls = _this.compileHtmlControls('reset_price', 0);

        $controls.closest('form').submit();
      });
    }
    /**
     * Compile html form controls.
     *
     * @param  {string} action
     * @param  {float}  amount
     * @return {void}
     */

  }, {
    key: "compileHtmlControls",
    value: function compileHtmlControls(action, amount) {
      var model = this.scheduler.model;
      var template = wp.template('scheduler-pricing-controls');
      var roomtype = {};

      if (window._listRoomTypes) {
        roomtype = _.findWhere(window._listRoomTypes, {
          id: model.get('calendar')
        });
      } // Destroy flatpickr first.


      if (this.flatpickr) {
        this.flatpickr.destroy();
      } // Compile the html template.


      var $controls = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#js-scheduler-form-controls').html(template({
        action: action,
        amount: amount,
        roomtype: roomtype,
        calendar: model.get('calendar'),
        endDate: model.get('endDate').format(DATE_FORMAT),
        startDate: model.get('startDate').format(DATE_FORMAT)
      })); // Create the flatpickr after.

      this.flatpickr = flatpickr('#date_start', {
        dateFormat: 'Y-m-d',
        plugins: [new plugin.utils.flatpickrRangePlugin({
          input: '#date_end'
        })]
      });
      return $controls;
    }
    /**
     * Handle bulk update action.
     */

  }, {
    key: "initBulkUpdate",
    value: function initBulkUpdate() {
      var $dialog = plugin.dialog('#bulk-update-dialog');
      jquery__WEBPACK_IMPORTED_MODULE_0___default()('.js-open-bulk-update').on('click', function (e) {
        e.preventDefault();
        $dialog.dialog('open');
      });
      flatpickr('#bulk_date_start', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        showMonths: plugin.isMobile() ? 1 : 2,
        plugins: [new plugin.utils.flatpickrRangePlugin({
          input: '#bulk_date_end'
        })]
      });
    }
  }]);

  return PricingScheduler;
}();
/**
 * Document ready!
 *
 * @return {void}
 */


jquery__WEBPACK_IMPORTED_MODULE_0___default()(function () {
  new PricingScheduler();
});

/***/ }),

/***/ "xeH2":
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });