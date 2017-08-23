const $ = window.jQuery;
const awebooking = window.TheAweBooking;

class EditBooking {
  constructor(form) {
    this.form  = (form instanceof jQuery) ? form[0] : form;
    this.$form = $(this.form);

    this.$form.on('change', '#add_room', $.proxy(this.handleAddRoomChanges, this));
    this.$form.on('change', '#add_check_in_out_0', $.proxy(this.handleDateChanges, this));
    this.$form.on('change', '#add_check_in_out_1', $.proxy(this.handleDateChanges, this));

    $('button[type="submit"]', this.$form).prop('disabled', true);
    this.$form.on('submit', $.proxy(this.onSubmit, this));
  }

  onSubmit(e) {
    e.preventDefault();

    awebooking.ajaxSubmit(this.form, 'add_awebooking_line_item')
      .done(function(response) {

        // TODO: Improve this!
        setTimeout(function() {
          window.location.reload();
        }, 250);

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

$(function() {

  const $form = $('#awebooking-add-line-item-form');
  if ($form.length > 0) {
    new EditBooking($form);
  }
});
