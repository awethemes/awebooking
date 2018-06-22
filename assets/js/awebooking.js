(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

window.awebooking = {};

(function ($, plugin) {
  'use strict';

  var _defaults = function _defaults(options, defaults) {
    return $.extend({}, defaults, options);
  };

  // Polyfill location.origin in IE, @see https://stackoverflow.com/a/25495161
  if (!window.location.origin) {
    window.location.origin = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
  }

  // Main objects
  plugin.utils = {};
  plugin.instances = {};
  plugin.utils.rangeDates = require('./core/flatpickr-dates.js');

  /**
   * Configure.
   *
   * @type {Object}
   */
  plugin.config = _defaults(window._awebooking, {
    route: window.location.origin + '?awebooking_route=/',
    ajax_url: window.location.origin + '/wp-admin/admin-ajax.php',
    i18n: {
      date_format: 'F j, Y',
      time_format: 'H:i:s'
    }
  });

  /**
   * The admin route.
   *
   * @param  {string} route
   * @return {string}
   */
  plugin.route = function (route) {
    return this.config.route + (route || '').replace(/^\//g, '');
  };

  /**
   * Create new datepicker.
   *
   * @see https://flatpickr.js.org/options/
   *
   * @return {flatpickr}
   */
  plugin.datepicker = function (instance, options) {
    var i18n = plugin.config.i18n;
    var defaults = plugin.config.datepicker;
    var disable = Array.isArray(defaults.disable) ? defaults.disable : [];

    if (Array.isArray(defaults.disable_days)) {
      disable.push(function (date) {
        return defaults.disable_days.indexOf(date.getDay()) !== -1;
      });
    }

    var minDate = new Date().fp_incr(defaults.min_date);
    var maxDate = defaults.max_date && defaults.max_date !== 0 ? new Date().fp_incr(defaults.max_date) : '';

    var fp = flatpickr(instance, _defaults(options, {
      dateFormat: 'Y-m-d',
      ariaDateFormat: i18n.date_format,
      minDate: 'today',
      // maxDate: max_date,
      // disable: disable,
      showMonths: defaults.show_months,
      enableTime: false,
      enableSeconds: false,
      onReady: function onReady(_, __, fp) {
        fp.calendarContainer.classList.add('awebooking-datepicker');
      }
    }));

    return fp;
  };

  /**
   * Document ready.
   *
   * @return {void}
   */
  $(function () {
    // Init
    require('./frontend/search-form').init();

    tippy('[data-awebooking="tooltip"]', []);
  });
})(jQuery, window.awebooking);

},{"./core/flatpickr-dates.js":2,"./frontend/search-form":3}],2:[function(require,module,exports){
'use strict';

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = jQuery;
var debounce = require('debounce');

var SelectedDates = function () {
  function SelectedDates(fp, input) {
    _classCallCheck(this, SelectedDates);

    this.fp = fp;
    this.input = input;
    this.dates = { startDate: null, endDate: null };
  }

  _createClass(SelectedDates, [{
    key: 'set',
    value: function set(startDate, endDate) {
      this.setStartDate(startDate);
      this.setEndDate(endDate);
      this.update();
    }
  }, {
    key: 'reset',
    value: function reset() {
      var update = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

      this.dates = { startDate: null, endDate: null };
      update && this.update();
    }
  }, {
    key: 'update',
    value: function update() {
      var _this = this;

      var input = this.input;
      var dateFormat = this.fp.config.dateFormat;

      this.fp.selectedDates = this.toArray();

      var _fp$selectedDates$map = this.fp.selectedDates.map(function (d) {
        return _this.fp.formatDate(d, dateFormat);
      });

      var _fp$selectedDates$map2 = _slicedToArray(_fp$selectedDates$map, 2);

      var _fp$selectedDates$map3 = _fp$selectedDates$map2[0];
      input.start.value = _fp$selectedDates$map3 === undefined ? '' : _fp$selectedDates$map3;
      var _fp$selectedDates$map4 = _fp$selectedDates$map2[1];
      input.end.value = _fp$selectedDates$map4 === undefined ? '' : _fp$selectedDates$map4;
    }
  }, {
    key: 'setStartDate',
    value: function setStartDate(startDate) {
      var parsedDate = this.fp.parseDate(startDate);

      if (!parsedDate) {
        this.reset();
        return;
      }

      this.dates.startDate = parsedDate;

      if (this.dates.endDate && parsedDate >= this.dates.endDate) {
        this.dates.endDate = null;
      }
    }
  }, {
    key: 'setEndDate',
    value: function setEndDate(endDate) {
      var parsedDate = this.fp.parseDate(endDate);

      if (!parsedDate) {
        this.reset();
        return;
      }

      this.dates.endDate = parsedDate;
    }
  }, {
    key: 'toArray',
    value: function toArray() {
      var dates = Object.values(this.dates);

      return dates.filter(function (d) {
        return d instanceof Date;
      });
    }
  }]);

  return SelectedDates;
}();

function RangeDatesPlugin(config) {
  var plugin = this;

  plugin.config = config;

  return function (fp) {
    var firstInput = config.firstInput;
    var secondInput = config.secondInput;

    var _minDate = void 0,
        _firstInputFocused = void 0,
        _secondInputFocused = void 0;

    var dates = new SelectedDates(fp, {
      start: firstInput,
      end: secondInput
    });

    /**
     * Handle binding inputs.
     */
    function bindingInputs() {
      firstInput.setAttribute('data-fp-omit', '');
      secondInput.setAttribute('data-fp-omit', '');

      fp._bind(firstInput, ['focus', 'click'], debounce(function (e) {
        e.preventDefault();

        _firstInputFocused = true;
        _secondInputFocused = false;


        fp.isOpen = false;
        fp.open(undefined, firstInput);
      }, 150, true));

      fp._bind(secondInput, ['focus', 'click'], debounce(function (e) {
        e.preventDefault();

        _firstInputFocused = false;
        _secondInputFocused = true;


        fp.isOpen = false;
        fp.open(undefined, firstInput);
      }, 150, true));
    }

    return {
      onParseConfig: function onParseConfig() {
        fp.config.mode = 'range';
        fp.config.clickOpens = false;
        fp.config.closeOnSelect = false;

        _minDate = fp.config.minDate;
      },
      onReady: function onReady() {
        bindingInputs();

        fp.config.ignoredFocusElements.push(firstInput);
        fp.config.ignoredFocusElements.push(secondInput);

        // dates.set(firstInput.value, secondInput.value);
      },
      onOpen: function onOpen() {
        $([firstInput, secondInput]).removeClass('selected');

        if (_firstInputFocused) {
          firstInput.classList.add('selected');
        } else {
          secondInput.classList.add('selected');
        }

        if (_secondInputFocused && fp.selectedDates[0]) {
          fp.config.minDate = fp.selectedDates[0];
        } else {
          fp.config.minDate = _minDate;
        }
      },
      onClose: function onClose() {
        $([firstInput, secondInput]).removeClass('selected');
        _firstInputFocused = false;
        _secondInputFocused = false;
      },
      onPreCalendarPosition: function onPreCalendarPosition() {
        fp._positionElement = firstInput;
        // fp._positionElement = _firstInputFocused ? firstInput : secondInput;
        setTimeout(function () {
          fp._positionElement = fp._input;
        }, 0);
      },
      onValueUpdate: function onValueUpdate(selfDates) {
        if (_firstInputFocused) {
          dates.setStartDate(selfDates[0]);
        } else if (_secondInputFocused) {
          dates.setEndDate(selfDates[1] ? selfDates[1] : selfDates[0]);
        }

        dates.update();
        fp.setDate(fp.selectedDates, false);
      },
      onChange: function onChange() {
        // if (_firstInputFocused) {
        //   setTimeout(() => { $(secondInput).trigger('focus'); }, 0);
        // } else if (_secondInputFocused && fp.selectedDates.length === 2) {
        //   setTimeout(fp.close, 0);
        // }
      }
    };
  };
}

module.exports = function FlatpickrDates(el) {
  var config = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

  this.el = el;
  this.config = config;

  var $checkin = $(el).find('input[name="check_in"]');
  var $checkout = $(el).find('input[name="check_out"]');
  var $rangepicker = $(el).find('[data-hotel="rangepicker"]');

  var rangedates = new RangeDatesPlugin({
    firstInput: $checkin[0],
    secondInput: $checkout[0]
  });

  this.datepicker = awebooking.datepicker($rangepicker[0], {
    mode: 'range',
    inline: true
    // static: true,
    // plugins: [ rangedates ],
  });
};

},{"debounce":4}],3:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.jQuery;
var plugin = window.awebooking;

