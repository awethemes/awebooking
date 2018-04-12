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

      this.scheduler.on('clear', this.handleClearSelected.bind(this));
    }

    /**
     * Handle on clear selected.
     *
     * @return {void}
     */
    handleClearSelected() {
      window.swal && swal.close();
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
