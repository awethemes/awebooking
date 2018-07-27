(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

(function ($, ko, plugin) {
  'use strict';

  function formatDate(date, format) {
    var _date = plugin.utils.dates.parse(date, format || plugin.i18n.dateFormat);

    if (!_date) {
      return '';
    }

    return plugin.utils.dates.format(_date, format || plugin.i18n.dateFormat);
  }

  function SearchFormModel() {
    var _this = this;

    var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

    this.adults = ko.observable(data.adults || 1);
    this.children = ko.observable(data.children || 0);
    this.infants = ko.observable(data.infants || 0);
    this.checkIn = ko.observable(data.checkIn || '');
    this.checkOut = ko.observable(data.checkOut || '');

    this.checkInDate = ko.computed(function () {
      return formatDate(_this.checkIn(), 'Y-m-d');
    });

    this.checkOutDate = ko.computed(function () {
      return formatDate(_this.checkOut(), 'Y-m-d');
    });

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

      _classCallCheck(this, SearchForm);

      var self = this;

      this.$el = $(el);

      this.model = new SearchFormModel({
        adults: this.$el.find('input[name="adults"]').val(),
        children: this.$el.find('input[name="children"]').val(),
        infants: this.$el.find('input[name="infants"]').val(),
        checkIn: this.$el.find('input[name="check_in"]').val(),
        checkOut: this.$el.find('input[name="check_out"]').val()
      });

      ko.applyBindings(this.model, el);

      var $rangepicker = this.$el.find('[data-hotel="rangepicker"]');
      if ($rangepicker.length === 0) {
        $rangepicker = $('<input type="text" data-hotel="rangepicker"/>').appendTo(this.$el);
      }

      var fp = awebooking.datepicker($rangepicker[0], {
        mode: 'range',
        altInput: false,
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

          fp._positionElement = $('.searchbox__box--checkout', self.$el)[0];
          setTimeout(function () {
            _this3._positionElement = _this3._input;
          }, 0);
        }
      });

      $('.searchbox__box--checkin, .searchbox__box--checkout', this.$el).on('click focus', function (e) {
        e.preventDefault();

        fp.isOpen = false;
        fp.open(undefined, this);
      });

      $('.searchbox__box', this.$el).each(function (i, box) {
        $(box).data('popup', _this2.setuptPopper(box));
      });
    }

    _createClass(SearchForm, [{
      key: 'setuptPopper',
      value: function setuptPopper(el) {
        var $html = $(el).find('.searchbox__popup');

        if ($html.length === 0) {
          return;
        }

        plugin.utils.dropdown($(el).find('.searchbox__box-wrap'), {
          drop: '.searchbox__popup',
          display: 'static'
        });
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
