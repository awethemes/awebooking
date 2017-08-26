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
/******/ 	return __webpack_require__(__webpack_require__.s = 8);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */,
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */,
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(9);


/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

var $ = window.jQuery;
var awebooking = window.TheAweBooking;

var AddLineItem = __webpack_require__(10);
var EditLineItem = __webpack_require__(11);

$(function () {

  var $form = $('#awebooking-add-line-item-form');
  if ($form.length > 0) {
    new AddLineItem($form);
  }

  new EditLineItem();

  $('.js-delete-booking-item').on('click', function () {
    if (!confirm(awebooking.trans('warning'))) {
      return false;
    }
  });
});

/***/ }),
/* 10 */
/***/ (function(module, exports) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var awebooking = window.TheAweBooking;

var AddLineItem = function () {
  function AddLineItem(form) {
    _classCallCheck(this, AddLineItem);

    this.form = form instanceof jQuery ? form[0] : form;
    this.$form = $(this.form);

    this.$form.on('change', '#add_room', $.proxy(this.handleAddRoomChanges, this));
    this.$form.on('change', '#add_check_in_out_0', $.proxy(this.handleDateChanges, this));
    this.$form.on('change', '#add_check_in_out_1', $.proxy(this.handleDateChanges, this));

    $('button[type="submit"]', this.$form).prop('disabled', true);
    this.$form.on('submit', $.proxy(this.onSubmit, this));
  }

  _createClass(AddLineItem, [{
    key: 'onSubmit',
    value: function onSubmit(e) {
      e.preventDefault();

      awebooking.ajaxSubmit(this.form, 'add_awebooking_line_item').done(function (response) {

        // TODO: Improve this!
        setTimeout(function () {
          window.location.reload();
        }, 250);
      }).fail(function (response) {
        if (response.error) {
          alert(response.error);
        }
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

  return AddLineItem;
}();

module.exports = AddLineItem;

/***/ }),
/* 11 */
/***/ (function(module, exports) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var awebooking = window.TheAweBooking;

var EditLineItem = function () {
  function EditLineItem() {
    _classCallCheck(this, EditLineItem);

    this.$popup = $('#awebooking-edit-line-item-popup');
    awebooking.Popup.setup(this.$popup);

    $('form', this.$popup).on('submit', this.submitForm);
    $('.js-edit-line-item').on('click', this.openPopup.bind(this));
  }

  _createClass(EditLineItem, [{
    key: 'openPopup',
    value: function openPopup(e) {
      e.preventDefault();

      var self = this;
      var lineItem = $(e.currentTarget).data('lineItem');

      self.$popup.find('.awebooking-dialog-contents').html('Loading...');
      self.$popup.dialog('open');

      return wp.ajax.post('get_awebooking_edit_line_item_form', { line_item_id: lineItem }).done(function (response) {
        self.$popup.find('.awebooking-dialog-contents').html(response.html);
      });
    }
  }, {
    key: 'submitForm',
    value: function submitForm(e) {
      e.preventDefault();

      awebooking.ajaxSubmit(this, 'edit_awebooking_line_item').done(function (response) {

        // TODO: Improve this!
        setTimeout(function () {
          window.location.reload();
        }, 250);
      }).fail(function (response) {
        if (response.error) {
          alert(response.error);
        }
      });
    }
  }]);

  return EditLineItem;
}();

module.exports = EditLineItem;

/***/ })
/******/ ]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMmQzOWIxYzM5NGE3YTBiMjBhN2MiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2VkaXQtYm9va2luZy5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vYm9va2luZy9hZGQtbGluZS1pdGVtLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi9ib29raW5nL2VkaXQtbGluZS1pdGVtLmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJqUXVlcnkiLCJhd2Vib29raW5nIiwiVGhlQXdlQm9va2luZyIsIkFkZExpbmVJdGVtIiwicmVxdWlyZSIsIkVkaXRMaW5lSXRlbSIsIiRmb3JtIiwibGVuZ3RoIiwib24iLCJjb25maXJtIiwidHJhbnMiLCJmb3JtIiwicHJveHkiLCJoYW5kbGVBZGRSb29tQ2hhbmdlcyIsImhhbmRsZURhdGVDaGFuZ2VzIiwicHJvcCIsIm9uU3VibWl0IiwiZSIsInByZXZlbnREZWZhdWx0IiwiYWpheFN1Ym1pdCIsImRvbmUiLCJyZXNwb25zZSIsInNldFRpbWVvdXQiLCJsb2NhdGlvbiIsInJlbG9hZCIsImZhaWwiLCJlcnJvciIsImFsZXJ0IiwiZW5zdXJlSW5wdXREYXRlcyIsImZpbmQiLCJ2YWwiLCJhamF4VXBkYXRlRm9ybSIsInNlbGYiLCIkY29udGFpbmVyIiwiZGF0ZXBpY2tlciIsImh0bWwiLCIkY2hlY2tfaW4iLCIkY2hlY2tfb3V0IiwibW9kdWxlIiwiZXhwb3J0cyIsIiRwb3B1cCIsIlBvcHVwIiwic2V0dXAiLCJzdWJtaXRGb3JtIiwib3BlblBvcHVwIiwiYmluZCIsImxpbmVJdGVtIiwiY3VycmVudFRhcmdldCIsImRhdGEiLCJkaWFsb2ciLCJ3cCIsImFqYXgiLCJwb3N0IiwibGluZV9pdGVtX2lkIl0sIm1hcHBpbmdzIjoiO0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFD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QVcsWUFBVztBQUNwQnZCLGlCQUFPd0IsUUFBUCxDQUFnQkMsTUFBaEI7QUFDRCxTQUZELEVBRUcsR0FGSDtBQUlELE9BUkgsRUFTR0MsSUFUSCxDQVNRLFVBQVNKLFFBQVQsRUFBbUI7QUFDdkIsWUFBSUEsU0FBU0ssS0FBYixFQUFvQjtBQUNsQkMsZ0JBQU1OLFNBQVNLLEtBQWY7QUFDRDtBQUNGLE9BYkg7QUFjRDs7Ozs7O0FBR0hZLE9BQU9DLE9BQVAsR0FBaUJsQyxZQUFqQixDIiwiZmlsZSI6Ii9qcy9hZG1pbi9lZGl0LWJvb2tpbmcuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSA4KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAyZDM5YjFjMzk0YTdhMGIyMGE3YyIsImNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xuY29uc3QgYXdlYm9va2luZyA9IHdpbmRvdy5UaGVBd2VCb29raW5nO1xuXG5jb25zdCBBZGRMaW5lSXRlbSA9IHJlcXVpcmUoJy4vYm9va2luZy9hZGQtbGluZS1pdGVtLmpzJyk7XG5jb25zdCBFZGl0TGluZUl0ZW0gPSByZXF1aXJlKCcuL2Jvb2tpbmcvZWRpdC1saW5lLWl0ZW0uanMnKTtcblxuJChmdW5jdGlvbigpIHtcblxuICBjb25zdCAkZm9ybSA9ICQoJyNhd2Vib29raW5nLWFkZC1saW5lLWl0ZW0tZm9ybScpO1xuICBpZiAoJGZvcm0ubGVuZ3RoID4gMCkge1xuICAgIG5ldyBBZGRMaW5lSXRlbSgkZm9ybSk7XG4gIH1cblxuICBuZXcgRWRpdExpbmVJdGVtO1xuXG4gICQoJy5qcy1kZWxldGUtYm9va2luZy1pdGVtJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgaWYgKCEgY29uZmlybShhd2Vib29raW5nLnRyYW5zKCd3YXJuaW5nJykpKSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9KTtcblxufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vZWRpdC1ib29raW5nLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBhd2Vib29raW5nID0gd2luZG93LlRoZUF3ZUJvb2tpbmc7XG5cbmNsYXNzIEFkZExpbmVJdGVtIHtcbiAgY29uc3RydWN0b3IoZm9ybSkge1xuICAgIHRoaXMuZm9ybSAgPSAoZm9ybSBpbnN0YW5jZW9mIGpRdWVyeSkgPyBmb3JtWzBdIDogZm9ybTtcbiAgICB0aGlzLiRmb3JtID0gJCh0aGlzLmZvcm0pO1xuXG4gICAgdGhpcy4kZm9ybS5vbignY2hhbmdlJywgJyNhZGRfcm9vbScsICQucHJveHkodGhpcy5oYW5kbGVBZGRSb29tQ2hhbmdlcywgdGhpcykpO1xuICAgIHRoaXMuJGZvcm0ub24oJ2NoYW5nZScsICcjYWRkX2NoZWNrX2luX291dF8wJywgJC5wcm94eSh0aGlzLmhhbmRsZURhdGVDaGFuZ2VzLCB0aGlzKSk7XG4gICAgdGhpcy4kZm9ybS5vbignY2hhbmdlJywgJyNhZGRfY2hlY2tfaW5fb3V0XzEnLCAkLnByb3h5KHRoaXMuaGFuZGxlRGF0ZUNoYW5nZXMsIHRoaXMpKTtcblxuICAgICQoJ2J1dHRvblt0eXBlPVwic3VibWl0XCJdJywgdGhpcy4kZm9ybSkucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgICB0aGlzLiRmb3JtLm9uKCdzdWJtaXQnLCAkLnByb3h5KHRoaXMub25TdWJtaXQsIHRoaXMpKTtcbiAgfVxuXG4gIG9uU3VibWl0KGUpIHtcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICBhd2Vib29raW5nLmFqYXhTdWJtaXQodGhpcy5mb3JtLCAnYWRkX2F3ZWJvb2tpbmdfbGluZV9pdGVtJylcbiAgICAgIC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cbiAgICAgICAgLy8gVE9ETzogSW1wcm92ZSB0aGlzIVxuICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKTtcbiAgICAgICAgfSwgMjUwKTtcblxuICAgICAgfSlcbiAgICAgIC5mYWlsKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgIGlmIChyZXNwb25zZS5lcnJvcikge1xuICAgICAgICAgIGFsZXJ0KHJlc3BvbnNlLmVycm9yKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gIH1cblxuICBoYW5kbGVEYXRlQ2hhbmdlcygpIHtcbiAgICBpZiAoISB0aGlzLmVuc3VyZUlucHV0RGF0ZXMoKSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIC8vIElmIGFueSBjaGVjay1pbi9vdXQgY2hhbmdlcyxcbiAgICAvLyB3ZSB3aWxsIHJlc2V0IHRoZSBgYWRkX3Jvb21gIGlucHV0LlxuICAgIHRoaXMuJGZvcm0uZmluZCgnI2FkZF9yb29tJykudmFsKCcnKTtcblxuICAgIC8vIFRoZW4sIGNhbGwgYWpheCB0byB1cGRhdGUgbmV3IHRlbXBsYXRlLlxuICAgIHRoaXMuYWpheFVwZGF0ZUZvcm0oKTtcbiAgfVxuXG4gIGhhbmRsZUFkZFJvb21DaGFuZ2VzKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgaWYgKCEgdGhpcy5lbnN1cmVJbnB1dERhdGVzKCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0aGlzLmFqYXhVcGRhdGVGb3JtKClcbiAgICAgIC5kb25lKGZ1bmN0aW9uKCkge1xuICAgICAgICAkKCdidXR0b25bdHlwZT1cInN1Ym1pdFwiXScsIHNlbGYuJGZvcm0pLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgfSk7XG4gIH1cblxuICBhamF4VXBkYXRlRm9ybSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCAkY29udGFpbmVyID0gc2VsZi4kZm9ybS5maW5kKCcuYXdlYm9va2luZy1kaWFsb2ctY29udGVudHMnKTtcblxuICAgIHJldHVybiBhd2Vib29raW5nLmFqYXhTdWJtaXQodGhpcy5mb3JtLCAnZ2V0X2F3ZWJvb2tpbmdfYWRkX2l0ZW1fZm9ybScpXG4gICAgICAuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICAkKCcjYWRkX2NoZWNrX2luX291dF8wJywgJGNvbnRhaW5lcikuZGF0ZXBpY2tlcignZGVzdHJveScpO1xuICAgICAgICAkKCcjYWRkX2NoZWNrX2luX291dF8xJywgJGNvbnRhaW5lcikuZGF0ZXBpY2tlcignZGVzdHJveScpO1xuXG4gICAgICAgICRjb250YWluZXIuaHRtbChyZXNwb25zZS5odG1sKTtcbiAgICAgIH0pO1xuICB9XG5cbiAgZW5zdXJlSW5wdXREYXRlcygpIHtcbiAgICB2YXIgJGNoZWNrX2luICA9IHRoaXMuJGZvcm0uZmluZCgnI2FkZF9jaGVja19pbl9vdXRfMCcpO1xuICAgIHZhciAkY2hlY2tfb3V0ID0gdGhpcy4kZm9ybS5maW5kKCcjYWRkX2NoZWNrX2luX291dF8xJyk7XG5cbiAgICByZXR1cm4gJGNoZWNrX2luLnZhbCgpICYmICRjaGVja19vdXQudmFsKCk7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBBZGRMaW5lSXRlbTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi9ib29raW5nL2FkZC1saW5lLWl0ZW0uanMiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IGF3ZWJvb2tpbmcgPSB3aW5kb3cuVGhlQXdlQm9va2luZztcblxuY2xhc3MgRWRpdExpbmVJdGVtIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy4kcG9wdXAgPSAkKCcjYXdlYm9va2luZy1lZGl0LWxpbmUtaXRlbS1wb3B1cCcpO1xuICAgIGF3ZWJvb2tpbmcuUG9wdXAuc2V0dXAodGhpcy4kcG9wdXApO1xuXG4gICAgJCgnZm9ybScsIHRoaXMuJHBvcHVwKS5vbignc3VibWl0JywgdGhpcy5zdWJtaXRGb3JtKTtcbiAgICAkKCcuanMtZWRpdC1saW5lLWl0ZW0nKS5vbignY2xpY2snLCB0aGlzLm9wZW5Qb3B1cC5iaW5kKHRoaXMpKTtcbiAgfVxuXG4gIG9wZW5Qb3B1cChlKSB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IGxpbmVJdGVtID0gJChlLmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2xpbmVJdGVtJyk7XG5cbiAgICBzZWxmLiRwb3B1cC5maW5kKCcuYXdlYm9va2luZy1kaWFsb2ctY29udGVudHMnKS5odG1sKCdMb2FkaW5nLi4uJyk7XG4gICAgc2VsZi4kcG9wdXAuZGlhbG9nKCdvcGVuJyk7XG5cbiAgICByZXR1cm4gd3AuYWpheC5wb3N0KCdnZXRfYXdlYm9va2luZ19lZGl0X2xpbmVfaXRlbV9mb3JtJywgeyBsaW5lX2l0ZW1faWQ6IGxpbmVJdGVtIH0pXG4gICAgICAuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICBzZWxmLiRwb3B1cC5maW5kKCcuYXdlYm9va2luZy1kaWFsb2ctY29udGVudHMnKS5odG1sKHJlc3BvbnNlLmh0bWwpO1xuICAgICAgfSk7XG4gIH1cblxuICBzdWJtaXRGb3JtKGUpIHtcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICBhd2Vib29raW5nLmFqYXhTdWJtaXQodGhpcywgJ2VkaXRfYXdlYm9va2luZ19saW5lX2l0ZW0nKVxuICAgICAgLmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblxuICAgICAgICAvLyBUT0RPOiBJbXByb3ZlIHRoaXMhXG4gICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgd2luZG93LmxvY2F0aW9uLnJlbG9hZCgpO1xuICAgICAgICB9LCAyNTApO1xuXG4gICAgICB9KVxuICAgICAgLmZhaWwoZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgaWYgKHJlc3BvbnNlLmVycm9yKSB7XG4gICAgICAgICAgYWxlcnQocmVzcG9uc2UuZXJyb3IpO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgfVxufVxuXG5tb2R1bGUuZXhwb3J0cyA9IEVkaXRMaW5lSXRlbTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi9ib29raW5nL2VkaXQtbGluZS1pdGVtLmpzIl0sInNvdXJjZVJvb3QiOiIifQ==