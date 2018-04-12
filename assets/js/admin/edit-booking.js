"use strict";

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(function ($, awebooking) {
  'use strict';

  var EditBooking =
  /*#__PURE__*/
  function () {
    function EditBooking() {
      _classCallCheck(this, EditBooking);

      $('.js-editnow').click(this.handleEditAddress);
    }

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
    }]);

    return EditBooking;
  }();
  /**
   * Document ready!
   *
   * @return {void}
   */


  $(function () {
    awebooking.instances.editBooking = new EditBooking();
  });
})(jQuery, window.awebooking || {});
//# sourceMappingURL=edit-booking.js.map
