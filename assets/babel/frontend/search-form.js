'use strict';

const $ = window.jQuery;
const plugin = window.awebooking;

class SearchForm {
  constructor (el) {
    this.$el = $(el);

    const rangeDates = new plugin.utils.rangeDates('.searchbox', {

    });

    $('.searchbox__box', this.$el).each((i, box) => {
      $(box).data('popup', this.setuptPopper(box));
    });

    // console.log(rangeDates);
    // console.log(this);
  }

  setuptPopper(el) {
    const $html = $(el).find('.searchbox__popup');
    if ($html.length === 0) {
      return;
    }

    tippy(el, {
      theme: 'awebooking-popup',
      delay: 0,
      arrow: true,
      distance: 0,
      placement: 'bottom',
      trigger: 'click',
      interactive: true,
      performance: true,
      hideOnClick: true,
      animation: 'shift-toward',
      duration: [150, 150],
      html: $html[0],
      popperOptions: { modifiers: {
        hide: { enabled: false },
        preventOverflow: { enabled: false },
      }}
    });

    return el._tippy;
  }
}

module.exports = {
  init: function() {
    $('.searchbox').each(function () {
      new SearchForm(this);
    });
  }
}
