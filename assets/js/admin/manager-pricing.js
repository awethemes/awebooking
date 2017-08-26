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
/******/ 	return __webpack_require__(__webpack_require__.s = 12);
/******/ })
/************************************************************************/
/******/ ({

/***/ 12:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(13);


/***/ }),

/***/ 13:
/***/ (function(module, exports) {

var $ = window.jQuery;
var awebooking = window.TheAweBooking;

$(function () {

  var rangepicker = new awebooking.RangeDatepicker('input[name="datepicker-start"]', 'input[name="datepicker-end"]');

  rangepicker.init();
  var $dialog = awebooking.Popup.setup('#awebooking-set-price-popup');

  var onApplyCalendar = function onApplyCalendar() {
    var calendar = this;
    calendar.data_id = this.$el.closest('[data-unit]').data('unit');

    var formTemplate = wp.template('pricing-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));
    $dialog.dialog('open');
  };

  $('.abkngcal--pricing-calendar', document).each(function (index, el) {
    var calendar = new PricingCalendar(el);
    calendar.on('apply', onApplyCalendar);
  });
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMmQzOWIxYzM5NGE3YTBiMjBhN2MiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL21hbmFnZXItcHJpY2luZy5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwialF1ZXJ5IiwiYXdlYm9va2luZyIsIlRoZUF3ZUJvb2tpbmciLCJyYW5nZXBpY2tlciIsIlJhbmdlRGF0ZXBpY2tlciIsImluaXQiLCIkZGlhbG9nIiwiUG9wdXAiLCJzZXR1cCIsIm9uQXBwbHlDYWxlbmRhciIsImNhbGVuZGFyIiwiZGF0YV9pZCIsIiRlbCIsImNsb3Nlc3QiLCJkYXRhIiwiZm9ybVRlbXBsYXRlIiwid3AiLCJ0ZW1wbGF0ZSIsImZpbmQiLCJodG1sIiwiZGlhbG9nIiwiZG9jdW1lbnQiLCJlYWNoIiwiaW5kZXgiLCJlbCIsIlByaWNpbmdDYWxlbmRhciIsIm9uIl0sIm1hcHBpbmdzIjoiO0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7Ozs7QUM3REEsSUFBTUEsSUFBSUMsT0FBT0MsTUFBakI7QUFDQSxJQUFNQyxhQUFhRixPQUFPRyxhQUExQjs7QUFFQUosRUFBRSxZQUFXOztBQUVYLE1BQU1LLGNBQWMsSUFBSUYsV0FBV0csZUFBZixDQUNsQixnQ0FEa0IsRUFFbEIsOEJBRmtCLENBQXBCOztBQUtBRCxjQUFZRSxJQUFaO0FBQ0EsTUFBTUMsVUFBVUwsV0FBV00sS0FBWCxDQUFpQkMsS0FBakIsQ0FBdUIsNkJBQXZCLENBQWhCOztBQUVBLE1BQU1DLGtCQUFrQixTQUFsQkEsZUFBa0IsR0FBVztBQUNqQyxRQUFNQyxXQUFXLElBQWpCO0FBQ0FBLGFBQVNDLE9BQVQsR0FBbUIsS0FBS0MsR0FBTCxDQUFTQyxPQUFULENBQWlCLGFBQWpCLEVBQWdDQyxJQUFoQyxDQUFxQyxNQUFyQyxDQUFuQjs7QUFFQSxRQUFNQyxlQUFlQyxHQUFHQyxRQUFILENBQVksdUJBQVosQ0FBckI7QUFDQVgsWUFBUVksSUFBUixDQUFhLDZCQUFiLEVBQTRDQyxJQUE1QyxDQUFpREosYUFBYUwsUUFBYixDQUFqRDtBQUNBSixZQUFRYyxNQUFSLENBQWUsTUFBZjtBQUNELEdBUEQ7O0FBU0F0QixJQUFFLDZCQUFGLEVBQWlDdUIsUUFBakMsRUFBMkNDLElBQTNDLENBQWdELFVBQVNDLEtBQVQsRUFBZ0JDLEVBQWhCLEVBQW9CO0FBQ2xFLFFBQU1kLFdBQVcsSUFBSWUsZUFBSixDQUFvQkQsRUFBcEIsQ0FBakI7QUFDQWQsYUFBU2dCLEVBQVQsQ0FBWSxPQUFaLEVBQXFCakIsZUFBckI7QUFDRCxHQUhEO0FBS0QsQ0F4QkQsRSIsImZpbGUiOiIvanMvYWRtaW4vbWFuYWdlci1wcmljaW5nLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMTIpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDJkMzliMWMzOTRhN2EwYjIwYTdjIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBhd2Vib29raW5nID0gd2luZG93LlRoZUF3ZUJvb2tpbmc7XG5cbiQoZnVuY3Rpb24oKSB7XG5cbiAgY29uc3QgcmFuZ2VwaWNrZXIgPSBuZXcgYXdlYm9va2luZy5SYW5nZURhdGVwaWNrZXIoXG4gICAgJ2lucHV0W25hbWU9XCJkYXRlcGlja2VyLXN0YXJ0XCJdJyxcbiAgICAnaW5wdXRbbmFtZT1cImRhdGVwaWNrZXItZW5kXCJdJ1xuICApO1xuXG4gIHJhbmdlcGlja2VyLmluaXQoKTtcbiAgY29uc3QgJGRpYWxvZyA9IGF3ZWJvb2tpbmcuUG9wdXAuc2V0dXAoJyNhd2Vib29raW5nLXNldC1wcmljZS1wb3B1cCcpO1xuXG4gIGNvbnN0IG9uQXBwbHlDYWxlbmRhciA9IGZ1bmN0aW9uKCkge1xuICAgIGNvbnN0IGNhbGVuZGFyID0gdGhpcztcbiAgICBjYWxlbmRhci5kYXRhX2lkID0gdGhpcy4kZWwuY2xvc2VzdCgnW2RhdGEtdW5pdF0nKS5kYXRhKCd1bml0Jyk7XG5cbiAgICBjb25zdCBmb3JtVGVtcGxhdGUgPSB3cC50ZW1wbGF0ZSgncHJpY2luZy1jYWxlbmRhci1mb3JtJyk7XG4gICAgJGRpYWxvZy5maW5kKCcuYXdlYm9va2luZy1kaWFsb2ctY29udGVudHMnKS5odG1sKGZvcm1UZW1wbGF0ZShjYWxlbmRhcikpO1xuICAgICRkaWFsb2cuZGlhbG9nKCdvcGVuJyk7XG4gIH07XG5cbiAgJCgnLmFia25nY2FsLS1wcmljaW5nLWNhbGVuZGFyJywgZG9jdW1lbnQpLmVhY2goZnVuY3Rpb24oaW5kZXgsIGVsKSB7XG4gICAgY29uc3QgY2FsZW5kYXIgPSBuZXcgUHJpY2luZ0NhbGVuZGFyKGVsKTtcbiAgICBjYWxlbmRhci5vbignYXBwbHknLCBvbkFwcGx5Q2FsZW5kYXIpO1xuICB9KTtcblxufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vbWFuYWdlci1wcmljaW5nLmpzIl0sInNvdXJjZVJvb3QiOiIifQ==