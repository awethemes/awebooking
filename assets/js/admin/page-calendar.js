(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

(function ($, plugin) {
  'use strict';

  var DATE_FORMAT = 'YYYY-MM-DD';

  var BookingScheduler = function () {
    /**
     * Constructor.
     *
     * @return {void}
     */
    function BookingScheduler() {
      _classCallCheck(this, BookingScheduler);

      var self = this;

      this.initBulkUpdate();

      this.scheduler = new ScheduleCalendar({
        el: '.scheduler',
        debug: plugin.debug,
        granularity: 'nightly'
      });

      this.scheduler.on('clear', this.handleClearSelected.bind(this));
      this.scheduler.on('action:block', this.handleBlockRoom.bind(this));
      this.scheduler.on('action:unblock', this.handleUnblockRoom.bind(this));

      $('.js-unlock-period').on('click', function (e) {
        e.preventDefault();
        var $el = $(this);

        self.scheduler.model.set('calendar', $el.data('room'));
        self.scheduler.model.set('startDate', moment($el.data('startDate')));
        self.scheduler.model.set('endDate', moment($el.data('endDate')));

        self.scheduler.trigger('action:unblock');
      });

      $('.scheduler__state-event, .scheduler__booking-event').each(function () {
        self.setupEventPopper(this);
      });
    }

    /**
     * Handle on clear selected.
     *
     * @return {void}
     */


    _createClass(BookingScheduler, [{
      key: 'handleClearSelected',
      value: function handleClearSelected() {
        window.swal && swal.close();
      }

      /**
       * Handle set price action.
       *
       * @param  {Event}  e
       * @param  {Object} model
       * @return {void}
       */

    }, {
      key: 'handleBlockRoom',
      value: function handleBlockRoom(e, model) {
        var _this = this;

        plugin.confirm(plugin.i18n.warning, function () {
          var $controls = _this.compileHtmlControls('block', model);
          $controls.closest('form').submit();
        });
      }

      /**
       * Handle reset price action.
       *
       * @param  {Event}  e
       * @param  {Object} model
       * @return {void}
       */

    }, {
      key: 'handleUnblockRoom',
      value: function handleUnblockRoom(e, model) {
        var _this2 = this;

        plugin.confirm(plugin.i18n.warning, function () {
          var $controls = _this2.compileHtmlControls('unblock', model);
          $controls.closest('form').submit();
        });
      }

      /**
       * Compile html form controls.
       *
       * @param  {string} action
       * @return {void}
       */

    }, {
      key: 'compileHtmlControls',
      value: function compileHtmlControls(action, model) {
        var template = wp.template('scheduler-pricing-controls');

        if (!model) {
          model = this.scheduler.model;
        }

        var data = {
          action: action,
          endDate: model.get('endDate').format(DATE_FORMAT),
          startDate: model.get('startDate').format(DATE_FORMAT),
          calendar: model.get('calendar')
        };

        return $('#js-scheduler-form-controls').html(template(data));
      }

      /**
       * Setup event popper.
       *
       * @param  {Object} el
       * @return {void}
       */

    }, {
      key: 'setupEventPopper',
      value: function setupEventPopper(el) {
        var $html = $(el).find('.js-tippy-html');

        tippy(el, {
          theme: 'abrs-tippy',
          delay: 150,
          arrow: true,
          distance: 0,
          maxWidth: '500px',
          placement: 'top',
          trigger: 'mouseenter focus',
          interactive: true,
          performance: true,
          hideOnClick: false,
          animation: 'shift-toward',
          duration: [150, 150],
          html: $html.length ? $html[0] : false
        });

        return el._tippy;
      }

      /**
       * Handle bulk update action.
       */

    }, {
      key: 'initBulkUpdate',
      value: function initBulkUpdate() {
        var $dialog = plugin.dialog('#bulk-update-dialog');

        $('.js-open-bulk-update').on('click', function (e) {
          e.preventDefault();
          $dialog.dialog('open');
        });

        flatpickr('#bulk_date_start', {
          dateFormat: 'Y-m-d',
          plugins: [new rangePlugin({ input: '#bulk_date_end' })]
        });
      }
    }]);

    return BookingScheduler;
  }();

  /**
   * Document ready!
   *
   * @return {void}
   */


  $(function () {
    new BookingScheduler();
  });
})(jQuery, window.awebooking);

},{}]},{},[1]);

//# sourceMappingURL=page-calendar.js.map
