(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

(function ($, plugin) {
  'use strict';

  var settings = window._awebookingSettings || {};

  var MainSetting = function () {
    function MainSetting() {
      _classCallCheck(this, MainSetting);
    }

    _createClass(MainSetting, [{
      key: 'handleLeaving',

      /**
       * Handle leaving using window.onbeforeunload.
       *
       * @return {void}
       */
      value: function handleLeaving() {
        var changed = false;

        // Set the changed if any controls fire change.
        $('input, textarea, select, checkbox').on('change', function () {
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
    }]);

    return MainSetting;
  }();

  /** Document ready */


  $(function () {
    var main = new MainSetting();

    main.handleLeaving();
  });
})(jQuery, window.awebooking);

},{}]},{},[1]);

//# sourceMappingURL=settings.js.map
