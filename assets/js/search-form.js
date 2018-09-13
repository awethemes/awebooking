(function (plugin,ko$1,$) {
  'use strict';

  plugin = plugin && plugin.hasOwnProperty('default') ? plugin['default'] : plugin;
  ko$1 = ko$1 && ko$1.hasOwnProperty('default') ? ko$1['default'] : ko$1;
  $ = $ && $.hasOwnProperty('default') ? $['default'] : $;

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
  }

  var SearchModel = function ($$$1) {

    function formatDate(date, format) {
      var _date = plugin.utils.dates.parse(date, 'Y-m-d');

      if (!_date) {
        return '';
      }

      return plugin.utils.dates.format(_date, format || plugin.i18n.dateFormat);
    }

    var Model =
    /*#__PURE__*/
    function () {
      function Model() {
        var _this = this;

        var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

        _classCallCheck(this, Model);

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

      _createClass(Model, [{
        key: "checkInFormatted",
        value: function checkInFormatted(format) {
          return formatDate(this.checkIn(), format);
        }
      }, {
        key: "checkOutFormatted",
        value: function checkOutFormatted(format) {
          return formatDate(this.checkOut(), format);
        }
      }]);

      return Model;
    }();

    return Model;
  }(jQuery);

  var SearchForm =
  /*#__PURE__*/
  function () {
    function SearchForm(el) {
      var _this = this;

      _classCallCheck(this, SearchForm);

      var self = this;
      this.$el = $(el);
      this.model = new SearchModel({
        adults: this.$el.find('input[name="adults"]').val(),
        children: this.$el.find('input[name="children"]').val(),
        infants: this.$el.find('input[name="infants"]').val(),
        checkIn: this.$el.find('input[name="check_in"]').val(),
        checkOut: this.$el.find('input[name="check_out"]').val()
      });
      ko$1.applyBindings(this.model, el);
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
            _this.model.checkIn(null);

            _this.model.checkIn(null);
          } else if (dates.length === 1) {
            _this.model.checkIn(dates[0]);

            _this.model.checkOut(null);
          } else {
            _this.model.checkIn(dates[0]);

            _this.model.checkOut(dates[1]);
          }
        },
        onPreCalendarPosition: function onPreCalendarPosition(_, __, fp) {
          var _this2 = this;

          fp._positionElement = $('.searchbox__box--checkout', self.$el)[0];
          setTimeout(function () {
            _this2._positionElement = _this2._input;
          }, 0);
        }
      });
      $(this.$el).on('click', '.searchbox__box--checkin, .searchbox__box--checkout', function (e) {
        e.preventDefault();
        fp.isOpen = false;
        fp.open(undefined, e.currentTarget);
      });
      $('.searchbox__box', this.$el).each(function (i, box) {
        $(box).data('popup', _this.setuptPopper(box));
      });
      $('[data-trigger="spinner"]', this.$el).on('changed.spinner', function () {
        $(this).find('[data-spin="spinner"]').trigger('change');
      });
    }

    _createClass(SearchForm, [{
      key: "setuptPopper",
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
    $('.awebooking .searchbox, .awebooking-block .searchbox').each(function (i, el) {
      new SearchForm(el);
    });
  });

}(window.awebooking,ko,jQuery));

//# sourceMappingURL=search-form.js.map
