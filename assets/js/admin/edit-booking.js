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
/* 10 */,
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(12);


/***/ }),
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

var $ = window.jQuery;
var awebooking = window.TheAweBooking;

var AddLineItem = __webpack_require__(13);
var EditLineItem = __webpack_require__(14);

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

  $('#awebooking-booking-notes').on('click', '.delete_note', function (e) {
    e.preventDefault();

    var el = $(this);
    var note = $(this).closest('li.note');

    wp.ajax.post('delete_awebooking_note', {
      note_id: $(note).attr('rel'),
      booking_id: $('#post_ID').val()
    }).done(function (response) {
      $(note).remove();
    });
  });

  $('#awebooking-booking-notes').on('click', 'button.add_note', function (e) {
    e.preventDefault();

    var noteContents = $('textarea#add_booking_note').val();
    if (!noteContents) {
      return;
    }

    wp.ajax.post('add_awebooking_note', {
      booking_id: $('#post_ID').val(),
      note: $('textarea#add_booking_note').val(),
      note_type: $('select#booking_note_type').val()
    }).done(function (data) {
      $('ul.booking_notes').prepend(data.new_note);
      $('#add_booking_note').val('');
    });
  });
});

/***/ }),
/* 13 */
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

        setTimeout(function () {
          $('form#post').submit();
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
/* 14 */
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

        setTimeout(function () {
          $('form#post').submit();
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
],[11]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vZWRpdC1ib29raW5nLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi9ib29raW5nL2FkZC1saW5lLWl0ZW0uanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2Jvb2tpbmcvZWRpdC1saW5lLWl0ZW0uanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsImpRdWVyeSIsImF3ZWJvb2tpbmciLCJUaGVBd2VCb29raW5nIiwiQWRkTGluZUl0ZW0iLCJyZXF1aXJlIiwiRWRpdExpbmVJdGVtIiwiJGZvcm0iLCJsZW5ndGgiLCJvbiIsImNvbmZpcm0iLCJ0cmFucyIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImVsIiwibm90ZSIsImNsb3Nlc3QiLCJ3cCIsImFqYXgiLCJwb3N0Iiwibm90ZV9pZCIsImF0dHIiLCJib29raW5nX2lkIiwidmFsIiwiZG9uZSIsInJlc3BvbnNlIiwicmVtb3ZlIiwibm90ZUNvbnRlbnRzIiwibm90ZV90eXBlIiwiZGF0YSIsInByZXBlbmQiLCJuZXdfbm90ZSIsImZvcm0iLCJwcm94eSIsImhhbmRsZUFkZFJvb21DaGFuZ2VzIiwiaGFuZGxlRGF0ZUNoYW5nZXMiLCJwcm9wIiwib25TdWJtaXQiLCJhamF4U3VibWl0Iiwic2V0VGltZW91dCIsInN1Ym1pdCIsImZhaWwiLCJlcnJvciIsImFsZXJ0IiwiZW5zdXJlSW5wdXREYXRlcyIsImZpbmQiLCJhamF4VXBkYXRlRm9ybSIsInNlbGYiLCIkY29udGFpbmVyIiwiZGF0ZXBpY2tlciIsImh0bWwiLCIkY2hlY2tfaW4iLCIkY2hlY2tfb3V0IiwibW9kdWxlIiwiZXhwb3J0cyIsIiRwb3B1cCIsIlBvcHVwIiwic2V0dXAiLCJzdWJtaXRGb3JtIiwib3BlblBvcHVwIiwiYmluZCIsImxpbmVJdGVtIiwiY3VycmVudFRhcmdldCIsImRpYWxvZyIsImxpbmVfaXRlbV9pZCJdLCJtYXBwaW5ncyI6Ijs7Ozs7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RCxZQUFqQixDIiwiZmlsZSI6Ii9qcy9hZG1pbi9lZGl0LWJvb2tpbmcuanMiLCJzb3VyY2VzQ29udGVudCI6WyJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IGF3ZWJvb2tpbmcgPSB3aW5kb3cuVGhlQXdlQm9va2luZztcblxuY29uc3QgQWRkTGluZUl0ZW0gPSByZXF1aXJlKCcuL2Jvb2tpbmcvYWRkLWxpbmUtaXRlbS5qcycpO1xuY29uc3QgRWRpdExpbmVJdGVtID0gcmVxdWlyZSgnLi9ib29raW5nL2VkaXQtbGluZS1pdGVtLmpzJyk7XG5cbiQoZnVuY3Rpb24oKSB7XG5cbiAgY29uc3QgJGZvcm0gPSAkKCcjYXdlYm9va2luZy1hZGQtbGluZS1pdGVtLWZvcm0nKTtcbiAgaWYgKCRmb3JtLmxlbmd0aCA+IDApIHtcbiAgICBuZXcgQWRkTGluZUl0ZW0oJGZvcm0pO1xuICB9XG5cbiAgbmV3IEVkaXRMaW5lSXRlbTtcblxuICAkKCcuanMtZGVsZXRlLWJvb2tpbmctaXRlbScpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgIGlmICghIGNvbmZpcm0oYXdlYm9va2luZy50cmFucygnd2FybmluZycpKSkge1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cbiAgfSk7XG5cbiAgJCgnI2F3ZWJvb2tpbmctYm9va2luZy1ub3RlcycpLm9uKCdjbGljaycsICcuZGVsZXRlX25vdGUnLCBmdW5jdGlvbihlKSB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgY29uc3QgZWwgPSAkKHRoaXMpO1xuICAgIGNvbnN0IG5vdGUgPSAkKHRoaXMpLmNsb3Nlc3QoJ2xpLm5vdGUnKTtcblxuICAgIHdwLmFqYXgucG9zdCgnZGVsZXRlX2F3ZWJvb2tpbmdfbm90ZScsIHtcbiAgICAgIG5vdGVfaWQ6ICQobm90ZSkuYXR0cigncmVsJyksXG4gICAgICBib29raW5nX2lkOiAkKCcjcG9zdF9JRCcpLnZhbCgpXG4gICAgfSlcbiAgICAuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgJChub3RlKS5yZW1vdmUoKTtcbiAgICB9KTtcbiAgfSk7XG5cbiAgJCgnI2F3ZWJvb2tpbmctYm9va2luZy1ub3RlcycpLm9uKCdjbGljaycsICdidXR0b24uYWRkX25vdGUnLCBmdW5jdGlvbiAoZSkge1xuICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgIGNvbnN0IG5vdGVDb250ZW50cyA9ICQoJ3RleHRhcmVhI2FkZF9ib29raW5nX25vdGUnKS52YWwoKTtcbiAgICBpZiAoISBub3RlQ29udGVudHMgKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgd3AuYWpheC5wb3N0KCdhZGRfYXdlYm9va2luZ19ub3RlJywge1xuICAgICAgYm9va2luZ19pZDogJCgnI3Bvc3RfSUQnKS52YWwoKSxcbiAgICAgIG5vdGU6ICAgICAgICQoJ3RleHRhcmVhI2FkZF9ib29raW5nX25vdGUnKS52YWwoKSxcbiAgICAgIG5vdGVfdHlwZTogICQoJ3NlbGVjdCNib29raW5nX25vdGVfdHlwZScpLnZhbCgpLFxuICAgIH0pXG4gICAgLmRvbmUoZnVuY3Rpb24oZGF0YSkge1xuICAgICAgJCgndWwuYm9va2luZ19ub3RlcycpLnByZXBlbmQoZGF0YS5uZXdfbm90ZSk7XG4gICAgICAkKCcjYWRkX2Jvb2tpbmdfbm90ZScpLnZhbCgnJyk7XG4gICAgfSlcbiAgfSk7XG5cbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL2VkaXQtYm9va2luZy5qcyIsImNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xuY29uc3QgYXdlYm9va2luZyA9IHdpbmRvdy5UaGVBd2VCb29raW5nO1xuXG5jbGFzcyBBZGRMaW5lSXRlbSB7XG4gIGNvbnN0cnVjdG9yKGZvcm0pIHtcbiAgICB0aGlzLmZvcm0gID0gKGZvcm0gaW5zdGFuY2VvZiBqUXVlcnkpID8gZm9ybVswXSA6IGZvcm07XG4gICAgdGhpcy4kZm9ybSA9ICQodGhpcy5mb3JtKTtcblxuICAgIHRoaXMuJGZvcm0ub24oJ2NoYW5nZScsICcjYWRkX3Jvb20nLCAkLnByb3h5KHRoaXMuaGFuZGxlQWRkUm9vbUNoYW5nZXMsIHRoaXMpKTtcbiAgICB0aGlzLiRmb3JtLm9uKCdjaGFuZ2UnLCAnI2FkZF9jaGVja19pbl9vdXRfMCcsICQucHJveHkodGhpcy5oYW5kbGVEYXRlQ2hhbmdlcywgdGhpcykpO1xuICAgIHRoaXMuJGZvcm0ub24oJ2NoYW5nZScsICcjYWRkX2NoZWNrX2luX291dF8xJywgJC5wcm94eSh0aGlzLmhhbmRsZURhdGVDaGFuZ2VzLCB0aGlzKSk7XG5cbiAgICAkKCdidXR0b25bdHlwZT1cInN1Ym1pdFwiXScsIHRoaXMuJGZvcm0pLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgdGhpcy4kZm9ybS5vbignc3VibWl0JywgJC5wcm94eSh0aGlzLm9uU3VibWl0LCB0aGlzKSk7XG4gIH1cblxuICBvblN1Ym1pdChlKSB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgYXdlYm9va2luZy5hamF4U3VibWl0KHRoaXMuZm9ybSwgJ2FkZF9hd2Vib29raW5nX2xpbmVfaXRlbScpXG4gICAgICAuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuXG4gICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgJCgnZm9ybSNwb3N0Jykuc3VibWl0KCk7XG4gICAgICAgIH0sIDI1MCk7XG5cbiAgICAgIH0pXG4gICAgICAuZmFpbChmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICBpZiAocmVzcG9uc2UuZXJyb3IpIHtcbiAgICAgICAgICBhbGVydChyZXNwb25zZS5lcnJvcik7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICB9XG5cbiAgaGFuZGxlRGF0ZUNoYW5nZXMoKSB7XG4gICAgaWYgKCEgdGhpcy5lbnN1cmVJbnB1dERhdGVzKCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAvLyBJZiBhbnkgY2hlY2staW4vb3V0IGNoYW5nZXMsXG4gICAgLy8gd2Ugd2lsbCByZXNldCB0aGUgYGFkZF9yb29tYCBpbnB1dC5cbiAgICB0aGlzLiRmb3JtLmZpbmQoJyNhZGRfcm9vbScpLnZhbCgnJyk7XG5cbiAgICAvLyBUaGVuLCBjYWxsIGFqYXggdG8gdXBkYXRlIG5ldyB0ZW1wbGF0ZS5cbiAgICB0aGlzLmFqYXhVcGRhdGVGb3JtKCk7XG4gIH1cblxuICBoYW5kbGVBZGRSb29tQ2hhbmdlcygpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgIGlmICghIHRoaXMuZW5zdXJlSW5wdXREYXRlcygpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5hamF4VXBkYXRlRm9ybSgpXG4gICAgICAuZG9uZShmdW5jdGlvbigpIHtcbiAgICAgICAgJCgnYnV0dG9uW3R5cGU9XCJzdWJtaXRcIl0nLCBzZWxmLiRmb3JtKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgIH0pO1xuICB9XG5cbiAgYWpheFVwZGF0ZUZvcm0oKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgJGNvbnRhaW5lciA9IHNlbGYuJGZvcm0uZmluZCgnLmF3ZWJvb2tpbmctZGlhbG9nLWNvbnRlbnRzJyk7XG5cbiAgICByZXR1cm4gYXdlYm9va2luZy5hamF4U3VibWl0KHRoaXMuZm9ybSwgJ2dldF9hd2Vib29raW5nX2FkZF9pdGVtX2Zvcm0nKVxuICAgICAgLmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgJCgnI2FkZF9jaGVja19pbl9vdXRfMCcsICRjb250YWluZXIpLmRhdGVwaWNrZXIoJ2Rlc3Ryb3knKTtcbiAgICAgICAgJCgnI2FkZF9jaGVja19pbl9vdXRfMScsICRjb250YWluZXIpLmRhdGVwaWNrZXIoJ2Rlc3Ryb3knKTtcblxuICAgICAgICAkY29udGFpbmVyLmh0bWwocmVzcG9uc2UuaHRtbCk7XG4gICAgICB9KTtcbiAgfVxuXG4gIGVuc3VyZUlucHV0RGF0ZXMoKSB7XG4gICAgdmFyICRjaGVja19pbiAgPSB0aGlzLiRmb3JtLmZpbmQoJyNhZGRfY2hlY2tfaW5fb3V0XzAnKTtcbiAgICB2YXIgJGNoZWNrX291dCA9IHRoaXMuJGZvcm0uZmluZCgnI2FkZF9jaGVja19pbl9vdXRfMScpO1xuXG4gICAgcmV0dXJuICRjaGVja19pbi52YWwoKSAmJiAkY2hlY2tfb3V0LnZhbCgpO1xuICB9XG59XG5cbm1vZHVsZS5leHBvcnRzID0gQWRkTGluZUl0ZW07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vYm9va2luZy9hZGQtbGluZS1pdGVtLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBhd2Vib29raW5nID0gd2luZG93LlRoZUF3ZUJvb2tpbmc7XG5cbmNsYXNzIEVkaXRMaW5lSXRlbSB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuJHBvcHVwID0gJCgnI2F3ZWJvb2tpbmctZWRpdC1saW5lLWl0ZW0tcG9wdXAnKTtcbiAgICBhd2Vib29raW5nLlBvcHVwLnNldHVwKHRoaXMuJHBvcHVwKTtcblxuICAgICQoJ2Zvcm0nLCB0aGlzLiRwb3B1cCkub24oJ3N1Ym1pdCcsIHRoaXMuc3VibWl0Rm9ybSk7XG4gICAgJCgnLmpzLWVkaXQtbGluZS1pdGVtJykub24oJ2NsaWNrJywgdGhpcy5vcGVuUG9wdXAuYmluZCh0aGlzKSk7XG4gIH1cblxuICBvcGVuUG9wdXAoZSkge1xuICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgIHZhciBzZWxmID0gdGhpcztcbiAgICBjb25zdCBsaW5lSXRlbSA9ICQoZS5jdXJyZW50VGFyZ2V0KS5kYXRhKCdsaW5lSXRlbScpO1xuXG4gICAgc2VsZi4kcG9wdXAuZmluZCgnLmF3ZWJvb2tpbmctZGlhbG9nLWNvbnRlbnRzJykuaHRtbCgnTG9hZGluZy4uLicpO1xuICAgIHNlbGYuJHBvcHVwLmRpYWxvZygnb3BlbicpO1xuXG4gICAgcmV0dXJuIHdwLmFqYXgucG9zdCgnZ2V0X2F3ZWJvb2tpbmdfZWRpdF9saW5lX2l0ZW1fZm9ybScsIHsgbGluZV9pdGVtX2lkOiBsaW5lSXRlbSB9KVxuICAgICAgLmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgc2VsZi4kcG9wdXAuZmluZCgnLmF3ZWJvb2tpbmctZGlhbG9nLWNvbnRlbnRzJykuaHRtbChyZXNwb25zZS5odG1sKTtcbiAgICAgIH0pO1xuICB9XG5cbiAgc3VibWl0Rm9ybShlKSB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgYXdlYm9va2luZy5hamF4U3VibWl0KHRoaXMsICdlZGl0X2F3ZWJvb2tpbmdfbGluZV9pdGVtJylcbiAgICAgIC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cbiAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgICAkKCdmb3JtI3Bvc3QnKS5zdWJtaXQoKTtcbiAgICAgICAgfSwgMjUwKTtcblxuICAgICAgfSlcbiAgICAgIC5mYWlsKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgIGlmIChyZXNwb25zZS5lcnJvcikge1xuICAgICAgICAgIGFsZXJ0KHJlc3BvbnNlLmVycm9yKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBFZGl0TGluZUl0ZW07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vYm9va2luZy9lZGl0LWxpbmUtaXRlbS5qcyJdLCJzb3VyY2VSb290IjoiIn0=