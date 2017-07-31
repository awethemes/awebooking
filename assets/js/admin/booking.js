jQuery(function($) {
  'use strict';

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

} );
