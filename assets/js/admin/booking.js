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
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */,
/* 1 */,
/* 2 */,
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(4);


/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

var Popup = __webpack_require__(5);

(function ($) {

  // const a = new Popup('body');

  // console.log(a)

})(jQuery);

/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(6);

var Popup = function () {
  function Popup(el) {
    _classCallCheck(this, Popup);

    this.$el = $(el);

    $(document).on('click', this.$el, function (e) {
      e.preventDefault();
    });

    console.log(Utils.getSelectorFromElement(this.$el[0]));
  }

  _createClass(Popup, [{
    key: 'setupDialog',
    value: function setupDialog() {
      $('#my-dialog').dialog({
        title: 'My Dialog',
        dialogClass: 'wp-dialog',
        autoOpen: false,
        draggable: false,
        width: 'auto',
        modal: true,
        resizable: false,
        closeOnEscape: true,
        position: {
          at: "center top+30%"
        },
        open: function open() {
          $("body").css({ overflow: 'hidden' });
          // close dialog by clicking the overlay behind it
          $('.ui-widget-overlay').bind('click', function () {
            // $('#my-dialog').dialog('close');
          });
        },
        beforeClose: function beforeClose(event, ui) {
          $("body").css({ overflow: 'inherit' });
        },
        create: function create() {
          // style fix for WordPress admin
          // $('.ui-dialog-titlebar-close').addClass('ui-button');
        }
      });
    }
  }, {
    key: 'openPopup',
    value: function openPopup() {}
  }, {
    key: 'closePopup',
    value: function closePopup() {}
  }]);

  return Popup;
}();

module.exports = Popup;

