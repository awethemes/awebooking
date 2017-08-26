const $ = window.jQuery;
const DATE_FORMAT = 'yy-mm-dd';

class RangeDatepicker {

  constructor(fromDate, toDate) {
    this.toDate = toDate;
    this.fromDate = fromDate;
  }

  init() {
    let beforeShowCallback = function() {
      $('#ui-datepicker-div').addClass('cmb2-element');
    };

    let closeCallback = function() {
      $('#ui-datepicker-div').addClass('cmb2-element');
    };

    $(this.fromDate).datepicker({
      onClose: closeCallback,
      beforeShow: beforeShowCallback,
      dateFormat: DATE_FORMAT,
    }).on('change', this.applyFromChange.bind(this));

    $(this.toDate).datepicker({
      onClose: closeCallback,
      beforeShow: beforeShowCallback,
      dateFormat: DATE_FORMAT,
    }).on('change', this.applyToChange.bind(this));

    this.applyToChange();
    this.applyFromChange();
  }

  applyFromChange() {
    try {
      var minDate = $.datepicker.parseDate(DATE_FORMAT, $(this.fromDate).val());
      minDate.setDate(minDate.getDate() + 1);

      $(this.toDate).datepicker('option', 'minDate', minDate);
    } catch(e) {}
  }

  applyToChange() {
    try {
      var maxDate = $.datepicker.parseDate(DATE_FORMAT, $(this.toDate).val());
      $(this.fromDate).datepicker('option', 'maxDate', maxDate);
    } catch(e) {}
  }
}

module.exports = RangeDatepicker;
