(function($, plugin) {
  'use strict';

  const settings = window._awebookingSettings || {};

  class MainSetting {
    /**
     * Handle leaving using window.onbeforeunload.
     *
     * @return {void}
     */
    handleLeaving() {
      let changed = false;

      // Set the changed if any controls fire change.
      $('input, textarea, select, checkbox').on( 'change', function() {
        changed = true;
      });

      $('.awebooking-settings')
        .on('click', '.nav-tab-wrapper a', function() {
          if (changed) {
            window.onbeforeunload = function() {
              return settings.i18n.nav_warning;
            };
          } else {
            window.onbeforeunload = null;
          }
        })
        .on('click', '.submit button', function() {
          window.onbeforeunload = null;
        });
    }

    /**
     * Create the datepicker.
     *
     * @return {void}
     */
    createDatepicker() {
      $('#display_datepicker_disabledates').flatpickr({
        mode: 'multiple',
        dateFormat: 'Y-m-d'
      });
    }
  }

  /** Document ready */
  $(function() {
    const main = new MainSetting;

    main.handleLeaving();
    main.createDatepicker();
  });

})(jQuery, window.awebooking);
