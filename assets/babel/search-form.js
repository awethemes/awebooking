(function ($, ko, plugin) {
  'use strict';

  function formatDate(date) {
    const _date = flatpickr.parseDate(date);

    if (!_date) {
      return '';
    }

    return flatpickr.formatDate(_date, plugin.i18n.dateFormat);
  }

  function SearchFormModel(data = {}) {
    this.adult    = ko.observable(data.adult || 1);
    this.children = ko.observable(data.children || 0);
    this.infants  = ko.observable(data.infants || 0);
    this.checkIn  = ko.observable(data.checkIn || '2018-07-05');
    this.checkOut = ko.observable(data.checkOut || '2018-07-10');

    this.checkInFormatted = ko.computed(() => {
      return formatDate(this.checkIn());
    });

    this.checkOutFormatted = ko.computed(() => {
      return formatDate(this.checkOut());
    });
  }

  class SearchForm {
    constructor (el) {
      this.$el = $(el);

      this.model = new SearchFormModel();
      ko.applyBindings(this.model, el);

      $('.searchbox__box', this.$el).each((i, box) => {
        $(box).data('popup', this.setuptPopper(box));
      });

      const $checkin = this.$el.find('input[name="check_in"]');
      const $checkout = this.$el.find('input[name="check_out"]');
      const $rangepicker = this.$el.find('[data-hotel="rangepicker"]');

      // const rangedates = new RangeDatesPlugin({
      //   firstInput: $checkin[0],
      //   secondInput: $checkout[0]
      // });

      const fp = awebooking.datepicker($rangepicker[0], {
        mode: 'range',
        clickOpens: false,
        closeOnSelect: true,

        onReady() {
          this.config.ignoredFocusElements.push($('.searchbox__box--checkin')[0]);
          this.config.ignoredFocusElements.push($('.searchbox__box--checkout')[0]);
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
          fp._positionElement = $('.searchbox__box--checkout')[0];
          setTimeout(() => { this._positionElement = this._input; }, 0);
        },
      });

      $('.searchbox__box--checkin, .searchbox__box--checkout')
        .on('click focus', function(e) {
          e.preventDefault();

          fp.isOpen = false;
          fp.open(undefined, this);
        });
    }

    setuptPopper(el) {
      const $html = $(el).find('.searchbox__popup');
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
            preventOverflow: { enabled: false },
          }}
      });

      return el._tippy;
    }
  }

  $(function () {
    $('.searchbox').each(function () {
      new SearchForm(this);
    });
  });

})(jQuery, window.ko, window.awebooking);
