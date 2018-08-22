(function (ko,$,plugin) {
  'use strict';

  ko = ko && ko.hasOwnProperty('default') ? ko['default'] : ko;
  $ = $ && $.hasOwnProperty('default') ? $['default'] : $;
  plugin = plugin && plugin.hasOwnProperty('default') ? plugin['default'] : plugin;

  var classCallCheck = function (instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  };

  var createClass = function () {
    function defineProperties(target, props) {
      for (var i = 0; i < props.length; i++) {
        var descriptor = props[i];
        descriptor.enumerable = descriptor.enumerable || false;
        descriptor.configurable = true;
        if ("value" in descriptor) descriptor.writable = true;
        Object.defineProperty(target, descriptor.key, descriptor);
      }
    }

    return function (Constructor, protoProps, staticProps) {
      if (protoProps) defineProperties(Constructor.prototype, protoProps);
      if (staticProps) defineProperties(Constructor, staticProps);
      return Constructor;
    };
  }();

  function formatDate(date, format) {
    var _date = plugin.utils.dates.parse(date, 'Y-m-d');

    if (!_date) {
      return '';
    }

    return plugin.utils.dates.format(_date, format || plugin.i18n.dateFormat);
  }

  var SearchFormModel = function () {
    function SearchFormModel() {
      var _this = this;

      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      classCallCheck(this, SearchFormModel);

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
    }

    createClass(SearchFormModel, [{
      key: 'checkInFormatted',
      value: function checkInFormatted(format) {
        return formatDate(this.checkIn(), format);
      }
    }, {
      key: 'checkOutFormatted',
      value: function checkOutFormatted(format) {
        return formatDate(this.checkOut(), format);
      }
    }]);
    return SearchFormModel;
  }();

  var SearchForm = function () {
    function SearchForm(el) {
      var _this2 = this;

      classCallCheck(this, SearchForm);

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

      var fp = plugin.datepicker($rangepicker[0], {
        mode: 'range',
        altInput: false,
        clickOpens: false,
        closeOnSelect: true,

        onReady: function onReady(_, __, fp) {
          fp.calendarContainer.classList.add('awebooking-datepicker');
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

        onPreCalendarPosition: function onPreCalendarPosition(_, __, fp) {
          var _this3 = this;

          fp._positionElement = $('.searchbox__box--checkout', self.$el)[0];
          setTimeout(function () {
            _this3._positionElement = _this3._input;
          }, 0);
        }
      });

      $(this.$el).on('click', '.searchbox__box--checkin, .searchbox__box--checkout', function (e) {
        e.preventDefault();

        fp.isOpen = false;
        fp.open(undefined, e.currentTarget);
      });

      $('.searchbox__box', this.$el).each(function (i, box) {
        $(box).data('popup', _this2.setuptPopper(box));
      });

      $('[data-trigger="spinner"]', this.$el).on('changed.spinner', function () {
        $(this).find('[data-spin="spinner"]').trigger('change');
      });
    }

    createClass(SearchForm, [{
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

}(ko,jQuery,window.awebooking));

//# sourceMappingURL=search-form.js.map
