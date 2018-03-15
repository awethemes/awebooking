webpackJsonp([2],{

/***/ 29:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(30);


/***/ }),

/***/ 30:
/***/ (function(module, exports) {

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

(function ($, Popper, Flatpickr, moment) {
  'use strict';

  var DATE_FORMAT = 'YYYY-MM-DD';
  var COLUMN_WIDTH = 60;

  var awebooking = window.TheAweBooking;

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
      this.set({ startDate: null, endDate: null });

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
      popper: '.scheduler__popper',
      datepicker: '.scheduler__datepicker > input'
    },

    events: {
      'click      .scheduler__body .scheduler__date': 'setSelectionDate',
      'mouseenter .scheduler__body .scheduler__date': 'drawMarkerOnHover',
      'click      .scheduler__actions [data-schedule-action]': 'triggerClickAction'
    },

    initialize: function initialize() {
      var _this = this;

      this.model = new Selection();

      this.$marker = this.$el.find(this.options.marker);
      this.$marker.hide();

      this.$popper = this.$el.find(this.options.popper);
      this.$popper.hide();

      this.popper = new Popper(this.$marker, this.$popper, {
        placement: 'bottom',
        modifiers: {
          flip: { enabled: false },
          hide: { enabled: false },
          preventOverflow: { enabled: false }
        }
      });

      this.datepicker = Flatpickr(this.options.datepicker, {
        altInput: true,
        dateFormat: awebooking.date_format,
        altFormat: awebooking.date_alt_format,
        position: 'below',
        onChange: function onChange(dates, date, flatpickr) {
          _this.trigger('pickdate', date, dates, flatpickr);
        }
      });

      this.setupLabelAnimate();

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


    triggerClickAction: function triggerClickAction(e) {
      e.preventDefault();

      var model = this.model;
      if (!model.isValid()) {
        return false;
      }

      var $targetLink = $(e.currentTarget);
      if (!$targetLink.data('scheduleAction')) {
        return false;
      }

      this.trigger('action:' + $targetLink.data('scheduleAction'), e, model);
    },

    onClearSelectedDates: function onClearSelectedDates() {
      this.$popper.hide();
      this.$marker.find('span').text('1');

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
      }

      if (!this.model.has('startDate') && !this.model.has('endDate')) {
        this.model.set('calendar', setUnit);
        this.model.set('startDate', clickDate.clone());
      } else {
        this.model.set('endDate', clickDate.clone());

        this.popper.update();
        this.$popper.show();

        this.trigger('apply', this.model, this);
      }
    },
    setMarkerPosition: function setMarkerPosition() {
      var endDate = this.model.get('endDate');
      var startDate = this.model.get('startDate');

      if (_.isNull(startDate) && _.isNull(endDate)) {
        this.$marker.css('width', 60).hide();
        return;
      }

      var $startDateEl = this.getElementByDate(this.model.get('calendar'), startDate);

      if (_.isNull(endDate)) {
        var position = this.getCellPossiton($startDateEl);
        this.$marker.show().css({ top: position.top, left: position.left });
      } else {
        var $endDateEl = this.getElementByDate(this.model.get('calendar'), endDate);
        this.$marker.css('width', ($endDateEl.index() - $startDateEl.index() + 1) * 60);
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
        this.$marker.find('span').text(days - 1);
      }
    },
    getElementByDate: function getElementByDate(calendar, date) {
      if ((typeof date === 'undefined' ? 'undefined' : _typeof(date)) === 'object') {
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

  TheAweBooking.ScheduleCalendar = ScheduleCalendar;
})(jQuery, TheAweBooking.Popper, TheAweBooking.Flatpickr, TheAweBooking.moment || window.moment);

/***/ })

},[29]);