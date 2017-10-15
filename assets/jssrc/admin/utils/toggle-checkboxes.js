const $ = window.jQuery;

class ToggleCheckboxes {
  /**
   * Wrapper the jquery-ui-popup.
   */
  constructor(table) {
    this.table = table;
    const $table = $(this.table);

    $(document).on( 'click', '.check-column :checkbox', function( event ) {
      // Toggle the "Select all" checkboxes depending if the other ones are all checked or not.
      var unchecked = $(this).closest('tbody').find(':checkbox').filter(':visible:enabled').not(':checked');

      $(document).find('.wp-toggle-checkboxes').prop('checked', function() {
        return ( 0 === unchecked.length );
      });

      return true;
    });

    $(document).on( 'click', '.wp-toggle-checkboxes', function(e) {
      $table.children( 'tbody' ).filter(':visible')
        .find('.check-column').find(':checkbox')
        .prop('checked', function() {
          if ( $(this).is(':hidden,:disabled') ) {
            return false;
          }
          return ! $(this).prop( 'checked' );
        });
    });
  }
}

module.exports = ToggleCheckboxes;
