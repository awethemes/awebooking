webpackJsonp([0],[
/* 0 */,
/* 1 */
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

/***/ }),
/* 2 */,
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(4);
__webpack_require__(10);
__webpack_require__(11);
module.exports = __webpack_require__(12);


/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

var $ = window.jQuery;
var settings = window._awebookingSettings || {};

var AweBooking = _.extend(settings, {
  Vue: __webpack_require__(0),
  Popup: __webpack_require__(6),
  ToggleClass: __webpack_require__(7),
  RangeDatepicker: __webpack_require__(8),

  /**
   * Init the AweBooking
   */
  init: function init() {
    var self = this;

    // Init the popup, use jquery-ui-popup.
    $('[data-toggle="awebooking-popup"]').each(function () {
      $(this).data('awebooking-popup', new self.Popup(this));
    });

    $('[data-init="awebooking-toggle"]').each(function () {
      $(this).data('awebooking-toggle', new self.ToggleClass(this));
    });

    __webpack_require__(9);
  },


  /**
   * Get a translator string
   */
  trans: function trans(context) {
    return this.strings[context] ? this.strings[context] : '';
  },


  /**
   * Make form ajax request.
   */
  ajaxSubmit: function ajaxSubmit(form, action) {
    var serialize = __webpack_require__(2);
    var data = serialize(form, { hash: true });

    // Add .ajax-loading class in to the form.
    $(form).addClass('ajax-loading');

    return wp.ajax.post(action, data).always(function () {
      $(form).removeClass('ajax-loading');
    });
  }
});

$(function () {
  AweBooking.init();
});

window.TheAweBooking = AweBooking;

/***/ }),
/* 5 */,
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(1);

var Popup = function () {
  /**
   * Wrapper the jquery-ui-popup.
   */
  function Popup(el) {
    _classCallCheck(this, Popup);

    this.el = el;
    this.target = Utils.getSelectorFromElement(el);

    if (this.target) {
      Popup.setup(this.target);

      $(this.el).on('click', this.open.bind(this));
      $(this.target).on('click', '[data-dismiss="awebooking-popup"]', this.close.bind(this));
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
  }], [{
    key: 'setup',
    value: function setup(target) {
      var $target = $(target);
      if (!$target.length) {
        return;
      }

      if ($target.dialog('instance')) {
        return;
      }

      var _triggerResize = function _triggerResize() {
        if ($target.dialog('isOpen')) {
          $target.dialog('option', 'position', { my: 'center', at: 'center top+25%', of: window });
        }
      };

      var dialog = $target.dialog({
        modal: true,
        width: 'auto',
        height: 'auto',
        autoOpen: false,
        draggable: true,
        resizable: false,
        closeOnEscape: true,
        dialogClass: 'wp-dialog awebooking-dialog',
        position: { my: 'center', at: 'center top+25%', of: window },
        open: function open() {
          // $('body').css({ overflow: 'hidden' });
        },
        beforeClose: function beforeClose(event, ui) {
          // $('body').css({ overflow: 'inherit' });
        }
      });

      // $(window).on('resize', _.debounce(_triggerResize, 250));

      return dialog;
    }
  }]);

  return Popup;
}();

module.exports = Popup;

/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(1);

var ToggleClass = function () {
  function ToggleClass(el) {
    _classCallCheck(this, ToggleClass);

    this.el = el;
    this.target = Utils.getSelectorFromElement(el);

    if (!this.target) {
      this.target = $(el).parent().children('.awebooking-main-toggle')[0];
    }

    if (this.target) {
      $(this.el).on('click', this.toggleClass.bind(this));
      $(document).on('click', this.removeClass.bind(this));
    }
  }

  _createClass(ToggleClass, [{
    key: 'toggleClass',
    value: function toggleClass(e) {
      e && e.preventDefault();
      $(this.target).parent().toggleClass('active');
    }
  }, {
    key: 'removeClass',
    value: function removeClass(e) {
      if (e && $.contains($(this.target).parent()[0], e.target)) {
        return;
      }

      $(this.target).parent().removeClass('active');
    }
  }]);

  return ToggleClass;
}();

module.exports = ToggleClass;

