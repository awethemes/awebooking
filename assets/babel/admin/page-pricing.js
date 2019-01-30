import $ from 'jquery'

const plugin = window.awebooking

const DATE_FORMAT = 'YYYY-MM-DD'

class PricingScheduler {
  /**
   * Constructor.
   *
   * @return {void}
   */
  constructor() {
    this.flatpickr = null

    this.scheduler = new ScheduleCalendar({
      el: '.scheduler',
      debug: plugin.debug,
      granularity: 'daily',
    })

    this.$dialog = plugin.dialog('#scheduler-form-dialog')

    this.scheduler.on('clear', this.handleClearSelected.bind(this))
    this.scheduler.on('action:set-price', this.handleSetPrice.bind(this))
    this.scheduler.on('action:reset-price', this.handleResetPrice.bind(this))

    this.initBulkUpdate()
  }

  /**
   * Handle on clear selected.
   *
   * @return {void}
   */
  handleClearSelected() {
    window.swal && swal.close()

    if (this.flatpickr) {
      this.flatpickr.destroy()
    }

    this.$dialog.dialog('close')
    $('#js-scheduler-form-controls').html('')
  }

  /**
   * Handle set price action.
   *
   * @param  {Event}  e
   * @param  {Object} model
   * @return {void}
   */
  handleSetPrice(e, model) {
    window.swal && swal.close()

    this.compileHtmlControls('set_price', 0)

    this.$dialog.dialog('open')
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
      const $controls = this.compileHtmlControls('reset_price', 0)
      $controls.closest('form').submit()
    })
  }

  /**
   * Compile html form controls.
   *
   * @param  {string} action
   * @param  {float}  amount
   * @return {void}
   */
  compileHtmlControls(action, amount) {
    const model = this.scheduler.model
    const template = wp.template('scheduler-pricing-controls')

    let roomtype = {}
    if (window._listRoomTypes) {
      roomtype = _.findWhere(window._listRoomTypes, { id: model.get('calendar') })
    }

    // Destroy flatpickr first.
    if (this.flatpickr) {
      this.flatpickr.destroy()
    }

    // Compile the html template.
    const $controls = $('#js-scheduler-form-controls').html(template({
      action: action,
      amount: amount,
      roomtype: roomtype,
      calendar: model.get('calendar'),
      endDate: model.get('endDate').format(DATE_FORMAT),
      startDate: model.get('startDate').format(DATE_FORMAT),
    }))

    // Create the flatpickr after.
    this.flatpickr = flatpickr('#date_start', {
      dateFormat: 'Y-m-d',
      plugins: [new plugin.utils.flatpickrRangePlugin({ input: '#date_end' })],
    })

    return $controls
  }

  /**
   * Handle bulk update action.
   */
  initBulkUpdate() {
    const $dialog = plugin.dialog('#bulk-update-dialog')

    $('.js-open-bulk-update').on('click', function (e) {
      e.preventDefault()
      $dialog.dialog('open')
    })

    flatpickr('#bulk_date_start', {
      mode: 'range',
      dateFormat: 'Y-m-d',
      showMonths: plugin.isMobile() ? 1 : 2,
      plugins: [new plugin.utils.flatpickrRangePlugin({ input: '#bulk_date_end' })],
    })
  }
}

/**
 * Document ready!
 *
 * @return {void}
 */
$(function () {
  new PricingScheduler()
})
