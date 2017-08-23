const $ = window.jQuery;
const settings = window._awebookingSettings || {};

const Popup = require('./utils/popup.js');

const AweBooking = _.extend(settings, {
  /**
   * Init the AweBooking
   */
  init() {
    const self = this;

    // Init the popup, use jquery-ui-popup.
    $('[data-toggle="awebooking-popup"]').each(function() {
      $(this).data('awebooking-popup', new Popup(this));
    });
  },

  /**
   * Make a ajax request
   */
  ajax(action, data) {
    var requestData = _.extend(data, {
      action: 'awebooking/' + action
    });

    return $.ajax({
        url: this.ajax_url,
        type: 'POST',
        data: requestData,
      })
      .fail(function() {
        console.log("error");
      });
  },

  /**
   * Get a translator string
   */
  trans(context) {
    return this.strings[context] ? this.strings[context] : '';
  }
});

$(function() {
  AweBooking.init();
});

window.TheAweBooking = AweBooking;
