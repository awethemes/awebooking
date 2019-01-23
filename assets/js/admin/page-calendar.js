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
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ 6:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("dz+X");


/***/ }),

/***/ "dz+X":
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

var BookingScheduler =
/*#__PURE__*/
function () {
  /**
   * Constructor.
   *
   * @return {void}
   */
  function BookingScheduler() {
    _classCallCheck(this, BookingScheduler);

    var self = this;
    this.initBulkUpdate();
    this.scheduler = new ScheduleCalendar({
      el: '.scheduler',
      debug: plugin.debug,
      granularity: 'nightly'
    });
    this.scheduler.on('clear', this.handleClearSelected.bind(this));
    this.scheduler.on('action:block', this.handleBlockRoom.bind(this));
    this.scheduler.on('action:unblock', this.handleUnblockRoom.bind(this));
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('.js-unlock-period').on('click', function (e) {
      e.preventDefault();
      var $el = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this);
      self.scheduler.model.set('calendar', $el.data('room'));
      self.scheduler.model.set('startDate', moment($el.data('startDate')));
      self.scheduler.model.set('endDate', moment($el.data('endDate')));
      self.scheduler.trigger('action:unblock');
    });
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('.scheduler__state-event, .scheduler__booking-event').each(function () {
      self.setupEventPopper(this);
    });
  }
  /**
   * Handle on clear selected.
   *
   * @return {void}
   */


  _createClass(BookingScheduler, [{
    key: "handleClearSelected",
    value: function handleClearSelected() {
      window.swal && swal.close();
    }
    /**
     * Handle set price action.
     *
     * @param  {Event}  e
     * @param  {Object} model
     * @return {void}
     */

  }, {
    key: "handleBlockRoom",
    value: function handleBlockRoom(e, model) {
      var _this = this;

      plugin.confirm(plugin.i18n.warning, function () {
        var $controls = _this.compileHtmlControls('block', model);

        $controls.closest('form').submit();
      });
    }
    /**
     * Handle reset price action.
     *
     * @param  {Event}  e
     * @param  {Object} model
     * @return {void}
     */

  }, {
    key: "handleUnblockRoom",
    value: function handleUnblockRoom(e, model) {
      var _this2 = this;

      plugin.confirm(plugin.i18n.warning, function () {
        var $controls = _this2.compileHtmlControls('unblock', model);

        $controls.closest('form').submit();
      });
    }
    /**
     * Compile html form controls.
     *
     * @param  {string} action
     * @return {void}
     */

  }, {
    key: "compileHtmlControls",
    value: function compileHtmlControls(action, model) {
      var template = wp.template('scheduler-pricing-controls');

      if (!model) {
        model = this.scheduler.model;
      }

      var data = {
        action: action,
        endDate: model.get('endDate').format(DATE_FORMAT),
        startDate: model.get('startDate').format(DATE_FORMAT),
        calendar: model.get('calendar')
      };
      return jquery__WEBPACK_IMPORTED_MODULE_0___default()('#js-scheduler-form-controls').html(template(data));
    }
    /**
     * Setup event popper.
     *
     * @param  {Object} el
     * @return {void}
     */

  }, {
    key: "setupEventPopper",
    value: function setupEventPopper(el) {
      var $html = jquery__WEBPACK_IMPORTED_MODULE_0___default()(el).find('.js-tippy-html');
      tippy(el, {
        theme: 'booking-popup',
        delay: 150,
        arrow: true,
        distance: 0,
        maxWidth: '500px',
        placement: 'bottom',
        trigger: 'mouseenter focus',
        interactive: true,
        performance: true,
        hideOnClick: false,
        animation: 'shift-toward',
        duration: [150, 150],
        html: $html.length ? $html[0] : false,
        popperOptions: {
          modifiers: {
            hide: {
              enabled: false
            },
            preventOverflow: {
              enabled: false
            }
          }
        }
      });
      return el._tippy;
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

  return BookingScheduler;
}();
/**
 * Document ready!
 *
 * @return {void}
 */


jquery__WEBPACK_IMPORTED_MODULE_0___default()(function () {
  new BookingScheduler();
});

/***/ }),

/***/ "xeH2":
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });