import $ from 'jquery'
import debounce from 'debounce'
import isMobile from 'is-mobile'
import * as queryString from 'query-string'
import Dropdown from '../core/dropdown'
import rangePlugin from '../core/range-dates'
import customerSearch from './utils/search-customer'
import accounting from 'accounting'

// Instance the awebooking object.
const awebooking = window.awebooking || {}
const i18n = awebooking.i18n || {}

// Create the properties.
awebooking.utils = {}
awebooking.instances = {}

awebooking.isMobile = isMobile
awebooking.utils.flatpickrRangePlugin = rangePlugin

/**
 * The admin route.
 *
 * @param  {string} route
 * @return {string}
 */
awebooking.route = function (route) {
  return this.admin_route + route.replace(/^\//g, '')
}

/**
 * Show the alert dialog.
 *
 * @return {SweetAlert}
 */
awebooking.alert = function (message, type = 'error') {
  return swal({
    text: message,
    type: type,
    toast: true,
    buttonsStyling: false,
    showCancelButton: false,
    showConfirmButton: true,
    confirmButtonClass: 'button'
  })
}

/**
 * Show the confirm message.
 *
 * @return {SweetAlert}
 */
awebooking.confirm = function (message, callback) {
  if (!window.swal) {
    return window.confirm(message || i18n.warning) && callback()
  }

  const confirm = window.swal({
    text: message || this.i18n.warning,
    customClass: 'awebooking-confirm-dialog',
    position: 'center',
    animation: false,
    backdrop: 'rgba(0,0,0,.8)',
    reverseButtons: true,
    buttonsStyling: false,
    showCancelButton: true,
    cancelButtonClass: '',
    confirmButtonClass: '',
    cancelButtonText: this.i18n.cancel,
    confirmButtonText: this.i18n.ok,
  })

  if (callback) {
    return confirm.then(function (result) {
      if (result.value) callback(result)
    })
  }

  return confirm
}

/**
 * Create the dialog.
 *
 * @param  {string} selector
 * @return {Object}
 */
awebooking.dialog = function (selector) {
  const $dialog = $(selector).dialog({
    modal: true,
    width: 'auto',
    height: 'auto',
    autoOpen: false,
    draggable: false,
    resizable: false,
    closeOnEscape: true,
    dialogClass: 'wp-dialog awebooking-dialog',
    position: { my: 'center', at: 'center center-15%', of: window },
  })

  $(window).on('resize', debounce(() => {
    $dialog.dialog('option', 'position', { my: 'center', at: 'center center-15%', of: window })
  }, 150))

  return $dialog
}

/**
 * Send a ajax request to a route.
 *
 * @param  {String}   method
 * @param  {String}   route
 * @param  {Object}   data
 * @param  {Function} callback
 * @return {JQuery.jqXHR}
 */
awebooking.ajax = function (method, route, data, callback) {
  const xhr = $.ajax({
    url: awebooking.route(route),
    data: data,
    method: method,
    dataType: 'json',
  })

  return xhr.done((data) => {
    if (callback) callback(data)
  }).fail((xhr) => {
    const json = xhr.responseJSON

    if (json && json.message) {
      awebooking.alert(json.message, 'error')
    } else {
      awebooking.alert(i18n.error, 'error')
    }
  })
}

/**
 * Create a form then append to body.
 *
 * @param  {String} action The form action.
 * @param  {String} method The form method.
 * @return {Object}
 */
awebooking.createForm = function (action, method) {
  const $form = $('<form>', { 'method': 'POST', 'action': action })

  const hiddenInput = $('<input>', { 'name': '_method', 'type': 'hidden', 'value': method })

  return $form.append(hiddenInput).appendTo('body')
}

/**
 * Format the price.
 *
 * @param amount
 * @returns {string}
 */
awebooking.formatPrice = function (amount) {
  return accounting.formatMoney(amount, {
    format: i18n.priceFormat,
    symbol: i18n.currencySymbol,
    decimal: i18n.decimalSeparator,
    thousand: i18n.priceThousandSeparator,
    precision: i18n.numberDecimals,
  })
}

/**
 * Retrieves a modified URL query string.
 *
 * @param {object} args
 * @param {string} url
 */
awebooking.utils.addQueryArgs = function (args, url) {
  if (typeof url === 'undefined') {
    url = window.location.href
  }

  const parsed = queryString.parseUrl(url)
  const query = $.extend({}, parsed.query, args)

  return parsed.url + '?' + queryString.stringify(query, { sort: false })
}

$(function () {
  if (window.tippy) {
    tippy('.tippy', {
      arrow: true,
      animation: 'shift-toward',
      duration: [200, 150],
    })
  }

  // Init the selectize.
  if ($.fn.selectize) {
    customerSearch()

    $('select.selectize, .with-selectize .cmb2_select').selectize({
      allowEmptyOption: true,
      searchField: ['value', 'text'],
    })
  }

  // Init warning before delete.
  $('[data-method="abrs-delete"]').on('click', function (e) {
    e.preventDefault()

    const link = $(this).attr('href')
    const message = $(this).data('warning')

    awebooking.confirm(message, function () {
      awebooking.createForm(link, 'DELETE').submit()
    })
  })

  $('[data-init="abrs-dropdown"]').each(function () {
    $(this).data('abrs-dropdown', new Dropdown(this, {
      drop: '.abrs-drop',
    }))
  })

})
