(function () {
  'use strict';

  function _typeof(obj) {
    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
      _typeof = function (obj) {
        return typeof obj;
      };
    } else {
      _typeof = function (obj) {
        return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
      };
    }

    return _typeof(obj);
  }

  (function ($, flatpickr, moment) {

    var COLUMN_WIDTH = 60;
    var DATE_FORMAT = 'YYYY-MM-DD';
    var Selection = Backbone.Model.extend({
      defaults: {
        endDate: null,
        startDate: null,
        calendar: null
      },
      clear: function clear() {
        this.clearSelectedDate(null);
      },
      isValid: function isValid() {
        if (!this.has('calendar') || this.get('calendar') < 1) {
          return false;
        }

        return this.getNights() >= 0;
      },
      clearSelectedDate: function clearSelectedDate(calendar) {
        this.set({
          startDate: null,
          endDate: null
        });
        this.set('calendar', calendar);
        this.trigger('clear_dates');
      },
      getNights: function getNights() {
        if (!this.has('endDate') || !this.has('startDate')) {
          return -1;
        }

        return this.get('endDate').diff(this.get('startDate'), 'days');
      }
    });
    var ScheduleCalendar = Backbone.View.extend({
      options: {
        debug: false,
        marker: '.scheduler__marker',
        popper: '.scheduler__popper > .scheduler__actions',
        granularity: 'nightly' // 'daily'

      },
      events: {
        'click      .scheduler__body .scheduler__date': 'setSelectionDate',
        'mouseenter .scheduler__body .scheduler__date': 'drawMarkerOnHover'
      },
      initialize: function initialize(options) {
        this.options = _.defaults(options || {}, this.options);
        this.model = new Selection();
        var testColumnWith = this.$el.find('.scheduler__column').first().outerWidth();

        if (testColumnWith > COLUMN_WIDTH) {
          COLUMN_WIDTH = testColumnWith;
        } // Handle click action before setup popper.


        this.$el.find('.scheduler__actions [data-schedule-action]').on('click', this.handleClickAction.bind(this));
        this.$marker = this.$el.find(this.options.marker);
        this.$marker.hide();
        this.setupPopper();
        this.popper = this.$marker[0]._tippy;

        if (window.Waypoint) {
          this.setupLabelAnimate();
        }

        $(document).on('keyup', this.onKeyup.bind(this));
        this.listenTo(this.model, 'change:startDate change:endDate', this.setMarkerPosition);
        this.listenTo(this.model, 'clear_dates', this.onClearSelectedDates);

        if (this.options.debug) {
          this.listenTo(this.model, 'change', this.debug);
        }

        this.$el.data('scheduler', this);
      },
      debug: function debug() {
        if (this.model.has('startDate') && this.model.has('endDate')) {
          console.log(this.model.get('calendar'), this.model.get('startDate').format(DATE_FORMAT) + ' - ' + this.model.get('endDate').format(DATE_FORMAT));
        } else if (this.model.has('startDate')) {
          console.log(this.model.get('calendar'), this.model.get('startDate').format(DATE_FORMAT) + ' - null');
        } else if (this.model.has('endDate')) {
          console.log(this.model.get('calendar'), 'null' + ' - ' + this.model.get('endDate').format(DATE_FORMAT));
        } else {
          console.log('null - null');
        }
      },
      onKeyup: function onKeyup(e) {
        if (e.keyCode == 27) {
          this.model.clearSelectedDate();
        }
      },
      handleClickAction: function handleClickAction(e) {
        e.preventDefault();
        var $targetLink = $(e.currentTarget);

        if (!this.model.isValid()) {
          return false;
        }

        if (!$targetLink.data('scheduleAction')) {
          return false;
        }

        this.trigger('action:' + $targetLink.data('scheduleAction'), e, this.model);
      },
      onClearSelectedDates: function onClearSelectedDates() {
        this.popper.hide();
        this.$marker.find('span').text(0);
        this.trigger('clear');
      },
      setSelectionDate: function setSelectionDate(e) {
        var $target = $(e.currentTarget);
        var setUnit = this.getUnitByElement($target);
        var clickDate = moment($target.data('date'));

        if (this.model.has('startDate') && this.model.has('endDate')) {
          this.model.clearSelectedDate(setUnit);
          return;
        }

        if (this.model.has('calendar') && setUnit !== this.model.get('calendar') || this.model.has('startDate') && clickDate.isBefore(this.model.get('startDate'), 'day')) {
          this.model.clearSelectedDate(setUnit);
        } // Set the start date first.


        if (!this.model.has('startDate') && !this.model.has('endDate')) {
          this.model.set('calendar', setUnit);
          this.model.set('startDate', clickDate.clone());
          this.drawMarkerOnHover(e);
          return;
        } // Require 1 night for granularity by nightly.


        if ('nightly' == this.options.granularity && clickDate.diff(this.model.get('startDate'), 'days') < 1) {
          return;
        }

        this.popper.show();
        this.model.set('endDate', clickDate.clone());
        this.trigger('apply', this.model, this);
      },
      setMarkerPosition: function setMarkerPosition() {
        var endDate = this.model.get('endDate');
        var startDate = this.model.get('startDate');

        if (_.isNull(startDate) && _.isNull(endDate)) {
          this.$marker.css('width', COLUMN_WIDTH).hide();
          return;
        }

        var $startDateEl = this.getElementByDate(this.model.get('calendar'), startDate);

        if (_.isNull(endDate)) {
          var position = this.getCellPossiton($startDateEl);
          this.$marker.show().css({
            top: position.top,
            left: position.left
          });
        } else {
          var $endDateEl = this.getElementByDate(this.model.get('calendar'), endDate);
          this.$marker.css('width', ($endDateEl.index() - $startDateEl.index() + 1) * COLUMN_WIDTH);
        }
      },
      drawMarkerOnHover: function drawMarkerOnHover(e) {
        var $target = $(e.currentTarget);
        var targetUnit = this.getUnitByElement($target);

        if (!this.model.has('calendar') || this.model.get('calendar') !== targetUnit || !this.model.has('startDate') || this.model.has('startDate') && this.model.has('endDate')) {
          return;
        }

        var hoverDate = moment($target.data('date'));
        var startDate = this.model.get('startDate');

        if (startDate.isSameOrBefore(hoverDate, 'day')) {
          var $startDateEl = this.getElementByDate(targetUnit, startDate);
          var days = $target.index() - $startDateEl.index() + 1;
          this.$marker.css('width', days * COLUMN_WIDTH);
          this.$marker.find('span').text('daily' == this.options.granularity ? days : days - 1);
        }
      },
      getElementByDate: function getElementByDate(calendar, date) {
        if (_typeof(date) === 'object') {
          date = date.format(DATE_FORMAT);
        }

        return this.$el.find('[data-calendar="' + calendar + '"]').find('.scheduler__date[data-date="' + date + '"]');
      },
      getUnitByElement: function getUnitByElement(element) {
        var calendar = $(element).data('calendar');

        if (typeof calendar === 'undefined') {
          calendar = $(element).closest('[data-calendar]').data('calendar');
        }

        calendar = parseInt(calendar, 10);
        return !isNaN(calendar) ? calendar : 0;
      },
      getCellPossiton: function getCellPossiton(element) {
        var childPos = element.offset();
        var parentPos = this.$el.find('.scheduler__body').offset();
        return {
          top: childPos.top - parentPos.top,
          left: childPos.left - parentPos.left
        };
      },

      /**
       * Setup the Popper.
       *
       * @return {void}
       */
      setupPopper: function setupPopper() {
        tippy(this.$marker[0], {
          html: this.$el.find(this.options.popper)[0],
          theme: 'abrs-tippy',
          arrow: true,
          distance: 0,
          trigger: 'manual',
          placement: 'bottom',
          hideOnClick: 'persistent',
          interactive: true,
          performance: true,
          animation: 'shift-away',
          duration: [150, 150],
          popperOptions: {
            modifiers: {
              hide: {
                enabled: false
              },
              preventOverflow: {
                enabled: false
              }
            }
          }
        });
      },

      /**
       * Setup the label animate scroll.
       *
       * @return {void}
       */
      setupLabelAnimate: function setupLabelAnimate() {
        var self = this;
        var $mainContext = self.$el.find('.scheduler__main')[0];

        var onHandler = function onHandler(direction) {
          var $mainLabel = self.$el.find('.scheduler__aside .scheduler__month-label');
          var $currentLabel = $(this.element);

          if ('right' === direction) {
            $currentLabel.attr('data-prev-text', $mainLabel.text());
            $mainLabel.text($currentLabel.text());
          } else {
            $mainLabel.text($currentLabel.data('prevText'));
            $currentLabel.attr('data-prev-text', '');
          }
        };

        self.$el.find('.scheduler__month-label').each(function () {
          new Waypoint({
            element: this,
            context: $mainContext,
            offset: -50,
            horizontal: true,
            handler: onHandler
          });
        });
      }
    });
    window.ScheduleCalendar = ScheduleCalendar;
  })(jQuery, window.flatpickr, window.moment);

}());

//# sourceMappingURL=schedule-calendar.js.map
