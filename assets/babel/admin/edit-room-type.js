(function ($, plugin, document) {
  'use strict'

  const data = window._awebookingRooms || {}

  class RoomsGenerator {
    constructor() {
      this.rooms = ko.observableArray(data.rooms || [])
      this.scaffoldNumber = ko.observable(0)
    }

    add() {
      const title = this.getCurrentTitle()
      const length = this.rooms().length

      this.rooms.push({
        id: -1,
        name: `${title} ${length + 1}`,
      })
    }

    remove(item, e) {
      const self = this

      if (item.id < 0) {
        this._removeItemEffect(item, e)
        return
      }

      const ajaxDelete = function () {
        return plugin.ajax('DELETE', `/ajax/delete-room/${item.id}`, {
          _method: 'DELETE',
          _wpnonce: data.deleteNonce,
        }, function (data) {
          self._removeItemEffect(item, e)
          plugin.alert(data.message, 'success')
        })
      }

      plugin.confirm(plugin.i18n.warning, () => {
        ajaxDelete()
      })
    }

    _removeItemEffect(item, e) {
      $(e.currentTarget).closest('li').effect('highlight', {}, 150, () => {
        this.rooms.remove(item)
      })
    }

    scaffold() {
      const title = this.getCurrentTitle()
      const total = parseInt(this.scaffoldNumber(), 10) || 1

      // Clear the rooms.
      this.rooms([])

      for (var i = 0; i < total; i++) {
        this.rooms.push({
          id: -1,
          name: `${title} ${i + 1}`,
        })
      }
    }

    getCurrentTitle() {
      let titleEl = document.getElementById('title')
      return titleEl ? titleEl.value.trim() : 'R'
    }
  }

  $(function () {
    // Apply rooms generator binding.
    ko.applyBindings(new RoomsGenerator(), document.getElementById('js-rooms-list'))

    const $roomSortable = $('.js-sorting-rooms')

    if ($roomSortable.length > 0) {
      new Sortable($roomSortable[0], {
        handle: '.abrs-sortable__handle',
        animation: 150,
      })
    }

    // Handle click on tabs.
    const $metabox = $('#awebooking-room-type-data')
    $('.awebooking-tabs > li:first > a', $metabox).trigger('click')

    $metabox.on('click', '.awebooking-tabs a', (e) => {
      e.preventDefault()
      const $target = $(e.target)

      $('.awebooking-tabs > li.active', $metabox).removeClass('active')
      $target.parent().addClass('active')

      $('.awebooking-tabs-panels > div', $metabox).hide()
      $($target.attr('href'), this.$metabox).show()
    })

    /**
     * Scroll to first checked category.
     *
     * @link https://github.com/scribu/wp-category-checklist-tree/blob/master/category-checklist-tree.php
     */
    $('[id$="-all"] > ul.categorychecklist').each(function () {
      const $list = $(this)
      const $firstChecked = $list.find(':checked').first()

      if (!$firstChecked.length) {
        return
      }

      const posFirst = $list.find('input').position().top
      const posChecked = $firstChecked.position().top

      $list.closest('.tabs-panel').scrollTop(posChecked - posFirst + 5)
    })
  })

})(jQuery, window.awebooking, document)
