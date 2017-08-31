const $ = window.jQuery;
const AweBooking = window.TheAweBooking;

class InitSelect2 {
  constructor() {
    this.searchCustomer();
  }

  // Ajax customer search boxes
  searchCustomer() {
    $(':input.awebooking-customer-search, select[name="booking_customer"]').filter( ':not(.enhanced)' ).each( function() {
      var select2_args = {
        allowClear:  $( this ).data( 'allowClear' ) ? true : false,
        placeholder: $( this ).data( 'placeholder' ) ? $( this ).data( 'placeholder' ) : "",
        minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '1',
        escapeMarkup: function( m ) {
          return m;
        },
        ajax: {
          url:         AweBooking.ajax_url,
          dataType:    'json',
          delay:       250,
          data:        function( params ) {
            return {
              term:     params.term,
              action:   'awebooking_json_search_customers',
              // security: wc_enhanced_select_params.search_customers_nonce,
              exclude:  $( this ).data( 'exclude' )
            };
          },
          processResults: function( data ) {
            var terms = [];
            if ( data ) {
              $.each( data, function( id, text ) {
                terms.push({
                  id: id,
                  text: text
                });
              });
            }
            return {
              results: terms
            };
          },
          cache: true
        }
      };

      $( this ).select2(select2_args).addClass( 'enhanced' );
    });

  }
}

module.exports = new InitSelect2;
