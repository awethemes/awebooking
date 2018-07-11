(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

(function ($, ko, plugin) {
  'use strict';

  function formatDate(date) {
    var _date = flatpickr.parseDate(date);

    if (!_date) {
      return '';
    }

    return flatpickr.formatDate(_date, plugin.i18n.dateFormat);
  }

  function SearchFormModel() {
    var _this = this;

    var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

    this.adult = ko.observable(data.adult || 1);
    this.children = ko.observable(data.children || 0);
    this.infants = ko.observable(data.infants || 0);
    this.checkIn = ko.observable(data.checkIn || '2018-07-05');
    this.checkOut = ko.observable(data.checkOut || '2018-07-10');

    this.checkInFormatted = ko.computed(function () {
      return formatDate(_this.checkIn());
    });

    this.checkOutFormatted = ko.computed(function () {
      return formatDate(_this.checkOut());
    });
  }

  var SearchForm = function () {
    function SearchForm(el) {
      var _this2 = this;

      babelHelpers.classCallCheck(this, SearchForm);

      var self = this;

      this.$el = $(el);
      this.model = new SearchFormModel();
      ko.applyBindings(this.model, el);

      $('.searchbox__box', this.$el).each(function (i, box) {
        $(box).data('popup', _this2.setuptPopper(box));
      });

      var $rangepicker = this.$el.find('[data-hotel="rangepicker"]');
      var fp = awebooking.datepicker($rangepicker[0], {
        mode: 'range',
        clickOpens: false,
        closeOnSelect: true,

        onReady: function onReady() {
          this.config.ignoredFocusElements.push($('.searchbox__box--checkin', self.$el)[0]);
          this.config.ignoredFocusElements.push($('.searchbox__box--checkout', self.$el)[0]);
        },


        onChange: function onChange(dates) {
          if (dates.length === 0) {
            _this2.model.checkIn(null);
            _this2.model.checkIn(null);
          } else if (dates.length === 1) {
            _this2.model.checkIn(dates[0]);
            _this2.model.checkOut(null);
          } else {
            _this2.model.checkIn(dates[0]);
            _this2.model.checkOut(dates[1]);
          }
        },

        onPreCalendarPosition: function onPreCalendarPosition() {
          var _this3 = this;

          fp._positionElement = $('.searchbox__box--checkout')[0];
          setTimeout(function () {
            _this3._positionElement = _this3._input;
          }, 0);
        }
      });

      $('.searchbox__box--checkin, .searchbox__box--checkout').on('click focus', function (e) {
        e.preventDefault();

        fp.isOpen = false;
        fp.open(undefined, this);
      });
    }

    babelHelpers.createClass(SearchForm, [{
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

  $(function () {
    $('.searchbox').each(function () {
      new SearchForm(this);
    });
  });
})(jQuery, window.ko, window.awebooking);

},{}]},{},[1]);

//# sourceMappingURL=search-form.js.map