/***/ }),
/* 6 */
/***/ (function(module, exports) {

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMjgwY2IxZDg2Y2M0ODk0NTViOGEiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2Jvb2tpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3BvcHVwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy91dGlscy5qcyJdLCJuYW1lcyI6WyJQb3B1cCIsInJlcXVpcmUiLCIkIiwialF1ZXJ5Iiwid2luZG93IiwiVXRpbHMiLCJlbCIsIiRlbCIsImRvY3VtZW50Iiwib24iLCJlIiwicHJldmVudERlZmF1bHQiLCJjb25zb2xlIiwibG9nIiwiZ2V0U2VsZWN0b3JGcm9tRWxlbWVudCIsImRpYWxvZyIsInRpdGxlIiwiZGlhbG9nQ2xhc3MiLCJhdXRvT3BlbiIsImRyYWdnYWJsZSIsIndpZHRoIiwibW9kYWwiLCJyZXNpemFibGUiLCJjbG9zZU9uRXNjYXBlIiwicG9zaXRpb24iLCJhdCIsIm9wZW4iLCJjc3MiLCJvdmVyZmxvdyIsImJpbmQiLCJiZWZvcmVDbG9zZSIsImV2ZW50IiwidWkiLCJjcmVhdGUiLCJtb2R1bGUiLCJleHBvcnRzIiwic2VsZWN0b3IiLCJnZXRBdHRyaWJ1dGUiLCIkc2VsZWN0b3IiLCJsZW5ndGgiLCJlcnJvciJdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7Ozs7Ozs7OztBQzdEQSxJQUFJQSxRQUFRLG1CQUFBQyxDQUFRLENBQVIsQ0FBWjs7QUFFQSxDQUFDLFVBQVNDLENBQVQsRUFBWTs7QUFFWDs7QUFFQTs7QUFFRCxDQU5ELEVBTUdDLE1BTkgsRTs7Ozs7Ozs7OztBQ0ZBLElBQU1ELElBQUlFLE9BQU9ELE1BQWpCO0FBQ0EsSUFBTUUsUUFBUSxtQkFBQUosQ0FBUSxDQUFSLENBQWQ7O0lBRU1ELEs7QUFDSixpQkFBWU0sRUFBWixFQUFnQjtBQUFBOztBQUNkLFNBQUtDLEdBQUwsR0FBV0wsRUFBRUksRUFBRixDQUFYOztBQUVBSixNQUFFTSxRQUFGLEVBQVlDLEVBQVosQ0FBZSxPQUFmLEVBQXdCLEtBQUtGLEdBQTdCLEVBQWtDLFVBQVNHLENBQVQsRUFBWTtBQUM1Q0EsUUFBRUMsY0FBRjtBQUNELEtBRkQ7O0FBS0FDLFlBQVFDLEdBQVIsQ0FBWVIsTUFBTVMsc0JBQU4sQ0FBNkIsS0FBS1AsR0FBTCxDQUFTLENBQVQsQ0FBN0IsQ0FBWjtBQUNEOzs7O2tDQUVhO0FBQ1pMLFFBQUUsWUFBRixFQUFnQmEsTUFBaEIsQ0FBdUI7QUFDckJDLGVBQU8sV0FEYztBQUVyQkMscUJBQWEsV0FGUTtBQUdyQkMsa0JBQVUsS0FIVztBQUlyQkMsbUJBQVcsS0FKVTtBQUtyQkMsZUFBTyxNQUxjO0FBTXJCQyxlQUFPLElBTmM7QUFPckJDLG1CQUFXLEtBUFU7QUFRckJDLHVCQUFlLElBUk07QUFTckJDLGtCQUFVO0FBQ1JDLGNBQUk7QUFESSxTQVRXO0FBWXJCQyxjQUFNLGdCQUFZO0FBQ2hCeEIsWUFBRSxNQUFGLEVBQVV5QixHQUFWLENBQWMsRUFBRUMsVUFBVSxRQUFaLEVBQWQ7QUFDQTtBQUNBMUIsWUFBRSxvQkFBRixFQUF3QjJCLElBQXhCLENBQTZCLE9BQTdCLEVBQXNDLFlBQVU7QUFDOUM7QUFDRCxXQUZEO0FBR0QsU0FsQm9CO0FBbUJyQkMscUJBQWEscUJBQVNDLEtBQVQsRUFBZ0JDLEVBQWhCLEVBQW9CO0FBQ2pDOUIsWUFBRSxNQUFGLEVBQVV5QixHQUFWLENBQWMsRUFBRUMsVUFBVSxTQUFaLEVBQWQ7QUFDQSxTQXJCcUI7QUFzQnJCSyxnQkFBUSxrQkFBWTtBQUNsQjtBQUNBO0FBQ0Q7QUF6Qm9CLE9BQXZCO0FBMkJEOzs7Z0NBRVcsQ0FFWDs7O2lDQUVZLENBRVo7Ozs7OztBQUdIQyxPQUFPQyxPQUFQLEdBQWlCbkMsS0FBakIsQzs7Ozs7O0FDdERBLElBQU1LLFFBQVE7QUFFWlMsd0JBRlksa0NBRVdSLEVBRlgsRUFFZTtBQUN6QixRQUFJOEIsV0FBVzlCLEdBQUcrQixZQUFILENBQWdCLGFBQWhCLENBQWY7O0FBRUEsUUFBSSxDQUFDRCxRQUFELElBQWFBLGFBQWEsR0FBOUIsRUFBbUM7QUFDakNBLGlCQUFXOUIsR0FBRytCLFlBQUgsQ0FBZ0IsTUFBaEIsS0FBMkIsRUFBdEM7QUFDRDs7QUFFRCxRQUFJO0FBQ0YsVUFBTUMsWUFBWXBDLEVBQUVrQyxRQUFGLENBQWxCO0FBQ0EsYUFBT0UsVUFBVUMsTUFBVixHQUFtQixDQUFuQixHQUF1QkgsUUFBdkIsR0FBa0MsSUFBekM7QUFDRCxLQUhELENBR0UsT0FBT0ksS0FBUCxFQUFjO0FBQ2QsYUFBTyxJQUFQO0FBQ0Q7QUFDRjtBQWZXLENBQWQ7O0FBbUJBTixPQUFPQyxPQUFQLEdBQWlCOUIsS0FBakIsQyIsImZpbGUiOiIvanMvYWRtaW4vYm9va2luZy5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDMpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDI4MGNiMWQ4NmNjNDg5NDU1YjhhIiwibGV0IFBvcHVwID0gcmVxdWlyZSgnLi91dGlscy9wb3B1cC5qcycpO1xuXG4oZnVuY3Rpb24oJCkge1xuXG4gIC8vIGNvbnN0IGEgPSBuZXcgUG9wdXAoJ2JvZHknKTtcblxuICAvLyBjb25zb2xlLmxvZyhhKVxuXG59KShqUXVlcnkpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL2Jvb2tpbmcuanMiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IFV0aWxzID0gcmVxdWlyZSgnLi91dGlscy5qcycpO1xuXG5jbGFzcyBQb3B1cCB7XG4gIGNvbnN0cnVjdG9yKGVsKSB7XG4gICAgdGhpcy4kZWwgPSAkKGVsKTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMuJGVsLCBmdW5jdGlvbihlKSB7XG4gICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgfSk7XG5cblxuICAgIGNvbnNvbGUubG9nKFV0aWxzLmdldFNlbGVjdG9yRnJvbUVsZW1lbnQodGhpcy4kZWxbMF0pKTtcbiAgfVxuXG4gIHNldHVwRGlhbG9nKCkge1xuICAgICQoJyNteS1kaWFsb2cnKS5kaWFsb2coe1xuICAgICAgdGl0bGU6ICdNeSBEaWFsb2cnLFxuICAgICAgZGlhbG9nQ2xhc3M6ICd3cC1kaWFsb2cnLFxuICAgICAgYXV0b09wZW46IGZhbHNlLFxuICAgICAgZHJhZ2dhYmxlOiBmYWxzZSxcbiAgICAgIHdpZHRoOiAnYXV0bycsXG4gICAgICBtb2RhbDogdHJ1ZSxcbiAgICAgIHJlc2l6YWJsZTogZmFsc2UsXG4gICAgICBjbG9zZU9uRXNjYXBlOiB0cnVlLFxuICAgICAgcG9zaXRpb246IHtcbiAgICAgICAgYXQ6IFwiY2VudGVyIHRvcCszMCVcIixcbiAgICAgIH0sXG4gICAgICBvcGVuOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICQoXCJib2R5XCIpLmNzcyh7IG92ZXJmbG93OiAnaGlkZGVuJyB9KVxuICAgICAgICAvLyBjbG9zZSBkaWFsb2cgYnkgY2xpY2tpbmcgdGhlIG92ZXJsYXkgYmVoaW5kIGl0XG4gICAgICAgICQoJy51aS13aWRnZXQtb3ZlcmxheScpLmJpbmQoJ2NsaWNrJywgZnVuY3Rpb24oKXtcbiAgICAgICAgICAvLyAkKCcjbXktZGlhbG9nJykuZGlhbG9nKCdjbG9zZScpO1xuICAgICAgICB9KVxuICAgICAgfSxcbiAgICAgIGJlZm9yZUNsb3NlOiBmdW5jdGlvbihldmVudCwgdWkpIHtcbiAgICAgICQoXCJib2R5XCIpLmNzcyh7IG92ZXJmbG93OiAnaW5oZXJpdCcgfSlcbiAgICAgfSxcbiAgICAgIGNyZWF0ZTogZnVuY3Rpb24gKCkge1xuICAgICAgICAvLyBzdHlsZSBmaXggZm9yIFdvcmRQcmVzcyBhZG1pblxuICAgICAgICAvLyAkKCcudWktZGlhbG9nLXRpdGxlYmFyLWNsb3NlJykuYWRkQ2xhc3MoJ3VpLWJ1dHRvbicpO1xuICAgICAgfSxcbiAgICB9KTtcbiAgfVxuXG4gIG9wZW5Qb3B1cCgpIHtcblxuICB9XG5cbiAgY2xvc2VQb3B1cCgpIHtcblxuICB9XG59XG5cbm1vZHVsZS5leHBvcnRzID0gUG9wdXA7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvcG9wdXAuanMiLCJjb25zdCBVdGlscyA9IHtcblxuICBnZXRTZWxlY3RvckZyb21FbGVtZW50KGVsKSB7XG4gICAgbGV0IHNlbGVjdG9yID0gZWwuZ2V0QXR0cmlidXRlKCdkYXRhLXRhcmdldCcpO1xuXG4gICAgaWYgKCFzZWxlY3RvciB8fCBzZWxlY3RvciA9PT0gJyMnKSB7XG4gICAgICBzZWxlY3RvciA9IGVsLmdldEF0dHJpYnV0ZSgnaHJlZicpIHx8ICcnO1xuICAgIH1cblxuICAgIHRyeSB7XG4gICAgICBjb25zdCAkc2VsZWN0b3IgPSAkKHNlbGVjdG9yKTtcbiAgICAgIHJldHVybiAkc2VsZWN0b3IubGVuZ3RoID4gMCA/IHNlbGVjdG9yIDogbnVsbDtcbiAgICB9IGNhdGNoIChlcnJvcikge1xuICAgICAgcmV0dXJuIG51bGw7XG4gICAgfVxuICB9LFxuXG59O1xuXG5tb2R1bGUuZXhwb3J0cyA9IFV0aWxzO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3V0aWxzLmpzIl0sInNvdXJjZVJvb3QiOiIifQ==