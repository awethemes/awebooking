"use strict";

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(function ($) {
  'use strict';

  var plugin = window.awebooking || {};
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

      this.flatpickr = null;
      this.scheduler = new ScheduleCalendar({
        el: '.scheduler',
        debug: plugin.debug,
        granularity: 'nightly'
      });
      this.$dialog = $('#scheduler-form-dialog').dialog({
        modal: true,
        width: 'auto',
        height: 'auto',
        autoOpen: false,
        draggable: false,
        resizable: false,
        closeOnEscape: true,
        dialogClass: 'wp-dialog awebooking-dialog',
        position: {
          my: 'center',
          at: 'center center-15%',
          of: window
        }
      });
      this.scheduler.on('clear', this.handleClearSelected.bind(this));
      this.scheduler.on('action:set-unavailable', this.handleBlockRoom.bind(this));
      this.scheduler.on('action:clear-unavailable', this.handleUnblockRoom.bind(this));
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
        window.swal && swal.close();
        this.compileHtmlControls('block_room', 0);
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
      key: "handleUnblockRoom",
      value: function handleUnblockRoom(e, model) {
        plugin.confirm(plugin.i18n.warning, function () {// const $form = this.compileHtmlControls('reset_price', 0);
          // $form.closest('form').submit();
        });
      }
      /**
       * Compile html form controls.
       *
       * @param  {string} action
       * @param  {string} state
       * @return {void}
       */

    }, {
      key: "compileHtmlControls",
      value: function compileHtmlControls(action, state) {
        var model = this.scheduler.model;
        var template = wp.template('scheduler-pricing-controls'); // Destroy flatpickr first.

        if (this.flatpickr) {} // this.flatpickr.destroy();
        // Compile the html template.


        var $form = $('#js-scheduler-form-controls').html(template({
          state: state,
          action: action,
          endDate: model.get('endDate').format(DATE_FORMAT),
          startDate: model.get('startDate').format(DATE_FORMAT),
          calendar: model.get('calendar')
        })); // Create the flatpickr after.

        this.flatpickr = flatpickr('#date_start', {
          dateFormat: 'Y-m-d',
          plugins: [new rangePlugin({
            input: '#date_end'
          })]
        });
        return $form;
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
})(jQuery);
//# sourceMappingURL=page-calendar.js.map