/***/ }),
/* 8 */
/***/ (function(module, exports) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var DATE_FORMAT = 'yy-mm-dd';

var RangeDatepicker = function () {
  function RangeDatepicker(fromDate, toDate) {
    _classCallCheck(this, RangeDatepicker);

    this.toDate = toDate;
    this.fromDate = fromDate;
  }

  _createClass(RangeDatepicker, [{
    key: 'init',
    value: function init() {
      var beforeShowCallback = function beforeShowCallback() {
        $('#ui-datepicker-div').addClass('cmb2-element');
      };

      $(this.fromDate).datepicker({
        dateFormat: DATE_FORMAT,
        beforeShow: beforeShowCallback
      }).on('change', this.applyFromChange.bind(this));

      $(this.toDate).datepicker({
        dateFormat: DATE_FORMAT,
        beforeShow: beforeShowCallback
      }).on('change', this.applyToChange.bind(this));

      this.applyToChange();
      this.applyFromChange();
    }
  }, {
    key: 'applyFromChange',
    value: function applyFromChange() {
      try {
        var minDate = $.datepicker.parseDate(DATE_FORMAT, $(this.fromDate).val());
        minDate.setDate(minDate.getDate() + 1);
        $(this.toDate).datepicker('option', 'minDate', minDate);
      } catch (e) {}
    }
  }, {
    key: 'applyToChange',
    value: function applyToChange() {
      try {
        var maxDate = $.datepicker.parseDate(DATE_FORMAT, $(this.toDate).val());
        $(this.fromDate).datepicker('option', 'maxDate', maxDate);
      } catch (e) {}
    }
  }]);

  return RangeDatepicker;
}();

module.exports = RangeDatepicker;

/***/ }),
/* 9 */
/***/ (function(module, exports) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var AweBooking = window.TheAweBooking;

var InitSelect2 = function () {
  function InitSelect2() {
    _classCallCheck(this, InitSelect2);

    this.searchCustomer();
  }

  // Ajax customer search boxes


  _createClass(InitSelect2, [{
    key: 'searchCustomer',
    value: function searchCustomer() {
      $(':input.awebooking-customer-search, select[name="booking_customer"]').filter(':not(.enhanced)').each(function () {
        var select2_args = {
          allowClear: $(this).data('allowClear') ? true : false,
          placeholder: $(this).data('placeholder') ? $(this).data('placeholder') : "",
          minimumInputLength: $(this).data('minimum_input_length') ? $(this).data('minimum_input_length') : '1',
          escapeMarkup: function escapeMarkup(m) {
            return m;
          },
          ajax: {
            url: AweBooking.ajax_url,
            dataType: 'json',
            delay: 250,
            data: function data(params) {
              return {
                term: params.term,
                action: 'awebooking_json_search_customers',
                // security: wc_enhanced_select_params.search_customers_nonce,
                exclude: $(this).data('exclude')
              };
            },
            processResults: function processResults(data) {
              var terms = [];
              if (data) {
                $.each(data, function (id, text) {
                  terms.push({
                    id: id,
                    text: text
                  });
                });
              }
              return {
                results: terms
              };
            },
            cache: true
          }
        };

        $(this).select2(select2_args).addClass('enhanced');
      });
    }
  }]);

  return InitSelect2;
}();

module.exports = new InitSelect2();

/***/ }),
/* 10 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 11 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 12 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
],[3]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvdXRpbHMuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2F3ZWJvb2tpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3BvcHVwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy90b2dnbGUtY2xhc3MuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3JhbmdlLWRhdGVwaWNrZXIuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL2luaXQtc2VsZWN0Mi5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvc2Fzcy9hZG1pbi5zY3NzIiwid2VicGFjazovLy8uL2Fzc2V0cy9zYXNzL3RoZW1lLnNjc3MiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3Nhc3MvYXdlYm9va2luZy5zY3NzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJqUXVlcnkiLCJVdGlscyIsImdldFNlbGVjdG9yRnJvbUVsZW1lbnQiLCJlbCIsInNlbGVjdG9yIiwiZ2V0QXR0cmlidXRlIiwiJHNlbGVjdG9yIiwibGVuZ3RoIiwiZXJyb3IiLCJtb2R1bGUiLCJleHBvcnRzIiwic2V0dGluZ3MiLCJfYXdlYm9va2luZ1NldHRpbmdzIiwiQXdlQm9va2luZyIsIl8iLCJleHRlbmQiLCJWdWUiLCJyZXF1aXJlIiwiUG9wdXAiLCJUb2dnbGVDbGFzcyIsIlJhbmdlRGF0ZXBpY2tlciIsImluaXQiLCJzZWxmIiwiZWFjaCIsImRhdGEiLCJ0cmFucyIsImNvbnRleHQiLCJzdHJpbmdzIiwiYWpheFN1Ym1pdCIsImZvcm0iLCJhY3Rpb24iLCJzZXJpYWxpemUiLCJoYXNoIiwiYWRkQ2xhc3MiLCJ3cCIsImFqYXgiLCJwb3N0IiwiYWx3YXlzIiwicmVtb3ZlQ2xhc3MiLCJUaGVBd2VCb29raW5nIiwidGFyZ2V0Iiwic2V0dXAiLCJvbiIsIm9wZW4iLCJiaW5kIiwiY2xvc2UiLCJlIiwicHJldmVudERlZmF1bHQiLCJkaWFsb2ciLCIkdGFyZ2V0IiwiX3RyaWdnZXJSZXNpemUiLCJteSIsImF0Iiwib2YiLCJtb2RhbCIsIndpZHRoIiwiaGVpZ2h0IiwiYXV0b09wZW4iLCJkcmFnZ2FibGUiLCJyZXNpemFibGUiLCJjbG9zZU9uRXNjYXBlIiwiZGlhbG9nQ2xhc3MiLCJwb3NpdGlvbiIsImJlZm9yZUNsb3NlIiwiZXZlbnQiLCJ1aSIsInBhcmVudCIsImNoaWxkcmVuIiwidG9nZ2xlQ2xhc3MiLCJkb2N1bWVudCIsImNvbnRhaW5zIiwiREFURV9GT1JNQVQiLCJmcm9tRGF0ZSIsInRvRGF0ZSIsImJlZm9yZVNob3dDYWxsYmFjayIsImRhdGVwaWNrZXIiLCJkYXRlRm9ybWF0IiwiYmVmb3JlU2hvdyIsImFwcGx5RnJvbUNoYW5nZSIsImFwcGx5VG9DaGFuZ2UiLCJtaW5EYXRlIiwicGFyc2VEYXRlIiwidmFsIiwic2V0RGF0ZSIsImdldERhdGUiLCJtYXhEYXRlIiwiSW5pdFNlbGVjdDIiLCJzZWFyY2hDdXN0b21lciIsImZpbHRlciIsInNlbGVjdDJfYXJncyIsImFsbG93Q2xlYXIiLCJwbGFjZWhvbGRlciIsIm1pbmltdW1JbnB1dExlbmd0aCIsImVzY2FwZU1hcmt1cCIsIm0iLCJ1cmwiLCJhamF4X3VybCIsImRhdGFUeXBlIiwiZGVsYXkiLCJwYXJhbXMiLCJ0ZXJtIiwiZXhjbHVkZSIsInByb2Nlc3NSZXN1bHRzIiwidGVybXMiLCJpZCIsInRleHQiLCJwdXNoIiwicmVzdWx0cyIsImNhY2hlIiwic2VsZWN0MiJdLCJtYXBwaW5ncyI6Ijs7Ozs7QUFBQSxJQUFJQSxJQUFJQyxPQUFPQyxNQUFmOztBQUVBLElBQU1DLFFBQVE7QUFFWkMsd0JBRlksa0NBRVdDLEVBRlgsRUFFZTtBQUN6QixRQUFJQyxXQUFXRCxHQUFHRSxZQUFILENBQWdCLGFBQWhCLENBQWY7O0FBRUEsUUFBSSxDQUFDRCxRQUFELElBQWFB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QWlCLElBQUk0RSxXQUFKLEVBQWpCLEM7Ozs7OztBQ3REQSx5Qzs7Ozs7O0FDQUEseUM7Ozs7OztBQ0FBLHlDIiwiZmlsZSI6IlxcanNcXGFkbWluXFxhd2Vib29raW5nLmpzIiwic291cmNlc0NvbnRlbnQiOlsidmFyICQgPSB3aW5kb3cualF1ZXJ5O1xyXG5cclxuY29uc3QgVXRpbHMgPSB7XHJcblxyXG4gIGdldFNlbGVjdG9yRnJvbUVsZW1lbnQoZWwpIHtcclxuICAgIGxldCBzZWxlY3RvciA9IGVsLmdldEF0dHJpYnV0ZSgnZGF0YS10YXJnZXQnKTtcclxuXHJcbiAgICBpZiAoIXNlbGVjdG9yIHx8IHNlbGVjdG9yID09PSAnIycpIHtcclxuICAgICAgc2VsZWN0b3IgPSBlbC5nZXRBdHRyaWJ1dGUoJ2hyZWYnKSB8fCAnJztcclxuICAgIH1cclxuXHJcbiAgICB0cnkge1xyXG4gICAgICBjb25zdCAkc2VsZWN0b3IgPSAkKHNlbGVjdG9yKTtcclxuICAgICAgcmV0dXJuICRzZWxlY3Rvci5sZW5ndGggPiAwID8gc2VsZWN0b3IgOiBudWxsO1xyXG4gICAgfSBjYXRjaCAoZXJyb3IpIHtcclxuICAgICAgcmV0dXJuIG51bGw7XHJcbiAgICB9XHJcbiAgfSxcclxuXHJcbn07XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IFV0aWxzO1xyXG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvdXRpbHMuanMiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcclxuY29uc3Qgc2V0dGluZ3MgPSB3aW5kb3cuX2F3ZWJvb2tpbmdTZXR0aW5ncyB8fCB7fTtcclxuXHJcbmNvbnN0IEF3ZUJvb2tpbmcgPSBfLmV4dGVuZChzZXR0aW5ncywge1xyXG4gIFZ1ZTogcmVxdWlyZSgndnVlJyksXHJcbiAgUG9wdXA6IHJlcXVpcmUoJy4vdXRpbHMvcG9wdXAuanMnKSxcclxuICBUb2dnbGVDbGFzczogcmVxdWlyZSgnLi91dGlscy90b2dnbGUtY2xhc3MuanMnKSxcclxuICBSYW5nZURhdGVwaWNrZXI6IHJlcXVpcmUoJy4vdXRpbHMvcmFuZ2UtZGF0ZXBpY2tlci5qcycpLFxyXG5cclxuICAvKipcclxuICAgKiBJbml0IHRoZSBBd2VCb29raW5nXHJcbiAgICovXHJcbiAgaW5pdCgpIHtcclxuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xyXG5cclxuICAgIC8vIEluaXQgdGhlIHBvcHVwLCB1c2UganF1ZXJ5LXVpLXBvcHVwLlxyXG4gICAgJCgnW2RhdGEtdG9nZ2xlPVwiYXdlYm9va2luZy1wb3B1cFwiXScpLmVhY2goZnVuY3Rpb24oKSB7XHJcbiAgICAgICQodGhpcykuZGF0YSgnYXdlYm9va2luZy1wb3B1cCcsIG5ldyBzZWxmLlBvcHVwKHRoaXMpKTtcclxuICAgIH0pO1xyXG5cclxuICAgICQoJ1tkYXRhLWluaXQ9XCJhd2Vib29raW5nLXRvZ2dsZVwiXScpLmVhY2goZnVuY3Rpb24oKSB7XHJcbiAgICAgICQodGhpcykuZGF0YSgnYXdlYm9va2luZy10b2dnbGUnLCBuZXcgc2VsZi5Ub2dnbGVDbGFzcyh0aGlzKSk7XHJcbiAgICB9KTtcclxuXHJcbiAgICByZXF1aXJlKCcuL3V0aWxzL2luaXQtc2VsZWN0Mi5qcycpO1xyXG4gIH0sXHJcblxyXG4gIC8qKlxyXG4gICAqIEdldCBhIHRyYW5zbGF0b3Igc3RyaW5nXHJcbiAgICovXHJcbiAgdHJhbnMoY29udGV4dCkge1xyXG4gICAgcmV0dXJuIHRoaXMuc3RyaW5nc1tjb250ZXh0XSA/IHRoaXMuc3RyaW5nc1tjb250ZXh0XSA6ICcnO1xyXG4gIH0sXHJcblxyXG4gIC8qKlxyXG4gICAqIE1ha2UgZm9ybSBhamF4IHJlcXVlc3QuXHJcbiAgICovXHJcbiAgYWpheFN1Ym1pdChmb3JtLCBhY3Rpb24pIHtcclxuICAgIGNvbnN0IHNlcmlhbGl6ZSA9IHJlcXVpcmUoJ2Zvcm0tc2VyaWFsaXplJyk7XHJcbiAgICBjb25zdCBkYXRhID0gc2VyaWFsaXplKGZvcm0sIHsgaGFzaDogdHJ1ZSB9KTtcclxuXHJcbiAgICAvLyBBZGQgLmFqYXgtbG9hZGluZyBjbGFzcyBpbiB0byB0aGUgZm9ybS5cclxuICAgICQoZm9ybSkuYWRkQ2xhc3MoJ2FqYXgtbG9hZGluZycpO1xyXG5cclxuICAgIHJldHVybiB3cC5hamF4LnBvc3QoYWN0aW9uLCBkYXRhKVxyXG4gICAgICAuYWx3YXlzKGZ1bmN0aW9uKCkge1xyXG4gICAgICAgICQoZm9ybSkucmVtb3ZlQ2xhc3MoJ2FqYXgtbG9hZGluZycpO1xyXG4gICAgICB9KTtcclxuICB9LFxyXG59KTtcclxuXHJcbiQoZnVuY3Rpb24oKSB7XHJcbiAgQXdlQm9va2luZy5pbml0KCk7XHJcbn0pO1xyXG5cclxud2luZG93LlRoZUF3ZUJvb2tpbmcgPSBBd2VCb29raW5nO1xyXG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vYXdlYm9va2luZy5qcyIsImNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xyXG5jb25zdCBVdGlscyA9IHJlcXVpcmUoJy4vdXRpbHMuanMnKTtcclxuXHJcbmNsYXNzIFBvcHVwIHtcclxuICAvKipcclxuICAgKiBXcmFwcGVyIHRoZSBqcXVlcnktdWktcG9wdXAuXHJcbiAgICovXHJcbiAgY29uc3RydWN0b3IoZWwpIHtcclxuICAgIHRoaXMuZWwgPSBlbDtcclxuICAgIHRoaXMudGFyZ2V0ID0gVXRpbHMuZ2V0U2VsZWN0b3JGcm9tRWxlbWVudChlbCk7XHJcblxyXG4gICAgaWYgKHRoaXMudGFyZ2V0KSB7XHJcbiAgICAgIFBvcHVwLnNldHVwKHRoaXMudGFyZ2V0KTtcclxuXHJcbiAgICAgICQodGhpcy5lbCkub24oJ2NsaWNrJywgdGhpcy5vcGVuLmJpbmQodGhpcykpO1xyXG4gICAgICAkKHRoaXMudGFyZ2V0KS5vbignY2xpY2snLCAnW2RhdGEtZGlzbWlzcz1cImF3ZWJvb2tpbmctcG9wdXBcIl0nLCB0aGlzLmNsb3NlLmJpbmQodGhpcykpO1xyXG4gICAgfVxyXG4gIH1cclxuXHJcbiAgb3BlbihlKSB7XHJcbiAgICBlICYmIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICQodGhpcy50YXJnZXQpLmRpYWxvZygnb3BlbicpO1xyXG4gIH1cclxuXHJcbiAgY2xvc2UoZSkge1xyXG4gICAgZSAmJiBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAkKHRoaXMudGFyZ2V0KS5kaWFsb2coJ2Nsb3NlJyk7XHJcbiAgfVxyXG5cclxuICBzdGF0aWMgc2V0dXAodGFyZ2V0KSB7XHJcbiAgICBjb25zdCAkdGFyZ2V0ID0gJCh0YXJnZXQpO1xyXG4gICAgaWYgKCEgJHRhcmdldC5sZW5ndGgpIHtcclxuICAgICAgcmV0dXJuO1xyXG4gICAgfVxyXG5cclxuICAgIGlmICgkdGFyZ2V0LmRpYWxvZygnaW5zdGFuY2UnKSkge1xyXG4gICAgICByZXR1cm47XHJcbiAgICB9XHJcblxyXG4gICAgbGV0IF90cmlnZ2VyUmVzaXplID0gZnVuY3Rpb24oKSB7XHJcbiAgICAgIGlmICgkdGFyZ2V0LmRpYWxvZygnaXNPcGVuJykpIHtcclxuICAgICAgICAkdGFyZ2V0LmRpYWxvZygnb3B0aW9uJywgJ3Bvc2l0aW9uJywgeyBteTogJ2NlbnRlcicsIGF0OiAnY2VudGVyIHRvcCsyNSUnLCBvZjogd2luZG93IH0pO1xyXG4gICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgbGV0IGRpYWxvZyA9ICR0YXJnZXQuZGlhbG9nKHtcclxuICAgICAgbW9kYWw6IHRydWUsXHJcbiAgICAgIHdpZHRoOiAnYXV0bycsXHJcbiAgICAgIGhlaWdodDogJ2F1dG8nLFxyXG4gICAgICBhdXRvT3BlbjogZmFsc2UsXHJcbiAgICAgIGRyYWdnYWJsZTogdHJ1ZSxcclxuICAgICAgcmVzaXphYmxlOiBmYWxzZSxcclxuICAgICAgY2xvc2VPbkVzY2FwZTogdHJ1ZSxcclxuICAgICAgZGlhbG9nQ2xhc3M6ICd3cC1kaWFsb2cgYXdlYm9va2luZy1kaWFsb2cnLFxyXG4gICAgICBwb3NpdGlvbjogeyBteTogJ2NlbnRlcicsIGF0OiAnY2VudGVyIHRvcCsyNSUnLCBvZjogd2luZG93IH0sXHJcbiAgICAgIG9wZW46IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAvLyAkKCdib2R5JykuY3NzKHsgb3ZlcmZsb3c6ICdoaWRkZW4nIH0pO1xyXG4gICAgICB9LFxyXG4gICAgICBiZWZvcmVDbG9zZTogZnVuY3Rpb24oZXZlbnQsIHVpKSB7XHJcbiAgICAgICAgLy8gJCgnYm9keScpLmNzcyh7IG92ZXJmbG93OiAnaW5oZXJpdCcgfSk7XHJcbiAgICAgfVxyXG4gICAgfSk7XHJcblxyXG4gICAgLy8gJCh3aW5kb3cpLm9uKCdyZXNpemUnLCBfLmRlYm91bmNlKF90cmlnZ2VyUmVzaXplLCAyNTApKTtcclxuXHJcbiAgICByZXR1cm4gZGlhbG9nO1xyXG4gIH1cclxufVxyXG5cclxubW9kdWxlLmV4cG9ydHMgPSBQb3B1cDtcclxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3BvcHVwLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XHJcbmNvbnN0IFV0aWxzID0gcmVxdWlyZSgnLi91dGlscy5qcycpO1xyXG5cclxuY2xhc3MgVG9nZ2xlQ2xhc3Mge1xyXG5cclxuICBjb25zdHJ1Y3RvcihlbCkge1xyXG4gICAgdGhpcy5lbCA9IGVsO1xyXG4gICAgdGhpcy50YXJnZXQgPSBVdGlscy5nZXRTZWxlY3RvckZyb21FbGVtZW50KGVsKTtcclxuXHJcbiAgICBpZiAoIXRoaXMudGFyZ2V0KSB7XHJcbiAgICAgIHRoaXMudGFyZ2V0ID0gJChlbCkucGFyZW50KCkuY2hpbGRyZW4oJy5hd2Vib29raW5nLW1haW4tdG9nZ2xlJylbMF07XHJcbiAgICB9XHJcblxyXG4gICAgaWYgKHRoaXMudGFyZ2V0KSB7XHJcbiAgICAgICQodGhpcy5lbCkub24oJ2NsaWNrJywgdGhpcy50b2dnbGVDbGFzcy5iaW5kKHRoaXMpKTtcclxuICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5yZW1vdmVDbGFzcy5iaW5kKHRoaXMpKTtcclxuICAgIH1cclxuICB9XHJcblxyXG4gIHRvZ2dsZUNsYXNzKGUpIHtcclxuICAgIGUgJiYgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgJCh0aGlzLnRhcmdldCkucGFyZW50KCkudG9nZ2xlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gIH1cclxuXHJcbiAgcmVtb3ZlQ2xhc3MoZSkge1xyXG4gICAgaWYgKGUgJiYgJC5jb250YWlucygkKHRoaXMudGFyZ2V0KS5wYXJlbnQoKVswXSwgZS50YXJnZXQpKSB7XHJcbiAgICAgIHJldHVybjtcclxuICAgIH1cclxuXHJcbiAgICAkKHRoaXMudGFyZ2V0KS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcbiAgfVxyXG59XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IFRvZ2dsZUNsYXNzO1xyXG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvdG9nZ2xlLWNsYXNzLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XHJcbmNvbnN0IERBVEVfRk9STUFUID0gJ3l5LW1tLWRkJztcclxuXHJcbmNsYXNzIFJhbmdlRGF0ZXBpY2tlciB7XHJcblxyXG4gIGNvbnN0cnVjdG9yKGZyb21EYXRlLCB0b0RhdGUpIHtcclxuICAgIHRoaXMudG9EYXRlID0gdG9EYXRlO1xyXG4gICAgdGhpcy5mcm9tRGF0ZSA9IGZyb21EYXRlO1xyXG4gIH1cclxuXHJcbiAgaW5pdCgpIHtcclxuICAgIGNvbnN0IGJlZm9yZVNob3dDYWxsYmFjayA9IGZ1bmN0aW9uKCkge1xyXG4gICAgICAkKCcjdWktZGF0ZXBpY2tlci1kaXYnKS5hZGRDbGFzcygnY21iMi1lbGVtZW50Jyk7XHJcbiAgICB9O1xyXG5cclxuICAgICQodGhpcy5mcm9tRGF0ZSkuZGF0ZXBpY2tlcih7XHJcbiAgICAgIGRhdGVGb3JtYXQ6IERBVEVfRk9STUFULFxyXG4gICAgICBiZWZvcmVTaG93OiBiZWZvcmVTaG93Q2FsbGJhY2ssXHJcbiAgICB9KS5vbignY2hhbmdlJywgdGhpcy5hcHBseUZyb21DaGFuZ2UuYmluZCh0aGlzKSk7XHJcblxyXG4gICAgJCh0aGlzLnRvRGF0ZSkuZGF0ZXBpY2tlcih7XHJcbiAgICAgIGRhdGVGb3JtYXQ6IERBVEVfRk9STUFULFxyXG4gICAgICBiZWZvcmVTaG93OiBiZWZvcmVTaG93Q2FsbGJhY2ssXHJcbiAgICB9KS5vbignY2hhbmdlJywgdGhpcy5hcHBseVRvQ2hhbmdlLmJpbmQodGhpcykpO1xyXG5cclxuICAgIHRoaXMuYXBwbHlUb0NoYW5nZSgpO1xyXG4gICAgdGhpcy5hcHBseUZyb21DaGFuZ2UoKTtcclxuICB9XHJcblxyXG4gIGFwcGx5RnJvbUNoYW5nZSgpIHtcclxuICAgIHRyeSB7XHJcbiAgICAgIGNvbnN0IG1pbkRhdGUgPSAkLmRhdGVwaWNrZXIucGFyc2VEYXRlKERBVEVfRk9STUFULCAkKHRoaXMuZnJvbURhdGUpLnZhbCgpKTtcclxuICAgICAgbWluRGF0ZS5zZXREYXRlKG1pbkRhdGUuZ2V0RGF0ZSgpICsgMSk7XHJcbiAgICAgICQodGhpcy50b0RhdGUpLmRhdGVwaWNrZXIoJ29wdGlvbicsICdtaW5EYXRlJywgbWluRGF0ZSk7XHJcbiAgICB9IGNhdGNoKGUpIHt9XHJcbiAgfVxyXG5cclxuICBhcHBseVRvQ2hhbmdlKCkge1xyXG4gICAgdHJ5IHtcclxuICAgICAgY29uc3QgbWF4RGF0ZSA9ICQuZGF0ZXBpY2tlci5wYXJzZURhdGUoREFURV9GT1JNQVQsICQodGhpcy50b0RhdGUpLnZhbCgpKTtcclxuICAgICAgJCh0aGlzLmZyb21EYXRlKS5kYXRlcGlja2VyKCdvcHRpb24nLCAnbWF4RGF0ZScsIG1heERhdGUpO1xyXG4gICAgfSBjYXRjaChlKSB7fVxyXG4gIH1cclxufVxyXG5cclxubW9kdWxlLmV4cG9ydHMgPSBSYW5nZURhdGVwaWNrZXI7XHJcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9yYW5nZS1kYXRlcGlja2VyLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XHJcbmNvbnN0IEF3ZUJvb2tpbmcgPSB3aW5kb3cuVGhlQXdlQm9va2luZztcclxuXHJcbmNsYXNzIEluaXRTZWxlY3QyIHtcclxuICBjb25zdHJ1Y3RvcigpIHtcclxuICAgIHRoaXMuc2VhcmNoQ3VzdG9tZXIoKTtcclxuICB9XHJcblxyXG4gIC8vIEFqYXggY3VzdG9tZXIgc2VhcmNoIGJveGVzXHJcbiAgc2VhcmNoQ3VzdG9tZXIoKSB7XHJcbiAgICAkKCc6aW5wdXQuYXdlYm9va2luZy1jdXN0b21lci1zZWFyY2gsIHNlbGVjdFtuYW1lPVwiYm9va2luZ19jdXN0b21lclwiXScpLmZpbHRlciggJzpub3QoLmVuaGFuY2VkKScgKS5lYWNoKCBmdW5jdGlvbigpIHtcclxuICAgICAgdmFyIHNlbGVjdDJfYXJncyA9IHtcclxuICAgICAgICBhbGxvd0NsZWFyOiAgJCggdGhpcyApLmRhdGEoICdhbGxvd0NsZWFyJyApID8gdHJ1ZSA6IGZhbHNlLFxyXG4gICAgICAgIHBsYWNlaG9sZGVyOiAkKCB0aGlzICkuZGF0YSggJ3BsYWNlaG9sZGVyJyApID8gJCggdGhpcyApLmRhdGEoICdwbGFjZWhvbGRlcicgKSA6IFwiXCIsXHJcbiAgICAgICAgbWluaW11bUlucHV0TGVuZ3RoOiAkKCB0aGlzICkuZGF0YSggJ21pbmltdW1faW5wdXRfbGVuZ3RoJyApID8gJCggdGhpcyApLmRhdGEoICdtaW5pbXVtX2lucHV0X2xlbmd0aCcgKSA6ICcxJyxcclxuICAgICAgICBlc2NhcGVNYXJrdXA6IGZ1bmN0aW9uKCBtICkge1xyXG4gICAgICAgICAgcmV0dXJuIG07XHJcbiAgICAgICAgfSxcclxuICAgICAgICBhamF4OiB7XHJcbiAgICAgICAgICB1cmw6ICAgICAgICAgQXdlQm9va2luZy5hamF4X3VybCxcclxuICAgICAgICAgIGRhdGFUeXBlOiAgICAnanNvbicsXHJcbiAgICAgICAgICBkZWxheTogICAgICAgMjUwLFxyXG4gICAgICAgICAgZGF0YTogICAgICAgIGZ1bmN0aW9uKCBwYXJhbXMgKSB7XHJcbiAgICAgICAgICAgIHJldHVybiB7XHJcbiAgICAgICAgICAgICAgdGVybTogICAgIHBhcmFtcy50ZXJtLFxyXG4gICAgICAgICAgICAgIGFjdGlvbjogICAnYXdlYm9va2luZ19qc29uX3NlYXJjaF9jdXN0b21lcnMnLFxyXG4gICAgICAgICAgICAgIC8vIHNlY3VyaXR5OiB3Y19lbmhhbmNlZF9zZWxlY3RfcGFyYW1zLnNlYXJjaF9jdXN0b21lcnNfbm9uY2UsXHJcbiAgICAgICAgICAgICAgZXhjbHVkZTogICQoIHRoaXMgKS5kYXRhKCAnZXhjbHVkZScgKVxyXG4gICAgICAgICAgICB9O1xyXG4gICAgICAgICAgfSxcclxuICAgICAgICAgIHByb2Nlc3NSZXN1bHRzOiBmdW5jdGlvbiggZGF0YSApIHtcclxuICAgICAgICAgICAgdmFyIHRlcm1zID0gW107XHJcbiAgICAgICAgICAgIGlmICggZGF0YSApIHtcclxuICAgICAgICAgICAgICAkLmVhY2goIGRhdGEsIGZ1bmN0aW9uKCBpZCwgdGV4dCApIHtcclxuICAgICAgICAgICAgICAgIHRlcm1zLnB1c2goe1xyXG4gICAgICAgICAgICAgICAgICBpZDogaWQsXHJcbiAgICAgICAgICAgICAgICAgIHRleHQ6IHRleHRcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIHJldHVybiB7XHJcbiAgICAgICAgICAgICAgcmVzdWx0czogdGVybXNcclxuICAgICAgICAgICAgfTtcclxuICAgICAgICAgIH0sXHJcbiAgICAgICAgICBjYWNoZTogdHJ1ZVxyXG4gICAgICAgIH1cclxuICAgICAgfTtcclxuXHJcbiAgICAgICQoIHRoaXMgKS5zZWxlY3QyKHNlbGVjdDJfYXJncykuYWRkQ2xhc3MoICdlbmhhbmNlZCcgKTtcclxuICAgIH0pO1xyXG5cclxuICB9XHJcbn1cclxuXHJcbm1vZHVsZS5leHBvcnRzID0gbmV3IEluaXRTZWxlY3QyO1xyXG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvaW5pdC1zZWxlY3QyLmpzIiwiLy8gcmVtb3ZlZCBieSBleHRyYWN0LXRleHQtd2VicGFjay1wbHVnaW5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL2Fzc2V0cy9zYXNzL2FkbWluLnNjc3Ncbi8vIG1vZHVsZSBpZCA9IDEwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCIsIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9hc3NldHMvc2Fzcy90aGVtZS5zY3NzXG4vLyBtb2R1bGUgaWQgPSAxMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAiLCIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vYXNzZXRzL3Nhc3MvYXdlYm9va2luZy5zY3NzXG4vLyBtb2R1bGUgaWQgPSAxMlxuLy8gbW9kdWxlIGNodW5rcyA9IDAiXSwic291cmNlUm9vdCI6IiJ9