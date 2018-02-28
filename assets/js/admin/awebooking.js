webpackJsonp([0],[
/* 0 */,
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */
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
/* 5 */,
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(7);
__webpack_require__(16);
__webpack_require__(17);
module.exports = __webpack_require__(18);


/***/ }),
/* 7 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_tooltip_js__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_popper_js__ = __webpack_require__(1);
var $ = window.jQuery;
var settings = window._awebookingSettings || {};




var AweBooking = _.extend(settings, {
  Vue: __webpack_require__(3),

  Popper: __WEBPACK_IMPORTED_MODULE_1_popper_js__["default"],
  Tooltip: __WEBPACK_IMPORTED_MODULE_0_tooltip_js__["default"],

  Popup: __webpack_require__(11),
  ToggleClass: __webpack_require__(12),
  RangeDatepicker: __webpack_require__(13),
  ToggleCheckboxes: __webpack_require__(14),

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
        template: '<div class="awebooking-tooltip tooltip" role="tooltip"><div class="tooltip__arrow"></div><div class="tooltip__inner"></div></div>'
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

      var link = $(this);

      swal({
        toast: true,
        title: self.trans('confirm_title'),
        html: self.trans('confirm_message'),
        type: 'warning',
        position: 'center',
        animation: false,
        reverseButtons: true,
        showCancelButton: true,
        buttonsStyling: false,
        cancelButtonClass: 'button',
        confirmButtonClass: 'button button-primary',
        cancelButtonText: self.trans('cancel'),
        confirmButtonText: self.trans('delete')
      }).then(function (result) {
        if (result.value) {
          var form = createForm(link.attr('href'), 'DELETE');
          form.submit();
        }
      });
    });

    __webpack_require__(15);
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
    var serialize = __webpack_require__(5);
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
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(4);

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
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var Utils = __webpack_require__(4);

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
/* 13 */
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
/* 14 */
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
/* 15 */
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
/* 16 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 17 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 18 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
],[6]);