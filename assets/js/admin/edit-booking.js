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
],[11]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vZWRpdC1ib29raW5nLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi9ib29raW5nL2FkZC1saW5lLWl0ZW0uanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2Jvb2tpbmcvZWRpdC1saW5lLWl0ZW0uanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsImpRdWVyeSIsImF3ZWJvb2tpbmciLCJUaGVBd2VCb29raW5nIiwiQWRkTGluZUl0ZW0iLCJyZXF1aXJlIiwiRWRpdExpbmVJdGVtIiwiJGZvcm0iLCJsZW5ndGgiLCJvbiIsImNvbmZpcm0iLCJ0cmFucyIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImVsIiwibm90ZSIsImNsb3Nlc3QiLCJ3cCIsImFqYXgiLCJwb3N0Iiwibm90ZV9pZCIsImF0dHIiLCJib29raW5nX2lkIiwidmFsIiwiZG9uZSIsInJlc3BvbnNlIiwicmVtb3ZlIiwibm90ZUNvbnRlbnRzIiwibm90ZV90eXBlIiwiZGF0YSIsInByZXBlbmQiLCJuZXdfbm90ZSIsImZvcm0iLCJwcm94eSIsImhhbmRsZUFkZFJvb21DaGFuZ2VzIiwiaGFuZGxlRGF0ZUNoYW5nZXMiLCJwcm9wIiwib25TdWJtaXQiLCJhamF4U3VibWl0Iiwic2V0VGltZW91dCIsImxvY2F0aW9uIiwicmVsb2FkIiwiZmFpbCIsImVycm9yIiwiYWxlcnQiLCJlbnN1cmVJbnB1dERhdGVzIiwiZmluZCIsImFqYXhVcGRhdGVGb3JtIiwic2VsZiIsIiRjb250YWluZXIiLCJkYXRlcGlja2VyIiwiaHRtbCIsIiRjaGVja19pbiIsIiRjaGVja19vdXQiLCJtb2R1bGUiLCJleHBvcnRzIiwiJHBvcHVwIiwiUG9wdXAiLCJzZXR1cCIsInN1Ym1pdEZvcm0iLCJvcGVuUG9wdXAiLCJiaW5kIiwibGluZUl0ZW0iLCJjdXJyZW50VGFyZ2V0IiwiZGlhbG9nIiwibGluZV9pdGVtX2lkIl0sIm1hcHBp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Ozs7Ozs7QUFHSFcsT0FBT0MsT0FBUCxHQUFpQmpELFlBQWpCLEMiLCJmaWxlIjoiL2pzL2FkbWluL2VkaXQtYm9va2luZy5qcyIsInNvdXJjZXNDb250ZW50IjpbImNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xuY29uc3QgYXdlYm9va2luZyA9IHdpbmRvdy5UaGVBd2VCb29raW5nO1xuXG5jb25zdCBBZGRMaW5lSXRlbSA9IHJlcXVpcmUoJy4vYm9va2luZy9hZGQtbGluZS1pdGVtLmpzJyk7XG5jb25zdCBFZGl0TGluZUl0ZW0gPSByZXF1aXJlKCcuL2Jvb2tpbmcvZWRpdC1saW5lLWl0ZW0uanMnKTtcblxuJChmdW5jdGlvbigpIHtcblxuICBjb25zdCAkZm9ybSA9ICQoJyNhd2Vib29raW5nLWFkZC1saW5lLWl0ZW0tZm9ybScpO1xuICBpZiAoJGZvcm0ubGVuZ3RoID4gMCkge1xuICAgIG5ldyBBZGRMaW5lSXRlbSgkZm9ybSk7XG4gIH1cblxuICBuZXcgRWRpdExpbmVJdGVtO1xuXG4gICQoJy5qcy1kZWxldGUtYm9va2luZy1pdGVtJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgaWYgKCEgY29uZmlybShhd2Vib29raW5nLnRyYW5zKCd3YXJuaW5nJykpKSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9KTtcblxuICAkKCcjYXdlYm9va2luZy1ib29raW5nLW5vdGVzJykub24oJ2NsaWNrJywgJy5kZWxldGVfbm90ZScsIGZ1bmN0aW9uKGUpIHtcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICBjb25zdCBlbCA9ICQodGhpcyk7XG4gICAgY29uc3Qgbm90ZSA9ICQodGhpcykuY2xvc2VzdCgnbGkubm90ZScpO1xuXG4gICAgd3AuYWpheC5wb3N0KCdkZWxldGVfYXdlYm9va2luZ19ub3RlJywge1xuICAgICAgbm90ZV9pZDogJChub3RlKS5hdHRyKCdyZWwnKSxcbiAgICAgIGJvb2tpbmdfaWQ6ICQoJyNwb3N0X0lEJykudmFsKClcbiAgICB9KVxuICAgIC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAkKG5vdGUpLnJlbW92ZSgpO1xuICAgIH0pO1xuICB9KTtcblxuICAkKCcjYXdlYm9va2luZy1ib29raW5nLW5vdGVzJykub24oJ2NsaWNrJywgJ2J1dHRvbi5hZGRfbm90ZScsIGZ1bmN0aW9uIChlKSB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgY29uc3Qgbm90ZUNvbnRlbnRzID0gJCgndGV4dGFyZWEjYWRkX2Jvb2tpbmdfbm90ZScpLnZhbCgpO1xuICAgIGlmICghIG5vdGVDb250ZW50cyApIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB3cC5hamF4LnBvc3QoJ2FkZF9hd2Vib29raW5nX25vdGUnLCB7XG4gICAgICBib29raW5nX2lkOiAkKCcjcG9zdF9JRCcpLnZhbCgpLFxuICAgICAgbm90ZTogICAgICAgJCgndGV4dGFyZWEjYWRkX2Jvb2tpbmdfbm90ZScpLnZhbCgpLFxuICAgICAgbm90ZV90eXBlOiAgJCgnc2VsZWN0I2Jvb2tpbmdfbm90ZV90eXBlJykudmFsKCksXG4gICAgfSlcbiAgICAuZG9uZShmdW5jdGlvbihkYXRhKSB7XG4gICAgICAkKCd1bC5ib29raW5nX25vdGVzJykucHJlcGVuZChkYXRhLm5ld19ub3RlKTtcbiAgICAgICQoJyNhZGRfYm9va2luZ19ub3RlJykudmFsKCcnKTtcbiAgICB9KVxuICB9KTtcblxufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vZWRpdC1ib29raW5nLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBhd2Vib29raW5nID0gd2luZG93LlRoZUF3ZUJvb2tpbmc7XG5cbmNsYXNzIEFkZExpbmVJdGVtIHtcbiAgY29uc3RydWN0b3IoZm9ybSkge1xuICAgIHRoaXMuZm9ybSAgPSAoZm9ybSBpbnN0YW5jZW9mIGpRdWVyeSkgPyBmb3JtWzBdIDogZm9ybTtcbiAgICB0aGlzLiRmb3JtID0gJCh0aGlzLmZvcm0pO1xuXG4gICAgdGhpcy4kZm9ybS5vbignY2hhbmdlJywgJyNhZGRfcm9vbScsICQucHJveHkodGhpcy5oYW5kbGVBZGRSb29tQ2hhbmdlcywgdGhpcykpO1xuICAgIHRoaXMuJGZvcm0ub24oJ2NoYW5nZScsICcjYWRkX2NoZWNrX2luX291dF8wJywgJC5wcm94eSh0aGlzLmhhbmRsZURhdGVDaGFuZ2VzLCB0aGlzKSk7XG4gICAgdGhpcy4kZm9ybS5vbignY2hhbmdlJywgJyNhZGRfY2hlY2tfaW5fb3V0XzEnLCAkLnByb3h5KHRoaXMuaGFuZGxlRGF0ZUNoYW5nZXMsIHRoaXMpKTtcblxuICAgICQoJ2J1dHRvblt0eXBlPVwic3VibWl0XCJdJywgdGhpcy4kZm9ybSkucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgICB0aGlzLiRmb3JtLm9uKCdzdWJtaXQnLCAkLnByb3h5KHRoaXMub25TdWJtaXQsIHRoaXMpKTtcbiAgfVxuXG4gIG9uU3VibWl0KGUpIHtcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICBhd2Vib29raW5nLmFqYXhTdWJtaXQodGhpcy5mb3JtLCAnYWRkX2F3ZWJvb2tpbmdfbGluZV9pdGVtJylcbiAgICAgIC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cbiAgICAgICAgLy8gVE9ETzogSW1wcm92ZSB0aGlzIVxuICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKTtcbiAgICAgICAgfSwgMjUwKTtcblxuICAgICAgfSlcbiAgICAgIC5mYWlsKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgIGlmIChyZXNwb25zZS5lcnJvcikge1xuICAgICAgICAgIGFsZXJ0KHJlc3BvbnNlLmVycm9yKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gIH1cblxuICBoYW5kbGVEYXRlQ2hhbmdlcygpIHtcbiAgICBpZiAoISB0aGlzLmVuc3VyZUlucHV0RGF0ZXMoKSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIC8vIElmIGFueSBjaGVjay1pbi9vdXQgY2hhbmdlcyxcbiAgICAvLyB3ZSB3aWxsIHJlc2V0IHRoZSBgYWRkX3Jvb21gIGlucHV0LlxuICAgIHRoaXMuJGZvcm0uZmluZCgnI2FkZF9yb29tJykudmFsKCcnKTtcblxuICAgIC8vIFRoZW4sIGNhbGwgYWpheCB0byB1cGRhdGUgbmV3IHRlbXBsYXRlLlxuICAgIHRoaXMuYWpheFVwZGF0ZUZvcm0oKTtcbiAgfVxuXG4gIGhhbmRsZUFkZFJvb21DaGFuZ2VzKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgaWYgKCEgdGhpcy5lbnN1cmVJbnB1dERhdGVzKCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0aGlzLmFqYXhVcGRhdGVGb3JtKClcbiAgICAgIC5kb25lKGZ1bmN0aW9uKCkge1xuICAgICAgICAkKCdidXR0b25bdHlwZT1cInN1Ym1pdFwiXScsIHNlbGYuJGZvcm0pLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgfSk7XG4gIH1cblxuICBhamF4VXBkYXRlRm9ybSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCAkY29udGFpbmVyID0gc2VsZi4kZm9ybS5maW5kKCcuYXdlYm9va2luZy1kaWFsb2ctY29udGVudHMnKTtcblxuICAgIHJldHVybiBhd2Vib29raW5nLmFqYXhTdWJtaXQodGhpcy5mb3JtLCAnZ2V0X2F3ZWJvb2tpbmdfYWRkX2l0ZW1fZm9ybScpXG4gICAgICAuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICAkKCcjYWRkX2NoZWNrX2luX291dF8wJywgJGNvbnRhaW5lcikuZGF0ZXBpY2tlcignZGVzdHJveScpO1xuICAgICAgICAkKCcjYWRkX2NoZWNrX2luX291dF8xJywgJGNvbnRhaW5lcikuZGF0ZXBpY2tlcignZGVzdHJveScpO1xuXG4gICAgICAgICRjb250YWluZXIuaHRtbChyZXNwb25zZS5odG1sKTtcbiAgICAgIH0pO1xuICB9XG5cbiAgZW5zdXJlSW5wdXREYXRlcygpIHtcbiAgICB2YXIgJGNoZWNrX2luICA9IHRoaXMuJGZvcm0uZmluZCgnI2FkZF9jaGVja19pbl9vdXRfMCcpO1xuICAgIHZhciAkY2hlY2tfb3V0ID0gdGhpcy4kZm9ybS5maW5kKCcjYWRkX2NoZWNrX2luX291dF8xJyk7XG5cbiAgICByZXR1cm4gJGNoZWNrX2luLnZhbCgpICYmICRjaGVja19vdXQudmFsKCk7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBBZGRMaW5lSXRlbTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi9ib29raW5nL2FkZC1saW5lLWl0ZW0uanMiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IGF3ZWJvb2tpbmcgPSB3aW5kb3cuVGhlQXdlQm9va2luZztcblxuY2xhc3MgRWRpdExpbmVJdGVtIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy4kcG9wdXAgPSAkKCcjYXdlYm9va2luZy1lZGl0LWxpbmUtaXRlbS1wb3B1cCcpO1xuICAgIGF3ZWJvb2tpbmcuUG9wdXAuc2V0dXAodGhpcy4kcG9wdXApO1xuXG4gICAgJCgnZm9ybScsIHRoaXMuJHBvcHVwKS5vbignc3VibWl0JywgdGhpcy5zdWJtaXRGb3JtKTtcbiAgICAkKCcuanMtZWRpdC1saW5lLWl0ZW0nKS5vbignY2xpY2snLCB0aGlzLm9wZW5Qb3B1cC5iaW5kKHRoaXMpKTtcbiAgfVxuXG4gIG9wZW5Qb3B1cChlKSB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IGxpbmVJdGVtID0gJChlLmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2xpbmVJdGVtJyk7XG5cbiAgICBzZWxmLiRwb3B1cC5maW5kKCcuYXdlYm9va2luZy1kaWFsb2ctY29udGVudHMnKS5odG1sKCdMb2FkaW5nLi4uJyk7XG4gICAgc2VsZi4kcG9wdXAuZGlhbG9nKCdvcGVuJyk7XG5cbiAgICByZXR1cm4gd3AuYWpheC5wb3N0KCdnZXRfYXdlYm9va2luZ19lZGl0X2xpbmVfaXRlbV9mb3JtJywgeyBsaW5lX2l0ZW1faWQ6IGxpbmVJdGVtIH0pXG4gICAgICAuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICBzZWxmLiRwb3B1cC5maW5kKCcuYXdlYm9va2luZy1kaWFsb2ctY29udGVudHMnKS5odG1sKHJlc3BvbnNlLmh0bWwpO1xuICAgICAgfSk7XG4gIH1cblxuICBzdWJtaXRGb3JtKGUpIHtcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICBhd2Vib29raW5nLmFqYXhTdWJtaXQodGhpcywgJ2VkaXRfYXdlYm9va2luZ19saW5lX2l0ZW0nKVxuICAgICAgLmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblxuICAgICAgICAvLyBUT0RPOiBJbXByb3ZlIHRoaXMhXG4gICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgd2luZG93LmxvY2F0aW9uLnJlbG9hZCgpO1xuICAgICAgICB9LCAyNTApO1xuXG4gICAgICB9KVxuICAgICAgLmZhaWwoZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgaWYgKHJlc3BvbnNlLmVycm9yKSB7XG4gICAgICAgICAgYWxlcnQocmVzcG9uc2UuZXJyb3IpO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgfVxufVxuXG5tb2R1bGUuZXhwb3J0cyA9IEVkaXRMaW5lSXRlbTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi9ib29raW5nL2VkaXQtbGluZS1pdGVtLmpzIl0sInNvdXJjZVJvb3QiOiIifQ==