var SearchForm = function () {
  function SearchForm(el) {
    var _this = this;

    _classCallCheck(this, SearchForm);

    this.$el = $(el);

    var rangeDates = new plugin.utils.rangeDates('.searchbox', {});

    $('.searchbox__box', this.$el).each(function (i, box) {
      $(box).data('popup', _this.setuptPopper(box));
    });

    // console.log(rangeDates);
    // console.log(this);
  }

  _createClass(SearchForm, [{
    key: 'setuptPopper',
    value: function setuptPopper(el) {
      var $html = $(el).find('.searchbox__popup');
      if ($html.length === 0) {
        return;
      }

      tippy(el, {
        theme: 'awebooking-popup',
        delay: 0,
        arrow: true,
        distance: 0,
        placement: 'bottom',
        trigger: 'click',
        interactive: true,
        performance: true,
        hideOnClick: true,
        animation: 'shift-toward',
        duration: [150, 150],
        html: $html[0],
        popperOptions: { modifiers: {
            hide: { enabled: false },
            preventOverflow: { enabled: false }
          } }
      });

      return el._tippy;
    }
  }]);

  return SearchForm;
}();

module.exports = {
  init: function init() {
    $('.searchbox').each(function () {
      new SearchForm(this);
    });
  }
};

},{}],4:[function(require,module,exports){
/**
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing. The function also has a property 'clear' 
 * that is a function which will clear the timer to prevent previously scheduled executions. 
 *
 * @source underscore.js
 * @see http://unscriptable.com/2009/03/20/debouncing-javascript-methods/
 * @param {Function} function to wrap
 * @param {Number} timeout in ms (`100`)
 * @param {Boolean} whether to execute at the beginning (`false`)
 * @api public
 */

module.exports = function debounce(func, wait, immediate){
  var timeout, args, context, timestamp, result;
  if (null == wait) wait = 100;

  function later() {
    var last = Date.now() - timestamp;

    if (last < wait && last >= 0) {
      timeout = setTimeout(later, wait - last);
    } else {
      timeout = null;
      if (!immediate) {
        result = func.apply(context, args);
        context = args = null;
      }
    }
  };

  var debounced = function(){
    context = this;
    args = arguments;
    timestamp = Date.now();
    var callNow = immediate && !timeout;
    if (!timeout) timeout = setTimeout(later, wait);
    if (callNow) {
      result = func.apply(context, args);
      context = args = null;
    }

    return result;
  };

  debounced.clear = function() {
    if (timeout) {
      clearTimeout(timeout);
      timeout = null;
    }
  };
  
  debounced.flush = function() {
    if (timeout) {
      result = func.apply(context, args);
      context = args = null;
      
      clearTimeout(timeout);
      timeout = null;
    }
  };

  return debounced;
};

},{}]},{},[1]);

//# sourceMappingURL=awebooking.js.map
