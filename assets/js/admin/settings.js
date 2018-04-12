"use strict";

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(function ($) {
  'use strict';

  var awebooking = window.awebooking || {};
  var settings = window._awebookingSettings || {};

  var MainSetting =
  /*#__PURE__*/
  function () {
    function MainSetting() {
      _classCallCheck(this, MainSetting);
    }

    _createClass(MainSetting, [{
      key: "handleLeaving",

      /**
       * Handle leaving using window.onbeforeunload.
       *
       * @return {void}
       */
      value: function handleLeaving() {
        var changed = false; // Set the changed if any controls fire change.

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
})(jQuery);
//# sourceMappingURL=settings.js.map
