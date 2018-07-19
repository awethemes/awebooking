(function ($, ko, plugin) {
  'use strict';

  function formatDate(date, format) {
    const _date = flatpickr.parseDate(date);

    if (!_date) {
      return '';
    }

    return flatpickr.formatDate(_date, format || plugin.i18n.dateFormat);
  }

  function SearchFormModel(data = {}) {
    this.adults    = ko.observable(data.adults || 1);
    this.children = ko.observable(data.children || 0);
    this.infants  = ko.observable(data.infants || 0);
    this.checkIn  = ko.observable(data.checkIn || '');
    this.checkOut = ko.observable(data.checkOut || '');

    this.checkInDate = ko.computed(() => {
      return formatDate(this.checkIn(), 'Y-m-d');
    });

    this.checkOutDate = ko.computed(() => {
      return formatDate(this.checkOut(), 'Y-m-d');
    });

    this.checkInFormatted = ko.computed(() => {
      return formatDate(this.checkIn());
    });

    this.checkOutFormatted = ko.computed(() => {
      return formatDate(this.checkOut());
    });
  }

  class SearchForm {
    constructor (el) {
      const self = this;

      this.$el = $(el);

      this.model = new SearchFormModel({
        adults: this.$el.find('input[name="adults"]').val(),
        children: this.$el.find('input[name="children"]').val(),
        infants: this.$el.find('input[name="infants"]').val(),
        checkIn: this.$el.find('input[name="check_in"]').val(),
        checkOut: this.$el.find('input[name="check_out"]').val(),
      });

      ko.applyBindings(this.model, el);

      let $rangepicker = this.$el.find('[data-hotel="rangepicker"]');
      if ($rangepicker.length === 0) {
        $rangepicker = $('<input type="text" data-hotel="rangepicker"/>').appendTo(this.$el);
      }

      const fp = awebooking.datepicker($rangepicker[0], {
        mode: 'range',
        altInput: false,
        clickOpens: false,
        closeOnSelect: true,

        onReady() {
          this.config.ignoredFocusElements.push($('.searchbox__box--checkin', self.$el)[0]);
          this.config.ignoredFocusElements.push($('.searchbox__box--checkout', self.$el)[0]);
        },

        onChange: (dates) => {
          if (dates.length === 0) {
            this.model.checkIn(null);
            this.model.checkIn(null);
          } else if (dates.length === 1) {
            this.model.checkIn(dates[0]);
            this.model.checkOut(null);
          } else {
            this.model.checkIn(dates[0]);
            this.model.checkOut(dates[1]);
          }
        },

        onPreCalendarPosition() {
          fp._positionElement = $('.searchbox__box--checkout', self.$el)[0];
          setTimeout(() => { this._positionElement = this._input; }, 0);
        },
      });

      $('.searchbox__box--checkin, .searchbox__box--checkout', this.$el)
        .on('click focus', function(e) {
          e.preventDefault();

          fp.isOpen = false;
          fp.open(undefined, this);
        });

      $('.searchbox__box', this.$el).each((i, box) => {
        $(box).data('popup', this.setuptPopper(box));
      });
    }

    setuptPopper(el) {
      const $html = $(el).find('.searchbox__popup');

      if ($html.length === 0) {
        return;
      }

      plugin.utils.dropdown($(el).find('.searchbox__box-wrap'), {
        dropClass: '.searchbox__popup',
      });
    }
  }

  $(function () {
    $('.searchbox').each(function () {
      new SearchForm(this);
    });
  });

})(jQuery, window.ko, window.awebooking);
