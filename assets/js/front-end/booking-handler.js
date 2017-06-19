jQuery(function($) {
    'use strict';

    /**
     * find page number
     */
    $('#awebooking-service input[type="checkbox"]').on('change', function (e) {
        var el = $(this);

        var data = $('#awebooking-booking-form').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        data['extra-services'] = $('input[name="awebooking_services\[\]"]:checked').map(function() {
          return $(this).val();
        }).get();

        $.ajax({
            url: booking_ajax.ajax_url,
            type: 'post',
            data: $.extend(data, {
                action: 'awebooking/price_calculator',
            }),
            beforeSend: function() {
              /**
               * create effect before send data.
               */
               $("#awebooking-booking-form").addClass('awebooking-loading');
            },

            success: function( response ) {
                /**
                 * remove loading button.
                 * show result.
                 */
              $('#awebooking-total-cost').html(response.data.total_price);
              $("#awebooking-booking-form").removeClass('awebooking-loading');
            }
        });
    });
} );
