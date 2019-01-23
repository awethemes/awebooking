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
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ 1:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("yXmg");


/***/ }),

/***/ "xeH2":
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),

/***/ "yXmg":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("xeH2");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

var SELECTED = 'selected';
var Selector = {
  ROOT: '.payment-methods',
  ITEM: '.payment-method',
  INPUT: 'input[type="radio"]',
  SELECTED: '.selected'
};

var _handleLabelClick = function _handleLabelClick(e) {
  var element = e.currentTarget;
  var input = element.querySelector(Selector.INPUT); // Prevent action on non-input.

  if (!input) {
    return;
  }

  var triggerChange = true;
  var rootElement = jquery__WEBPACK_IMPORTED_MODULE_0___default()(element).closest(Selector.ROOT)[0];

  if (input.checked && jquery__WEBPACK_IMPORTED_MODULE_0___default()(element).hasClass(SELECTED)) {
    triggerChange = false;
  } else {
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(rootElement).children(Selector.SELECTED).removeClass(SELECTED);
  }

  if (triggerChange) {
    if (input.hasAttribute('disabled') || input.classList.contains('disabled')) {
      e.preventDefault();
      return;
    }

    input.checked = !element.classList.contains(SELECTED);
    input.focus({
      preventScroll: true
    });
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(input).trigger('change');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(element).addClass(SELECTED);
  }
};

var _triggerPaymentMethod = function _triggerPaymentMethod(e) {
  var input = e.currentTarget;

  if (!input.checked) {
    return;
  }

  var root = jquery__WEBPACK_IMPORTED_MODULE_0___default()(input).closest(Selector.ROOT)[0];
  var event = jquery__WEBPACK_IMPORTED_MODULE_0___default.a.Event('selected.awebooking.gateway', {
    relatedTarget: input
  });
  jquery__WEBPACK_IMPORTED_MODULE_0___default()(root).trigger(event, input.value);
};

jquery__WEBPACK_IMPORTED_MODULE_0___default()(function () {
  var $el = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#payment-methods').on('click', Selector.ITEM, _handleLabelClick).on('change', Selector.INPUT, _triggerPaymentMethod);
  setTimeout(function () {
    $el.find("".concat(Selector.INPUT, ":checked")).closest(Selector.ITEM).trigger('click');
  }, 0);
});

/***/ })

/******/ });