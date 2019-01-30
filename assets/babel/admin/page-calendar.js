import $ from 'jquery'

const plugin = window.awebooking

const DATE_FORMAT = 'YYYY-MM-DD'

class BookingScheduler {
  /**
   * Constructor.
   *
   * @return {void}
   */
  constructor() {
    const self = this

    this.initBulkUpdate()

    this.scheduler = new ScheduleCalendar({
      el: '.scheduler',
      debug: plugin.debug,
      granularity: 'nightly',
    })

    this.scheduler.on('clear', this.handleClearSelected.bind(this))
    this.scheduler.on('action:block', this.handleBlockRoom.bind(this))
    this.scheduler.on('action:unblock', this.handleUnblockRoom.bind(this))

    $('.js-unlock-period').on('click', function (e) {
      e.preventDefault()
      const $el = $(this)

      self.scheduler.model.set('calendar', $el.data('room'))
      self.scheduler.model.set('startDate', moment($el.data('startDate')))
      self.scheduler.model.set('endDate', moment($el.data('endDate')))

      self.scheduler.trigger('action:unblock')
    })

    $('.scheduler__state-event, .scheduler__booking-event').each(function () {
      self.setupEventPopper(this)
    })
  }

  /**
   * Handle on clear selected.
   *
   * @return {void}
   */
  handleClearSelected() {
    window.swal && swal.close()
  }

  /**
   * Handle set price action.
   *
   * @param  {Event}  e
   * @param  {Object} model
   * @return {void}
   */
  handleBlockRoom(e, model) {
    plugin.confirm(plugin.i18n.warning, () => {
      const $controls = this.compileHtmlControls('block', model)
      $controls.closest('form').submit()
    })
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
      const $controls = this.compileHtmlControls('unblock', model)
      $controls.closest('form').submit()
    })
  }

  /**
   * Compile html form controls.
   *
   * @param  {string} action
   * @return {void}
   */
  compileHtmlControls(action, model) {
    const template = wp.template('scheduler-pricing-controls')

    if (!model) {
      model = this.scheduler.model
    }

    const data = {
      action: action,
      endDate: model.get('endDate').format(DATE_FORMAT),
      startDate: model.get('startDate').format(DATE_FORMAT),
      calendar: model.get('calendar'),
    }

    return $('#js-scheduler-form-controls').html(template(data))
  }

  /**
   * Setup event popper.
   *
   * @param  {Object} el
   * @return {void}
   */
  setupEventPopper(el) {
    const $html = $(el).find('.js-tippy-html')

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
          hide: { enabled: false },
          preventOverflow: { enabled: false },
        }
      }
    })

    return el._tippy
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
  new BookingScheduler()
})
