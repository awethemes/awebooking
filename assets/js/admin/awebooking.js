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
module.exports = __webpack_require__(9);


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
      var _this = this;

      var beforeShowCallback = function beforeShowCallback() {
        $('#ui-datepicker-div').addClass('cmb2-element');
      };

      $(this.fromDate).datepicker({
        dateFormat: DATE_FORMAT,
        beforeShow: beforeShowCallback,
        onClose: function onClose() {
          if ($(_this.fromDate).datepicker('getDate')) {
            $(_this.toDate).datepicker('show');
          }
        }
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

        var toDateVal = $(this.toDate).datepicker('getDate');
        if (!toDateVal) {
          // $(this.toDate).datepicker('setDate', minDate);
        }
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

// removed by extract-text-webpack-plugin

/***/ })
],[3]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvdXRpbHMuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2F3ZWJvb2tpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3BvcHVwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy90b2dnbGUtY2xhc3MuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3JhbmdlLWRhdGVwaWNrZXIuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3Nhc3MvYWRtaW4uc2Nzcz9kOWU2Il0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJqUXVlcnkiLCJVdGlscyIsImdldFNlbGVjdG9yRnJvbUVsZW1lbnQiLCJlbCIsInNlbGVjdG9yIiwiZ2V0QXR0cmlidXRlIiwiJHNlbGVjdG9yIiwibGVuZ3RoIiwiZXJyb3IiLCJtb2R1bGUiLCJleHBvcnRzIiwic2V0dGluZ3MiLCJfYXdlYm9va2luZ1NldHRpbmdzIiwiQXdlQm9va2luZyIsIl8iLCJleHRlbmQiLCJWdWUiLCJyZXF1aXJlIiwiUG9wdXAiLCJUb2dnbGVDbGFzcyIsIlJhbmdlRGF0ZXBpY2tlciIsImluaXQiLCJzZWxmIiwiZWFjaCIsImRhdGEiLCJ0cmFucyIsImNvbnRleHQiLCJzdHJpbmdzIiwiYWpheFN1Ym1pdCIsImZvcm0iLCJhY3Rpb24iLCJzZXJpYWxpemUiLCJoYXNoIiwiYWRkQ2xhc3MiLCJ3cCIsImFqYXgiLCJwb3N0IiwiYWx3YXlzIiwicmVtb3ZlQ2xhc3MiLCJUaGVBd2VCb29raW5nIiwidGFyZ2V0Iiwic2V0dXAiLCJvbiIsIm9wZW4iLCJiaW5kIiwiY2xvc2UiLCJlIiwicHJldmVudERlZmF1bHQiLCJkaWFsb2ciLCIkdGFyZ2V0IiwiX3RyaWdnZXJSZXNpemUiLCJteSIsImF0Iiwib2YiLCJtb2RhbCIsIndpZHRoIiwiaGVpZ2h0IiwiYXV0b09wZW4iLCJkcmFnZ2FibGUiLCJyZXNpemFibGUiLCJjbG9zZU9uRXNjYXBlIiwiZGlhbG9nQ2xhc3MiLCJwb3NpdGlvbiIsImNzcyIsIm92ZXJmbG93IiwiYmVmb3JlQ2xvc2UiLCJldmVudCIsInVpIiwiZGVib3VuY2UiLCJwYXJlbnQiLCJjaGlsZHJlbiIsInRvZ2dsZUNsYXNzIiwiZG9jdW1lbnQiLCJjb250YWlucyIsIkRBVEVfRk9STUFUIiwiZnJvbURhdGUiLCJ0b0RhdGUiLCJiZWZvcmVTaG93Q2FsbGJhY2siLCJkYXRlcGlja2VyIiwiZGF0ZUZvcm1hdCIsImJlZm9yZVNob3ciLCJvbkNsb3NlIiwiYXBwbHlGcm9tQ2hhbmdlIiwiYXBwbHlUb0NoYW5nZSIsIm1pbkRhdGUiLCJwYXJzZURhdGUiLCJ2YWwiLCJzZXREYXRlIiwiZ2V0RGF0ZSIsInRvRGF0ZVZhbCIsIm1heERhdGUiXSwibWFwcGluZ3MiOiI7Ozs7O0FBQUEsSUFBSUEsSUFBSUMsT0FBT0MsTUFBZjs7QUFFQSxJQUFNQyxRQUFRO0FBRVpDLHdCQUZZLGtDQUVXQyxFQUZYLEVBRWU7QUFDekIsUUFBSUMsV0FBV0QsR0FBR0UsWUFBSCxDQUFnQixhQUFoQixDQUFmOztBQUVBLFFBQUksQ0FBQ0QsUUFBRCxJQUFhQSxhQUFhLEdBQTlCLEVBQW1DO0FBQ2pDQSxpQkFBV0QsR0FBR0UsWUFBSCxDQUFnQixNQUFoQixLQUEyQixFQUF0QztBQUNEOztBQUVELFFBQUk7QUFDRixVQUFNQyxZQUFZUixFQUFFTSxRQUFGLENBQWxCO0FBQ0EsYUFBT0UsVUFBVUMsTUFBVixHQUFtQixDQUFuQixHQUF1QkgsUUFBdkIsR0FBa0MsSUFBekM7QUFDRCxLQUhELENBR0UsT0FBT0ksS0FBUCxFQUFjO0FBQ2QsYUFBTyxJQUFQO0FBQ0Q7QUFDRjtBQWZXLENBQWQ7O0FBbUJBQyxPQUFPQyxPQUFQLEdBQWlCVCxLQUFqQixDOzs7Ozs7Ozs7Ozs7Ozs7QUNyQkEsSUFBTUgsSUFBSUMsT0FBT0MsTUFBakI7QUFDQSxJQUFNVyxXQUFXWixP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Q0YsWUFBTUUsVUFBVXRGLEVBQUVnRixVQUFGLENBQWFPLFNBQWIsQ0FBdUJYLFdBQXZCLEVBQW9DNUUsRUFBRSxLQUFLNkUsUUFBUCxFQUFpQlcsR0FBakIsRUFBcEMsQ0FBaEI7QUFDQUYsZ0JBQVFHLE9BQVIsQ0FBZ0JILFFBQVFJLE9BQVIsS0FBb0IsQ0FBcEM7QUFDQTFGLFVBQUUsS0FBSzhFLE1BQVAsRUFBZUUsVUFBZixDQUEwQixRQUExQixFQUFvQyxTQUFwQyxFQUErQ00sT0FBL0M7O0FBRUEsWUFBTUssWUFBWTNGLEVBQUUsS0FBSzhFLE1BQVAsRUFBZUUsVUFBZixDQUEwQixTQUExQixDQUFsQjtBQUNBLFlBQUksQ0FBRVcsU0FBTixFQUFpQjtBQUNmO0FBQ0Q7QUFDRixPQVRELENBU0UsT0FBTTNDLENBQU4sRUFBUyxDQUFFO0FBQ2Q7OztvQ0FFZTtBQUNkLFVBQUk7QUFDRixZQUFNNEMsVUFBVTVGLEVBQUVnRixVQUFGLENBQWFPLFNBQWIsQ0FBdUJYLFdBQXZCLEVBQW9DNUUsRUFBRSxLQUFLOEUsTUFBUCxFQUFlVSxHQUFmLEVBQXBDLENBQWhCO0FBQ0F4RixVQUFFLEtBQUs2RSxRQUFQLEVBQWlCRyxVQUFqQixDQUE0QixRQUE1QixFQUFzQyxTQUF0QyxFQUFpRFksT0FBakQ7QUFDRCxPQUhELENBR0UsT0FBTTVDLENBQU4sRUFBUyxDQUFFO0FBQ2Q7Ozs7OztBQUdIckMsT0FBT0MsT0FBUCxHQUFpQlUsZUFBakIsQzs7Ozs7O0FDdkRBLHlDIiwiZmlsZSI6Ii9qcy9hZG1pbi9hd2Vib29raW5nLmpzIiwic291cmNlc0NvbnRlbnQiOlsidmFyICQgPSB3aW5kb3cualF1ZXJ5O1xuXG5jb25zdCBVdGlscyA9IHtcblxuICBnZXRTZWxlY3RvckZyb21FbGVtZW50KGVsKSB7XG4gICAgbGV0IHNlbGVjdG9yID0gZWwuZ2V0QXR0cmlidXRlKCdkYXRhLXRhcmdldCcpO1xuXG4gICAgaWYgKCFzZWxlY3RvciB8fCBzZWxlY3RvciA9PT0gJyMnKSB7XG4gICAgICBzZWxlY3RvciA9IGVsLmdldEF0dHJpYnV0ZSgnaHJlZicpIHx8ICcnO1xuICAgIH1cblxuICAgIHRyeSB7XG4gICAgICBjb25zdCAkc2VsZWN0b3IgPSAkKHNlbGVjdG9yKTtcbiAgICAgIHJldHVybiAkc2VsZWN0b3IubGVuZ3RoID4gMCA/IHNlbGVjdG9yIDogbnVsbDtcbiAgICB9IGNhdGNoIChlcnJvcikge1xuICAgICAgcmV0dXJuIG51bGw7XG4gICAgfVxuICB9LFxuXG59O1xuXG5tb2R1bGUuZXhwb3J0cyA9IFV0aWxzO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3V0aWxzLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBzZXR0aW5ncyA9IHdpbmRvdy5fYXdlYm9va2luZ1NldHRpbmdzIHx8IHt9O1xuXG5jb25zdCBBd2VCb29raW5nID0gXy5leHRlbmQoc2V0dGluZ3MsIHtcbiAgVnVlOiByZXF1aXJlKCd2dWUnKSxcbiAgUG9wdXA6IHJlcXVpcmUoJy4vdXRpbHMvcG9wdXAuanMnKSxcbiAgVG9nZ2xlQ2xhc3M6IHJlcXVpcmUoJy4vdXRpbHMvdG9nZ2xlLWNsYXNzLmpzJyksXG4gIFJhbmdlRGF0ZXBpY2tlcjogcmVxdWlyZSgnLi91dGlscy9yYW5nZS1kYXRlcGlja2VyLmpzJyksXG5cbiAgLyoqXG4gICAqIEluaXQgdGhlIEF3ZUJvb2tpbmdcbiAgICovXG4gIGluaXQoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAvLyBJbml0IHRoZSBwb3B1cCwgdXNlIGpxdWVyeS11aS1wb3B1cC5cbiAgICAkKCdbZGF0YS10b2dnbGU9XCJhd2Vib29raW5nLXBvcHVwXCJdJykuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICQodGhpcykuZGF0YSgnYXdlYm9va2luZy1wb3B1cCcsIG5ldyBzZWxmLlBvcHVwKHRoaXMpKTtcbiAgICB9KTtcblxuICAgICQoJ1tkYXRhLWluaXQ9XCJhd2Vib29raW5nLXRvZ2dsZVwiXScpLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAkKHRoaXMpLmRhdGEoJ2F3ZWJvb2tpbmctdG9nZ2xlJywgbmV3IHNlbGYuVG9nZ2xlQ2xhc3ModGhpcykpO1xuICAgIH0pO1xuICB9LFxuXG4gIC8qKlxuICAgKiBHZXQgYSB0cmFuc2xhdG9yIHN0cmluZ1xuICAgKi9cbiAgdHJhbnMoY29udGV4dCkge1xuICAgIHJldHVybiB0aGlzLnN0cmluZ3NbY29udGV4dF0gPyB0aGlzLnN0cmluZ3NbY29udGV4dF0gOiAnJztcbiAgfSxcblxuICAvKipcbiAgICogTWFrZSBmb3JtIGFqYXggcmVxdWVzdC5cbiAgICovXG4gIGFqYXhTdWJtaXQoZm9ybSwgYWN0aW9uKSB7XG4gICAgY29uc3Qgc2VyaWFsaXplID0gcmVxdWlyZSgnZm9ybS1zZXJpYWxpemUnKTtcbiAgICBjb25zdCBkYXRhID0gc2VyaWFsaXplKGZvcm0sIHsgaGFzaDogdHJ1ZSB9KTtcblxuICAgIC8vIEFkZCAuYWpheC1sb2FkaW5nIGNsYXNzIGluIHRvIHRoZSBmb3JtLlxuICAgICQoZm9ybSkuYWRkQ2xhc3MoJ2FqYXgtbG9hZGluZycpO1xuXG4gICAgcmV0dXJuIHdwLmFqYXgucG9zdChhY3Rpb24sIGRhdGEpXG4gICAgICAuYWx3YXlzKGZ1bmN0aW9uKCkge1xuICAgICAgICAkKGZvcm0pLnJlbW92ZUNsYXNzKCdhamF4LWxvYWRpbmcnKTtcbiAgICAgIH0pO1xuICB9LFxufSk7XG5cbiQoZnVuY3Rpb24oKSB7XG4gIEF3ZUJvb2tpbmcuaW5pdCgpO1xufSk7XG5cbndpbmRvdy5UaGVBd2VCb29raW5nID0gQXdlQm9va2luZztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi9hd2Vib29raW5nLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBVdGlscyA9IHJlcXVpcmUoJy4vdXRpbHMuanMnKTtcblxuY2xhc3MgUG9wdXAge1xuICAvKipcbiAgICogV3JhcHBlciB0aGUganF1ZXJ5LXVpLXBvcHVwLlxuICAgKi9cbiAgY29uc3RydWN0b3IoZWwpIHtcbiAgICB0aGlzLmVsID0gZWw7XG4gICAgdGhpcy50YXJnZXQgPSBVdGlscy5nZXRTZWxlY3RvckZyb21FbGVtZW50KGVsKTtcblxuICAgIGlmICh0aGlzLnRhcmdldCkge1xuICAgICAgUG9wdXAuc2V0dXAodGhpcy50YXJnZXQpO1xuXG4gICAgICAkKHRoaXMuZWwpLm9uKCdjbGljaycsIHRoaXMub3Blbi5iaW5kKHRoaXMpKTtcbiAgICAgICQodGhpcy50YXJnZXQpLm9uKCdjbGljaycsICdbZGF0YS1kaXNtaXNzPVwiYXdlYm9va2luZy1wb3B1cFwiXScsIHRoaXMuY2xvc2UuYmluZCh0aGlzKSk7XG4gICAgfVxuICB9XG5cbiAgb3BlbihlKSB7XG4gICAgZSAmJiBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgJCh0aGlzLnRhcmdldCkuZGlhbG9nKCdvcGVuJyk7XG4gIH1cblxuICBjbG9zZShlKSB7XG4gICAgZSAmJiBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgJCh0aGlzLnRhcmdldCkuZGlhbG9nKCdjbG9zZScpO1xuICB9XG5cbiAgc3RhdGljIHNldHVwKHRhcmdldCkge1xuICAgIGNvbnN0ICR0YXJnZXQgPSAkKHRhcmdldCk7XG4gICAgaWYgKCEgJHRhcmdldC5sZW5ndGgpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAoJHRhcmdldC5kaWFsb2coJ2luc3RhbmNlJykpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBsZXQgX3RyaWdnZXJSZXNpemUgPSBmdW5jdGlvbigpIHtcbiAgICAgIGlmICgkdGFyZ2V0LmRpYWxvZygnaXNPcGVuJykpIHtcbiAgICAgICAgJHRhcmdldC5kaWFsb2coJ29wdGlvbicsICdwb3NpdGlvbicsIHsgbXk6ICdjZW50ZXInLCBhdDogJ2NlbnRlciB0b3ArMjUlJywgb2Y6IHdpbmRvdyB9KTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICBsZXQgZGlhbG9nID0gJHRhcmdldC5kaWFsb2coe1xuICAgICAgbW9kYWw6IHRydWUsXG4gICAgICB3aWR0aDogJ2F1dG8nLFxuICAgICAgaGVpZ2h0OiAnYXV0bycsXG4gICAgICBhdXRvT3BlbjogZmFsc2UsXG4gICAgICBkcmFnZ2FibGU6IGZhbHNlLFxuICAgICAgcmVzaXphYmxlOiBmYWxzZSxcbiAgICAgIGNsb3NlT25Fc2NhcGU6IHRydWUsXG4gICAgICBkaWFsb2dDbGFzczogJ3dwLWRpYWxvZyBhd2Vib29raW5nLWRpYWxvZycsXG4gICAgICBwb3NpdGlvbjogeyBteTogJ2NlbnRlcicsIGF0OiAnY2VudGVyIHRvcCsyNSUnLCBvZjogd2luZG93IH0sXG4gICAgICBvcGVuOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICQoJ2JvZHknKS5jc3MoeyBvdmVyZmxvdzogJ2hpZGRlbicgfSk7XG4gICAgICB9LFxuICAgICAgYmVmb3JlQ2xvc2U6IGZ1bmN0aW9uKGV2ZW50LCB1aSkge1xuICAgICAgICAkKCdib2R5JykuY3NzKHsgb3ZlcmZsb3c6ICdpbmhlcml0JyB9KTtcbiAgICAgfVxuICAgIH0pO1xuXG4gICAgJCh3aW5kb3cpLm9uKCdyZXNpemUnLCBfLmRlYm91bmNlKF90cmlnZ2VyUmVzaXplLCAyNTApKTtcblxuICAgIHJldHVybiBkaWFsb2c7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBQb3B1cDtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9wb3B1cC5qcyIsImNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xuY29uc3QgVXRpbHMgPSByZXF1aXJlKCcuL3V0aWxzLmpzJyk7XG5cbmNsYXNzIFRvZ2dsZUNsYXNzIHtcblxuICBjb25zdHJ1Y3RvcihlbCkge1xuICAgIHRoaXMuZWwgPSBlbDtcbiAgICB0aGlzLnRhcmdldCA9IFV0aWxzLmdldFNlbGVjdG9yRnJvbUVsZW1lbnQoZWwpO1xuXG4gICAgaWYgKCF0aGlzLnRhcmdldCkge1xuICAgICAgdGhpcy50YXJnZXQgPSAkKGVsKS5wYXJlbnQoKS5jaGlsZHJlbignLmF3ZWJvb2tpbmctbWFpbi10b2dnbGUnKVswXTtcbiAgICB9XG5cbiAgICBpZiAodGhpcy50YXJnZXQpIHtcbiAgICAgICQodGhpcy5lbCkub24oJ2NsaWNrJywgdGhpcy50b2dnbGVDbGFzcy5iaW5kKHRoaXMpKTtcbiAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMucmVtb3ZlQ2xhc3MuYmluZCh0aGlzKSk7XG4gICAgfVxuICB9XG5cbiAgdG9nZ2xlQ2xhc3MoZSkge1xuICAgIGUgJiYgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICQodGhpcy50YXJnZXQpLnBhcmVudCgpLnRvZ2dsZUNsYXNzKCdhY3RpdmUnKTtcbiAgfVxuXG4gIHJlbW92ZUNsYXNzKGUpIHtcbiAgICBpZiAoZSAmJiAkLmNvbnRhaW5zKCQodGhpcy50YXJnZXQpLnBhcmVudCgpWzBdLCBlLnRhcmdldCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAkKHRoaXMudGFyZ2V0KS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBUb2dnbGVDbGFzcztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy90b2dnbGUtY2xhc3MuanMiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IERBVEVfRk9STUFUID0gJ3l5LW1tLWRkJztcblxuY2xhc3MgUmFuZ2VEYXRlcGlja2VyIHtcblxuICBjb25zdHJ1Y3Rvcihmcm9tRGF0ZSwgdG9EYXRlKSB7XG4gICAgdGhpcy50b0RhdGUgPSB0b0RhdGU7XG4gICAgdGhpcy5mcm9tRGF0ZSA9IGZyb21EYXRlO1xuICB9XG5cbiAgaW5pdCgpIHtcbiAgICBjb25zdCBiZWZvcmVTaG93Q2FsbGJhY2sgPSBmdW5jdGlvbigpIHtcbiAgICAgICQoJyN1aS1kYXRlcGlja2VyLWRpdicpLmFkZENsYXNzKCdjbWIyLWVsZW1lbnQnKTtcbiAgICB9O1xuXG4gICAgJCh0aGlzLmZyb21EYXRlKS5kYXRlcGlja2VyKHtcbiAgICAgIGRhdGVGb3JtYXQ6IERBVEVfRk9STUFULFxuICAgICAgYmVmb3JlU2hvdzogYmVmb3JlU2hvd0NhbGxiYWNrLFxuICAgICAgb25DbG9zZTogKCkgPT4ge1xuICAgICAgICBpZiAoJCh0aGlzLmZyb21EYXRlKS5kYXRlcGlja2VyKCdnZXREYXRlJykpIHtcbiAgICAgICAgICAkKHRoaXMudG9EYXRlKS5kYXRlcGlja2VyKCdzaG93Jyk7XG4gICAgICAgIH1cbiAgICAgIH0sXG4gICAgfSkub24oJ2NoYW5nZScsIHRoaXMuYXBwbHlGcm9tQ2hhbmdlLmJpbmQodGhpcykpO1xuXG4gICAgJCh0aGlzLnRvRGF0ZSkuZGF0ZXBpY2tlcih7XG4gICAgICBkYXRlRm9ybWF0OiBEQVRFX0ZPUk1BVCxcbiAgICAgIGJlZm9yZVNob3c6IGJlZm9yZVNob3dDYWxsYmFjayxcbiAgICB9KS5vbignY2hhbmdlJywgdGhpcy5hcHBseVRvQ2hhbmdlLmJpbmQodGhpcykpO1xuXG4gICAgdGhpcy5hcHBseVRvQ2hhbmdlKCk7XG4gICAgdGhpcy5hcHBseUZyb21DaGFuZ2UoKTtcbiAgfVxuXG4gIGFwcGx5RnJvbUNoYW5nZSgpIHtcbiAgICB0cnkge1xuICAgICAgY29uc3QgbWluRGF0ZSA9ICQuZGF0ZXBpY2tlci5wYXJzZURhdGUoREFURV9GT1JNQVQsICQodGhpcy5mcm9tRGF0ZSkudmFsKCkpO1xuICAgICAgbWluRGF0ZS5zZXREYXRlKG1pbkRhdGUuZ2V0RGF0ZSgpICsgMSk7XG4gICAgICAkKHRoaXMudG9EYXRlKS5kYXRlcGlja2VyKCdvcHRpb24nLCAnbWluRGF0ZScsIG1pbkRhdGUpO1xuXG4gICAgICBjb25zdCB0b0RhdGVWYWwgPSAkKHRoaXMudG9EYXRlKS5kYXRlcGlja2VyKCdnZXREYXRlJyk7XG4gICAgICBpZiAoISB0b0RhdGVWYWwpIHtcbiAgICAgICAgLy8gJCh0aGlzLnRvRGF0ZSkuZGF0ZXBpY2tlcignc2V0RGF0ZScsIG1pbkRhdGUpO1xuICAgICAgfVxuICAgIH0gY2F0Y2goZSkge31cbiAgfVxuXG4gIGFwcGx5VG9DaGFuZ2UoKSB7XG4gICAgdHJ5IHtcbiAgICAgIGNvbnN0IG1heERhdGUgPSAkLmRhdGVwaWNrZXIucGFyc2VEYXRlKERBVEVfRk9STUFULCAkKHRoaXMudG9EYXRlKS52YWwoKSk7XG4gICAgICAkKHRoaXMuZnJvbURhdGUpLmRhdGVwaWNrZXIoJ29wdGlvbicsICdtYXhEYXRlJywgbWF4RGF0ZSk7XG4gICAgfSBjYXRjaChlKSB7fVxuICB9XG59XG5cbm1vZHVsZS5leHBvcnRzID0gUmFuZ2VEYXRlcGlja2VyO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3JhbmdlLWRhdGVwaWNrZXIuanMiLCIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vYXNzZXRzL3Nhc3MvYWRtaW4uc2Nzc1xuLy8gbW9kdWxlIGlkID0gOVxuLy8gbW9kdWxlIGNodW5rcyA9IDAiXSwic291cmNlUm9vdCI6IiJ9