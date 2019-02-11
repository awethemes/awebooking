import ko from 'ko'
import $ from 'jquery'
import { formatDateString } from '../utils/date-utils'

class SearchFormModel {
  constructor(data) {
    this.hotel = ko.observable(data.adults || 0)
    this.adults = ko.observable(data.adults || 1)
    this.children = ko.observable(data.children || 0)
    this.infants = ko.observable(data.infants || 0)
    this.checkIn = ko.observable(data.check_in || '')
    this.checkOut = ko.observable(data.check_out || '')
    this.checkInDate = ko.computed(() => formatDateString(this.checkIn(), 'Y-m-d'))
    this.checkOutDate = ko.computed(() => formatDateString(this.checkOut(), 'Y-m-d'))
  }

  checkInFormatted(format) {
    return formatDateString(this.checkIn(), format)
  }

  checkOutFormatted(format) {
    return formatDateString(this.checkOut(), format)
  }
}

export default class OldSearchForm {
  constructor(el, form) {
    this.$el = $(el)
    this.form = form

    this._setupDatePicker()

    this.$el.find('.searchbox__box').each((i, box) => {
      $(box).data('popup', this._setuptPopper(box))
    })

    this.$el.find('[data-trigger="spinner"]').on('changed.spinner', function () {
      $(this).find('[data-spin="spinner"]').trigger('change')
    })

    this.model = new SearchFormModel(form.getFormData())
    ko.applyBindings(this.model, el)
  }

  _setupDatePicker() {
    const self = this
    const plugin = window.awebooking

    let $rangepicker = this.$el.find('[data-hotel="rangepicker"]')
    if ($rangepicker.length === 0) {
      $rangepicker = $('<input type="text" data-hotel="rangepicker"/>').appendTo(this.$el)
    }

    const fp = plugin.datepicker($rangepicker[0], {
      mode: 'range',
      altInput: false,
      clickOpens: false,
      closeOnSelect: true,

      onReady(_, __, fp) {
        fp.calendarContainer.classList.add('awebooking-datepicker')
        this.config.ignoredFocusElements.push($('.searchbox__box--checkin', self.$el)[0])
        this.config.ignoredFocusElements.push($('.searchbox__box--checkout', self.$el)[0])
      },

      onChange: (dates) => {
        if (dates.length === 0) {
          this.model.checkIn(null)
          this.model.checkIn(null)
        } else if (dates.length === 1) {
          this.model.checkIn(dates[0])
          this.model.checkOut(null)
        } else {
          this.model.checkIn(dates[0])
          this.model.checkOut(dates[1])
        }
      },

      onPreCalendarPosition(_, __, fp) {
        fp._positionElement = $('.searchbox__box--checkout', self.$el)[0]
        setTimeout(() => { this._positionElement = this._input }, 0)
      },
    })

    $(this.$el).on('click', '.searchbox__box--checkin, .searchbox__box--checkout', (e) => {
      e.preventDefault()

      fp.isOpen = false
      fp.open(undefined, e.currentTarget)
    })
  }

  _setuptPopper(el) {
    const plugin = window.awebooking
    const $html = $(el).find('.searchbox__popup')

    if ($html.length === 0) {
      return
    }

    plugin.utils.dropdown($(el).find('.searchbox__box-wrap'), {
      drop: '.searchbox__popup',
      display: 'static',
    })
  }
}
