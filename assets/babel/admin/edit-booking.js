(function($, plugin) {
  'use strict';

  class EditBooking {
    constructor() {
      $('.js-editnow').click(this.handleEditAddress);
    }

    handleEditAddress(e) {
      e.preventDefault();

      const focus = $(this).data('focus');
      const $wrapper = $(this).closest('.js-booking-column');

      $wrapper.find('h3 > a.button-editnow').hide();
      $wrapper.find('div.js-booking-data').hide();
      $wrapper.find('div.js-edit-booking-data').show();

      if (focus && $(focus, $wrapper).length) {
        $(focus, $wrapper).focus();
      }
    }
  }

  /**
   * Document ready!
   *
   * @return {void}
   */
  $(function() {
    plugin.instances.editBooking = new EditBooking;
  });

})(jQuery, window.awebooking || {});
