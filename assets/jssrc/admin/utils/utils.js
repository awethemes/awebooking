var $ = window.jQuery;

const Utils = {

  getSelectorFromElement(el) {
    let selector = el.getAttribute('data-target');

    if (!selector || selector === '#') {
      selector = el.getAttribute('href') || '';
    }

    try {
      const $selector = $(selector);
      return $selector.length > 0 ? selector : null;
    } catch (error) {
      return null;
    }
  },

};

module.exports = Utils;
