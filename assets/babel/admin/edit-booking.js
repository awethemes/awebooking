(function ($, plugin) {
  'use strict'

  const localize = window._awebookingEditBooking || {}
  const i18n = localize.i18n || {}

  /**
   * Handle the xhr fail.
   *
   * @param  {jqXHR} xhr
   * @return {void}
   */
  const handleXhrFail = function (xhr) {
    xhr.fail(function (xhr) {
      const res = xhr.responseJSON || $.parseJSON(xhr.responseText)

      if (res.message) {
        plugin.alert(res.message, res.status)
      }

      if (plugin.debug) {
        console.log(xhr)
      }
    })
  }

  class EditBooking {
    /**
     * Constructor.
     *
     * @return {void}
     */
    constructor() {
      $('.js-editnow').click(this.handleEditAddress)

      $('#js-add-note').click(this.handleAddNote)
      $(document).on('click', '.js-delete-note', this.handleDeleteNote)
    }

    /**
     * Handle toggle edit address.
     *
     * @param  {Event} e
     * @return {void}
     */
    handleEditAddress(e) {
      e.preventDefault()

      const focus = $(this).data('focus')
      const $wrapper = $(this).closest('.js-booking-column')

      $wrapper.find('h3 > a.button-editnow').hide()
      $wrapper.find('div.js-booking-data').hide()
      $wrapper.find('div.js-edit-booking-data').show()

      if (focus && $(focus, $wrapper).length) {
        $(focus, $wrapper).focus()
      }
    }

    /**
     * Handle click add note button.
     *
     * @param  {Event} e
     * @return {void}
     */
    handleAddNote(e) {
      e.preventDefault()

      const $notes = $('#js-booking-notes')
      const $noteInput = $('#js-booking-note')

      let content = (new String($noteInput.val())).trim()
      if (content.length === 0) {
        plugin.alert(i18n.empty_note_warning, 'warning')
        return
      }

      let noteType = ''
      if ($('#js-customer-note').prop('checked') === true) {
        noteType = 'customer'
      }

      const xhr = $.ajax({
        type: 'POST',
        url: plugin.route('/ajax/booking-note'),
        data: {
          note: content,
          note_type: noteType,
          booking: parseInt($('#post_ID').val(), 10),
          _ajax_nonce: localize.add_note_nonce,
        }
      })

      xhr.done(function (res) {
        $noteInput.val('')
        $(res.data).prependTo($notes)

        if ($notes.find('.awebooking-no-items').length) {
          $notes.find('.awebooking-no-items').closest('li').remove()
        }
      })

      handleXhrFail(xhr)
    }

    /**
     * Handle delete note.
     *
     * @param  {Event} e
     * @return {void}
     */
    handleDeleteNote(e) {
      e.preventDefault()

      const $el = $(this).closest('.booking-note')
      if (!$el.length || !$el.attr('rel')) {
        return
      }

      plugin.confirm(i18n.delete_note_warning, function () {
        const noteID = parseInt($el.attr('rel'), 10)

        const xhr = $.ajax({
          type: 'POST',
          url: plugin.route('/ajax/booking-note/' + noteID),
          data: { _method: 'DELETE', _ajax_nonce: localize.delete_note_nonce }
        })

        xhr.done(function () {
          $el.slideUp(100, function () { $el.remove() })
        })

        handleXhrFail(xhr)
      })
    }
  }

  // Document ready!
  $(function () {
    plugin.instances.editBooking = new EditBooking
  })

})(jQuery, window.awebooking || {})
