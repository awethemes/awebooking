"use strict";

(function ($) {
  'use strict';

  var awebooking = window.awebooking || {}; // Create the properties.

  awebooking.utils = {};
  awebooking.instances = {};
  /**
   * The admin route.
   *
   * @param  {string} route
   * @return {string}
   */

  awebooking.route = function (route) {
    return this.admin_route + route.replace(/^\//g, '');
  };
  /**
   * Show the confirm message.
   *
   * @return {SweetAlert}
   */


  awebooking.confirm = function (message, callback) {
    if (!window.swal) {
      return window.confirm(message || this.i18n.warning) && callback();
    }

    var confirm = swal({
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
      confirmButtonText: this.i18n.ok
    });

    if (callback) {
      return confirm.then(function (result) {
        if (result.value) callback(result);
      });
    }

    return confirm;
  },
  /**
   * Create a form then append to body.
   *
   * @param  {string} link   The form action.
   * @param  {string} method The form method.
   * @return {Object}
   */
  awebooking.createForm = function (action, method) {
    var $form = $('<form>', {
      'method': 'POST',
      'action': action
    });
    var hiddenInput = $('<input>', {
      'name': '_method',
      'type': 'hidden',
      'value': method
    });
    return $form.append(hiddenInput).appendTo('body');
  };
  /**
   * Init the search customers.
   *
   * @return {void}
   */

  awebooking.utils.initSearchCustomer = function () {
    var $selectors = $('select.awebooking-search-customer, .selectize-search-customer .cmb2_select');

    var ajaxSearch = function ajaxSearch(query, callback) {
      $.ajax({
        type: 'GET',
        url: awebooking.route('/search/customers'),
        data: {
          term: encodeURIComponent(query)
        },
        error: function error() {
          callback();
        },
        success: function success(res) {
          callback(res);
        }
      });
    };

    $selectors.each(function () {
      $(this).selectize({
        valueField: 'id',
        labelField: 'display',
        searchField: 'display',
        dropdownParent: 'body',
        placeholder: $(this).data('placeholder'),
        load: function load(query, callback) {
          if (!query.length) {
            return callback();
          }

          ajaxSearch(query, callback);
        }
      });
    });
  };

  $(function () {
    // Init tippy.
    if (window.tippy) {
      tippy('.tippy', {
        arrow: true,
        animation: 'shift-toward',
        duration: [200, 150]
      });
    } // Init the selectize.


    if ($.fn.selectize) {
      $('select.selectize, .with-selectize .cmb2_select').selectize({
        allowEmptyOption: true,
        searchField: ['value', 'text']
      });
      awebooking.utils.initSearchCustomer();
    } // Init warning before delete.


    $('[data-method="abrs-delete"]').on('click', function (e) {
      e.preventDefault();
      var link = $(this).attr('href');
      var message = $(this).data('warning');
      awebooking.confirm(message, function () {
        awebooking.createForm(link, 'DELETE').submit();
      });
    });
  });
})(jQuery);
//# sourceMappingURL=admin.js.map
