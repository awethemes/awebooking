const $ = window.jQuery;
const settings = window._awebookingSettings || {};

import Popper from 'popper.js';
import Tooltip from 'tooltip.js';

const AweBooking = _.extend(settings, {
  Vue: require('vue'),
  Popper: Popper,
  Tooltip: Tooltip,

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

    require('./utils/init-select2.js');
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

    return wp.ajax.post(action, data)
      .always(function() {
        $(form).removeClass('ajax-loading');
      });
  },
});

$(function() {
  AweBooking.init();
});

window.TheAweBooking = AweBooking;
