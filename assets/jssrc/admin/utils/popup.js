const $ = window.jQuery;
const Utils = require('./utils.js');

class Popup {
  /**
   * Wrapper the jquery-ui-popup.
   */
  constructor(el) {
    this.el = el;
    this.target = Utils.getSelectorFromElement(el);

    if (this.target) {
      this.setup();

      $(this.el).on('click', $.proxy(this.open, this));
      $(this.target).on('click', '[data-dismiss="awebooking-popup"]', $.proxy(this.close, this));
    }
  }

  open(e) {
    e && e.preventDefault();
    $(this.target).dialog('open');
  }

  close(e) {
    e && e.preventDefault();
    $(this.target).dialog('close');
  }

  setup() {
    if ($(this.target).dialog('instance')) {
      return;
    }

    $(this.target).dialog({
      title: $(this.el).attr('title'),
      dialogClass: 'wp-dialog awebooking-dialog',
      modal: true,
      width: 'auto',
      height: 'auto',
      autoOpen: false,
      draggable: false,
      resizable: false,
      closeOnEscape: true,
      position: { at: 'center top+35%' },
      open: function () {
        $('body').css({ overflow: 'hidden' });
      },
      beforeClose: function(event, ui) {
        $('body').css({ overflow: 'inherit' });
     }
    });
  }
}

module.exports = Popup;
