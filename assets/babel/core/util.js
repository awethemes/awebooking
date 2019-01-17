import debounce from 'debounce'
import isMobile from 'is-mobile'
import * as queryString from 'query-string'

const Utils = (function ($) {
  function getTransitionEndEvent() {
    let transitionEndEvent = ''

    const transitionEndEvents = {
      'WebkitTransition': 'webkitTransitionEnd',
      'MozTransition': 'transitionend',
      'OTransition': 'otransitionend',
      'transition': 'transitionend'
    }

    for (let name in transitionEndEvents) {
      if ({}.hasOwnProperty.call(transitionEndEvents, name)) {
        let tempEl = document.createElement('p')
        if (typeof tempEl.style[name] !== 'undefined') {
          transitionEndEvent = transitionEndEvents[name]
        }
      }
    }

    return transitionEndEvent
  }

  return {
    isMobile: isMobile,
    debounce: debounce,

    queryString: queryString,

    TRANSITION_END: getTransitionEndEvent(),

    onTransitionEnd(el, callback) {
      let called = false

      $(el).one(this.TRANSITION_END, () => {
        callback()
        called = true
      })

      setTimeout(() => {
        if (!called) $(el).trigger(this.TRANSITION_END)
      }, this.getTransitionDurationFromElement(el))
    },

    getTransitionDurationFromElement(element) {
      if (!element) {
        return 0
      }

      // Get transition-duration of the element.
      let transitionDuration = $(element).css('transition-duration')
      const floatTransitionDuration = parseFloat(transitionDuration)

      // Return 0 if element or transition duration is not found.
      if (!floatTransitionDuration) {
        return 0
      }

      // If multiple durations are defined, take the first.
      transitionDuration = transitionDuration.split(',')[0]

      return parseFloat(transitionDuration) * 1000
    },

    getTargetFromElement(element) {
      let selector = element.getAttribute('data-target')

      if (!selector || selector === '#') {
        selector = element.getAttribute('href') || ''
      }

      try {
        return document.querySelector(selector) ? selector : null
      } catch (err) {
        return null
      }
    },
  }
})(jQuery)

export default Utils
