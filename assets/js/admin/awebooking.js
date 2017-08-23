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
module.exports = __webpack_require__(2);


/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

var $ = window.jQuery;
var settings = window._awebookingSettings || {};

var Popup = __webpack_require__(5);

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
   * Make a ajax request
   */
  ajax: function ajax(action, data) {
    var requestData = _.extend(data, {
      action: 'awebooking/' + action
    });

    return $.ajax({
      url: this.ajax_url,
      type: 'POST',
      data: requestData
    }).fail(function () {
      console.log("error");
    });
  },


  /**
   * Get a translator string
   */
  trans: function trans(context) {
    return this.strings[context] ? this.strings[context] : '';
  }
});

$(function () {
  AweBooking.init();
});

window.TheAweBooking = AweBooking;

/***/ }),
/* 2 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 3 */,
/* 4 */,
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(6);

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
        title: $(this.el).attr('title'),
        dialogClass: 'wp-dialog awebooking-dialog',
        modal: true,
        width: 'auto',
        height: 'auto',
        autoOpen: false,
        draggable: false,
        resizable: false,
        closeOnEscape: true,
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
/* 6 */
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

/***/ })
/******/ ]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgZmM4NGE0ZDNlM2RmMzcwMjM4MTAiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2F3ZWJvb2tpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3Nhc3MvYWRtaW4uc2Nzcz9kOWU2Iiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9wb3B1cC5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvdXRpbHMuanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsImpRdWVyeSIsInNldHRpbmdzIiwiX2F3ZWJvb2tpbmdTZXR0aW5ncyIsIlBvcHVwIiwicmVxdWlyZSIsIkF3ZUJvb2tpbmciLCJfIiwiZXh0ZW5kIiwiaW5pdCIsInNlbGYiLCJlYWNoIiwiZGF0YSIsImFqYXgiLCJhY3Rpb24iLCJyZXF1ZXN0RGF0YSIsInVybCIsImFqYXhfdXJsIiwidHlwZSIsImZhaWwiLCJjb25zb2xlIiwibG9nIiwidHJhbnMiLCJjb250ZXh0Iiwic3RyaW5ncyIsIlRoZUF3ZUJvb2tpbmciLCJVdGlscyIsImVsIiwidGFyZ2V0IiwiZ2V0U2VsZWN0b3JGcm9tRWxlbWVudCIsInNldHVwIiwib24iLCJwcm94eSIsIm9wZW4iLCJjbG9zZSIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImRpYWxvZyIsInRpdGxlIiwiYXR0ciIsImRpYWxvZ0NsYXNzIiwibW9kYWwiLCJ3aWR0aCIsImhlaWdodCIsImF1dG9PcGVuIiwiZHJhZ2dhYmxlIiwicmVzaXphYmxlIiwiY2xvc2VPbkVzY2FwZSIsInBvc2l0aW9uIiwiYXQiLCJjc3MiLCJvdmVyZmxvdyIsImJlZm9yZUNsb3NlIiwiZXZlbnQiLCJ1aSIsIm1vZHVsZSIsImV4cG9ydHMiLCJzZWxlY3RvciIsImdldEF0dHJpYnV0ZSIsIiRzZWxlY3RvciIsImxlbmd0aCIsImVycm9yIl0sIm1hcHBpbmdzIjoiO0FBQUE7QUFDQTs7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QixFQUF0QztBQUNEOztBQUVELFFBQUk7QUFDRixVQUFNQyxZQUFZNUQsRUFBRTBELFFBQUYsQ0FBbEI7QUFDQSxhQUFPRSxVQUFVQyxNQUFWLEdBQW1CLENBQW5CLEdBQXVCSCxRQUF2QixHQUFrQyxJQUF6QztBQUNELEtBSEQsQ0FHRSxPQUFPSSxLQUFQLEVBQWM7QUFDZCxhQUFPLElBQVA7QUFDRDtBQUNGO0FBZlcsQ0FBZDs7QUFtQkFOLE9BQU9DLE9BQVAsR0FBaUI5QixLQUFqQixDIiwiZmlsZSI6Ii9qcy9hZG1pbi9hd2Vib29raW5nLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgZmM4NGE0ZDNlM2RmMzcwMjM4MTAiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IHNldHRpbmdzID0gd2luZG93Ll9hd2Vib29raW5nU2V0dGluZ3MgfHwge307XG5cbmNvbnN0IFBvcHVwID0gcmVxdWlyZSgnLi91dGlscy9wb3B1cC5qcycpO1xuXG5jb25zdCBBd2VCb29raW5nID0gXy5leHRlbmQoc2V0dGluZ3MsIHtcbiAgLyoqXG4gICAqIEluaXQgdGhlIEF3ZUJvb2tpbmdcbiAgICovXG4gIGluaXQoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAvLyBJbml0IHRoZSBwb3B1cCwgdXNlIGpxdWVyeS11aS1wb3B1cC5cbiAgICAkKCdbZGF0YS10b2dnbGU9XCJhd2Vib29raW5nLXBvcHVwXCJdJykuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICQodGhpcykuZGF0YSgnYXdlYm9va2luZy1wb3B1cCcsIG5ldyBQb3B1cCh0aGlzKSk7XG4gICAgfSk7XG4gIH0sXG5cbiAgLyoqXG4gICAqIE1ha2UgYSBhamF4IHJlcXVlc3RcbiAgICovXG4gIGFqYXgoYWN0aW9uLCBkYXRhKSB7XG4gICAgdmFyIHJlcXVlc3REYXRhID0gXy5leHRlbmQoZGF0YSwge1xuICAgICAgYWN0aW9uOiAnYXdlYm9va2luZy8nICsgYWN0aW9uXG4gICAgfSk7XG5cbiAgICByZXR1cm4gJC5hamF4KHtcbiAgICAgICAgdXJsOiB0aGlzLmFqYXhfdXJsLFxuICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgIGRhdGE6IHJlcXVlc3REYXRhLFxuICAgICAgfSlcbiAgICAgIC5mYWlsKGZ1bmN0aW9uKCkge1xuICAgICAgICBjb25zb2xlLmxvZyhcImVycm9yXCIpO1xuICAgICAgfSk7XG4gIH0sXG5cbiAgLyoqXG4gICAqIEdldCBhIHRyYW5zbGF0b3Igc3RyaW5nXG4gICAqL1xuICB0cmFucyhjb250ZXh0KSB7XG4gICAgcmV0dXJuIHRoaXMuc3RyaW5nc1tjb250ZXh0XSA/IHRoaXMuc3RyaW5nc1tjb250ZXh0XSA6ICcnO1xuICB9XG59KTtcblxuJChmdW5jdGlvbigpIHtcbiAgQXdlQm9va2luZy5pbml0KCk7XG59KTtcblxud2luZG93LlRoZUF3ZUJvb2tpbmcgPSBBd2VCb29raW5nO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL2F3ZWJvb2tpbmcuanMiLCIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vYXNzZXRzL3Nhc3MvYWRtaW4uc2Nzc1xuLy8gbW9kdWxlIGlkID0gMlxuLy8gbW9kdWxlIGNodW5rcyA9IDEiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IFV0aWxzID0gcmVxdWlyZSgnLi91dGlscy5qcycpO1xuXG5jbGFzcyBQb3B1cCB7XG4gIC8qKlxuICAgKiBXcmFwcGVyIHRoZSBqcXVlcnktdWktcG9wdXAuXG4gICAqL1xuICBjb25zdHJ1Y3RvcihlbCkge1xuICAgIHRoaXMuZWwgPSBlbDtcbiAgICB0aGlzLnRhcmdldCA9IFV0aWxzLmdldFNlbGVjdG9yRnJvbUVsZW1lbnQoZWwpO1xuXG4gICAgaWYgKHRoaXMudGFyZ2V0KSB7XG4gICAgICB0aGlzLnNldHVwKCk7XG5cbiAgICAgICQodGhpcy5lbCkub24oJ2NsaWNrJywgJC5wcm94eSh0aGlzLm9wZW4sIHRoaXMpKTtcbiAgICAgICQodGhpcy50YXJnZXQpLm9uKCdjbGljaycsICdbZGF0YS1kaXNtaXNzPVwiYXdlYm9va2luZy1wb3B1cFwiXScsICQucHJveHkodGhpcy5jbG9zZSwgdGhpcykpO1xuICAgIH1cbiAgfVxuXG4gIG9wZW4oZSkge1xuICAgIGUgJiYgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICQodGhpcy50YXJnZXQpLmRpYWxvZygnb3BlbicpO1xuICB9XG5cbiAgY2xvc2UoZSkge1xuICAgIGUgJiYgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICQodGhpcy50YXJnZXQpLmRpYWxvZygnY2xvc2UnKTtcbiAgfVxuXG4gIHNldHVwKCkge1xuICAgIGlmICgkKHRoaXMudGFyZ2V0KS5kaWFsb2coJ2luc3RhbmNlJykpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAkKHRoaXMudGFyZ2V0KS5kaWFsb2coe1xuICAgICAgdGl0bGU6ICQodGhpcy5lbCkuYXR0cigndGl0bGUnKSxcbiAgICAgIGRpYWxvZ0NsYXNzOiAnd3AtZGlhbG9nIGF3ZWJvb2tpbmctZGlhbG9nJyxcbiAgICAgIG1vZGFsOiB0cnVlLFxuICAgICAgd2lkdGg6ICdhdXRvJyxcbiAgICAgIGhlaWdodDogJ2F1dG8nLFxuICAgICAgYXV0b09wZW46IGZhbHNlLFxuICAgICAgZHJhZ2dhYmxlOiBmYWxzZSxcbiAgICAgIHJlc2l6YWJsZTogZmFsc2UsXG4gICAgICBjbG9zZU9uRXNjYXBlOiB0cnVlLFxuICAgICAgcG9zaXRpb246IHsgYXQ6ICdjZW50ZXIgdG9wKzM1JScgfSxcbiAgICAgIG9wZW46IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgJCgnYm9keScpLmNzcyh7IG92ZXJmbG93OiAnaGlkZGVuJyB9KTtcbiAgICAgIH0sXG4gICAgICBiZWZvcmVDbG9zZTogZnVuY3Rpb24oZXZlbnQsIHVpKSB7XG4gICAgICAgICQoJ2JvZHknKS5jc3MoeyBvdmVyZmxvdzogJ2luaGVyaXQnIH0pO1xuICAgICB9XG4gICAgfSk7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBQb3B1cDtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9wb3B1cC5qcyIsInZhciAkID0gd2luZG93LmpRdWVyeTtcblxuY29uc3QgVXRpbHMgPSB7XG5cbiAgZ2V0U2VsZWN0b3JGcm9tRWxlbWVudChlbCkge1xuICAgIGxldCBzZWxlY3RvciA9IGVsLmdldEF0dHJpYnV0ZSgnZGF0YS10YXJnZXQnKTtcblxuICAgIGlmICghc2VsZWN0b3IgfHwgc2VsZWN0b3IgPT09ICcjJykge1xuICAgICAgc2VsZWN0b3IgPSBlbC5nZXRBdHRyaWJ1dGUoJ2hyZWYnKSB8fCAnJztcbiAgICB9XG5cbiAgICB0cnkge1xuICAgICAgY29uc3QgJHNlbGVjdG9yID0gJChzZWxlY3Rvcik7XG4gICAgICByZXR1cm4gJHNlbGVjdG9yLmxlbmd0aCA+IDAgPyBzZWxlY3RvciA6IG51bGw7XG4gICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgIHJldHVybiBudWxsO1xuICAgIH1cbiAgfSxcblxufTtcblxubW9kdWxlLmV4cG9ydHMgPSBVdGlscztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy91dGlscy5qcyJdLCJzb3VyY2VSb290IjoiIn0=