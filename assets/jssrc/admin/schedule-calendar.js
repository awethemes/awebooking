(function($, Popper, moment) {
  'use strict';

  const DATE_FORMAT  = 'YYYY-MM-DD';
  const COLUMN_WIDTH = 60;

  const Selection = Backbone.Model.extend({
    defaults: {
      unit: null,
      endDate: null,
      startDate: null,
    },

    clearSelectedDate(newUnit) {
      this.set({ startDate: null, endDate: null });
      this.set('unit', newUnit);
    },

    getNights() {
      if (! this.has('endDate') || ! this.has('startDate')) {
        return 0;
      }

      return this.get('endDate').diff(this.get('startDate'), 'days');
    }
  });

  const ScheduleCalendar = Backbone.View.extend({
    options: {
      debug: true,
      marker: '.awebooking-schedule__marker',
      popper: '.awebooking-schedule_popper',
    },

    events: {
      'click .awebooking-schedule__day': 'setSelectionDate',
      'mouseenter .awebooking-schedule__day': 'drawMarkerOnHover',
    },

    initialize() {
      this.model = new Selection;

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

      $(document).on('keyup', this.keyup.bind(this));
      // $(document).off('keyup', this.keyup);

      this.listenTo(this.model, 'change:startDate change:endDate', this.setMarkerPosition);

      if (this.options.debug) {
        this.listenTo(this.model, 'change', this.debug);
      }

      this.$el.data('schedule-calendar', this);
    },

    debug () {
      if (this.model.has('startDate') && this.model.has('endDate')) {
        console.log(this.model.get('calendar'), this.model.get('startDate').format(DATE_FORMAT) + ' - ' + this.model.get('endDate').format(DATE_FORMAT));
      } else if (this.model.has('startDate')) {
        console.log(this.model.get('calendar'), this.model.get('startDate').format(DATE_FORMAT) + ' - null');
      } else if(this.model.has('endDate')) {
        console.log(this.model.get('calendar'), 'null' + ' - ' + this.model.get('endDate').format(DATE_FORMAT));
      }
    },

    keyup(e) {
      if (e.keyCode == 27) {
        this.model.clearSelectedDate();
        this.$popper.hide();
      }
    },

    setSelectionDate(e) {
      const $target = $(e.currentTarget);
      const setUnit = this.getUnitByElement($target);
      const clickDate = moment($target.data('date'));

      console.log(e);

      if (this.model.has('calendar') && setUnit !== this.model.get('calendar')
          || this.model.has('startDate') && this.model.has('endDate')
          || this.model.has('startDate') && clickDate.isBefore(this.model.get('startDate'), 'day')) {
        this.model.clearSelectedDate(setUnit);
        this.$popper.hide();
      }

      if (!this.model.has('startDate') && !this.model.has('endDate')) {
        this.model.set('calendar', setUnit);
        this.model.set('startDate', clickDate.clone());
      } else {
        this.model.set('endDate', clickDate.clone());

        this.$popper.show();
        this.popper.update();

        this.trigger('apply', this.model, this);
      }
    },

    setMarkerPosition() {
      const endDate = this.model.get('endDate');
      const startDate = this.model.get('startDate');

      if (_.isNull(startDate) && _.isNull(endDate)) {
        this.$marker.css('width', 60).hide();
        return;
      }

      const $startDateEl = this.getElementByDate(this.model.get('calendar'), startDate);
      if (_.isNull(endDate)) {
        const position = this.getCellPossiton($startDateEl);
        this.$marker.show().css({ top: position.top, left: position.left });
      } else {
        const $endDateEl = this.getElementByDate(this.model.get('calendar'), endDate);
        this.$marker.css('width', ($endDateEl.index() - $startDateEl.index() + 1) * 60);
      }
    },

    drawMarkerOnHover(e) {
      const $target = $(e.currentTarget);
      const targetUnit = this.getUnitByElement($target);

      if (!this.model.has('calendar')
        || this.model.get('calendar') !== targetUnit
        || !this.model.has('startDate')
        || this.model.has('startDate') && this.model.has('endDate')) {
        return;
      }

      const hoverDate = moment($target.data('date'));
      const startDate = this.model.get('startDate');

      if (startDate.isSameOrBefore(hoverDate, 'day')) {
        const $startDateEl = this.getElementByDate(targetUnit, startDate);
        const nights = ($target.index() - $startDateEl.index() + 1);

        this.$marker.css('width', nights * COLUMN_WIDTH);
        this.$marker.find('span').text(nights);
      }
    },

    getElementByDate(calendar, date) {
      if (typeof date === 'object') {
        date = date.format(DATE_FORMAT);
      }

      return this.$el
        .find('[data-calendar="' + calendar + '"]')
        .find('.awebooking-schedule__day[data-date="' + date + '"]');
    },

    getUnitByElement(element) {
      let calendar = $(element).data('calendar');

      if (typeof calendar === 'undefined') {
        calendar = $(element).closest('[data-calendar]').data('calendar');
      }

      calendar = parseInt(calendar, 10);
      return ! isNaN(calendar) ? calendar : 0;
    },

    getCellPossiton(element) {
      var childPos = element.offset();
      var parentPos = this.$el.find('.awebooking-schedule__body').offset();

      return {
        top: childPos.top - parentPos.top,
        left: childPos.left - parentPos.left
      };
    }
  });

  new ScheduleCalendar({
    el: '.awebooking-schedule'
  });

})(jQuery, TheAweBooking.Popper, TheAweBooking.momment || window.moment);
