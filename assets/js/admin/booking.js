(function($) {
  'use strict';

  $(function() {

    jQuery(function($) {
      var $el = $('#my-dialog', document);

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
            action: 'awebooking_booking_add_item_form',
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

    $(function ($) {
      // initalise the dialog
      $('#my-dialog').dialog({
        title: 'My Dialog',
        dialogClass: 'wp-dialog',
        autoOpen: false,
        draggable: false,
        width: 'auto',
        modal: true,
        resizable: false,
        closeOnEscape: true,
        position: {
          at: "center top+30%",
        },
        open: function () {
          $("body").css({ overflow: 'hidden' })
          // close dialog by clicking the overlay behind it
          $('.ui-widget-overlay').bind('click', function(){
            // $('#my-dialog').dialog('close');
          })
        },
        beforeClose: function(event, ui) {
        $("body").css({ overflow: 'inherit' })
       },
        create: function () {
          // style fix for WordPress admin
          // $('.ui-dialog-titlebar-close').addClass('ui-button');
        },
      });
      // bind a button or a link to open the dialog
      $('a.open-my-dialog').click(function(e) {
        e.preventDefault();
        $('#my-dialog').dialog('open');
      });
    });

  });

  $(function() {
    $(document).on('click', '#awebooking-booking-notes .delete_note', function (e) {
      e.preventDefault();
      var el = $(this);
      var note = $( this ).closest( 'li.note' );

      $.ajax({
        url: awebooking_booking_ajax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'awebooking/delete_booking_note',
          note_id:  $( note ).attr( 'rel' ),
          booking_id: $('#post_ID').val()
        },
      })
      .done(function(response) {
        $( note ).remove();
      })
      .fail(function(xhr, status, error) {

      })
      .always(function() {
      });
    });

    $(document).on('click', '#awebooking-booking-notes button.add_note', function (e) {
      e.preventDefault();
      var el = $(this);

      if ( ! $( 'textarea#add_booking_note' ).val() ) {
        return;
      }

      $.ajax({
        url: awebooking_booking_ajax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'awebooking/add_booking_note',
          booking_id: $('#post_ID').val(),
          note:       $( 'textarea#add_booking_note' ).val(),
          note_type:  $( 'select#booking_note_type' ).val(),
        },
      })
      .done(function(response) {
        $( 'ul.booking_notes' ).prepend( response.data.new_note );
        $( '#add_booking_note' ).val( '' );
      })
      .fail(function(xhr, status, error) {

      })
      .always(function() {
      });
    });
  });

})(jQuery);
