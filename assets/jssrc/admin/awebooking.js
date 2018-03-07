const $ = window.jQuery;
const settings = window._awebookingSettings || {};

import popper from 'popper.js';
import tooltip from 'tooltip.js';
import flatpickr from 'flatpickr';

const AweBooking = _.extend(settings, {
  Vue: require('vue'),

  Popper: popper,
  Tooltip: tooltip,
  Flatpickr: flatpickr,

  Popup: require('./utils/popup.js'),
  ToggleClass: require('./utils/toggle-class.js'),
  RangeDatepicker: require('./utils/range-datepicker.js'),
  ToggleCheckboxes: require('./utils/toggle-checkboxes.js'),

  /**
   * Init the AweBooking
   */
  init() {
    const self = this;

    // Init the popup, use jquery-ui-popup.
    $('[data-toggle="awebooking-popup"]').each(function() {
      $(this).data('awebooking-popup', new self.Popup(this));
    });

    $('[data-init="awebooking-toggle"]').each(function() {
      $(this).data('awebooking-toggle', new self.ToggleClass(this));
    });

    $('[data-init="awebooking-tooltip"]').each(function() {
      const options = {
        template: '<div class="awebooking-tooltip tooltip" role="tooltip"><div class="tooltip__arrow"></div><div class="tooltip__inner"></div></div>',
      };

      $(this).data('awebooking-tooltip', new self.Tooltip(this, options));
    });

    const createForm = function(link, method) {
      const form = $('<form>', { 'method': 'POST', 'action': link });
      const hiddenInput = $('<input>', { 'name': '_method',  'type': 'hidden', 'value': method });

      return form.append(hiddenInput).appendTo('body');
    };

    $('a[data-method="awebooking-delete"]').on( 'click', function(e) {
      e.preventDefault();
      const link = $(this).attr('href');

      self.confirm(function(result) {
        const form = createForm(link, 'DELETE');
        form.submit();
      }, { confirmButtonText: self.trans('delete') });
    });

    require('./utils/init-select2.js');
  },

  /**
   * Show the confirm message.
   */
  confirm(callback, settings) {
    const confirm = swal(_.extend({
      toast: true,
      title: this.trans('confirm_title'),
      html: this.trans('confirm_message'),
      type: 'warning',
      position: 'center',
      animation: false,
      reverseButtons: true,
      showCancelButton: true,
      buttonsStyling: false,
      cancelButtonClass: 'button',
      confirmButtonClass: 'button button-primary',
      cancelButtonText: this.trans('cancel'),
      confirmButtonText: this.trans('ok'),
    }, settings || {}));

    if (callback) {
      return confirm.then(function(result) {
        if (result.value) callback(result);
      });
    }

    return confirm;
  },

  /**
   * Get a translator string
   */
  trans(context) {
    return this.strings[context] ? this.strings[context] : '';
  },

  /**
   * Make form ajax request.
   */
  ajaxSubmit(form, action) {
    const serialize = require('form-serialize');
    const data = serialize(form, { hash: true });

    // Add .ajax-loading class in to the form.
    $(form).addClass('ajax-loading');

    return wp.ajax.post(action, data).always(function() {
      $(form).removeClass('ajax-loading');
    });
  },
});

$(function() {
  AweBooking.init();
});

window.TheAweBooking = AweBooking;
