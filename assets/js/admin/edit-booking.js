(function () {
  'use strict';

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
  }

  (function ($, plugin) {

    var localize = window._awebookingEditBooking || {};
    var i18n = localize.i18n || {};
    /**
     * Handle the xhr fail.
     *
     * @param  {jqXHR} xhr
     * @return {void}
     */

    var handleXhrFail = function handleXhrFail(xhr) {
      xhr.fail(function (xhr) {
        var res = xhr.responseJSON || $.parseJSON(xhr.responseText);

        if (res.message) {
          plugin.alert(res.message, res.status);
        }

        if (plugin.debug) {
          console.log(xhr);
        }
      });
    };

    var EditBooking =
    /*#__PURE__*/
    function () {
      /**
       * Constructor.
       *
       * @return {void}
       */
      function EditBooking() {
        _classCallCheck(this, EditBooking);

        $('.js-editnow').click(this.handleEditAddress);
        $('#js-add-note').click(this.handleAddNote);
        $(document).on('click', '.js-delete-note', this.handleDeleteNote);
      }
      /**
       * Handle toggle edit address.
       *
       * @param  {Event} e
       * @return {void}
       */


      _createClass(EditBooking, [{
        key: "handleEditAddress",
        value: function handleEditAddress(e) {
          e.preventDefault();
          var focus = $(this).data('focus');
          var $wrapper = $(this).closest('.js-booking-column');
          $wrapper.find('h3 > a.button-editnow').hide();
          $wrapper.find('div.js-booking-data').hide();
          $wrapper.find('div.js-edit-booking-data').show();

          if (focus && $(focus, $wrapper).length) {
            $(focus, $wrapper).focus();
          }
        }
        /**
         * Handle click add note button.
         *
         * @param  {Event} e
         * @return {void}
         */

      }, {
        key: "handleAddNote",
        value: function handleAddNote(e) {
          e.preventDefault();
          var $notes = $('#js-booking-notes');
          var $noteInput = $('#js-booking-note');
          var content = new String($noteInput.val()).trim();

          if (content.length === 0) {
            plugin.alert(i18n.empty_note_warning, 'warning');
            return;
          }

          var noteType = '';

          if ($('#js-customer-note').prop('checked') === true) {
            noteType = 'customer';
          }

          var xhr = $.ajax({
            type: 'POST',
            url: plugin.route('/ajax/booking-note'),
            data: {
              note: content,
              note_type: noteType,
              booking: parseInt($('#post_ID').val(), 10),
              _ajax_nonce: localize.add_note_nonce
            }
          });
          xhr.done(function (res) {
            $noteInput.val('');
            $(res.data).prependTo($notes);

            if ($notes.find('.awebooking-no-items').length) {
              $notes.find('.awebooking-no-items').closest('li').remove();
            }
          });
          handleXhrFail(xhr);
        }
        /**
         * Handle delete note.
         *
         * @param  {Event} e
         * @return {void}
         */

      }, {
        key: "handleDeleteNote",
        value: function handleDeleteNote(e) {
          e.preventDefault();
          var $el = $(this).closest('.booking-note');

          if (!$el.length || !$el.attr('rel')) {
            return;
          }

          plugin.confirm(i18n.delete_note_warning, function () {
            var noteID = parseInt($el.attr('rel'), 10);
            var xhr = $.ajax({
              type: 'POST',
              url: plugin.route('/ajax/booking-note/' + noteID),
              data: {
                _method: 'DELETE',
                _ajax_nonce: localize.delete_note_nonce
              }
            });
            xhr.done(function () {
              $el.slideUp(100, function () {
                $el.remove();
              });
            });
            handleXhrFail(xhr);
          });
        }
      }]);

      return EditBooking;
    }(); // Document ready!


    $(function () {
      plugin.instances.editBooking = new EditBooking();
    });
  })(jQuery, window.awebooking || {});

}());

//# sourceMappingURL=edit-booking.js.map
