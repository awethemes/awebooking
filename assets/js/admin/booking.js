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
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ 6:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(7);


/***/ }),

/***/ 7:
/***/ (function(module, exports) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var awebooking = window.TheAweBooking;

var EditBooking = function () {
  function EditBooking(form) {
    _classCallCheck(this, EditBooking);

    this.form = form instanceof jQuery ? form[0] : form;
    this.$form = $(this.form);

    this.$form.on('change', '#add_room', $.proxy(this.handleAddRoomChanges, this));
    this.$form.on('change', '#add_check_in_out_0', $.proxy(this.handleDateChanges, this));
    this.$form.on('change', '#add_check_in_out_1', $.proxy(this.handleDateChanges, this));

    $('button[type="submit"]', this.$form).prop('disabled', true);
    this.$form.on('submit', $.proxy(this.onSubmit, this));
  }

  _createClass(EditBooking, [{
    key: 'onSubmit',
    value: function onSubmit(e) {
      e.preventDefault();

      awebooking.ajaxSubmit(this.form, 'add_awebooking_line_item').done(function (response) {

        // TODO: Improve this!
        setTimeout(function () {
          window.location.reload();
        }, 250);
      });
    }
  }, {
    key: 'handleDateChanges',
    value: function handleDateChanges() {
      if (!this.ensureInputDates()) {
        return;
      }

      // If any check-in/out changes,
      // we will reset the `add_room` input.
      this.$form.find('#add_room').val('');

      // Then, call ajax to update new template.
      this.ajaxUpdateForm();
    }
  }, {
    key: 'handleAddRoomChanges',
    value: function handleAddRoomChanges() {
      var self = this;

      if (!this.ensureInputDates()) {
        return;
      }

      this.ajaxUpdateForm().done(function () {
        $('button[type="submit"]', self.$form).prop('disabled', false);
      });
    }
  }, {
    key: 'ajaxUpdateForm',
    value: function ajaxUpdateForm() {
      var self = this;
      var $container = self.$form.find('.awebooking-dialog-contents');

      return awebooking.ajaxSubmit(this.form, 'get_awebooking_add_item_form').done(function (response) {
        $('#add_check_in_out_0', $container).datepicker('destroy');
        $('#add_check_in_out_1', $container).datepicker('destroy');

        $container.html(response.html);
      });
    }
  }, {
    key: 'ensureInputDates',
    value: function ensureInputDates() {
      var $check_in = this.$form.find('#add_check_in_out_0');
      var $check_out = this.$form.find('#add_check_in_out_1');

      return $check_in.val() && $check_out.val();
    }
  }]);

  return EditBooking;
}();

$(function () {

  var $form = $('#awebooking-add-line-item-form');
  if ($form.length > 0) {
    new EditBooking($form);
  }
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNTVjMWYwOTA2OTk4M2I3N2FlZjEiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2Jvb2tpbmcuanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsImpRdWVyeSIsImF3ZWJvb2tpbmciLCJUaGVBd2VCb29raW5nIiwiRWRpdEJvb2tpbmciLCJmb3JtIiwiJGZvcm0iLCJvbiIsInByb3h5IiwiaGFuZGxlQWRkUm9vbUNoYW5nZXMiLCJoYW5kbGVEYXRlQ2hhbmdlcyIsInByb3AiLCJvblN1Ym1pdCIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImFqYXhTdWJtaXQiLCJkb25lIiwicmVzcG9uc2UiLCJzZXRUaW1lb3V0IiwibG9jYXRpb24iLCJyZWxvYWQiLCJlbnN1cmVJbnB1dERhdGVzIiwiZmluZCIsInZhbCIsImFqYXhVcGRhdGVGb3JtIiwic2VsZiIsIiRjb250YWluZXIiLCJkYXRlcGlja2VyIiwiaHRtbCIsIiRjaGVja19pbiIsIiRjaGVja19vdXQiLCJsZW5ndGgiXSwibWFwcGluZ3MiOiI7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUM3REEsSUFBTUEsSUFBSUMsT0FBT0MsTUFBakI7QUFDQSxJQUFNQyxhQUFhRixPQUFPRyxhQUExQjs7SUFF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TUFBSU8sTUFBTXlCLE1BQU4sR0FBZSxDQUFuQixFQUFzQjtBQUNwQixRQUFJM0IsV0FBSixDQUFnQkUsS0FBaEI7QUFDRDtBQUNGLENBTkQsRSIsImZpbGUiOiIvanMvYWRtaW4vYm9va2luZy5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDYpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDU1YzFmMDkwNjk5ODNiNzdhZWYxIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBhd2Vib29raW5nID0gd2luZG93LlRoZUF3ZUJvb2tpbmc7XG5cbmNsYXNzIEVkaXRCb29raW5nIHtcbiAgY29uc3RydWN0b3IoZm9ybSkge1xuICAgIHRoaXMuZm9ybSAgPSAoZm9ybSBpbnN0YW5jZW9mIGpRdWVyeSkgPyBmb3JtWzBdIDogZm9ybTtcbiAgICB0aGlzLiRmb3JtID0gJCh0aGlzLmZvcm0pO1xuXG4gICAgdGhpcy4kZm9ybS5vbignY2hhbmdlJywgJyNhZGRfcm9vbScsICQucHJveHkodGhpcy5oYW5kbGVBZGRSb29tQ2hhbmdlcywgdGhpcykpO1xuICAgIHRoaXMuJGZvcm0ub24oJ2NoYW5nZScsICcjYWRkX2NoZWNrX2luX291dF8wJywgJC5wcm94eSh0aGlzLmhhbmRsZURhdGVDaGFuZ2VzLCB0aGlzKSk7XG4gICAgdGhpcy4kZm9ybS5vbignY2hhbmdlJywgJyNhZGRfY2hlY2tfaW5fb3V0XzEnLCAkLnByb3h5KHRoaXMuaGFuZGxlRGF0ZUNoYW5nZXMsIHRoaXMpKTtcblxuICAgICQoJ2J1dHRvblt0eXBlPVwic3VibWl0XCJdJywgdGhpcy4kZm9ybSkucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgICB0aGlzLiRmb3JtLm9uKCdzdWJtaXQnLCAkLnByb3h5KHRoaXMub25TdWJtaXQsIHRoaXMpKTtcbiAgfVxuXG4gIG9uU3VibWl0KGUpIHtcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICBhd2Vib29raW5nLmFqYXhTdWJtaXQodGhpcy5mb3JtLCAnYWRkX2F3ZWJvb2tpbmdfbGluZV9pdGVtJylcbiAgICAgIC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cbiAgICAgICAgLy8gVE9ETzogSW1wcm92ZSB0aGlzIVxuICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKTtcbiAgICAgICAgfSwgMjUwKTtcblxuICAgICAgfSk7XG4gIH1cblxuICBoYW5kbGVEYXRlQ2hhbmdlcygpIHtcbiAgICBpZiAoISB0aGlzLmVuc3VyZUlucHV0RGF0ZXMoKSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIC8vIElmIGFueSBjaGVjay1pbi9vdXQgY2hhbmdlcyxcbiAgICAvLyB3ZSB3aWxsIHJlc2V0IHRoZSBgYWRkX3Jvb21gIGlucHV0LlxuICAgIHRoaXMuJGZvcm0uZmluZCgnI2FkZF9yb29tJykudmFsKCcnKTtcblxuICAgIC8vIFRoZW4sIGNhbGwgYWpheCB0byB1cGRhdGUgbmV3IHRlbXBsYXRlLlxuICAgIHRoaXMuYWpheFVwZGF0ZUZvcm0oKTtcbiAgfVxuXG4gIGhhbmRsZUFkZFJvb21DaGFuZ2VzKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgaWYgKCEgdGhpcy5lbnN1cmVJbnB1dERhdGVzKCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0aGlzLmFqYXhVcGRhdGVGb3JtKClcbiAgICAgIC5kb25lKGZ1bmN0aW9uKCkge1xuICAgICAgICAkKCdidXR0b25bdHlwZT1cInN1Ym1pdFwiXScsIHNlbGYuJGZvcm0pLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgfSk7XG4gIH1cblxuICBhamF4VXBkYXRlRm9ybSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCAkY29udGFpbmVyID0gc2VsZi4kZm9ybS5maW5kKCcuYXdlYm9va2luZy1kaWFsb2ctY29udGVudHMnKTtcblxuICAgIHJldHVybiBhd2Vib29raW5nLmFqYXhTdWJtaXQodGhpcy5mb3JtLCAnZ2V0X2F3ZWJvb2tpbmdfYWRkX2l0ZW1fZm9ybScpXG4gICAgICAuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICAkKCcjYWRkX2NoZWNrX2luX291dF8wJywgJGNvbnRhaW5lcikuZGF0ZXBpY2tlcignZGVzdHJveScpO1xuICAgICAgICAkKCcjYWRkX2NoZWNrX2luX291dF8xJywgJGNvbnRhaW5lcikuZGF0ZXBpY2tlcignZGVzdHJveScpO1xuXG4gICAgICAgICRjb250YWluZXIuaHRtbChyZXNwb25zZS5odG1sKTtcbiAgICAgIH0pO1xuICB9XG5cbiAgZW5zdXJlSW5wdXREYXRlcygpIHtcbiAgICB2YXIgJGNoZWNrX2luICA9IHRoaXMuJGZvcm0uZmluZCgnI2FkZF9jaGVja19pbl9vdXRfMCcpO1xuICAgIHZhciAkY2hlY2tfb3V0ID0gdGhpcy4kZm9ybS5maW5kKCcjYWRkX2NoZWNrX2luX291dF8xJyk7XG5cbiAgICByZXR1cm4gJGNoZWNrX2luLnZhbCgpICYmICRjaGVja19vdXQudmFsKCk7XG4gIH1cbn1cblxuJChmdW5jdGlvbigpIHtcblxuICBjb25zdCAkZm9ybSA9ICQoJyNhd2Vib29raW5nLWFkZC1saW5lLWl0ZW0tZm9ybScpO1xuICBpZiAoJGZvcm0ubGVuZ3RoID4gMCkge1xuICAgIG5ldyBFZGl0Qm9va2luZygkZm9ybSk7XG4gIH1cbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL2Jvb2tpbmcuanMiXSwic291cmNlUm9vdCI6IiJ9