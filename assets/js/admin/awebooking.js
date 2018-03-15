webpackJsonp([0],[
/* 0 */,
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */
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
/* 6 */,
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(8);
__webpack_require__(18);
__webpack_require__(19);
module.exports = __webpack_require__(20);


/***/ }),
/* 8 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_popper_js__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_tooltip_js__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_flatpickr__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_flatpickr___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_flatpickr__);
var $ = window.jQuery;
var settings = window._awebooking || {};





var AweBooking = _.extend(settings, {
  Vue: __webpack_require__(4),

  Popper: __WEBPACK_IMPORTED_MODULE_0_popper_js__["default"],
  Tooltip: __WEBPACK_IMPORTED_MODULE_1_tooltip_js__["default"],
  Flatpickr: __WEBPACK_IMPORTED_MODULE_2_flatpickr___default.a,
  FlatpickrRange: __webpack_require__(12),

  Popup: __webpack_require__(13),
  ToggleClass: __webpack_require__(14),
  RangeDatepicker: __webpack_require__(15),
  ToggleCheckboxes: __webpack_require__(16),

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

    $('[data-init="awebooking-tooltip"]').each(function () {
      var options = {
        template: '<div class="awebooking-tooltip" role="tooltip"><div class="awebooking-tooltip__arrow" x-arrow></div><div class="tooltip__inner"></div></div>'
      };

      $(this).data('awebooking-tooltip', new self.Tooltip(this, options));
    });

    var createForm = function createForm(link, method) {
      var form = $('<form>', { 'method': 'POST', 'action': link });
      var hiddenInput = $('<input>', { 'name': '_method', 'type': 'hidden', 'value': method });

      return form.append(hiddenInput).appendTo('body');
    };

    $('a[data-method="awebooking-delete"]').on('click', function (e) {
      e.preventDefault();
      var link = $(this).attr('href');

      self.confirm(function (result) {
        var form = createForm(link, 'DELETE');
        form.submit();
      }, { confirmButtonText: self.trans('delete') });
    });

    __webpack_require__(17);
  },


  /**
   * Show the confirm message.
   */
  confirm: function confirm(callback, settings) {
    var confirm = swal(_.extend({
      toast: true,
      title: this.trans('confirm_title'),
      html: this.trans('confirm_message'),
      type: 'warning',
      position: 'center',
      animation: false,
      reverseButtons: true,
      showCancelButton: true,
      buttonsStyling: false,
      cancelButtonClass: 'button',
      confirmButtonClass: 'button button-primary',
      cancelButtonText: this.trans('cancel'),
      confirmButtonText: this.trans('ok')
    }, settings || {}));

    if (callback) {
      return confirm.then(function (result) {
        if (result.value) callback(result);
      });
    }

    return confirm;
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
    var serialize = __webpack_require__(6);
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
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

/* flatpickr v4.3.2, @license MIT */
(function (global, factory) {
	 true ? module.exports = factory() :
	typeof define === 'function' && define.amd ? define(factory) :
	(global.rangePlugin = factory());
}(this, (function () { 'use strict';

function rangePlugin(config) {
    if (config === void 0) { config = {}; }
    return function (fp) {
        var dateFormat = "", secondInput, _firstInputFocused, _secondInputFocused, _prevDates;
        var createSecondInput = function () {
            if (config.input) {
                secondInput =
                    config.input instanceof Element
                        ? config.input
                        : window.document.querySelector(config.input);
            }
            else {
                secondInput = fp._input.cloneNode();
                secondInput.removeAttribute("id");
                secondInput._flatpickr = undefined;
            }
            if (secondInput.value) {
                var parsedDate = fp.parseDate(secondInput.value);
                if (parsedDate)
                    fp.selectedDates.push(parsedDate);
            }
            secondInput.setAttribute("data-fp-omit", "");
            fp._bind(secondInput, ["focus", "click"], function () {
                if (fp.selectedDates[1]) {
                    fp.latestSelectedDateObj = fp.selectedDates[1];
                    fp._setHoursFromDate(fp.selectedDates[1]);
                    fp.jumpToDate(fp.selectedDates[1]);
                }
                _a = [false, true], _firstInputFocused = _a[0], _secondInputFocused = _a[1];
                fp.open(undefined, secondInput);
                var _a;
            });
            fp._bind(secondInput, "keydown", function (e) {
                if (e.key === "Enter") {
                    fp.setDate([fp.selectedDates[0], secondInput.value], true, dateFormat);
                    secondInput.click();
                }
            });
            if (!config.input)
                fp._input.parentNode &&
                    fp._input.parentNode.insertBefore(secondInput, fp._input.nextSibling);
        };
        var plugin = {
            onParseConfig: function () {
                fp.config.mode = "range";
                fp.config.allowInput = true;
                dateFormat = fp.config.altInput
                    ? fp.config.altFormat
                    : fp.config.dateFormat;
            },
            onReady: function () {
                createSecondInput();
                fp.config.ignoredFocusElements.push(secondInput);
                fp._input.removeAttribute("readonly");
                secondInput.removeAttribute("readonly");
                fp._bind(fp._input, "focus", function () {
                    fp.latestSelectedDateObj = fp.selectedDates[0];
                    fp._setHoursFromDate(fp.selectedDates[0]);
                    _a = [true, false], _firstInputFocused = _a[0], _secondInputFocused = _a[1];
                    fp.jumpToDate(fp.selectedDates[0]);
                    var _a;
                });
                fp._bind(fp._input, "keydown", function (e) {
                    if (e.key === "Enter")
                        fp.setDate([fp._input.value, fp.selectedDates[1]], true, dateFormat);
                });
                fp.setDate(fp.selectedDates, false);
                plugin.onValueUpdate(fp.selectedDates);
            },
            onPreCalendarPosition: function () {
                if (_secondInputFocused) {
                    fp._positionElement = secondInput;
                    setTimeout(function () {
                        fp._positionElement = fp._input;
                    }, 0);
                }
            },
            onChange: function () {
                if (!fp.selectedDates.length) {
                    setTimeout(function () {
                        if (fp.selectedDates.length)
                            return;
                        secondInput.value = "";
                        _prevDates = [];
                    }, 10);
                }
                if (_secondInputFocused) {
                    setTimeout(function () {
                        secondInput.focus();
                    }, 0);
                }
            },
            onDestroy: function () {
                if (!config.input)
                    secondInput.parentNode &&
                        secondInput.parentNode.removeChild(secondInput);
            },
            onValueUpdate: function (selDates) {
                if (!secondInput)
                    return;
                _prevDates =
                    !_prevDates || selDates.length >= _prevDates.length
                        ? selDates.slice() : _prevDates;
                if (_prevDates.length > selDates.length) {
                    var newSelectedDate = selDates[0];
                    var newDates = _secondInputFocused
                        ? [_prevDates[0], newSelectedDate]
                        : [newSelectedDate, _prevDates[1]];
                    fp.setDate(newDates, false);
                    _prevDates = newDates.slice();
                }
                _a = fp.selectedDates.map(function (d) { return fp.formatDate(d, dateFormat); }), _b = _a[0], fp._input.value = _b === void 0 ? "" : _b, _c = _a[1], secondInput.value = _c === void 0 ? "" : _c;
                var _a, _b, _c;
            },
        };
        return plugin;
    };
}

return rangePlugin;

})));


/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(5);

var Popup = function () {
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
/* 14 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(5);

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
/* 15 */
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
/* 16 */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;

var ToggleCheckboxes =
/**
 * Wrapper the jquery-ui-popup.
 */
function ToggleCheckboxes(table) {
  _classCallCheck(this, ToggleCheckboxes);

  this.table = table;
  var $table = $(this.table);

  $(document).on('click', '.check-column :checkbox', function (event) {
    // Toggle the "Select all" checkboxes depending if the other ones are all checked or not.
    var unchecked = $(this).closest('tbody').find(':checkbox').filter(':visible:enabled').not(':checked');

    $(document).find('.wp-toggle-checkboxes').prop('checked', function () {
      return 0 === unchecked.length;
    });

    return true;
  });

  $(document).on('click', '.wp-toggle-checkboxes', function (e) {
    $table.children('tbody').filter(':visible').find('.check-column').find(':checkbox').prop('checked', function () {
      if ($(this).is(':hidden,:disabled')) {
        return false;
      }
      return !$(this).prop('checked');
    });
  });
};

module.exports = ToggleCheckboxes;

/***/ }),
/* 17 */
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
/* 18 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 19 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 20 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
],[7]);