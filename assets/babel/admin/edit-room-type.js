(function($, plugin) {
  'use strict';

  /**
   * Main room type meta-box class.
   */
  class RoomTypeMetabox {
    /**
     * Constructor
     *
     * @return {void}
     */
    constructor() {
      this.$metabox = $('#awebooking-room-type-data');

      // Handle click on tabs.
      this.$metabox.on('click', '.awebooking-tabs a', this.handleClickTab.bind(this));
      $('.awebooking-tabs > li:first > a', this.$metabox).trigger('click');

      // Handle rooms.
      this.sortableRooms = new Sortable($('.js-sorting-rooms')[0], {
        handle: '.abrs-sortable__handle',
        animation: 150,
      });

      this.$metabox.on('click', '.js-add-room', this.handleAddRoom.bind(this));
      this.$metabox.on('click', '.js-scaffold-rooms', this.handleScaffoldRooms.bind(this));
    }

    /**
     * Handle click on tab.
     *
     * @param  {Event]} e
     * @return {void}
     */
    handleClickTab(e) {
      e.preventDefault();

      const $target = $(e.target);

      $('.awebooking-tabs > li.active', this.$metabox).removeClass('active');
      $target.parent().addClass('active');

      $('.awebooking-tabs-panels > div', this.$metabox).hide();
      $($target.attr('href'), this.$metabox).show();
    }

    handleAddRoom(e) {
      e.preventDefault();

      const title = (new String($('#title').val())).trim();
      const template = wp.template('template-room-item');

      const i = -1;
      $('.js-list-rooms').append(template({
        index:   i,
        id:      -1,
        name:   `${title} ${i+1}`,
        prefix: `_rooms[${i}]`,
      }));
    }

    /**
     * Handle scaffold rooms.
     *
     * @param  {Event]} e
     * @return {void}
     */
    handleScaffoldRooms(e) {
      e.preventDefault();

      let title = (new String($('#title').val())).trim();

      let total = $('[name="_scaffold_number_rooms"]', this.$metabox).val();
      total = parseInt(total, 10);

      let htmlRooms = '';
      const template = wp.template('template-room-item');

      for (var i = 0; i < total; i++) {
        htmlRooms = htmlRooms + template({
          index:   i,
          id:      -1,
          name:   `${title} ${i+1}`,
          prefix: `_scaffold_rooms[${i}]`,
        });
      }

      $('.js-list-rooms').show().html(htmlRooms);
    }
  }

  // Document ready!
  $(function() {
    plugin.instances.roomtype_metabox = new RoomTypeMetabox;
  });

  /**
   * Scroll to first checked category.
   *
   * @link https://github.com/scribu/wp-category-checklist-tree/blob/master/category-checklist-tree.php
   */
  $(function() {
    $('[id$="-all"] > ul.categorychecklist').each(function() {
      const $list = $(this);
      const $firstChecked = $list.find(':checked').first();

      if (! $firstChecked.length) {
        return;
      }

      const posFirst = $list.find('input').position().top;
      const posChecked = $firstChecked.position().top;

      $list.closest('.tabs-panel').scrollTop(posChecked - posFirst + 5);
    });
  });

})(jQuery, window.awebooking);
