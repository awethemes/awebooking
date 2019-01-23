import $ from 'jquery'
import Control from '../utils/control'
import { formatDateString } from '../utils/date-utils'
import isSameDay from 'react-dates/lib/utils/isSameDay'
import toMomentObject from 'react-dates/lib/utils/toMomentObject'

export default class SearchForm {
  constructor(root, instance) {
    this.root = $(root)
    this.instance = instance

    // Store the input elements
    this.elements = {}
    this.linkElements()

    this._registerBindings()

    if (window.createReactDatePicker && this.root.find('.abrs-searchbox__dates').length > 0) {
      this._createDatePicker()
    }
  }

  getFormData() {
    const elements = this.elements

    let data = {}

    Object.keys(elements).forEach(index => {
      data[index] = elements[index].get()
    })

    return data
  }

  getRootElement() {
    return this.root[0]
  }

  _createDatePicker() {
    const datepicker = window.awebooking.config.datepicker

    let { disableDays, disableDates } = datepicker
    disableDates = disableDates.split(/,\s?/).map(day => toMomentObject(day))

    const isDayBlocked = (day) => {
      let disabled = false

      if (Array.isArray(disableDays) && disableDays.length > 0) {
        disabled = disableDays.includes(parseInt(day.format('d'), 10))
      }

      if (!disabled && disableDates.length > 0) {
        disabled = disableDates.some(test => isSameDay(day, test))
      }

      return disabled
    }

    window.createReactDatePicker(this, {
      isDayBlocked: isDayBlocked,
      minimumNights: datepicker.minNights || 1,
      maximumNights: datepicker.maxNights || 0,
      minimumDateRange: datepicker.minDate || 0,
      maximumDateRange: datepicker.maxDate || 0,
      numberOfMonths: datepicker.showMonths || 1
    })
  }

  _registerBindings() {
    const binding = (bind) => {
      return (value) => {
        this.elements[bind].set(value ? formatDateString(value) : '')
      }
    }

    if (this.elements.hasOwnProperty('check_in_alt')) {
      this.elements['check_in'].bind(binding('check_in_alt'))
    }

    if (this.elements.hasOwnProperty('check_out_alt')) {
      this.elements['check_out'].bind(binding('check_out_alt'))
    }
  }

  /**
   * Link elements between settings and inputs
   */
  linkElements() {
    const control = this

    const nodes = control.root.find('select, input, textarea')
    let radios = {}

    nodes.each((index, element) => {
      let node = $(element)

      if (node.data('_elementLinked')) {
        return
      }

      // Prevent re-linking element.
      node.data('_elementLinked', true)
      let name = node.prop('name')

      if (node.is(':radio')) {
        if (radios[name]) {
          return
        }

        radios[name] = true
        node = nodes.filter('[name="' + name + '"]')
      }

      index = name || index

      if (node.data('element')) {
        index = node.data('element')
      }

      control.elements[index] = new Control(node)
    })
  }
}
