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
      Popup.setup(this.target);

      $(this.el).on('click', this.open.bind(this));
      $(this.target).on('click', '[data-dismiss="awebooking-popup"]', this.close.bind(this));
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

  static setup(target) {
    const $target = $(target);
    if (! $target.length) {
      return;
    }

    if ($target.dialog('instance')) {
      return;
    }

    let _triggerResize = function() {
      if ($target.dialog('isOpen')) {
        $target.dialog('option', 'position', { my: 'center', at: 'center top+25%', of: window });
      }
    }

    let dialog = $target.dialog({
      modal: true,
      width: 'auto',
      height: 'auto',
      autoOpen: false,
      draggable: true,
      resizable: false,
      closeOnEscape: true,
      dialogClass: 'wp-dialog awebooking-dialog',
      position: { my: 'center', at: 'center top+25%', of: window },
      open: function () {
        // $('body').css({ overflow: 'hidden' });
      },
      beforeClose: function(event, ui) {
        // $('body').css({ overflow: 'inherit' });
     }
    });

    // $(window).on('resize', _.debounce(_triggerResize, 250));

    return dialog;
  }
}

module.exports = Popup;
