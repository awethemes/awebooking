(function($) {
  'use strict';

  const plugin = window.awebooking || {};
  const DATE_FORMAT  = 'YYYY-MM-DD';

  class BookingScheduler {
    /**
     * Constructor.
     *
     * @return {void}
     */
    constructor() {
      this.flatpickr = null;

      this.scheduler = new ScheduleCalendar({
        el: '.scheduler',
        debug: plugin.debug,
        granularity: 'nightly',
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
        position: { my: 'center', at: 'center center-15%', of: window },
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
    handleClearSelected() {
      window.swal && swal.close();
    }

    /**
     * Handle set price action.
     *
     * @param  {Event}  e
     * @param  {Object} model
     * @return {void}
     */
    handleBlockRoom(e, model) {
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
    handleUnblockRoom(e, model) {
      plugin.confirm(plugin.i18n.warning, () => {
        // const $form = this.compileHtmlControls('reset_price', 0);
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
    compileHtmlControls(action, state) {
      const model = this.scheduler.model;
      const template = wp.template('scheduler-pricing-controls');

      // Destroy flatpickr first.
      if (this.flatpickr) {
        // this.flatpickr.destroy();
      }

      // Compile the html template.
      const $form = $('#js-scheduler-form-controls').html(template({
        state:     state,
        action:    action,
        endDate:   model.get('endDate').format(DATE_FORMAT),
        startDate: model.get('startDate').format(DATE_FORMAT),
        calendar:  model.get('calendar'),
      }));

      // Create the flatpickr after.
      this.flatpickr = flatpickr('#date_start', {
        dateFormat: 'Y-m-d',
        plugins: [ new rangePlugin({ input: '#date_end' }) ],
      });

      return $form;
    }
  }

  /**
   * Document ready!
   *
   * @return {void}
   */
  $(function() {
    new BookingScheduler;
  });

})(jQuery);
