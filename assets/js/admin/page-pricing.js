(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

(function ($, plugin) {
  'use strict';

  var DATE_FORMAT = 'YYYY-MM-DD';

  var PricingScheduler = function () {
    /**
     * Constructor.
     *
     * @return {void}
     */
    function PricingScheduler() {
      _classCallCheck(this, PricingScheduler);

      this.flatpickr = null;

      this.scheduler = new ScheduleCalendar({
        el: '.scheduler',
        debug: plugin.debug,
        granularity: 'daily'
      });

      this.$dialog = plugin.dialog('#scheduler-form-dialog');

      this.scheduler.on('clear', this.handleClearSelected.bind(this));
      this.scheduler.on('action:set-price', this.handleSetPrice.bind(this));
      this.scheduler.on('action:reset-price', this.handleResetPrice.bind(this));

      this.initBulkUpdate();
    }

    /**
     * Handle on clear selected.
     *
     * @return {void}
     */


    _createClass(PricingScheduler, [{
      key: 'handleClearSelected',
      value: function handleClearSelected() {
        window.swal && swal.close();

        if (this.flatpickr) {
          this.flatpickr.destroy();
        }

        this.$dialog.dialog('close');
        $('#js-scheduler-form-controls').html('');
      }

      /**
       * Handle set price action.
       *
       * @param  {Event}  e
       * @param  {Object} model
       * @return {void}
       */

    }, {
      key: 'handleSetPrice',
      value: function handleSetPrice(e, model) {
        window.swal && swal.close();

        this.compileHtmlControls('set_price', 0);

        this.$dialog.dialog('open');
      }

      /**
       * Handle reset price action.
       *
       * @param  {Event}  e
       * @param  {Object} model
       * @return {void}
       */

    }, {
      key: 'handleResetPrice',
      value: function handleResetPrice(e, model) {
        var _this = this;

        plugin.confirm(plugin.i18n.warning, function () {
          var $controls = _this.compileHtmlControls('reset_price', 0);
          $controls.closest('form').submit();
        });
      }

      /**
       * Compile html form controls.
       *
       * @param  {string} action
       * @param  {float}  amount
       * @return {void}
       */

    }, {
      key: 'compileHtmlControls',
      value: function compileHtmlControls(action, amount) {
        var model = this.scheduler.model;
        var template = wp.template('scheduler-pricing-controls');

        var roomtype = {};
        if (window._listRoomTypes) {
          roomtype = _.findWhere(window._listRoomTypes, { id: model.get('calendar') });
        }

        // Destroy flatpickr first.
        if (this.flatpickr) {
          this.flatpickr.destroy();
        }

        // Compile the html template.
        var $controls = $('#js-scheduler-form-controls').html(template({
          action: action,
          amount: amount,
          roomtype: roomtype,
          calendar: model.get('calendar'),
          endDate: model.get('endDate').format(DATE_FORMAT),
          startDate: model.get('startDate').format(DATE_FORMAT)
        }));

        // Create the flatpickr after.
        this.flatpickr = flatpickr('#date_start', {
          dateFormat: 'Y-m-d',
          plugins: [new rangePlugin({ input: '#date_end' })]
        });

        return $controls;
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

    return PricingScheduler;
  }();

  /**
   * Document ready!
   *
   * @return {void}
   */


  $(function () {
    new PricingScheduler();
  });
})(jQuery, window.awebooking);

},{}]},{},[1]);

//# sourceMappingURL=page-pricing.js.map
