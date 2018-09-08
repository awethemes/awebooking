(function ($) {
  'use strict';

  $ = $ && $.hasOwnProperty('default') ? $['default'] : $;

  var SELECTED = 'selected';
  var Selector = {
    ROOT: '.payment-methods',
    ITEM: '.payment-method',
    INPUT: 'input[type="radio"]',
    SELECTED: '.selected'
  };

  var _handleLabelClick = function _handleLabelClick(e) {
    var element = e.currentTarget;
    var input = element.querySelector(Selector.INPUT); // Prevent action on non-input.

    if (!input) {
      return;
    }

    var triggerChange = true;
    var rootElement = $(element).closest(Selector.ROOT)[0];

    if (input.checked && $(element).hasClass(SELECTED)) {
      triggerChange = false;
    } else {
      $(rootElement).children(Selector.SELECTED).removeClass(SELECTED);
    }

    if (triggerChange) {
      if (input.hasAttribute('disabled') || input.classList.contains('disabled')) {
        e.preventDefault();
        return;
      }

      input.checked = !element.classList.contains(SELECTED);
      input.focus({
        preventScroll: true
      });
      $(input).trigger('change');
      $(element).addClass(SELECTED);
    }
  };

  var _triggerPaymentMethod = function _triggerPaymentMethod(e) {
    var input = e.currentTarget;

    if (!input.checked) {
      return;
    }

    var root = $(input).closest(Selector.ROOT)[0];
    var event = $.Event('selected.awebooking.gateway', {
      relatedTarget: input
    });
    $(root).trigger(event, input.value);
  };

  $(function () {
    var $el = $('#payment-methods').on('click', Selector.ITEM, _handleLabelClick).on('change', Selector.INPUT, _triggerPaymentMethod);
    setTimeout(function () {
      $el.find("".concat(Selector.INPUT, ":checked")).closest(Selector.ITEM).trigger('click');
    }, 0);
  });

}(jQuery));

//# sourceMappingURL=checkout.js.map
