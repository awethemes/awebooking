(function($) {
  'use strict';

  const plugin = window.awebooking || {};
  const DATE_FORMAT  = 'YYYY-MM-DD';

  class PricingScheduler {
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
        granularity: 'daily',
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
      this.scheduler.on('action:set-price', this.handleSetPrice.bind(this));
      this.scheduler.on('action:reset-price', this.handleResetPrice.bind(this));
    }

    /**
     * Handle on clear selected.
     *
     * @return {void}
     */
    handleClearSelected() {
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
    handleSetPrice(e, model) {
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
    handleResetPrice(e, model) {
      plugin.confirm(plugin.i18n.warning, () => {
        const $form = this.compileHtmlControls('reset_price', 0);
        $form.closest('form').submit();
      });
    }

    /**
     * Compile html form controls.
     *
     * @param  {string} action
     * @param  {float}  amount
     * @return {void}
     */
    compileHtmlControls(action, amount) {
      const model = this.scheduler.model;
      const template = wp.template('scheduler-pricing-controls');

      let roomtype = {};
      if (window._listRoomTypes) {
        roomtype = _.findWhere(window._listRoomTypes, { id: model.get('calendar') });
      }

      // Destroy flatpickr first.
      if (this.flatpickr) {
        this.flatpickr.destroy();
      }

      // Compile the html template.
      const $form = $('#js-scheduler-form-controls').html(template({
        action:    action,
        amount:    amount,
        roomtype:  roomtype,
        calendar:  model.get('calendar'),
        endDate:   model.get('endDate').format(DATE_FORMAT),
        startDate: model.get('startDate').format(DATE_FORMAT),
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
    new PricingScheduler;
  });

})(jQuery);
