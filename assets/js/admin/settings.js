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
/******/ 	return __webpack_require__(__webpack_require__.s = 9);
/******/ })
/************************************************************************/
/******/ ({

/***/ "3ux1":
/***/ (function(module, exports) {

(function ($, plugin) {
  'use strict';

  var settings = window._awebookingSettings || {};
  /**
   * Handle leaving using window.onbeforeunload.
   *
   * @return {void}
   */

  function handleLeaving() {
    var changed = false; // Set the changed if any controls fire change.

    $('input, textarea, select').on('change', function () {
      changed = true;
    });
    $('.awebooking-settings').on('click', '.nav-tab-wrapper a', function () {
      if (changed) {
        window.onbeforeunload = function () {
          return settings.i18n.nav_warning;
        };
      } else {
        window.onbeforeunload = null;
      }
    }).on('click', '.submit button', function () {
      window.onbeforeunload = null;
    });
  }
  /**
   * Init the datepicker.
   *
   * @return {void}
   */


  function initDatepicker() {
    $('#display_datepicker_disabledates').flatpickr({
      mode: 'multiple',
      dateFormat: 'Y-m-d'
    });
  }
  /**
   * Setup input table bindings.
   *
   * @return {void}
   */


  function setupInputTable() {
    var $table = $('.awebooking-input-table');
    var shifted, controlled, hasFocus;
    shifted = controlled = hasFocus = false;
    $(document).bind('keyup keydown', function (e) {
      shifted = e.shiftKey;
      controlled = e.ctrlKey || e.metaKey;
    });

    var handleClickInputTable = function handleClickInputTable(el, e) {
      var $elRow = $(el).closest('tr');
      var $elTable = $(el).closest('table, tbody');

      if (e.type === 'focus' && hasFocus !== $elRow.index() || e.type === 'click' && $(el).is(':focus')) {
        hasFocus = $elRow.index();

        if (!shifted && !controlled) {
          $('tr', $elTable).removeClass('current').removeClass('last-selected');
          $elRow.addClass('current').addClass('last-selected');
        } else if (shifted) {
          $('tr', $elTable).removeClass('current');
          $elRow.addClass('selected-now').addClass('current');

          if ($('tr.last-selected', $elTable).length > 0) {
            if ($elRow.index() > $('tr.last-selected', $elTable).index()) {
              $('tr', $elTable).slice($('tr.last-selected', $elTable).index(), $elRow.index()).addClass('current');
            } else {
              $('tr', $elTable).slice($elRow.index(), $('tr.last-selected', $elTable).index() + 1).addClass('current');
            }
          }

          $('tr', $elTable).removeClass('last-selected');
          $elRow.addClass('last-selected');
        } else {
          $('tr', $elTable).removeClass('last-selected');

          if (controlled && $(el).closest('tr').is('.current')) {
            $elRow.removeClass('current');
          } else {
            $elRow.addClass('current').addClass('last-selected');
          }
        }

        $('tr', $elTable).removeClass('selected-now');
      }
    };

    var handleRemoveRows = function handleRemoveRows(el, e) {
      var $tbody = $(el).closest('table').find('tbody');

      if ($tbody.find('tr.current').length > 0) {
        var $current = $tbody.find('tr.current');
        $current.each(function () {
          $(this).remove();
        });
      }
    };

    $table.on('blur', 'input', function () {
      hasFocus = false;
    }).on('focus click', 'input', function (e) {
      handleClickInputTable(this, e);
    }).on('click', '.remove_rows', function (e) {
      e.preventDefault();
      handleRemoveRows(this, e);
    });
  }

  function setupInputTableSortable() {}
  /*$('.wc_input_table.sortable tbody').sortable({
   items: 'tr',
   cursor: 'move',
   axis: 'y',
   scrollSensitivity: 40,
   forcePlaceholderSize: true,
   helper: 'clone',
   opacity: 0.65,
   placeholder: 'wc-metabox-sortable-placeholder',
   start: function (event, ui) {
   ui.item.css('background-color', '#f6f6f6');
   },
   stop: function (event, ui) {
   ui.item.removeAttr('style');
   }
   });
    // Focus on inputs within the table if clicked instead of trying to sort.
   $('.wc_input_table.sortable tbody input').on('click', function () {
   $(this).focus();
   });*/

  /** Document ready */


  $(function () {
    handleLeaving();
    initDatepicker();
    setupInputTable(); // setupInputTableSortable();
  });
})(jQuery, window.awebooking);

/***/ }),

/***/ 9:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("3ux1");


/***/ })

/******/ });