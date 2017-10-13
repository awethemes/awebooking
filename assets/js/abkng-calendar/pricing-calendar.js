;(function($, Backbone) {
  'use strict';

  var PricingCalendar = function(element, options) {
    this.$el = $(element);

    if (this.$el.prop('tagName') == 'TR') {
      this.$table = this.$el.closest('table');
    } else {
      this.$table = this.$el;
    }

    this.options = $.extend(options, {
      debug: false
    });

    // Do not change this format,
    // We only using ISO-8601 date format for all of the process.
    this.format = 'YYYY-MM-DD';

    this.endDay = null;
    this.startDay = null;
    this.keepRange = false;

    // Initialize the calendar.
    this.initialize();
  };

  $.extend(PricingCalendar.prototype, Backbone.Events, {
    debug: function() {
      if (this.startDay && this.endDay) {
        console.log(this.startDay.format(this.format) + ' - ' + this.endDay.format(this.format));
      } else if (this.startDay) {
        console.log(this.startDay.format(this.format) + ' - null');
      } else if( this.endDay) {
        console.log('null' + ' - ' + this.endDay.format(this.format));
      }
    },

    /**
     * Initialize the Calendar.
     *
     * @return {void}
     */
    initialize: function() {
      // Store this object in "pricing-calendar" element data.
      this.$el.data('pricing-calendar', this);

      // Trigger date-range handler.
      this.$el.find('.abkngcal__day')
        .on('mousedown', this._clickDay.bind(this))
        .on('mouseenter', this._hoverDay.bind(this))
        .on('mouseover', this.ui.hoverHeadingOver.bind(this))
        .on('mouseleave', this.ui.hoverHeadingLeave.bind(this));

      // Trigger UI handler.
      this.on('set:endDay', this.ui.onSetEndDay.bind(this))
        .on('set:startDay', this.ui.onSetStartDay.bind(this))
        .on('clear:endDay', this.ui.onClearEndDay.bind(this))
        .on('clear:startDay', this.ui.onClearStartDay.bind(this));

      $(document).on('click', this._documentClick.bind(this));
    },

    /**
     * Destroy the Calendar.
     *
     * @return {void}
     */
    destroy: function() {
      this.clearStartDay();
      this.off();

      this.$el.find('.abkngcal__day').off();
      this.$el.removeData();
    },

    getNights: function() {
      if (! this.endDay || ! this.startDay) {
        return 0;
      }

      return this.endDay.diff(this.startDay, 'days') + 1;
    },

    getElementDay: function(date) {
      if (typeof date === 'object') {
        date = date.format(this.format);
      }

      return this.$el.find('.abkngcal__day[data-date="' + date + '"]');
    },

    setStartDay: function(day) {
      if (typeof day === 'string') {
        day = moment(day, this.format);
      }

      this.startDay = day.startOf('day');
      this.trigger('set:startDay', this.startDay, this.getElementDay(this.startDay));

      if (this.options.debug) {
        this.debug();
      }
    },

    setEndDay: function(day) {
      if (typeof day === 'string') {
        day = moment(day, this.format);
      }

      this.endDay = day.endOf('day');
      this.trigger('set:endDay', this.endDay, this.getElementDay(this.endDay));

      if (this.startDay) {
        this.ui.buildRangeDays.call(this);
        this.trigger('apply', this);
      }

      if (this.options.debug) {
        this.debug();
      }
    },

    clearStartDay: function() {
      this.endDay = null;
      this.startDay = null;
      this.trigger('clear:startDay');
    },

    clearEndDay: function() {
      this.endDay = null;
      this.trigger('clear:endDay');
    },

    _clickDay: function(e) {
      var $target = $(e.currentTarget);
      var targetDay = moment($target.data('date'));

      // Picking start date in first time.
      if (!this.startDay && !this.endDay) {
        this.setStartDay(targetDay.clone());
        return;
      }

      // If target-day is less than current start-day,
      // we'll reset and force start-day is new targetDay.
      if (this.endDay || targetDay.isBefore(this.startDay, 'day')) {
        this.clearEndDay();
        this.setStartDay(targetDay.clone());
        return;
      }

      this.setEndDay(targetDay.clone());
    },

    _hoverDay: function(e) {
      if (! this.startDay || this.endDay) {
        return;
      }

      var $currentTarget = $(e.currentTarget);
      var targetDay = moment($currentTarget.data('date'));

      this.ui.buildRangeDays.call(this, targetDay, $currentTarget);
    },

    _documentClick: function(e) {
      if (! $.contains(this.$el[0], e.target) && ! this.keepRange) {
        this.clearStartDay();
      }
    },

    ui: {
      onSetStartDay: function(startDay, $el) {
        this.$el.find('.abkngcal__day').removeClass('range-start');
        $el.addClass('range-start');
      },

      onSetEndDay: function(endDay, $el) {
        this.$el.find('.abkngcal__day').removeClass('range-end');
        $el.addClass('range-end');
      },

      onClearStartDay: function() {
        this.$el.find('.abkngcal__day')
          .removeClass('range-start')
          .removeClass('range-end')
          .removeClass('in-range')
          .removeClass('in-hover');
      },

      onClearEndDay: function() {
        this.$el.find('.abkngcal__day')
          .removeClass('range-end')
          .removeClass('in-range')
          .removeClass('in-hover');
      },

      // UI hover heading
      hoverHeadingOver: function(e) {
        var $target = $(e.currentTarget);

        this.$table.find('.abkngcal__day-heading[data-day="' + $target.data('day') + '"]').addClass('hover');
        $target.closest('tr').find('.abkngcal__month-heading[data-month="' +  $target.data('month') + '"]').addClass('hover');
      },

      hoverHeadingLeave: function(e) {
        var selector = '.abkngcal__day-heading.hover, .abkngcal__month-heading.hover';
        this.$table.find(selector).removeClass('hover');
      },

      buildRangeDays: function(targetDay, $currentTarget) {
        var endDay = this.endDay;
        var startDay  = this.startDay;

        this.$el.find('.abkngcal__day').each(function(index, el) {
          var dt = moment($(el).data('date'));
          var $el = $(el);

          if ((endDay && startDay && dt.isBetween(startDay, endDay, 'day')) ||
              (targetDay && dt.isBetween(startDay, targetDay, 'day'))) {
            $el.addClass('in-range');
          } else {
            $el.removeClass('in-range');
          }

          $el.removeClass('in-hover');
        });

        if (targetDay && $currentTarget && ! startDay.isSameOrAfter(targetDay, 'day')) {
          $currentTarget.addClass('in-hover');
        }
      },
    }
  });

  window.PricingCalendar = PricingCalendar;
})(jQuery, Backbone);
