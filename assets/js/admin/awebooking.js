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

// removed by extract-text-webpack-plugin

/***/ })
],[3]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vdXRpbHMvdXRpbHMuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL2F3ZWJvb2tpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3BvcHVwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy90b2dnbGUtY2xhc3MuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3JhbmdlLWRhdGVwaWNrZXIuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3Nhc3MvYWRtaW4uc2Nzcz9kOWU2Il0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJqUXVlcnkiLCJVdGlscyIsImdldFNlbGVjdG9yRnJvbUVsZW1lbnQiLCJlbCIsInNlbGVjdG9yIiwiZ2V0QXR0cmlidXRlIiwiJHNlbGVjdG9yIiwibGVuZ3RoIiwiZXJyb3IiLCJtb2R1bGUiLCJleHBvcnRzIiwic2V0dGluZ3MiLCJfYXdlYm9va2luZ1NldHRpbmdzIiwiQXdlQm9va2luZyIsIl8iLCJleHRlbmQiLCJWdWUiLCJyZXF1aXJlIiwiUG9wdXAiLCJUb2dnbGVDbGFzcyIsIlJhbmdlRGF0ZXBpY2tlciIsImluaXQiLCJzZWxmIiwiZWFjaCIsImRhdGEiLCJ0cmFucyIsImNvbnRleHQiLCJzdHJpbmdzIiwiYWpheFN1Ym1pdCIsImZvcm0iLCJhY3Rpb24iLCJzZXJpYWxpemUiLCJoYXNoIiwiYWRkQ2xhc3MiLCJ3cCIsImFqYXgiLCJwb3N0IiwiYWx3YXlzIiwicmVtb3ZlQ2xhc3MiLCJUaGVBd2VCb29raW5nIiwidGFyZ2V0Iiwic2V0dXAiLCJvbiIsIm9wZW4iLCJiaW5kIiwiY2xvc2UiLCJlIiwicHJldmVudERlZmF1bHQiLCJkaWFsb2ciLCIkdGFyZ2V0IiwiX3RyaWdnZXJSZXNpemUiLCJteSIsImF0Iiwib2YiLCJtb2RhbCIsIndpZHRoIiwiaGVpZ2h0IiwiYXV0b09wZW4iLCJkcmFnZ2FibGUiLCJyZXNpemFibGUiLCJjbG9zZU9uRXNjYXBlIiwiZGlhbG9nQ2xhc3MiLCJwb3NpdGlvbiIsImNzcyIsIm92ZXJmbG93IiwiYmVmb3JlQ2xvc2UiLCJldmVudCIsInVpIiwiZGVib3VuY2UiLCJwYXJlbnQiLCJjaGlsZHJlbiIsInRvZ2dsZUNsYXNzIiwiZG9jdW1lbnQiLCJjb250YWlucyIsIkRBVEVfRk9STUFUIiwiZnJvbURhdGUiLCJ0b0RhdGUiLCJiZWZvcmVTaG93Q2FsbGJhY2siLCJkYXRlcGlja2VyIiwiZGF0ZUZvcm1hdCIsImJlZm9yZVNob3ciLCJhcHBseUZyb21DaGFuZ2UiLCJhcHBseVRvQ2hhbmdlIiwibWluRGF0ZSIsInBhcnNlRGF0ZSIsInZhbCIsInNldERhdGUiLCJnZXREYXRlIiwibWF4RGF0ZSJdLCJtYXBwaW5ncyI6Ijs7Ozs7QUFBQSxJQUFJQSxJQUFJQyxPQUFPQyxNQUFmOztBQUVBLElBQU1DLFFBQVE7QUFFWkMsd0JBRlksa0NBRVdDLEVBRlgsRUFFZTtBQUN6QixRQUFJQyxXQUFXRCxHQUFHRSxZQUFILENBQWdCLGFBQWhCLENBQWY7O0FBRUEsUUFBSSxDQUFDRCxRQUFELElBQWFBLGFBQWEsR0FBOUIsRUFBbUM7QUFDakNBLGlCQUFXRCxHQUFHRSxZQUFILENBQWdCLE1BQWhCLEtBQTJCLEVBQXRDO0FBQ0Q7O0FBRUQsUUFBSTtBQUNGLFVBQU1DLFlBQVlSLEVBQUVNLFFBQUYsQ0FBbEI7QUFDQSxhQUFPRSxVQUFVQyxNQUFWLEdBQW1CLENBQW5CLEdBQXVCSCxRQUF2QixHQUFrQyxJQUF6QztBQUNELEtBSEQsQ0FHRSxPQUFPSSxLQUFQLEVBQWM7QUFDZCxhQUFPLElBQVA7QUFDRDtBQUNGO0FBZlcsQ0FBZDs7QUFtQkFDLE9BQU9DLE9BQVAsR0FBaUJULEtBQWpCLEM7Ozs7Ozs7Ozs7Ozs7OztBQ3JCQSxJQUFNSCxJQUFJQyxPQUFPQyxNQUFqQjtBQUNBLElBQU1XLFdBQVdaLE9BQU9hLG1CQUFQLElBQThCLEVBQS9DOztBQUVBLElBQU1DLGFBQWFDLEVBQUVDLE1BQUYsQ0FBU0osUUFBVCxFQUFtQjtBQUNwQ0ssT0FBSyxtQkFBQUMsQ0FBUSxDQUFSLENBRCtCO0FBRXBDQyxTQUFPLG1CQUFBRCxDQUFRLENBQVIsQ0FGNkI7QUFHcENFLGVBQWEsbUJBQUFGLENBQVEsQ0FBUixDQUh1QjtBQUlwQ0csbUJBQWlCLG1CQUFBSCxDQUFRLENBQVIsQ0FKbUI7O0FBTXBDOzs7QUFHQUksTUFUb0Msa0JBUzdCO0FBQ0wsUUFBTUMsT0FBTyxJQUFiOztBQUVBO0FBQ0F4QixNQUFFLGtDQUFGLEVBQXNDeUIsSUFBdEMsQ0FBMkMsWUFBVztBQUNwRHpCLFFBQUUsSUFBRixFQUFRMEIsSUFBUixDQUFhLGtCQUFiLEVBQWlDLElBQUlGLEtBQUtKLEtBQVQsQ0FBZSxJQUFmLENBQWpDO0FBQ0QsS0FGRDs7QUFJQXBCLE1BQUUsaUNBQUYsRUFBcUN5QixJQUFyQyxDQUEwQyxZQUFXO0FBQ25EekIsUUFBRSxJQUFGLEVBQVEwQixJQUFSLENBQWEsbUJBQWIsRUFBa0MsSUFBSUYsS0FBS0gsV0FBVCxDQUFxQixJQUFyQixDQUFsQztBQUNELEtBRkQ7QUFHRCxHQXBCbUM7OztBQXNCcEM7OztBQUdBTSxPQXpCb0MsaUJBeUI5QkMsT0F6QjhCLEVBeUJyQjtBQUNiLFdBQU8sS0FBS0MsT0FBTCxDQUFhRCxPQUFiLElBQXdCLEtBQUtDLE9BQUwsQ0FBYUQsT0FBYixDQUF4QixHQUFnRCxFQUF2RDtBQUNELEdBM0JtQzs7O0FBNkJwQzs7O0FBR0FFLFlBaENvQyxzQkFnQ3pCQyxJQWhDeUIsRUFnQ25CQyxNQWhDbUIsRUFnQ1g7QUFDdkIsUUFBTUMsWUFBWSxtQkFBQWQsQ0FBUSxDQUFSLENBQWxCO0FBQ0EsUUFBTU8sT0FBT08sVUFBVUYsSUFBVixFQUFnQixFQUFFRyxNQUFNLElBQVIsRUFBaEIsQ0FBYjs7QUFFQTtBQUNBbEMsTUFBRStCLElBQUYsRUFBUUksUUFBUixDQUFpQixjQUFqQjs7QUFFQSxXQUFPQyxHQUFHQyxJQUFILENBQVFDLElBQVIsQ0FBYU4sTUFBYixFQUFxQk4sSUFBckIsRUFDSmEsTUFESSxDQUNHLFlBQVc7QUFDakJ2QyxRQUFFK0IsSUFBRixFQUFRUyxXQUFSLENBQW9CLGNBQXBCO0FBQ0QsS0FISSxDQUFQO0FBSUQ7QUEzQ21DLENBQW5CLENBQW5COztBQThDQXhDLEVBQUUsWUFBVztBQUNYZSxhQUFXUSxJQUFYO0FBQ0QsQ0FGRDs7QUFJQXRCLE9BQU93QyxhQUFQLEdBQXVCMUIsVUFBdkIsQzs7Ozs7Ozs7Ozs7QUNyREEsSUFBTWYsSUFBSUMsT0FBT0MsTUFBakI7QUFDQSxJQUFNQyxRQUFRLG1CQUFBZ0IsQ0FBUSxDQUFSLENBQWQ7O0lBRU1DLEs7QUFDSjs7O0FBR0EsaUJBQVlmLEVBQVosRUFBZ0I7QUFBQTs7QUFDZCxTQUFLQSxFQUFMLEdBQVVBLEVBQVY7QUFDQSxTQUFLcUMsTUFBTCxHQUFjdkMsTUFBTUMsc0JBQU4sQ0FBNkJDLEVBQTdCLENBQWQ7O0FBRUEsUUFBSSxLQUFLcUMsTUFBVCxFQUFpQjtBQUNmdEIsWUFBTXVCLEtBQU4sQ0FBWSxLQUFLRCxNQUFqQjs7QUFFQTFDLFFBQUUsS0FBS0ssRUFBUCxFQUFXdUMsRUFBWCxDQUFjLE9BQWQsRUFBdUIsS0FBS0MsSUFBTCxDQUFVQyxJQUFWLENBQWUsSUFBZixDQUF2QjtBQUNBOUMsUUFBRSxLQUFLMEMsTUFBUCxFQUFlRSxFQUFmLENBQWtCLE9BQWxCLEVBQTJCLG1DQUEzQixFQUFnRSxLQUFLRyxLQUFMLENBQVdELElBQVgsQ0FBZ0IsSUFBaEIsQ0FBaEU7QUFDRDtBQUNGOzs7O3lCQUVJRSxDLEVBQUc7QUFDTkEsV0FBS0EsRUFBRUMsY0FBRixFQUFMO0FBQ0FqRCxRQUFFLEtBQUswQyxNQUFQLEVBQWVRLE1BQWYsQ0FBc0IsTUFBdEI7QUFDRDs7OzBCQUVLRixDLEVBQUc7QUFDUEEsV0FBS0EsRUFBRUMsY0FBRixFQUFMO0FBQ0FqRCxRQUFFLEtBQUswQyxNQUFQLEVBQWVRLE1BQWYsQ0FBc0IsT0FBdEI7QUFDRDs7OzBCQUVZUixNLEVBQVE7QUFDbkIsVUFBTVMsVUFBVW5ELEVBQUUwQyxNQUFGLENBQWhCO0FBQ0EsVUFBSSxDQUFFUyxRQUFRMUMsTUFBZCxFQUFzQjtBQUNwQjtBQUNEOztBQUVELFVBQUkwQyxRQUFRRCxNQUFSLENBQWUsVUFBZixDQUFKLEVBQWdDO0FBQzlCO0FBQ0Q7O0FBRUQsVUFBSUUsaUJBQWlCLFNBQWpCQSxjQUFpQixHQUFXO0FBQzlCLFlBQUlELFFBQVFELE1BQVIsQ0FBZSxRQUFmLENBQUosRUFBOEI7QUFDNUJDLGtCQUFRRCxNQUFSLENBQWUsUUFBZixFQUF5QixVQUF6QixFQUFxQyxFQUFFRyxJQUFJLFFBQU4sRUFBZ0JDLElBQUksZ0JBQXBCLEVBQXNDQyxJQUFJdEQsTUFBMUMsRUFBckM7QUFDRDtBQUNGLE9BSkQ7O0FBTUEsVUFBSWlELFNBQVNDLFFBQVFELE1BQVIsQ0FBZTtBQUMxQk0sZUFBTyxJQURtQjtBQUUxQkMsZUFBTyxNQUZtQjtBQUcxQkMsZ0JBQVEsTUFIa0I7QUFJMUJDLGtCQUFVLEtBSmdCO0FBSzFCQyxtQkFBVyxLQUxlO0FBTTFCQyxtQkFBVyxLQU5lO0FBTzFCQyx1QkFBZSxJQVBXO0FBUTFCQyxxQkFBYSw2QkFSYTtBQVMxQkMsa0JBQVUsRUFBRVgsSUFBSSxRQUFOLEVBQWdCQyxJQUFJLGdCQUFwQixFQUFzQ0MsSUFBSXRELE1BQTFDLEVBVGdCO0FBVTFCNEMsY0FBTSxnQkFBWTtBQUNoQjdDLFlBQUUsTUFBRixFQUFVaUUsR0FBVixDQUFjLEVBQUVDLFVBQVUsUUFBWixFQUFkO0FBQ0QsU0FaeUI7QUFhMUJDLHFCQUFhLHFCQUFTQyxLQUFULEVBQWdCQyxFQUFoQixFQUFvQjtBQUMvQnJFLFlBQUUsTUFBRixFQUFVaUUsR0FBVixDQUFjLEVBQUVDLFVBQVUsU0FBWixFQUFkO0FBQ0Y7QUFmMEIsT0FBZixDQUFiOztBQWtCQWxFLFFBQUVDLE1BQUYsRUFBVTJDLEVBQVYsQ0FBYSxRQUFiLEVBQXVCNUIsRUFBRXNELFFBQUYsQ0FBV2xCLGNBQVgsRUFBMkIsR0FBM0IsQ0FBdkI7O0FBRUEsYUFBT0YsTUFBUDtBQUNEOzs7Ozs7QUFHSHZDLE9BQU9DLE9BQVAsR0FBaUJRLEtBQWpCLEM7Ozs7Ozs7Ozs7QUNyRUEsSUFBTXBCLElBQUlDLE9BQU9DLE1BQWpCO0FBQ0EsSUFBTUMsUUFBUSxtQkFBQWdCLENBQVEsQ0FBUixDQUFkOztJQUVNRSxXO0FBRUosdUJBQVloQixFQUFaLEVBQWdCO0FBQUE7O0FBQ2QsU0FBS0EsRUFBTCxHQUFVQSxFQUFWO0FBQ0EsU0FBS3FDLE1BQUwsR0FBY3ZDLE1BQU1DLHNCQUFOLENBQTZCQyxFQUE3QixDQUFkOztBQUVBLFFBQUksQ0FBQyxLQUFLcUMsTUFBVixFQUFrQjtBQUNoQixXQUFLQSxNQUFMLEdBQWMxQyxFQUFFSyxFQUFGLEVBQU1rRSxNQUFOLEdBQWVDLFFBQWYsQ0FBd0IseUJBQXhCLEVBQW1ELENBQW5ELENBQWQ7QUFDRDs7QUFFRCxRQUFJLEtBQUs5QixNQUFULEVBQWlCO0FBQ2YxQyxRQUFFLEtBQUtLLEVBQVAsRUFBV3VDLEVBQVgsQ0FBYyxPQUFkLEVBQXVCLEtBQUs2QixXQUFMLENBQWlCM0IsSUFBakIsQ0FBc0IsSUFBdEIsQ0FBdkI7QUFDQTlDLFFBQUUwRSxRQUFGLEVBQVk5QixFQUFaLENBQWUsT0FBZixFQUF3QixLQUFLSixXQUFMLENBQWlCTSxJQUFqQixDQUFzQixJQUF0QixDQUF4QjtBQUNEO0FBQ0Y7Ozs7Z0NBRVdFLEMsRUFBRztBQUNiQSxXQUFLQSxFQUFFQyxjQUFGLEVBQUw7QUFDQWpELFFBQUUsS0FBSzBDLE1BQVAsRUFBZTZCLE1BQWYsR0FBd0JFLFdBQXhCLENBQW9DLFFBQXBDO0FBQ0Q7OztnQ0FFV3pCLEMsRUFBRztBQUNiLFVBQUlBLEtBQUtoRCxFQUFFMkUsUUFBRixDQUFXM0UsRUFBRSxLQUFLMEMsTUFBUCxFQUFlNkIsTUFBZixHQUF3QixDQUF4QixDQUFYLEVBQXVDdkIsRUFBRU4sTUFBekMsQ0FBVCxFQUEyRDtBQUN6RDtBQUNEOztBQUVEMUMsUUFBRSxLQUFLMEMsTUFBUCxFQUFlNkIsTUFBZixHQUF3Qi9CLFdBQXhCLENBQW9DLFFBQXBDO0FBQ0Q7Ozs7OztBQUdIN0IsT0FBT0MsT0FBUCxHQUFpQlMsV0FBakIsQzs7Ozs7Ozs7OztBQ2pDQSxJQUFNckIsSUFBSUMsT0FBT0MsTUFBakI7QUFDQSxJQUFNMEUsY0FBYyxVQUFwQjs7SUFFTXRELGU7QUFFSiwyQkFBWXVELFFBQVosRUFBc0JDLE1BQXRCLEVBQThCO0FBQUE7O0FBQzVCLFNBQUtBLE1BQUwsR0FBY0EsTUFBZDtBQUNBLFNBQUtELFFBQUwsR0FBZ0JBLFFBQWhCO0FBQ0Q7Ozs7MkJBRU07QUFDTCxVQUFNRSxxQkFBcUIsU0FBckJBLGtCQUFxQixHQUFXO0FBQ3BDL0UsVUFBRSxvQkFBRixFQUF3Qm1DLFFBQXhCLENBQWlDLGNBQWpDO0FBQ0QsT0FGRDs7QUFJQW5DLFFBQUUsS0FBSzZFLFFBQVAsRUFBaUJHLFVBQWpCLENBQTRCO0FBQzFCQyxvQkFBWUwsV0FEYztBQUUxQk0sb0JBQVlIO0FBRmMsT0FBNUIsRUFHR25DLEVBSEgsQ0FHTSxRQUhOLEVBR2dCLEtBQUt1QyxlQUFMLENBQXFCckMsSUFBckIsQ0FBMEIsSUFBMUIsQ0FIaEI7O0FBS0E5QyxRQUFFLEtBQUs4RSxNQUFQLEVBQWVFLFVBQWYsQ0FBMEI7QUFDeEJDLG9CQUFZTCxXQURZO0FBRXhCTSxvQkFBWUg7QUFGWSxPQUExQixFQUdHbkMsRUFISCxDQUdNLFFBSE4sRUFHZ0IsS0FBS3dDLGFBQUwsQ0FBbUJ0QyxJQUFuQixDQUF3QixJQUF4QixDQUhoQjs7QUFLQSxXQUFLc0MsYUFBTDtBQUNBLFdBQUtELGVBQUw7QUFDRDs7O3NDQUVpQjtBQUNoQixVQUFJO0FBQ0YsWUFBTUUsVUFBVXJGLEVBQUVnRixVQUFGLENBQWFNLFNBQWIsQ0FBdUJWLFdBQXZCLEVBQW9DNUUsRUFBRSxLQUFLNkUsUUFBUCxFQUFpQlUsR0FBakIsRUFBcEMsQ0FBaEI7QUFDQUYsZ0JBQVFHLE9BQVIsQ0FBZ0JILFFBQVFJLE9BQVIsS0FBb0IsQ0FBcEM7QUFDQXpGLFVBQUUsS0FBSzhFLE1BQVAsRUFBZUUsVUFBZixDQUEwQixRQUExQixFQUFvQyxTQUFwQyxFQUErQ0ssT0FBL0M7QUFDRCxPQUpELENBSUUsT0FBTXJDLENBQU4sRUFBUyxDQUFFO0FBQ2Q7OztvQ0FFZTtBQUNkLFVBQUk7QUFDRixZQUFNMEMsVUFBVTFGLEVBQUVnRixVQUFGLENBQWFNLFNBQWIsQ0FBdUJWLFdBQXZCLEVBQW9DNUUsRUFBRSxLQUFLOEUsTUFBUCxFQUFlUyxHQUFmLEVBQXBDLENBQWhCO0FBQ0F2RixVQUFFLEtBQUs2RSxRQUFQLEVBQWlCRyxVQUFqQixDQUE0QixRQUE1QixFQUFzQyxTQUF0QyxFQUFpRFUsT0FBakQ7QUFDRCxPQUhELENBR0UsT0FBTTFDLENBQU4sRUFBUyxDQUFFO0FBQ2Q7Ozs7OztBQUdIckMsT0FBT0MsT0FBUCxHQUFpQlUsZUFBakIsQzs7Ozs7O0FDN0NBLHlDIiwiZmlsZSI6Ii9qcy9hZG1pbi9hd2Vib29raW5nLmpzIiwic291cmNlc0NvbnRlbnQiOlsidmFyICQgPSB3aW5kb3cualF1ZXJ5O1xuXG5jb25zdCBVdGlscyA9IHtcblxuICBnZXRTZWxlY3RvckZyb21FbGVtZW50KGVsKSB7XG4gICAgbGV0IHNlbGVjdG9yID0gZWwuZ2V0QXR0cmlidXRlKCdkYXRhLXRhcmdldCcpO1xuXG4gICAgaWYgKCFzZWxlY3RvciB8fCBzZWxlY3RvciA9PT0gJyMnKSB7XG4gICAgICBzZWxlY3RvciA9IGVsLmdldEF0dHJpYnV0ZSgnaHJlZicpIHx8ICcnO1xuICAgIH1cblxuICAgIHRyeSB7XG4gICAgICBjb25zdCAkc2VsZWN0b3IgPSAkKHNlbGVjdG9yKTtcbiAgICAgIHJldHVybiAkc2VsZWN0b3IubGVuZ3RoID4gMCA/IHNlbGVjdG9yIDogbnVsbDtcbiAgICB9IGNhdGNoIChlcnJvcikge1xuICAgICAgcmV0dXJuIG51bGw7XG4gICAgfVxuICB9LFxuXG59O1xuXG5tb2R1bGUuZXhwb3J0cyA9IFV0aWxzO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL3V0aWxzL3V0aWxzLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBzZXR0aW5ncyA9IHdpbmRvdy5fYXdlYm9va2luZ1NldHRpbmdzIHx8IHt9O1xuXG5jb25zdCBBd2VCb29raW5nID0gXy5leHRlbmQoc2V0dGluZ3MsIHtcbiAgVnVlOiByZXF1aXJlKCd2dWUnKSxcbiAgUG9wdXA6IHJlcXVpcmUoJy4vdXRpbHMvcG9wdXAuanMnKSxcbiAgVG9nZ2xlQ2xhc3M6IHJlcXVpcmUoJy4vdXRpbHMvdG9nZ2xlLWNsYXNzLmpzJyksXG4gIFJhbmdlRGF0ZXBpY2tlcjogcmVxdWlyZSgnLi91dGlscy9yYW5nZS1kYXRlcGlja2VyLmpzJyksXG5cbiAgLyoqXG4gICAqIEluaXQgdGhlIEF3ZUJvb2tpbmdcbiAgICovXG4gIGluaXQoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAvLyBJbml0IHRoZSBwb3B1cCwgdXNlIGpxdWVyeS11aS1wb3B1cC5cbiAgICAkKCdbZGF0YS10b2dnbGU9XCJhd2Vib29raW5nLXBvcHVwXCJdJykuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICQodGhpcykuZGF0YSgnYXdlYm9va2luZy1wb3B1cCcsIG5ldyBzZWxmLlBvcHVwKHRoaXMpKTtcbiAgICB9KTtcblxuICAgICQoJ1tkYXRhLWluaXQ9XCJhd2Vib29raW5nLXRvZ2dsZVwiXScpLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAkKHRoaXMpLmRhdGEoJ2F3ZWJvb2tpbmctdG9nZ2xlJywgbmV3IHNlbGYuVG9nZ2xlQ2xhc3ModGhpcykpO1xuICAgIH0pO1xuICB9LFxuXG4gIC8qKlxuICAgKiBHZXQgYSB0cmFuc2xhdG9yIHN0cmluZ1xuICAgKi9cbiAgdHJhbnMoY29udGV4dCkge1xuICAgIHJldHVybiB0aGlzLnN0cmluZ3NbY29udGV4dF0gPyB0aGlzLnN0cmluZ3NbY29udGV4dF0gOiAnJztcbiAgfSxcblxuICAvKipcbiAgICogTWFrZSBmb3JtIGFqYXggcmVxdWVzdC5cbiAgICovXG4gIGFqYXhTdWJtaXQoZm9ybSwgYWN0aW9uKSB7XG4gICAgY29uc3Qgc2VyaWFsaXplID0gcmVxdWlyZSgnZm9ybS1zZXJpYWxpemUnKTtcbiAgICBjb25zdCBkYXRhID0gc2VyaWFsaXplKGZvcm0sIHsgaGFzaDogdHJ1ZSB9KTtcblxuICAgIC8vIEFkZCAuYWpheC1sb2FkaW5nIGNsYXNzIGluIHRvIHRoZSBmb3JtLlxuICAgICQoZm9ybSkuYWRkQ2xhc3MoJ2FqYXgtbG9hZGluZycpO1xuXG4gICAgcmV0dXJuIHdwLmFqYXgucG9zdChhY3Rpb24sIGRhdGEpXG4gICAgICAuYWx3YXlzKGZ1bmN0aW9uKCkge1xuICAgICAgICAkKGZvcm0pLnJlbW92ZUNsYXNzKCdhamF4LWxvYWRpbmcnKTtcbiAgICAgIH0pO1xuICB9LFxufSk7XG5cbiQoZnVuY3Rpb24oKSB7XG4gIEF3ZUJvb2tpbmcuaW5pdCgpO1xufSk7XG5cbndpbmRvdy5UaGVBd2VCb29raW5nID0gQXdlQm9va2luZztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi9hd2Vib29raW5nLmpzIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBVdGlscyA9IHJlcXVpcmUoJy4vdXRpbHMuanMnKTtcblxuY2xhc3MgUG9wdXAge1xuICAvKipcbiAgICogV3JhcHBlciB0aGUganF1ZXJ5LXVpLXBvcHVwLlxuICAgKi9cbiAgY29uc3RydWN0b3IoZWwpIHtcbiAgICB0aGlzLmVsID0gZWw7XG4gICAgdGhpcy50YXJnZXQgPSBVdGlscy5nZXRTZWxlY3RvckZyb21FbGVtZW50KGVsKTtcblxuICAgIGlmICh0aGlzLnRhcmdldCkge1xuICAgICAgUG9wdXAuc2V0dXAodGhpcy50YXJnZXQpO1xuXG4gICAgICAkKHRoaXMuZWwpLm9uKCdjbGljaycsIHRoaXMub3Blbi5iaW5kKHRoaXMpKTtcbiAgICAgICQodGhpcy50YXJnZXQpLm9uKCdjbGljaycsICdbZGF0YS1kaXNtaXNzPVwiYXdlYm9va2luZy1wb3B1cFwiXScsIHRoaXMuY2xvc2UuYmluZCh0aGlzKSk7XG4gICAgfVxuICB9XG5cbiAgb3BlbihlKSB7XG4gICAgZSAmJiBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgJCh0aGlzLnRhcmdldCkuZGlhbG9nKCdvcGVuJyk7XG4gIH1cblxuICBjbG9zZShlKSB7XG4gICAgZSAmJiBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgJCh0aGlzLnRhcmdldCkuZGlhbG9nKCdjbG9zZScpO1xuICB9XG5cbiAgc3RhdGljIHNldHVwKHRhcmdldCkge1xuICAgIGNvbnN0ICR0YXJnZXQgPSAkKHRhcmdldCk7XG4gICAgaWYgKCEgJHRhcmdldC5sZW5ndGgpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAoJHRhcmdldC5kaWFsb2coJ2luc3RhbmNlJykpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBsZXQgX3RyaWdnZXJSZXNpemUgPSBmdW5jdGlvbigpIHtcbiAgICAgIGlmICgkdGFyZ2V0LmRpYWxvZygnaXNPcGVuJykpIHtcbiAgICAgICAgJHRhcmdldC5kaWFsb2coJ29wdGlvbicsICdwb3NpdGlvbicsIHsgbXk6ICdjZW50ZXInLCBhdDogJ2NlbnRlciB0b3ArMjUlJywgb2Y6IHdpbmRvdyB9KTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICBsZXQgZGlhbG9nID0gJHRhcmdldC5kaWFsb2coe1xuICAgICAgbW9kYWw6IHRydWUsXG4gICAgICB3aWR0aDogJ2F1dG8nLFxuICAgICAgaGVpZ2h0OiAnYXV0bycsXG4gICAgICBhdXRvT3BlbjogZmFsc2UsXG4gICAgICBkcmFnZ2FibGU6IGZhbHNlLFxuICAgICAgcmVzaXphYmxlOiBmYWxzZSxcbiAgICAgIGNsb3NlT25Fc2NhcGU6IHRydWUsXG4gICAgICBkaWFsb2dDbGFzczogJ3dwLWRpYWxvZyBhd2Vib29raW5nLWRpYWxvZycsXG4gICAgICBwb3NpdGlvbjogeyBteTogJ2NlbnRlcicsIGF0OiAnY2VudGVyIHRvcCsyNSUnLCBvZjogd2luZG93IH0sXG4gICAgICBvcGVuOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICQoJ2JvZHknKS5jc3MoeyBvdmVyZmxvdzogJ2hpZGRlbicgfSk7XG4gICAgICB9LFxuICAgICAgYmVmb3JlQ2xvc2U6IGZ1bmN0aW9uKGV2ZW50LCB1aSkge1xuICAgICAgICAkKCdib2R5JykuY3NzKHsgb3ZlcmZsb3c6ICdpbmhlcml0JyB9KTtcbiAgICAgfVxuICAgIH0pO1xuXG4gICAgJCh3aW5kb3cpLm9uKCdyZXNpemUnLCBfLmRlYm91bmNlKF90cmlnZ2VyUmVzaXplLCAyNTApKTtcblxuICAgIHJldHVybiBkaWFsb2c7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBQb3B1cDtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9wb3B1cC5qcyIsImNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xuY29uc3QgVXRpbHMgPSByZXF1aXJlKCcuL3V0aWxzLmpzJyk7XG5cbmNsYXNzIFRvZ2dsZUNsYXNzIHtcblxuICBjb25zdHJ1Y3RvcihlbCkge1xuICAgIHRoaXMuZWwgPSBlbDtcbiAgICB0aGlzLnRhcmdldCA9IFV0aWxzLmdldFNlbGVjdG9yRnJvbUVsZW1lbnQoZWwpO1xuXG4gICAgaWYgKCF0aGlzLnRhcmdldCkge1xuICAgICAgdGhpcy50YXJnZXQgPSAkKGVsKS5wYXJlbnQoKS5jaGlsZHJlbignLmF3ZWJvb2tpbmctbWFpbi10b2dnbGUnKVswXTtcbiAgICB9XG5cbiAgICBpZiAodGhpcy50YXJnZXQpIHtcbiAgICAgICQodGhpcy5lbCkub24oJ2NsaWNrJywgdGhpcy50b2dnbGVDbGFzcy5iaW5kKHRoaXMpKTtcbiAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMucmVtb3ZlQ2xhc3MuYmluZCh0aGlzKSk7XG4gICAgfVxuICB9XG5cbiAgdG9nZ2xlQ2xhc3MoZSkge1xuICAgIGUgJiYgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICQodGhpcy50YXJnZXQpLnBhcmVudCgpLnRvZ2dsZUNsYXNzKCdhY3RpdmUnKTtcbiAgfVxuXG4gIHJlbW92ZUNsYXNzKGUpIHtcbiAgICBpZiAoZSAmJiAkLmNvbnRhaW5zKCQodGhpcy50YXJnZXQpLnBhcmVudCgpWzBdLCBlLnRhcmdldCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAkKHRoaXMudGFyZ2V0KS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG4gIH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBUb2dnbGVDbGFzcztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy90b2dnbGUtY2xhc3MuanMiLCJjb25zdCAkID0gd2luZG93LmpRdWVyeTtcbmNvbnN0IERBVEVfRk9STUFUID0gJ3l5LW1tLWRkJztcblxuY2xhc3MgUmFuZ2VEYXRlcGlja2VyIHtcblxuICBjb25zdHJ1Y3Rvcihmcm9tRGF0ZSwgdG9EYXRlKSB7XG4gICAgdGhpcy50b0RhdGUgPSB0b0RhdGU7XG4gICAgdGhpcy5mcm9tRGF0ZSA9IGZyb21EYXRlO1xuICB9XG5cbiAgaW5pdCgpIHtcbiAgICBjb25zdCBiZWZvcmVTaG93Q2FsbGJhY2sgPSBmdW5jdGlvbigpIHtcbiAgICAgICQoJyN1aS1kYXRlcGlja2VyLWRpdicpLmFkZENsYXNzKCdjbWIyLWVsZW1lbnQnKTtcbiAgICB9O1xuXG4gICAgJCh0aGlzLmZyb21EYXRlKS5kYXRlcGlja2VyKHtcbiAgICAgIGRhdGVGb3JtYXQ6IERBVEVfRk9STUFULFxuICAgICAgYmVmb3JlU2hvdzogYmVmb3JlU2hvd0NhbGxiYWNrLFxuICAgIH0pLm9uKCdjaGFuZ2UnLCB0aGlzLmFwcGx5RnJvbUNoYW5nZS5iaW5kKHRoaXMpKTtcblxuICAgICQodGhpcy50b0RhdGUpLmRhdGVwaWNrZXIoe1xuICAgICAgZGF0ZUZvcm1hdDogREFURV9GT1JNQVQsXG4gICAgICBiZWZvcmVTaG93OiBiZWZvcmVTaG93Q2FsbGJhY2ssXG4gICAgfSkub24oJ2NoYW5nZScsIHRoaXMuYXBwbHlUb0NoYW5nZS5iaW5kKHRoaXMpKTtcblxuICAgIHRoaXMuYXBwbHlUb0NoYW5nZSgpO1xuICAgIHRoaXMuYXBwbHlGcm9tQ2hhbmdlKCk7XG4gIH1cblxuICBhcHBseUZyb21DaGFuZ2UoKSB7XG4gICAgdHJ5IHtcbiAgICAgIGNvbnN0IG1pbkRhdGUgPSAkLmRhdGVwaWNrZXIucGFyc2VEYXRlKERBVEVfRk9STUFULCAkKHRoaXMuZnJvbURhdGUpLnZhbCgpKTtcbiAgICAgIG1pbkRhdGUuc2V0RGF0ZShtaW5EYXRlLmdldERhdGUoKSArIDEpO1xuICAgICAgJCh0aGlzLnRvRGF0ZSkuZGF0ZXBpY2tlcignb3B0aW9uJywgJ21pbkRhdGUnLCBtaW5EYXRlKTtcbiAgICB9IGNhdGNoKGUpIHt9XG4gIH1cblxuICBhcHBseVRvQ2hhbmdlKCkge1xuICAgIHRyeSB7XG4gICAgICBjb25zdCBtYXhEYXRlID0gJC5kYXRlcGlja2VyLnBhcnNlRGF0ZShEQVRFX0ZPUk1BVCwgJCh0aGlzLnRvRGF0ZSkudmFsKCkpO1xuICAgICAgJCh0aGlzLmZyb21EYXRlKS5kYXRlcGlja2VyKCdvcHRpb24nLCAnbWF4RGF0ZScsIG1heERhdGUpO1xuICAgIH0gY2F0Y2goZSkge31cbiAgfVxufVxuXG5tb2R1bGUuZXhwb3J0cyA9IFJhbmdlRGF0ZXBpY2tlcjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi91dGlscy9yYW5nZS1kYXRlcGlja2VyLmpzIiwiLy8gcmVtb3ZlZCBieSBleHRyYWN0LXRleHQtd2VicGFjay1wbHVnaW5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL2Fzc2V0cy9zYXNzL2FkbWluLnNjc3Ncbi8vIG1vZHVsZSBpZCA9IDlcbi8vIG1vZHVsZSBjaHVua3MgPSAwIl0sInNvdXJjZVJvb3QiOiIifQ==