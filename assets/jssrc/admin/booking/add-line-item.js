const $ = window.jQuery;
const awebooking = window.TheAweBooking;

class AddLineItem {
  constructor(form) {
    this.form  = (form instanceof jQuery) ? form[0] : form;
    this.$form = $(this.form);

    this.$form.on('change', '#add_room', this.handleAddRoomChanges.bind(this));
    this.$form.on('change', '#add_check_in_out_0', this.handleDateChanges.bind(this));
    this.$form.on('change', '#add_check_in_out_1', this.handleDateChanges.bind(this));

    this.$form.on('change', '#add_adults, #add_children, [name="add_services\[\]"]', this.handleCalculateTotal.bind(this));

    $('button[type="submit"]', this.$form).prop('disabled', true);
    this.$form.on('submit', $.proxy(this.onSubmit, this));
  }

  onSubmit(e) {
    e.preventDefault();

    awebooking.ajaxSubmit(this.form, 'add_awebooking_line_item')
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

  handleCalculateTotal() {
    const self = this;

    awebooking.ajaxSubmit(this.form, 'awebooking_calculate_line_item_total')
      .done(function(response) {
        if (response.total) {
          self.$form.find('#add_price').val(response.total);
        }
      });
  }

  handleDateChanges() {
    if (! this.ensureInputDates()) {
      return;
    }

    // If any check-in/out changes,
    // we will reset the `add_room` input.
    this.$form.find('#add_room').val('');

    // Then, call ajax to update new template.
    this.ajaxUpdateForm();
  }

  handleAddRoomChanges() {
    const self = this;

    if (! this.ensureInputDates()) {
      return;
    }

    this.ajaxUpdateForm()
      .done(function() {
        $('button[type="submit"]', self.$form).prop('disabled', false);
      });
  }

  ajaxUpdateForm() {
    const self = this;
    const $container = self.$form.find('.awebooking-dialog-contents');

    return awebooking.ajaxSubmit(this.form, 'get_awebooking_add_item_form')
      .done(function(response) {
        $('#add_check_in_out_0', $container).datepicker('destroy');
        $('#add_check_in_out_1', $container).datepicker('destroy');

        $container.html(response.html);
      });
  }

  ensureInputDates() {
    var $check_in  = this.$form.find('#add_check_in_out_0');
    var $check_out = this.$form.find('#add_check_in_out_1');

    return $check_in.val() && $check_out.val();
  }
}

module.exports = AddLineItem;
