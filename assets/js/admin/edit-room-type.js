(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

(function ($, plugin) {
  'use strict';

  /**
   * Main room type meta-box class.
   */

  var RoomTypeMetabox = function () {
    /**
     * Constructor
     *
     * @return {void}
     */
    function RoomTypeMetabox() {
      _classCallCheck(this, RoomTypeMetabox);

      this.$metabox = $('#awebooking-room-type-data');

      // Handle click on tabs.
      this.$metabox.on('click', '.awebooking-tabs a', this.handleClickTab.bind(this));
      $('.awebooking-tabs > li:first > a', this.$metabox).trigger('click');

      // Handle rooms.
      this.sortableRooms = new Sortable($('.js-sorting-rooms')[0], {
        handle: '.abrs-sortable__handle',
        animation: 150
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


    _createClass(RoomTypeMetabox, [{
      key: 'handleClickTab',
      value: function handleClickTab(e) {
        e.preventDefault();

        var $target = $(e.target);

        $('.awebooking-tabs > li.active', this.$metabox).removeClass('active');
        $target.parent().addClass('active');

        $('.awebooking-tabs-panels > div', this.$metabox).hide();
        $($target.attr('href'), this.$metabox).show();
      }
    }, {
      key: 'handleAddRoom',
      value: function handleAddRoom(e) {
        e.preventDefault();

        var title = new String($('#title').val()).trim();
        var template = wp.template('template-room-item');

        var i = -1;
        $('.js-list-rooms').append(template({
          index: i,
          id: -1,
          name: title + ' ' + (i + 1),
          prefix: '_rooms[' + i + ']'
        }));
      }

      /**
       * Handle scaffold rooms.
       *
       * @param  {Event]} e
       * @return {void}
       */

    }, {
      key: 'handleScaffoldRooms',
      value: function handleScaffoldRooms(e) {
        e.preventDefault();

        var title = new String($('#title').val()).trim();

        var total = $('[name="_scaffold_number_rooms"]', this.$metabox).val();
        total = parseInt(total, 10);

        var htmlRooms = '';
        var template = wp.template('template-room-item');

        for (var i = 0; i < total; i++) {
          htmlRooms = htmlRooms + template({
            index: i,
            id: -1,
            name: title + ' ' + (i + 1),
            prefix: '_scaffold_rooms[' + i + ']'
          });
        }

        $('.js-list-rooms').show().html(htmlRooms);
      }
    }]);

    return RoomTypeMetabox;
  }();

  // Document ready!


  $(function () {
    plugin.instances.roomtype_metabox = new RoomTypeMetabox();
  });

  /**
   * Scroll to first checked category.
   *
   * @link https://github.com/scribu/wp-category-checklist-tree/blob/master/category-checklist-tree.php
   */
  $(function () {
    $('[id$="-all"] > ul.categorychecklist').each(function () {
      var $list = $(this);
      var $firstChecked = $list.find(':checked').first();

      if (!$firstChecked.length) {
        return;
      }

      var posFirst = $list.find('input').position().top;
      var posChecked = $firstChecked.position().top;

      $list.closest('.tabs-panel').scrollTop(posChecked - posFirst + 5);
    });
  });
})(jQuery, window.awebooking);

},{}]},{},[1]);

//# sourceMappingURL=edit-room-type.js.map
