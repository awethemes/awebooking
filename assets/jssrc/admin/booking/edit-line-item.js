const $ = window.jQuery;
const awebooking = window.TheAweBooking;

class EditLineItem {
  constructor() {
    this.$popup = $('#awebooking-edit-line-item-popup');
    awebooking.Popup.setup(this.$popup);

    $('form', this.$popup).on('submit', this.submitForm);
    $('.js-edit-line-item').on('click', this.openPopup.bind(this));
  }

  openPopup(e) {
    e.preventDefault();

    var self = this;
    const lineItem = $(e.currentTarget).data('lineItem');

    self.$popup.find('.awebooking-dialog-contents').html('Loading...');
    self.$popup.dialog('open');

    return wp.ajax.post('get_awebooking_edit_line_item_form', { line_item_id: lineItem })
      .done(function(response) {
        self.$popup.find('.awebooking-dialog-contents').html(response.html);
      });
  }

  submitForm(e) {
    e.preventDefault();

    awebooking.ajaxSubmit(this, 'edit_awebooking_line_item')
      .done(function(response) {

        // TODO: Improve this!
        setTimeout(function() {
          // window.location.reload();
        }, 250);

      })
      .fail(function(response) {
        if (response.error) {
          alert(response.error);
        }
      });
  }
}

module.exports = EditLineItem;
