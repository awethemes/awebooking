const $ = window.jQuery;
const awebooking = window.TheAweBooking;

class EditLineItem {
  constructor() {
    this.doingAjax = false;
    this.currentAjax = null;

    this.$popup = $('#awebooking-edit-line-item-popup');
    awebooking.Popup.setup(this.$popup);

    $('form', this.$popup).on('submit', this.submitForm);
    $('.js-edit-line-item').on('click', this.openPopup.bind(this));

    this.$popup.on(
      'change', '#edit_adults, #edit_children, #edit_check_in_out_0, #edit_check_in_out_1, [name="edit_services\[\]"]',
      _.debounce(this.handleCalculateTotal.bind(this), 250)
    );
  }

  handleCalculateTotal() {
    const self = this;

    if (this.doingAjax && this.currentAjax) {
      this.currentAjax.abort();
    }

    self.doingAjax = true;

    this.currentAjax = awebooking.ajaxSubmit(this.$popup.find('form')[0], 'awebooking_calculate_update_line_item_total')
      .done(function(response) {
        const $inputTotal = self.$popup.find('#edit_total');

        if (response.total && $inputTotal.val() != response.total) {
            $inputTotal
            .val(response.total)
            .effect('highlight');
        }
      })
      .always(function() {
        self.doingAjax = false;
      });
  }

  openPopup(e) {
    e.preventDefault();

    var self = this;
    const lineItem = $(e.currentTarget).data('lineItem');

    self.$popup.find('.awebooking-dialog-contents').html('<div class="awebooking-static-spinner"><span class="spinner"></span></div>');
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

        setTimeout(function() {
          $('form#post').submit();
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
