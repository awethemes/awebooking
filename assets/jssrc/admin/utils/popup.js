const $ = window.jQuery;
const Utils = require('./utils.js');

class Popup {
  constructor(el) {
    this.$el = $(el);

    $(document).on('click', this.$el, function(e) {
      e.preventDefault();
    });


    console.log(Utils.getSelectorFromElement(this.$el[0]));
  }

  setupDialog() {
    $('#my-dialog').dialog({
      title: 'My Dialog',
      dialogClass: 'wp-dialog',
      autoOpen: false,
      draggable: false,
      width: 'auto',
      modal: true,
      resizable: false,
      closeOnEscape: true,
      position: {
        at: "center top+30%",
      },
      open: function () {
        $("body").css({ overflow: 'hidden' })
        // close dialog by clicking the overlay behind it
        $('.ui-widget-overlay').bind('click', function(){
          // $('#my-dialog').dialog('close');
        })
      },
      beforeClose: function(event, ui) {
      $("body").css({ overflow: 'inherit' })
     },
      create: function () {
        // style fix for WordPress admin
        // $('.ui-dialog-titlebar-close').addClass('ui-button');
      },
    });
  }

  openPopup() {

  }

  closePopup() {

  }
}

module.exports = Popup;
