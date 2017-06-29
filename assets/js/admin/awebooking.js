window.ABKNG = window.ABKNG || {};

;(function($, awebooking, Backbone) {
  'use strict';

  /**
   * Setup core member object.
   */
  _.extend(awebooking, {
    Utils: {},
    Components: {},
    strings: awebooking.strings || {},
    ajax_url: awebooking.ajax_url || window.ajaxurl,
  });

  _.extend(awebooking, Backbone.Events);

  /**
   * Get translator string.
   *
   * @param  {string} context
   * @return {string}
   */
  awebooking.trans = function(context) {
    return this.strings[context] ? this.strings[context] : '';
  };

  awebooking.ajax = function(action, data) {
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
  }

  awebooking.Utils.ToggleClass = function(element) {

    $('.drawer-toggle', document).on('click', function(e) {
      e.preventDefault();
      $(this).parent().toggleClass('open');
    });

  };

  /**
   * Document ready to fire!
   */
  $(function() {
    // Do something...
    awebooking.Utils.ToggleClass();

    /*$('[data-init="toggle-class"]').each(function() {
      var $this = $(this);
      var data  = $this.data('abkng.toggleclass');

      if (! data) {
        data = new awebooking.Utils.ToggleClass(this);
        $this.data('abkng.toggleclass', data);
      }

      // ...
    });*/

    // Fire event when everything ready.
    awebooking.trigger('ready', awebooking);
  });

})(jQuery, ABKNG, Backbone);
