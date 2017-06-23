;(function($, Backbone) {
  'use strict';

  var PricingCalendar = function(element, options) {
    this.$el = $(element);

    this.options = $.extend(options, {
      debug: true
    });

    // Do not change this format,
    // We only using ISO-8601 date format for all of the process.
    this.format = 'YYYY-MM-DD';

    this.endDay = null;
    this.startDay = null;

    // Initialize the calendar.
    this.initialize();
  };

  $.extend(PricingCalendar.prototype, Backbone.Events, {
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
        .on('mousedown', this.clickDay.bind(this))
        .on('mouseenter', this.hoverDay.bind(this))
        .on('mouseover', this.ui.hoverHeadingOver.bind(this))
        .on('mouseleave', this.ui.hoverHeadingLeave.bind(this));

      // Trigger UI handler.
      this.on('set:endDay', this.ui.onSetEndDay.bind(this))
        .on('set:startDay', this.ui.onSetStartDay.bind(this))
        .on('clear:endDay', this.ui.onClearEndDay.bind(this))
        .on('clear:startDay', this.ui.onClearEndDay.bind(this))
        .on('apply', this.toggleModal.bind(this));

      var self = this;
      $('.media-modal-close').on('click', function() {
        $(this).parents('.pricing-calendar-modal').hide();
        self.clearStartDay();
      })
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

      if (this.options.rangePicker) {
        this.picker.remove();
        this.$picker = null;
      }
    },

    debug: function() {
      if (this.startDay && this.endDay) {
        console.log(this.startDay.format(this.format) + ' - ' + this.endDay.format(this.format));
      } else if (this.startDay) {
        console.log(this.startDay.format(this.format) + ' - null');
      } else if( this.endDay) {
        console.log('null' + ' - ' + this.endDay.format(this.format));
      }
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

    clickDay: function(e) {
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

    hoverDay: function(e) {
      if (! this.startDay || this.endDay) {
        return;
      }

      var $currentTarget = $(e.currentTarget);
      var targetDay = moment($currentTarget.data('date'));

      this.ui.buildRangeDays.call(this, targetDay, $currentTarget);
    },

    toggleModal: function() {
      var $modal = $('.pricing-calendar-modal', document);
      var template = wp.template('pricing-calendar-form');

      this.data_id = this.$el.closest('.abkngcal-container').data('roomType');

      $modal.find('.media-modal-content').html(template(this));
      $modal.toggle();

      $('#pricing-calendar-form', document).on('submit', function(e) {
        // e.preventDefault();
      });

    },

    showComments: function() {
      var nights = this.endDay.diff(this.startDay, 'days') + 1;
      var text = '';

      var thatNight = this.endDay;

      if (nights === 1) {
        text = 'One night: ' + thatNight.format(this.format);
      } else {
        text = nights + ' nights, from <b>' + this.startDay.format(this.format) + '</b> to <b>' + thatNight.format(this.format) + '</b> night.';
      }

      return text;
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
          .removeClass('in-range');
      },

      onClearEndDay: function() {
        this.$el.find('.abkngcal__day')
          .removeClass('range-end')
          .removeClass('in-range');
      },

      // UI hover heading
      hoverHeadingOver: function(e) {
        var $target  = $(e.currentTarget);
        var selector = '.abkngcal__day-heading[data-day="' + $target.data('day') + '"],' +
          '.abkngcal__month-heading[data-month="' +  $target.data('month') + '"]';

        this.$el.find(selector).addClass('hover');
      },

      hoverHeadingLeave: function(e) {
        var selector = '.abkngcal__day-heading.hover, .abkngcal__month-heading.hover';
        this.$el.find(selector).removeClass('hover');
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

  $(function() {

    window.PricingCalendar = PricingCalendar;

    $('.abkngcal--pricing-calendar', document).each(function(index, el) {
      new PricingCalendar(el);
    });

  });

})(jQuery, Backbone);
