(function($) {
  'use strict';

  const awebooking = window.awebooking || {};
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
  }

  /** Document ready */
  $(function() {
    const main = new MainSetting;

    main.handleLeaving();
  });

})(jQuery);
