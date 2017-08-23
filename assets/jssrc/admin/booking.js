(function($) {

    jQuery(function($) {
      var $el = $('#my-dialogsss', document);

      var updateFirst = function() {
        var $check_in  = $el.find('#add_check_in_out_0');
        var $check_out = $el.find('#add_check_in_out_1');

        if (! $check_in.val() || ! $check_out.val()) {
          return;
        }

        $el.find('#add_room').val('');
        requestAjax();
      };

      var updateSecond = function() {
        requestAjax();
      };

      var requestAjax = function() {
        $.ajax({
          url: ajaxurl,
          type: 'GET',
          dataType: 'html',
          data: {
            action: 'awebooking/get_booking_add_item_form',
            check_in: $el.find('#add_check_in_out_0').val(),
            check_out: $el.find('#add_check_in_out_1').val(),
            add_room: $el.find('#add_room').val(),
          },
        })
        .done(function(html) {
          $('#add_check_in_out_0').datepicker('destroy');
          $('#add_check_in_out_1').datepicker('destroy');

          $el.find('.dialog-contents').html(html);

          $el.find('#add_check_in_out_0').on('change', updateFirst);
          $el.find('#add_check_in_out_1').on('change', updateFirst);
          $el.find('#add_room').on('change', updateSecond);
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
      };

      $el.find('#add_check_in_out_0').on('change', updateFirst);
      $el.find('#add_check_in_out_1').on('change', updateFirst);
      $el.find('#add_room').on('change', updateSecond);
    });

})(jQuery);
