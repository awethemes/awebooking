/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 5);
/******/ })
/************************************************************************/
/******/ ({

/***/ 5:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("ahWw");


/***/ }),

/***/ "ahWw":
/***/ (function(module, exports) {

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

(function ($, plugin, document) {
  'use strict';

  var data = window._awebookingRooms || {};

  var RoomsGenerator =
  /*#__PURE__*/
  function () {
    function RoomsGenerator() {
      _classCallCheck(this, RoomsGenerator);

      this.rooms = ko.observableArray(data.rooms || []);
      this.scaffoldNumber = ko.observable(0);
    }

    _createClass(RoomsGenerator, [{
      key: "add",
      value: function add() {
        var title = this.getCurrentTitle();
        var length = this.rooms().length;
        this.rooms.push({
          id: -1,
          name: "".concat(title, " ").concat(length + 1)
        });
      }
    }, {
      key: "remove",
      value: function remove(item, e) {
        var self = this;

        if (item.id < 0) {
          this._removeItemEffect(item, e);

          return;
        }

        var ajaxDelete = function ajaxDelete() {
          return plugin.ajax('DELETE', "/ajax/delete-room/".concat(item.id), {
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
      key: "_removeItemEffect",
      value: function _removeItemEffect(item, e) {
        var _this = this;

        $(e.currentTarget).closest('li').effect('highlight', {}, 150, function () {
          _this.rooms.remove(item);
        });
      }
    }, {
      key: "scaffold",
      value: function scaffold() {
        var title = this.getCurrentTitle();
        var total = parseInt(this.scaffoldNumber(), 10) || 1; // Clear the rooms.

        this.rooms([]);

        for (var i = 0; i < total; i++) {
          this.rooms.push({
            id: -1,
            name: "".concat(title, " ").concat(i + 1)
          });
        }
      }
    }, {
      key: "getCurrentTitle",
      value: function getCurrentTitle() {
        var titleEl = document.getElementById('title');
        return titleEl ? titleEl.value.trim() : 'R';
      }
    }]);

    return RoomsGenerator;
  }();

  $(function () {
    var _this2 = this; // Apply rooms generator binding.


    ko.applyBindings(new RoomsGenerator(), document.getElementById('js-rooms-list'));
    var $roomSortable = $('.js-sorting-rooms');

    if ($roomSortable.length > 0) {
      new Sortable($roomSortable[0], {
        handle: '.abrs-sortable__handle',
        animation: 150
      });
    } // Handle click on tabs.


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

/***/ })

/******/ });