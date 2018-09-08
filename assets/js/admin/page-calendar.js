(function ($,plugin) {
  'use strict';

  $ = $ && $.hasOwnProperty('default') ? $['default'] : $;
  plugin = plugin && plugin.hasOwnProperty('default') ? plugin['default'] : plugin;

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

  var DATE_FORMAT = 'YYYY-MM-DD';

  var BookingScheduler =
  /*#__PURE__*/
  function () {
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
      key: "handleClearSelected",
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
      key: "handleBlockRoom",
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
      key: "handleUnblockRoom",
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
      key: "compileHtmlControls",
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
      key: "setupEventPopper",
      value: function setupEventPopper(el) {
        var $html = $(el).find('.js-tippy-html');
        tippy(el, {
          theme: 'booking-popup',
          delay: 150,
          arrow: true,
          distance: 0,
          maxWidth: '500px',
          placement: 'bottom',
          trigger: 'mouseenter focus',
          interactive: true,
          performance: true,
          hideOnClick: false,
          animation: 'shift-toward',
          duration: [150, 150],
          html: $html.length ? $html[0] : false,
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
        return el._tippy;
      }
      /**
       * Handle bulk update action.
       */

    }, {
      key: "initBulkUpdate",
      value: function initBulkUpdate() {
        var $dialog = plugin.dialog('#bulk-update-dialog');
        $('.js-open-bulk-update').on('click', function (e) {
          e.preventDefault();
          $dialog.dialog('open');
        });
        flatpickr('#bulk_date_start', {
          mode: 'range',
          dateFormat: 'Y-m-d',
          showMonths: plugin.isMobile() ? 1 : 2,
          plugins: [new plugin.utils.flatpickrRangePlugin({
            input: '#bulk_date_end'
          })]
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

}(jQuery,window.awebooking));

//# sourceMappingURL=page-calendar.js.map
