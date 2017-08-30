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
module.exports = __webpack_require__(10);


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
        draggable: false,
        resizable: false,
        closeOnEscape: true,
        dialogClass: 'wp-dialog awebooking-dialog',
        position: { my: 'center', at: 'center top+25%', of: window },
        open: function open() {
          $('body').css({ overflow: 'hidden' });
        },
        beforeClose: function beforeClose(event, ui) {
          $('body').css({ overflow: 'inherit' });
        }
      });

      $(window).on('resize', _.debounce(_triggerResize, 250));

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

/***/ })
],[3]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvdXRpbHMuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2F3ZWJvb2tpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3BvcHVwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy90b2dnbGUtY2xhc3MuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3JhbmdlLWRhdGVwaWNrZXIuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL2luaXQtc2VsZWN0Mi5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvc2Fzcy9hZG1pbi5zY3NzP2Q5ZTYiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsImpRdWVyeSIsIlV0aWxzIiwiZ2V0U2VsZWN0b3JGcm9tRWxlbWVudCIsImVsIiwic2VsZWN0b3IiLCJnZXRBdHRyaWJ1dGUiLCIkc2VsZWN0b3IiLCJsZW5ndGgiLCJlcnJvciIsIm1vZHVsZSIsImV4cG9ydHMiLCJzZXR0aW5ncyIsIl9hd2Vib29raW5nU2V0dGluZ3MiLCJBd2VCb29raW5nIiwiXyIsImV4dGVuZCIsIlZ1ZSIsInJlcXVpcmUiLCJQb3B1cCIsIlRvZ2dsZUNsYXNzIiwiUmFuZ2VEYXRlcGlja2VyIiwiaW5pdCIsInNlbGYiLCJlYWNoIiwiZGF0YSIsInRyYW5zIiwiY29udGV4dCIsInN0cmluZ3MiLCJhamF4U3VibWl0IiwiZm9ybSIsImFjdGlvbiIsInNlcmlhbGl6ZSIsImhhc2giLCJhZGRDbGFzcyIsIndwIiwiYWpheCIsInBvc3QiLCJhbHdheXMiLCJyZW1vdmVDbGFzcyIsIlRoZUF3ZUJvb2tpbmciLCJ0YXJnZXQiLCJzZXR1cCIsIm9uIiwib3BlbiIsImJpbmQiLCJjbG9zZSIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImRpYWxvZyIsIiR0YXJnZXQiLCJfdHJpZ2dlclJlc2l6ZSIsIm15IiwiYXQiLCJvZiIsIm1vZGFsIiwid2lkdGgiLCJoZWlnaHQiLCJhdXRvT3BlbiIsImRyYWdnYWJsZSIsInJlc2l6YWJsZSIsImNsb3NlT25Fc2NhcGUiLCJkaWFsb2dDbGFzcyIsInBvc2l0aW9uIiwiY3NzIiwib3ZlcmZsb3ciLCJiZWZvcmVDbG9zZSIsImV2ZW50IiwidWkiLCJkZWJvdW5jZSIsInBhcmVudCIsImNoaWxkcmVuIiwidG9nZ2xlQ2xhc3MiLCJkb2N1bWVudCIsImNvbnRhaW5zIiwiREFURV9GT1JNQVQiLCJmcm9tRGF0ZSIsInRvRGF0ZSIsImJlZm9yZVNob3dDYWxsYmFjayIsImRhdGVwaWNrZXIiLCJkYXRlRm9ybWF0IiwiYmVmb3JlU2hvdyIsImFwcGx5RnJvbUNoYW5nZSIsImFwcGx5VG9DaGFuZ2UiLCJtaW5EYXRlIiwicGFyc2VEYXRlIiwidmFsIiwic2V0RGF0ZSIsImdldERhdGUiLCJtYXhEYXRlIiwiSW5pdFNlbGVjdDIiLCJzZWFyY2hDdXN0b21lciIsImZpbHRlciIsInNlbGVjdDJfYXJncyIsImFsbG93Q2xlYXIiLCJwbGFjZWhvbGRlciIsIm1pbmltdW1JbnB1dExlbmd0aCIsImVzY2FwZU1hcmt1cCIsIm0iLCJ1cmwiLCJhamF4X3VybCIsImRhdGFUeXBlIiwiZGVsYXkiLCJwYXJhbXMiLCJ0ZXJtIiwiZXhjbHVkZSIsInByb2Nlc3NSZXN1bHRzIiwidGVybXMiLCJpZCIsInRleHQiLCJwdXNoIiwicmVzdWx0cyIsImNhY2hlIiwic2VsZWN0MiJdLCJtYXBwaW5ncyI6Ijs7Ozs7QUFBQSxJQUFJQSxJQUFJQyxPQUFPQyxNQUFmOztBQUVBLElBQU1DLFFBQVE7QUFFWkMsd0JBRlksa0NBRVdDLEVBRlgsRUFFZTtBQUN6QixRQUFJQyxXQUFXRCxHQUFHRSxZQUFILENBQWdCLGFBQWhCLENBQWY7O0FBRUEsUUFBSSxDQUFDRCxRQUFELElBQWFBLGFBQWEsR0FBOUIsRUFBbUM7QUFDakNBLGlCQUFXRCxHQUFHRSxZQUFILENBQWdC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QU87QUExQkg7QUFQVyxTQUFuQjs7QUFxQ0FqSCxVQUFHLElBQUgsRUFBVWtILE9BQVYsQ0FBa0JwQixZQUFsQixFQUFnQzNELFFBQWhDLENBQTBDLFVBQTFDO0FBQ0QsT0F2Q0Q7QUF5Q0Q7Ozs7OztBQUdIeEIsT0FBT0MsT0FBUCxHQUFpQixJQUFJK0UsV0FBSixFQUFqQixDOzs7Ozs7QUN0REEseUMiLCJmaWxlIjoiL2pzL2FkbWluL2F3ZWJvb2tpbmcuanMiLCJzb3VyY2VzQ29udGVudCI6WyJ2YXIgJCA9IHdpbmRvdy5qUXVlcnk7XG5cbmNvbnN0IFV0aWxzID0ge1xuXG4gIGdldFNlbGVjdG9yRnJvbUVsZW1lbnQoZWwpIHtcbiAgICBsZXQgc2VsZWN0b3IgPSBlbC5nZXRBdHRyaWJ1dGUoJ2RhdGEtdGFyZ2V0Jyk7XG5cbiAgICBpZiAoIXNlbGVjdG9yIHx8IHNlbGVjdG9yID09PSAnIycpIHtcbiAgICAgIHNlbGVjdG9yID0gZWwuZ2V0QXR0cmlidXRlKCdocmVmJykgfHwgJyc7XG4gICAgfVxuXG4gICAgdHJ5IHtcbiAgICAgIGNvbnN0ICRzZWxlY3RvciA9ICQoc2VsZWN0b3IpO1xuICAgICAgcmV0dXJuICRzZWxlY3Rvci5sZW5ndGggPiAwID8gc2VsZWN0b3IgOiBudWxsO1xuICAgIH0gY2F0Y2ggKGVycm9yKSB7XG4gICAgICByZXR1cm4gbnVsbDtcbiAgICB9XG4gIH0sXG5cbn07XG5cbm1vZHVsZS5leHBvcnRzID0gVXRpbHM7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvdXRpbHMuanMiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IHNldHRpbmdzID0gd2luZG93Ll9hd2Vib29raW5nU2V0dGluZ3MgfHwge307XG5cbmNvbnN0IEF3ZUJvb2tpbmcgPSBfLmV4dGVuZChzZXR0aW5ncywge1xuICBWdWU6IHJlcXVpcmUoJ3Z1ZScpLFxuICBQb3B1cDogcmVxdWlyZSgnLi91dGlscy9wb3B1cC5qcycpLFxuICBUb2dnbGVDbGFzczogcmVxdWlyZSgnLi91dGlscy90b2dnbGUtY2xhc3MuanMnKSxcbiAgUmFuZ2VEYXRlcGlja2VyOiByZXF1aXJlKCcuL3V0aWxzL3JhbmdlLWRhdGVwaWNrZXIuanMnKSxcblxuICAvKipcbiAgICogSW5pdCB0aGUgQXdlQm9va2luZ1xuICAgKi9cbiAgaW5pdCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgIC8vIEluaXQgdGhlIHBvcHVwLCB1c2UganF1ZXJ5LXVpLXBvcHVwLlxuICAgICQoJ1tkYXRhLXRvZ2dsZT1cImF3ZWJvb2tpbmctcG9wdXBcIl0nKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgJCh0aGlzKS5kYXRhKCdhd2Vib29raW5nLXBvcHVwJywgbmV3IHNlbGYuUG9wdXAodGhpcykpO1xuICAgIH0pO1xuXG4gICAgJCgnW2RhdGEtaW5pdD1cImF3ZWJvb2tpbmctdG9nZ2xlXCJdJykuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICQodGhpcykuZGF0YSgnYXdlYm9va2luZy10b2dnbGUnLCBuZXcgc2VsZi5Ub2dnbGVDbGFzcyh0aGlzKSk7XG4gICAgfSk7XG5cbiAgICByZXF1aXJlKCcuL3V0aWxzL2luaXQtc2VsZWN0Mi5qcycpO1xuICB9LFxuXG4gIC8qKlxuICAgKiBHZXQgYSB0cmFuc2xhdG9yIHN0cmluZ1xuICAgKi9cbiAgdHJhbnMoY29udGV4dCkge1xuICAgIHJldHVybiB0aGlzLnN0cmluZ3NbY29udGV4dF0gPyB0aGlzLnN0cmluZ3NbY29udGV4dF0gOiAnJztcbiAgfSxcblxuICAvKipcbiAgICogTWFrZSBmb3JtIGFqYXggcmVxdWVzdC5cbiAgICovXG4gIGFqYXhTdWJtaXQoZm9ybSwgYWN0aW9uKSB7XG4gICAgY29uc3Qgc2VyaWFsaXplID0gcmVxdWlyZSgnZm9ybS1zZXJpYWxpemUnKTtcbiAgICBjb25zdCBkYXRhID0gc2VyaWFsaXplKGZvcm0sIHsgaGFzaDogdHJ1ZSB9KTtcblxuICAgIC8vIEFkZCAuYWpheC1sb2FkaW5nIGNsYXNzIGluIHRvIHRoZSBmb3JtLlxuICAgICQoZm9ybSkuYWRkQ2xhc3MoJ2FqYXgtbG9hZGluZycpO1xuXG4gICAgcmV0dXJuIHdwLmFqYXgucG9zdChhY3Rpb24sIGRhdGEpXG4gICAgICAuYWx3YXlzKGZ1bmN0aW9uKCkge1xuICAgICAgICAkKGZvcm0pLnJlbW92ZUNsYXNzKCdhamF4LWxvYWRpbmcnKTtcbiAgICAgIH0pO1xuICB9LFxufSk7XG5cbiQoZnVuY3Rpb24oKSB7XG4gIEF3ZUJvb2tpbmcuaW5pdCgpO1xufSk7XG5cbndpbmRvdy5UaGVBd2VCb29raW5nID0gQXdlQm9va2luZztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi9hd2Vib29raW5nLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBVdGlscyA9IHJlcXVpcmUoJy4vdXRpbHMuanMnKTtcblxuY2xhc3MgUG9wdXAge1xuICAvKipcbiAgICogV3JhcHBlciB0aGUganF1ZXJ5LXVpLXBvcHVwLlxuICAgKi9cbiAgY29uc3RydWN0b3IoZWwpIHtcbiAgICB0aGlzLmVsID0gZWw7XG4gICAgdGhpcy50YXJnZXQgPSBVdGlscy5nZXRTZWxlY3RvckZyb21FbGVtZW50KGVsKTtcblxuICAgIGlmICh0aGlzLnRhcmdldCkge1xuICAgICAgUG9wdXAuc2V0dXAodGhpcy50YXJnZXQpO1xuXG4gICAgICAkKHRoaXMuZWwpLm9uKCdjbGljaycsIHRoaXMub3Blbi5iaW5kKHRoaXMpKTtcbiAgICAgICQodGhpcy50YXJnZXQpLm9uKCdjbGljaycsICdbZGF0YS1kaXNtaXNzPVwiYXdlYm9va2luZy1wb3B1cFwiXScsIHRoaXMuY2xvc2UuYmluZCh0aGlzKSk7XG4gICAgfVxuICB9XG5cbiAgb3BlbihlKSB7XG4gICAgZSAmJiBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgJCh0aGlzLnRhcmdldCkuZGlhbG9nKCdvcGVuJyk7XG4gIH1cblxuICBjbG9zZShlKSB7XG4gICAgZSAmJiBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgJCh0aGlzLnRhcmdldCkuZGlhbG9nKCdjbG9zZScpO1xuICB9XG5cbiAgc3RhdGljIHNldHVwKHRhcmdldCkge1xuICAgIGNvbnN0ICR0YXJnZXQgPSAkKHRhcmdldCk7XG4gICAgaWYgKCEgJHRhcmdldC5sZW5ndGgpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAoJHRhcmdldC5kaWFsb2coJ2luc3RhbmNlJykpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBsZXQgX3RyaWdnZXJSZXNpemUgPSBmdW5jdGlvbigpIHtcbiAgICAgIGlmICgkdGFyZ2V0LmRpYWxvZygnaXNPcGVuJykpIHtcbiAgICAgICAgJHRhcmdldC5kaWFsb2coJ29wdGlvbicsICdwb3NpdGlvbicsIHsgbXk6ICdjZW50ZXInLCBhdDogJ2NlbnRlciB0b3ArMjUlJywgb2Y6IHdpbmRvdyB9KTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICBsZXQgZGlhbG9nID0gJHRhcmdldC5kaWFsb2coe1xuICAgICAgbW9kYWw6IHRydWUsXG4gICAgICB3aWR0aDogJ2F1dG8nLFxuICAgICAgaGVpZ2h0OiAnYXV0bycsXG4gICAgICBhdXRvT3BlbjogZmFsc2UsXG4gICAgICBkcmFnZ2FibGU6IGZhbHNlLFxuICAgICAgcmVzaXphYmxlOiBmYWxzZSxcbiAgICAgIGNsb3NlT25Fc2NhcGU6IHRydWUsXG4gICAgICBkaWFsb2dDbGFzczogJ3dwLWRpYWxvZyBhd2Vib29raW5nLWRpYWxvZycsXG4gICAgICBwb3NpdGlvbjogeyBteTogJ2NlbnRlcicsIGF0OiAnY2VudGVyIHRvcCsyNSUnLCBvZjogd2luZG93IH0sXG4gICAgICBvcGVuOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICQoJ2JvZHknKS5jc3MoeyBvdmVyZmxvdzogJ2hpZGRlbicgfSk7XG4gICAgICB9LFxuICAgICAgYmVmb3JlQ2xvc2U6IGZ1bmN0aW9uKGV2ZW50LCB1aSkge1xuICAgICAgICAkKCdib2R5JykuY3NzKHsgb3ZlcmZsb3c6ICdpbmhlcml0JyB9KTtcbiAgICAgfVxuICAgIH0pO1xuXG4gICAgJCh3aW5kb3cpLm9uKCdyZXNpemUnLCBfLmRlYm91bmNlKF90cmlnZ2VyUmVzaXplLCAyNTApKTtcblxuICAgIHJldHVybiBkaWFsb2c7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBQb3B1cDtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9wb3B1cC5qcyIsImNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xuY29uc3QgVXRpbHMgPSByZXF1aXJlKCcuL3V0aWxzLmpzJyk7XG5cbmNsYXNzIFRvZ2dsZUNsYXNzIHtcblxuICBjb25zdHJ1Y3RvcihlbCkge1xuICAgIHRoaXMuZWwgPSBlbDtcbiAgICB0aGlzLnRhcmdldCA9IFV0aWxzLmdldFNlbGVjdG9yRnJvbUVsZW1lbnQoZWwpO1xuXG4gICAgaWYgKCF0aGlzLnRhcmdldCkge1xuICAgICAgdGhpcy50YXJnZXQgPSAkKGVsKS5wYXJlbnQoKS5jaGlsZHJlbignLmF3ZWJvb2tpbmctbWFpbi10b2dnbGUnKVswXTtcbiAgICB9XG5cbiAgICBpZiAodGhpcy50YXJnZXQpIHtcbiAgICAgICQodGhpcy5lbCkub24oJ2NsaWNrJywgdGhpcy50b2dnbGVDbGFzcy5iaW5kKHRoaXMpKTtcbiAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMucmVtb3ZlQ2xhc3MuYmluZCh0aGlzKSk7XG4gICAgfVxuICB9XG5cbiAgdG9nZ2xlQ2xhc3MoZSkge1xuICAgIGUgJiYgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICQodGhpcy50YXJnZXQpLnBhcmVudCgpLnRvZ2dsZUNsYXNzKCdhY3RpdmUnKTtcbiAgfVxuXG4gIHJlbW92ZUNsYXNzKGUpIHtcbiAgICBpZiAoZSAmJiAkLmNvbnRhaW5zKCQodGhpcy50YXJnZXQpLnBhcmVudCgpWzBdLCBlLnRhcmdldCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAkKHRoaXMudGFyZ2V0KS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBUb2dnbGVDbGFzcztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy90b2dnbGUtY2xhc3MuanMiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IERBVEVfRk9STUFUID0gJ3l5LW1tLWRkJztcblxuY2xhc3MgUmFuZ2VEYXRlcGlja2VyIHtcblxuICBjb25zdHJ1Y3Rvcihmcm9tRGF0ZSwgdG9EYXRlKSB7XG4gICAgdGhpcy50b0RhdGUgPSB0b0RhdGU7XG4gICAgdGhpcy5mcm9tRGF0ZSA9IGZyb21EYXRlO1xuICB9XG5cbiAgaW5pdCgpIHtcbiAgICBjb25zdCBiZWZvcmVTaG93Q2FsbGJhY2sgPSBmdW5jdGlvbigpIHtcbiAgICAgICQoJyN1aS1kYXRlcGlja2VyLWRpdicpLmFkZENsYXNzKCdjbWIyLWVsZW1lbnQnKTtcbiAgICB9O1xuXG4gICAgJCh0aGlzLmZyb21EYXRlKS5kYXRlcGlja2VyKHtcbiAgICAgIGRhdGVGb3JtYXQ6IERBVEVfRk9STUFULFxuICAgICAgYmVmb3JlU2hvdzogYmVmb3JlU2hvd0NhbGxiYWNrLFxuICAgIH0pLm9uKCdjaGFuZ2UnLCB0aGlzLmFwcGx5RnJvbUNoYW5nZS5iaW5kKHRoaXMpKTtcblxuICAgICQodGhpcy50b0RhdGUpLmRhdGVwaWNrZXIoe1xuICAgICAgZGF0ZUZvcm1hdDogREFURV9GT1JNQVQsXG4gICAgICBiZWZvcmVTaG93OiBiZWZvcmVTaG93Q2FsbGJhY2ssXG4gICAgfSkub24oJ2NoYW5nZScsIHRoaXMuYXBwbHlUb0NoYW5nZS5iaW5kKHRoaXMpKTtcblxuICAgIHRoaXMuYXBwbHlUb0NoYW5nZSgpO1xuICAgIHRoaXMuYXBwbHlGcm9tQ2hhbmdlKCk7XG4gIH1cblxuICBhcHBseUZyb21DaGFuZ2UoKSB7XG4gICAgdHJ5IHtcbiAgICAgIGNvbnN0IG1pbkRhdGUgPSAkLmRhdGVwaWNrZXIucGFyc2VEYXRlKERBVEVfRk9STUFULCAkKHRoaXMuZnJvbURhdGUpLnZhbCgpKTtcbiAgICAgIG1pbkRhdGUuc2V0RGF0ZShtaW5EYXRlLmdldERhdGUoKSArIDEpO1xuICAgICAgJCh0aGlzLnRvRGF0ZSkuZGF0ZXBpY2tlcignb3B0aW9uJywgJ21pbkRhdGUnLCBtaW5EYXRlKTtcbiAgICB9IGNhdGNoKGUpIHt9XG4gIH1cblxuICBhcHBseVRvQ2hhbmdlKCkge1xuICAgIHRyeSB7XG4gICAgICBjb25zdCBtYXhEYXRlID0gJC5kYXRlcGlja2VyLnBhcnNlRGF0ZShEQVRFX0ZPUk1BVCwgJCh0aGlzLnRvRGF0ZSkudmFsKCkpO1xuICAgICAgJCh0aGlzLmZyb21EYXRlKS5kYXRlcGlja2VyKCdvcHRpb24nLCAnbWF4RGF0ZScsIG1heERhdGUpO1xuICAgIH0gY2F0Y2goZSkge31cbiAgfVxufVxuXG5tb2R1bGUuZXhwb3J0cyA9IFJhbmdlRGF0ZXBpY2tlcjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9yYW5nZS1kYXRlcGlja2VyLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBBd2VCb29raW5nID0gd2luZG93LlRoZUF3ZUJvb2tpbmc7XG5cbmNsYXNzIEluaXRTZWxlY3QyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5zZWFyY2hDdXN0b21lcigpO1xuICB9XG5cbiAgLy8gQWpheCBjdXN0b21lciBzZWFyY2ggYm94ZXNcbiAgc2VhcmNoQ3VzdG9tZXIoKSB7XG4gICAgJCgnOmlucHV0LmF3ZWJvb2tpbmctY3VzdG9tZXItc2VhcmNoLCBzZWxlY3RbbmFtZT1cImJvb2tpbmdfY3VzdG9tZXJcIl0nKS5maWx0ZXIoICc6bm90KC5lbmhhbmNlZCknICkuZWFjaCggZnVuY3Rpb24oKSB7XG4gICAgICB2YXIgc2VsZWN0Ml9hcmdzID0ge1xuICAgICAgICBhbGxvd0NsZWFyOiAgJCggdGhpcyApLmRhdGEoICdhbGxvd0NsZWFyJyApID8gdHJ1ZSA6IGZhbHNlLFxuICAgICAgICBwbGFjZWhvbGRlcjogJCggdGhpcyApLmRhdGEoICdwbGFjZWhvbGRlcicgKSA/ICQoIHRoaXMgKS5kYXRhKCAncGxhY2Vob2xkZXInICkgOiBcIlwiLFxuICAgICAgICBtaW5pbXVtSW5wdXRMZW5ndGg6ICQoIHRoaXMgKS5kYXRhKCAnbWluaW11bV9pbnB1dF9sZW5ndGgnICkgPyAkKCB0aGlzICkuZGF0YSggJ21pbmltdW1faW5wdXRfbGVuZ3RoJyApIDogJzEnLFxuICAgICAgICBlc2NhcGVNYXJrdXA6IGZ1bmN0aW9uKCBtICkge1xuICAgICAgICAgIHJldHVybiBtO1xuICAgICAgICB9LFxuICAgICAgICBhamF4OiB7XG4gICAgICAgICAgdXJsOiAgICAgICAgIEF3ZUJvb2tpbmcuYWpheF91cmwsXG4gICAgICAgICAgZGF0YVR5cGU6ICAgICdqc29uJyxcbiAgICAgICAgICBkZWxheTogICAgICAgMjUwLFxuICAgICAgICAgIGRhdGE6ICAgICAgICBmdW5jdGlvbiggcGFyYW1zICkge1xuICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgdGVybTogICAgIHBhcmFtcy50ZXJtLFxuICAgICAgICAgICAgICBhY3Rpb246ICAgJ2F3ZWJvb2tpbmdfanNvbl9zZWFyY2hfY3VzdG9tZXJzJyxcbiAgICAgICAgICAgICAgLy8gc2VjdXJpdHk6IHdjX2VuaGFuY2VkX3NlbGVjdF9wYXJhbXMuc2VhcmNoX2N1c3RvbWVyc19ub25jZSxcbiAgICAgICAgICAgICAgZXhjbHVkZTogICQoIHRoaXMgKS5kYXRhKCAnZXhjbHVkZScgKVxuICAgICAgICAgICAgfTtcbiAgICAgICAgICB9LFxuICAgICAgICAgIHByb2Nlc3NSZXN1bHRzOiBmdW5jdGlvbiggZGF0YSApIHtcbiAgICAgICAgICAgIHZhciB0ZXJtcyA9IFtdO1xuICAgICAgICAgICAgaWYgKCBkYXRhICkge1xuICAgICAgICAgICAgICAkLmVhY2goIGRhdGEsIGZ1bmN0aW9uKCBpZCwgdGV4dCApIHtcbiAgICAgICAgICAgICAgICB0ZXJtcy5wdXNoKHtcbiAgICAgICAgICAgICAgICAgIGlkOiBpZCxcbiAgICAgICAgICAgICAgICAgIHRleHQ6IHRleHRcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICByZXN1bHRzOiB0ZXJtc1xuICAgICAgICAgICAgfTtcbiAgICAgICAgICB9LFxuICAgICAgICAgIGNhY2hlOiB0cnVlXG4gICAgICAgIH1cbiAgICAgIH07XG5cbiAgICAgICQoIHRoaXMgKS5zZWxlY3QyKHNlbGVjdDJfYXJncykuYWRkQ2xhc3MoICdlbmhhbmNlZCcgKTtcbiAgICB9KTtcblxuICB9XG59XG5cbm1vZHVsZS5leHBvcnRzID0gbmV3IEluaXRTZWxlY3QyO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL2luaXQtc2VsZWN0Mi5qcyIsIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9hc3NldHMvc2Fzcy9hZG1pbi5zY3NzXG4vLyBtb2R1bGUgaWQgPSAxMFxuLy8gbW9kdWxlIGNodW5rcyA9IDAiXSwic291cmNlUm9vdCI6IiJ9