const $ = window.jQuery;
const Utils = require('./utils.js');

class ToggleClass {

  constructor(el) {
    this.el = el;
    this.target = Utils.getSelectorFromElement(el);

    if (!this.target) {
      this.target = $(el).parent().children('.awebooking-main-toggle')[0];
    }

    if (this.target) {
      $(this.el).on('click', this.toggleClass.bind(this));
      $(document).on('click', this.removeClass.bind(this));
    }
  }

  toggleClass(e) {
    e && e.preventDefault();
    $(this.target).parent().toggleClass('active');
  }

  removeClass(e) {
    if (e && $.contains($(this.target).parent()[0], e.target)) {
      return;
    }

    $(this.target).parent().removeClass('active');
  }
}

module.exports = ToggleClass;
