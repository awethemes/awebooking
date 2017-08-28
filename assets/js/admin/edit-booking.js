webpackJsonp([2],[
/* 0 */,
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(11);


/***/ }),
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

var $ = window.jQuery;
var awebooking = window.TheAweBooking;

var AddLineItem = __webpack_require__(12);
var EditLineItem = __webpack_require__(13);

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
/* 12 */
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
/* 13 */
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
          // window.location.reload();
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
],[10]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vZWRpdC1ib29raW5nLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi9ib29raW5nL2FkZC1saW5lLWl0ZW0uanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2Jvb2tpbmcvZWRpdC1saW5lLWl0ZW0uanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsImpRdWVyeSIsImF3ZWJvb2tpbmciLCJUaGVBd2VCb29raW5nIiwiQWRkTGluZUl0ZW0iLCJyZXF1aXJlIiwiRWRpdExpbmVJdGVtIiwiJGZvcm0iLCJsZW5ndGgiLCJvbiIsImNvbmZpcm0iLCJ0cmFucyIsImZvcm0iLCJwcm94eSIsImhhbmRsZUFkZFJvb21DaGFuZ2VzIiwiaGFuZGxlRGF0ZUNoYW5nZXMiLCJwcm9wIiwib25TdWJtaXQiLCJlIiwicHJldmVudERlZmF1bHQiLCJhamF4U3VibWl0IiwiZG9uZSIsInJlc3BvbnNlIiwic2V0VGltZW91dCIsImxvY2F0aW9uIiwicmVsb2FkIiwiZmFpbCIsImVycm9yIiwiYWxlcnQiLCJlbnN1cmVJbnB1dERhdGVzIiwiZmluZCIsInZhbCIsImFqYXhVcGRhdGVGb3JtIiwic2VsZiIsIiRjb250YWluZXIiLCJkYXRlcGlja2VyIiwiaHRtbCIsIiRjaGVja19pbiIsIiRjaGVja19vdXQiLCJtb2R1bGUiLCJleHBvcnRzIiwiJHBvcHVwIiwiUG9wdXAiLCJzZXR1cCIsInN1Ym1pdEZvcm0iLCJvcGVuUG9wdXAiLCJiaW5kIiwibGluZUl0ZW0iLCJjdXJyZW50VGFyZ2V0IiwiZGF0YSIsImRpYWxvZyIsIndwIiwiYWpheCIsInBvc3QiLCJsaW5lX2l0ZW1faWQiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBLElBQU1BLElBQUlDLE9BQU9DLE1BQWpCO0FBQ0EsSUFBTUMsYUFBYUYsT0FBT0csYUFBMUI7O0FBRUEsSUFBTUMsY0FBYyxtQkFBQUMs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Y0FBRjs7QUFFQSxVQUFJYyxPQUFPLElBQVg7QUFDQSxVQUFNYyxXQUFXaEQsRUFBRW1CLEVBQUU4QixhQUFKLEVBQW1CQyxJQUFuQixDQUF3QixVQUF4QixDQUFqQjs7QUFFQWhCLFdBQUtRLE1BQUwsQ0FBWVgsSUFBWixDQUFpQiw2QkFBakIsRUFBZ0RNLElBQWhELENBQXFELFlBQXJEO0FBQ0FILFdBQUtRLE1BQUwsQ0FBWVMsTUFBWixDQUFtQixNQUFuQjs7QUFFQSxhQUFPQyxHQUFHQyxJQUFILENBQVFDLElBQVIsQ0FBYSxvQ0FBYixFQUFtRCxFQUFFQyxjQUFjUCxRQUFoQixFQUFuRCxFQUNKMUIsSUFESSxDQUNDLFVBQVNDLFFBQVQsRUFBbUI7QUFDdkJXLGFBQUtRLE1BQUwsQ0FBWVgsSUFBWixDQUFpQiw2QkFBakIsRUFBZ0RNLElBQWhELENBQXFEZCxTQUFTYyxJQUE5RDtBQUNELE9BSEksQ0FBUDtBQUlEOzs7K0JBRVVsQixDLEVBQUc7QUFDWkEsUUFBRUMsY0FBRjs7QUFFQWpCLGlCQUFXa0IsVUFBWCxDQUFzQixJQUF0QixFQUE0QiwyQkFBNUIsRUFDR0MsSUFESCxDQUNRLFVBQVNDLFFBQVQsRUFBbUI7O0FBRXZCO0FBQ0FDLG1CQUFXLFlBQVc7QUFDcEI7QUFDRCxTQUZELEVBRUcsR0FGSDtBQUlELE9BUkgsRUFTR0csSUFUSCxDQVNRLFVBQVNKLFFBQVQsRUFBbUI7QUFDdkIsWUFBSUEsU0FBU0ssS0FBYixFQUFvQjtBQUNsQkMsZ0JBQU1OLFNBQVNLLEtBQWY7QUFDRDtBQUNGLE9BYkg7QUFjRDs7Ozs7O0FBR0hZLE9BQU9DLE9BQVAsR0FBaUJsQyxZQUFqQixDIiwiZmlsZSI6Ii9qcy9hZG1pbi9lZGl0LWJvb2tpbmcuanMiLCJzb3VyY2VzQ29udGVudCI6WyJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IGF3ZWJvb2tpbmcgPSB3aW5kb3cuVGhlQXdlQm9va2luZztcblxuY29uc3QgQWRkTGluZUl0ZW0gPSByZXF1aXJlKCcuL2Jvb2tpbmcvYWRkLWxpbmUtaXRlbS5qcycpO1xuY29uc3QgRWRpdExpbmVJdGVtID0gcmVxdWlyZSgnLi9ib29raW5nL2VkaXQtbGluZS1pdGVtLmpzJyk7XG5cbiQoZnVuY3Rpb24oKSB7XG5cbiAgY29uc3QgJGZvcm0gPSAkKCcjYXdlYm9va2luZy1hZGQtbGluZS1pdGVtLWZvcm0nKTtcbiAgaWYgKCRmb3JtLmxlbmd0aCA+IDApIHtcbiAgICBuZXcgQWRkTGluZUl0ZW0oJGZvcm0pO1xuICB9XG5cbiAgbmV3IEVkaXRMaW5lSXRlbTtcblxuICAkKCcuanMtZGVsZXRlLWJvb2tpbmctaXRlbScpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgIGlmICghIGNvbmZpcm0oYXdlYm9va2luZy50cmFucygnd2FybmluZycpKSkge1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cbiAgfSk7XG5cbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL2VkaXQtYm9va2luZy5qcyIsImNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xuY29uc3QgYXdlYm9va2luZyA9IHdpbmRvdy5UaGVBd2VCb29raW5nO1xuXG5jbGFzcyBBZGRMaW5lSXRlbSB7XG4gIGNvbnN0cnVjdG9yKGZvcm0pIHtcbiAgICB0aGlzLmZvcm0gID0gKGZvcm0gaW5zdGFuY2VvZiBqUXVlcnkpID8gZm9ybVswXSA6IGZvcm07XG4gICAgdGhpcy4kZm9ybSA9ICQodGhpcy5mb3JtKTtcblxuICAgIHRoaXMuJGZvcm0ub24oJ2NoYW5nZScsICcjYWRkX3Jvb20nLCAkLnByb3h5KHRoaXMuaGFuZGxlQWRkUm9vbUNoYW5nZXMsIHRoaXMpKTtcbiAgICB0aGlzLiRmb3JtLm9uKCdjaGFuZ2UnLCAnI2FkZF9jaGVja19pbl9vdXRfMCcsICQucHJveHkodGhpcy5oYW5kbGVEYXRlQ2hhbmdlcywgdGhpcykpO1xuICAgIHRoaXMuJGZvcm0ub24oJ2NoYW5nZScsICcjYWRkX2NoZWNrX2luX291dF8xJywgJC5wcm94eSh0aGlzLmhhbmRsZURhdGVDaGFuZ2VzLCB0aGlzKSk7XG5cbiAgICAkKCdidXR0b25bdHlwZT1cInN1Ym1pdFwiXScsIHRoaXMuJGZvcm0pLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgdGhpcy4kZm9ybS5vbignc3VibWl0JywgJC5wcm94eSh0aGlzLm9uU3VibWl0LCB0aGlzKSk7XG4gIH1cblxuICBvblN1Ym1pdChlKSB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgYXdlYm9va2luZy5hamF4U3VibWl0KHRoaXMuZm9ybSwgJ2FkZF9hd2Vib29raW5nX2xpbmVfaXRlbScpXG4gICAgICAuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuXG4gICAgICAgIC8vIFRPRE86IEltcHJvdmUgdGhpcyFcbiAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgICB3aW5kb3cubG9jYXRpb24ucmVsb2FkKCk7XG4gICAgICAgIH0sIDI1MCk7XG5cbiAgICAgIH0pXG4gICAgICAuZmFpbChmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICBpZiAocmVzcG9uc2UuZXJyb3IpIHtcbiAgICAgICAgICBhbGVydChyZXNwb25zZS5lcnJvcik7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICB9XG5cbiAgaGFuZGxlRGF0ZUNoYW5nZXMoKSB7XG4gICAgaWYgKCEgdGhpcy5lbnN1cmVJbnB1dERhdGVzKCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAvLyBJZiBhbnkgY2hlY2staW4vb3V0IGNoYW5nZXMsXG4gICAgLy8gd2Ugd2lsbCByZXNldCB0aGUgYGFkZF9yb29tYCBpbnB1dC5cbiAgICB0aGlzLiRmb3JtLmZpbmQoJyNhZGRfcm9vbScpLnZhbCgnJyk7XG5cbiAgICAvLyBUaGVuLCBjYWxsIGFqYXggdG8gdXBkYXRlIG5ldyB0ZW1wbGF0ZS5cbiAgICB0aGlzLmFqYXhVcGRhdGVGb3JtKCk7XG4gIH1cblxuICBoYW5kbGVBZGRSb29tQ2hhbmdlcygpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgIGlmICghIHRoaXMuZW5zdXJlSW5wdXREYXRlcygpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5hamF4VXBkYXRlRm9ybSgpXG4gICAgICAuZG9uZShmdW5jdGlvbigpIHtcbiAgICAgICAgJCgnYnV0dG9uW3R5cGU9XCJzdWJtaXRcIl0nLCBzZWxmLiRmb3JtKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgIH0pO1xuICB9XG5cbiAgYWpheFVwZGF0ZUZvcm0oKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgJGNvbnRhaW5lciA9IHNlbGYuJGZvcm0uZmluZCgnLmF3ZWJvb2tpbmctZGlhbG9nLWNvbnRlbnRzJyk7XG5cbiAgICByZXR1cm4gYXdlYm9va2luZy5hamF4U3VibWl0KHRoaXMuZm9ybSwgJ2dldF9hd2Vib29raW5nX2FkZF9pdGVtX2Zvcm0nKVxuICAgICAgLmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgJCgnI2FkZF9jaGVja19pbl9vdXRfMCcsICRjb250YWluZXIpLmRhdGVwaWNrZXIoJ2Rlc3Ryb3knKTtcbiAgICAgICAgJCgnI2FkZF9jaGVja19pbl9vdXRfMScsICRjb250YWluZXIpLmRhdGVwaWNrZXIoJ2Rlc3Ryb3knKTtcblxuICAgICAgICAkY29udGFpbmVyLmh0bWwocmVzcG9uc2UuaHRtbCk7XG4gICAgICB9KTtcbiAgfVxuXG4gIGVuc3VyZUlucHV0RGF0ZXMoKSB7XG4gICAgdmFyICRjaGVja19pbiAgPSB0aGlzLiRmb3JtLmZpbmQoJyNhZGRfY2hlY2tfaW5fb3V0XzAnKTtcbiAgICB2YXIgJGNoZWNrX291dCA9IHRoaXMuJGZvcm0uZmluZCgnI2FkZF9jaGVja19pbl9vdXRfMScpO1xuXG4gICAgcmV0dXJuICRjaGVja19pbi52YWwoKSAmJiAkY2hlY2tfb3V0LnZhbCgpO1xuICB9XG59XG5cbm1vZHVsZS5leHBvcnRzID0gQWRkTGluZUl0ZW07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vYm9va2luZy9hZGQtbGluZS1pdGVtLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBhd2Vib29raW5nID0gd2luZG93LlRoZUF3ZUJvb2tpbmc7XG5cbmNsYXNzIEVkaXRMaW5lSXRlbSB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuJHBvcHVwID0gJCgnI2F3ZWJvb2tpbmctZWRpdC1saW5lLWl0ZW0tcG9wdXAnKTtcbiAgICBhd2Vib29raW5nLlBvcHVwLnNldHVwKHRoaXMuJHBvcHVwKTtcblxuICAgICQoJ2Zvcm0nLCB0aGlzLiRwb3B1cCkub24oJ3N1Ym1pdCcsIHRoaXMuc3VibWl0Rm9ybSk7XG4gICAgJCgnLmpzLWVkaXQtbGluZS1pdGVtJykub24oJ2NsaWNrJywgdGhpcy5vcGVuUG9wdXAuYmluZCh0aGlzKSk7XG4gIH1cblxuICBvcGVuUG9wdXAoZSkge1xuICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgIHZhciBzZWxmID0gdGhpcztcbiAgICBjb25zdCBsaW5lSXRlbSA9ICQoZS5jdXJyZW50VGFyZ2V0KS5kYXRhKCdsaW5lSXRlbScpO1xuXG4gICAgc2VsZi4kcG9wdXAuZmluZCgnLmF3ZWJvb2tpbmctZGlhbG9nLWNvbnRlbnRzJykuaHRtbCgnTG9hZGluZy4uLicpO1xuICAgIHNlbGYuJHBvcHVwLmRpYWxvZygnb3BlbicpO1xuXG4gICAgcmV0dXJuIHdwLmFqYXgucG9zdCgnZ2V0X2F3ZWJvb2tpbmdfZWRpdF9saW5lX2l0ZW1fZm9ybScsIHsgbGluZV9pdGVtX2lkOiBsaW5lSXRlbSB9KVxuICAgICAgLmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgc2VsZi4kcG9wdXAuZmluZCgnLmF3ZWJvb2tpbmctZGlhbG9nLWNvbnRlbnRzJykuaHRtbChyZXNwb25zZS5odG1sKTtcbiAgICAgIH0pO1xuICB9XG5cbiAgc3VibWl0Rm9ybShlKSB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgYXdlYm9va2luZy5hamF4U3VibWl0KHRoaXMsICdlZGl0X2F3ZWJvb2tpbmdfbGluZV9pdGVtJylcbiAgICAgIC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cbiAgICAgICAgLy8gVE9ETzogSW1wcm92ZSB0aGlzIVxuICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgIC8vIHdpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKTtcbiAgICAgICAgfSwgMjUwKTtcblxuICAgICAgfSlcbiAgICAgIC5mYWlsKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgIGlmIChyZXNwb25zZS5lcnJvcikge1xuICAgICAgICAgIGFsZXJ0KHJlc3BvbnNlLmVycm9yKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBFZGl0TGluZUl0ZW07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vYm9va2luZy9lZGl0LWxpbmUtaXRlbS5qcyJdLCJzb3VyY2VSb290IjoiIn0=