class Field {
  /**
   * Constructor of class.
   */
  constructor($el, args) {
    this.$el  = $el;
    this.args = args

    jQuery(document).on('cmb2_add_row', function(e, row) {
      console.log(row);
    })
  }

  /**
   * Init the field.
   */
  fire() {
    // ...
  }

  getControl() {

  }
}

module.exports = Field;
