;(function($, Backbone) {
  'use strict';

  /**
   * AweBooking Yearly Calendar.
   * Handler searching, manager-availablity for a single room.
   *
   * @param {string} element Selector element.
   * @param {object} options Optional for the calendar options.
   */
  var YearlyCalendar = function(element, options) {
    this.$el = $(element);

    this.options = $.extend(options, {
      debug: false,
      rangePicker: false,
    });

    // Do not change this format,
    // We only using ISO-8601 date format for all of the process.
    this.format = 'YYYY-MM-DD';

    this.endDay = null;
    this.startDay = null;

    // Initialize the calendar.
    this.initialize();
  };

  $.extend(YearlyCalendar.prototype, Backbone.Events, {
    /**
     * Initialize the Calendar.
     *
     * @return {void}
     */
    initialize: function() {
      // Store this object in "yearly-calendar" element data.
      this.$el.data('yearly-calendar', this);

      // Setup the range datepicker if enable.
      if (this.options.rangePicker) {
        var $picker = this.$el.parent().find('.daterange').daterangepicker({
          autoApply: true,
          linkedCalendars: true,
          alwaysShowCalendars: true,
          showCustomRangeLabel: false,
          locale: {
            format: this.format
          }
        });

        this.$picker = $picker;
        this.picker  = this.$picker.data('daterangepicker');

        // Handler sync between calendar and picker.
        this.on('apply', this.syncPickerWithCalendar.bind(this));
        this.$picker.on('apply.daterangepicker', this.syncCalendarWithPicker.bind(this));
      }

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
        .on('apply', this.ui.showComments.bind(this));

      // ...
      var self = this;
      var $container = this.$el.parents('.abkngcal-container');
      var room_id = $container.data('room');

      this.$el.parent().find('.button').on('click', function(e) {
        e.preventDefault();

        var currentState = $(this).parent().find('input[name="state"]:checked').val();
        console.log(currentState);

        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'awebooking/set_event',
            start: self.startDay.format(self.format),
            end: self.endDay.format(self.format),
            room_id: room_id,
            state: currentState,
          },
        })
        .done(function() {
          self.ui.testReload(room_id, self.$el.find('select').val(), $container);
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
      });
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

      if ($target.hasClass('abkngcal__day--past')) {
        // return;
      }

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

      // Set end-day with current targetDay.
      if (! targetDay.isSame(this.startDay, 'day')) {
        this.setEndDay(targetDay.clone());
      }
    },

    hoverDay: function(e) {
      if (! this.startDay || this.endDay) {
        return;
      }

      var $currentTarget = $(e.currentTarget);
      var targetDay = moment($currentTarget.data('date'));

      this.ui.buildRangeDays.call(this, targetDay, $currentTarget);
    },

    syncPickerWithCalendar: function(self) {
      this.picker.setEndDate(self.endDay);
      this.picker.setStartDate(self.startDay);
    },

    syncCalendarWithPicker: function(e, picker) {
      this.setStartDay(picker.startDate);
      this.setEndDay(picker.endDate);
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

      showComments: function() {
        var $text = this.$el.parent().find('.datepicker-container').find('.write-here');
        var nights = this.endDay.diff(this.startDay, 'days');
        var text = '';

        var thatNight = this.endDay.clone().subtract(1, 'day');

        if (nights === 1) {
          text = 'One night: ' + thatNight.format(this.format);
        } else {
          text = nights + ' nights, from  ' + this.startDay.format(this.format) + ' to ' + thatNight.format(this.format) + ' night.';
        }

        $text.html(text);

        this.$el.parent().find('input[name="state"]').prop('disabled', false);
      },

      testReload: function(room_id, requestYear, $container) {

        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'awebooking/get_yearly_calendar',
            year: requestYear,
            room: room_id,
          }
        })
        .done(function(response) {
          var calendar = $container.find('.abkngcal.abkngcal--yearly').data('yearly-calendar');

          calendar.destroy();

          $container.html($(response).html());
          calendar.$el = $container.find('.abkngcal.abkngcal--yearly');

          calendar.initialize();
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });

      },
    }
  });

  $(function() {

    window.YearlyCalendar = YearlyCalendar;

    $('.abkngcal.abkngcal--yearly', document).each(function(index, el) {
      new YearlyCalendar(el);
    });

    // Binding ajax handler.
    $( document).on('change', '.abkngcal select', function() {
      var $this = $(this);
      var requestYear = $(this).val();
      var $container = $this.parents('.abkngcal-container');

      $container.find('.abkngcal-ajax-loading').show();
      var room_id = $container.data('room');

      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'awebooking/get_yearly_calendar',
          year: requestYear,
          room: room_id,
        }
      })
      .done(function(response) {
        var calendar = $container.find('.abkngcal.abkngcal--yearly').data('yearly-calendar');

        calendar.destroy();

        $container.html($(response).html());
        calendar.$el = $container.find('.abkngcal.abkngcal--yearly');

        calendar.initialize();
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
    });

  });

})(jQuery, Backbone);
