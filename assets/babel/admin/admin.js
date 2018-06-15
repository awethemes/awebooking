(function($) {
  'use strict';

  const awebooking = window.awebooking || {};

  // Create the properties.
  awebooking.utils = {};
  awebooking.instances = {};

  /**
   * The admin route.
   *
   * @param  {string} route
   * @return {string}
   */
  awebooking.route = function(route) {
    return this.admin_route + route.replace(/^\//g, '');
  }

  /**
   * Show the alert dialog.
   *
   * @return {SweetAlert}
   */
  awebooking.alert = function(message, type = 'error') {
    return swal({
      text: message,
      type: type,
      toast: true,
      buttonsStyling: false,
      showCancelButton: false,
      showConfirmButton: true,
      confirmButtonClass: 'button'
    });
  }

  /**
   * Show the confirm message.
   *
   * @return {SweetAlert}
   */
  awebooking.confirm = function(message, callback) {
    if (! window.swal) {
      return window.confirm(message || this.i18n.warning) && callback();
    }

    const confirm = swal({
      toast: true,
      text: message || this.i18n.warning,
      type: 'warning',
      position: 'center',
      reverseButtons: true,
      buttonsStyling: false,
      showCancelButton: true,
      cancelButtonClass: 'button',
      confirmButtonClass: 'button button-primary',
      cancelButtonText: this.i18n.cancel,
      confirmButtonText: this.i18n.ok,
    });

    if (callback) {
      return confirm.then(function(result) {
        if (result.value) callback(result);
      });
    }

    return confirm;
  },

  /**
   * Create the dialog.
   *
   * @param  {string} selector
   * @return {Object}
   */
  awebooking.dialog = function(selector) {
    const debounce = require('debounce');

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
    });

    $(window).resize(debounce(() => {
      $dialog.dialog('option', 'position', { my: 'center', at: 'center center-15%', of: window });
    }, 150));

    return $dialog;
  },

  /**
   * Create a form then append to body.
   *
   * @param  {string} link   The form action.
   * @param  {string} method The form method.
   * @return {Object}
   */
  awebooking.createForm = function(action, method) {
    const $form = $('<form>', { 'method': 'POST', 'action': action });

    const hiddenInput = $('<input>', { 'name': '_method',  'type': 'hidden', 'value': method });

    return $form.append(hiddenInput).appendTo('body');
  };

  /**
   * Retrieves a modified URL query string.
   *
   * @param {object} args
   * @param {string} url
   */
  awebooking.utils.addQueryArgs =function(args, url) {
    const queryString = require('query-string');

    if (typeof url === 'undefined') {
      url = window.location.href;
    }

    const parsed = queryString.parseUrl(url);
    const query  = $.extend({}, parsed.query, args);

    return parsed.url + '?' + queryString.stringify(query, { sort: false });
  }

  $(function() {
    // Init tippy.
    if (window.tippy) {
      tippy('.tippy', {
        arrow: true,
        animation: 'shift-toward',
        duration: [200, 150],
      });
    }

    // Init the selectize.
    if ($.fn.selectize) {
      require('./utils/search-customer.js')();

      $('select.selectize, .with-selectize .cmb2_select').selectize({
        allowEmptyOption: true,
        searchField: ['value', 'text'],
      });
    }

    // Init warning before delete.
    $('[data-method="abrs-delete"]').on( 'click', function(e) {
      e.preventDefault();

      const link = $(this).attr('href');
      const message = $(this).data('warning');

      awebooking.confirm(message, function() {
        awebooking.createForm(link, 'DELETE').submit();
      });
    });

  });

})(jQuery);
