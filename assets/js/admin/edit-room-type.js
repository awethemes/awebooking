(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

(function ($, plugin, document) {
  'use strict';

  var data = window._awebookingRooms || {};

  var RoomsGenerator = function () {
    function RoomsGenerator() {
      _classCallCheck(this, RoomsGenerator);

      this.rooms = ko.observableArray(data.rooms || []);
      this.scaffoldNumber = ko.observable(0);
    }

    _createClass(RoomsGenerator, [{
      key: 'add',
      value: function add() {
        var title = this.getCurrentTitle();
        var length = this.rooms().length;

        this.rooms.push({
          id: -1,
          name: title + ' ' + (length + 1)
        });
      }
    }, {
      key: 'remove',
      value: function remove(item, e) {
        var self = this;

        if (item.id < 0) {
          this._removeItemEffect(item, e);
          return;
        }

        var ajaxDelete = function ajaxDelete() {
          return plugin.ajax('DELETE', '/ajax/delete-room/' + item.id, {
            _method: 'DELETE',
            _wpnonce: data.deleteNonce
          }, function (data) {
            self._removeItemEffect(item, e);
            plugin.alert(data.message, 'success');
          });
        };

        plugin.confirm(plugin.i18n.warning, function () {
          ajaxDelete();
        });
      }
    }, {
      key: '_removeItemEffect',
      value: function _removeItemEffect(item, e) {
        var _this = this;

        $(e.currentTarget).closest('li').effect('highlight', {}, 150, function () {
          _this.rooms.remove(item);
        });
      }
    }, {
      key: 'scaffold',
      value: function scaffold() {
        var title = this.getCurrentTitle();
        var total = parseInt(this.scaffoldNumber(), 10) || 1;

        // Clear the rooms.
        this.rooms([]);

        for (var i = 0; i < total; i++) {
          this.rooms.push({
            id: -1,
            name: title + ' ' + (i + 1)
          });
        }
      }
    }, {
      key: 'getCurrentTitle',
      value: function getCurrentTitle() {
        var titleEl = document.getElementById('title');
        return titleEl ? titleEl.value.trim() : 'R';
      }
    }]);

    return RoomsGenerator;
  }();

  $(function () {
    var _this2 = this;

    // Apply rooms generator binding.
    ko.applyBindings(new RoomsGenerator(), document.getElementById('js-rooms-list'));

    new Sortable($('.js-sorting-rooms')[0], {
      handle: '.abrs-sortable__handle',
      animation: 150
    });

    // Handle click on tabs.
    var $metabox = $('#awebooking-room-type-data');
    $('.awebooking-tabs > li:first > a', $metabox).trigger('click');

    $metabox.on('click', '.awebooking-tabs a', function (e) {
      e.preventDefault();
      var $target = $(e.target);

      $('.awebooking-tabs > li.active', $metabox).removeClass('active');
      $target.parent().addClass('active');

      $('.awebooking-tabs-panels > div', $metabox).hide();
      $($target.attr('href'), _this2.$metabox).show();
    });

    /**
     * Scroll to first checked category.
     *
     * @link https://github.com/scribu/wp-category-checklist-tree/blob/master/category-checklist-tree.php
     */
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
})(jQuery, window.awebooking, document);

},{}]},{},[1]);

//# sourceMappingURL=edit-room-type.js.map
