import Util from './util'

const Dropdown = (($, Popper) => {
  'use strict'

  class Dropdown {
    constructor(element, options) {
      this.element = element
      this.options = Object.assign({}, Dropdown.defaults, options)
      this.drop = this._getDropElement()
      this.popper = null

      if (!this.drop || typeof this.drop === 'undefined') {
        throw new Error('Drop Error: Cannot find the drop element.')
      }

      if (typeof Popper !== 'undefined' && !this.popper) {
        let referenceElement = this.element
        this.popper = new Popper(referenceElement, this.drop, this._getPopperConfig())
      }

      this._addEventListeners()
      Dropdown.allDrops.push(this)
    }

    isOpened() {
      return this.drop.classList.contains('open')
    }

    isDisabled() {
      return (this.element.disabled || this.element.classList.contains('disabled'))
    }

    toggle() {
      if (this.isOpened()) {
        this.close()
      } else {
        this.open()
      }
    }

    open() {
      if (this.isDisabled() || this.isOpened()) {
        return
      }

      this.element.focus()
      this.element.setAttribute('aria-expanded', true)

      // If this is a touch-enabled device we add extra
      // empty mouseover listeners to the body's immediate children;
      // only needed because of broken event delegation on iOS
      // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html
      if ('ontouchstart' in document.documentElement) {
        $(document.body).children().on('mouseover', null, $.noop)
      }

      this.drop.classList.add('open')
      this.drop.setAttribute('aria-hidden', true)

      if (this.popper) {
        this.popper.update()
      }

      setTimeout(() => { this.drop.classList.add('open--transition') })
    }

    close() {
      if (this.isDisabled() || !this.isOpened()) {
        return
      }

      // If this is a touch-enabled device we remove the extra
      // empty mouseover listeners we added for iOS support
      if ('ontouchstart' in document.documentElement) {
        $(document.body).children().off('mouseover', null, $.noop)
      }

      this.element.setAttribute('aria-expanded', false)
      this.drop.removeAttribute('aria-hidden')
      this.drop.classList.remove('open--transition')

      Util.onTransitionEnd(this.drop, () => {
        this.drop.classList.remove('open')
      })
    }

    _addEventListeners() {
      if (!this.options.openOn) {
        return
      }

      if (this.options.openOn === 'always') {
        setTimeout(this.open.bind(this))
        return
      }

      const events = this.options.openOn.split(' ')

      if (events.indexOf('click') >= 0) {
        $(this.element).on('click', (e) => {
          e.preventDefault()
          // e.stopPropagation();

          this.toggle()
        })

        $(document).on('click', (e) => {
          if (!this.isOpened()) {
            return
          }

          // Clicking inside dropdown
          if (e.target === this.drop || this.drop.contains(e.target)) {
            return
          }

          // Clicking target
          if (e.target === this.element || this.element.contains(e.target)) {
            return
          }

          this.close(e)
        })
      }

      if (events.indexOf('hover') >= 0) {
        // TODO: ...
      }

      if (events.indexOf('focus') >= 0) {
        // TODO: ...
      }
    }

    _getDropElement() {
      if (!this.drop) {
        const parent = this.element.parentNode
        const target = Util.getTargetFromElement(this.element)

        if (target) {
          this.drop = document.querySelector(target)
        } else {
          this.drop = parent ? parent.querySelector(this.options.drop) : null
        }
      }

      return this.drop
    }

    _getPopperConfig() {
      const offset = {}

      if (typeof this.options.offset === 'function') {
        offset.fn = (data) => {
          data.offsets = Object.assign({}, data.offsets, this.options.offset(data.offsets) || {})
          return data
        }
      } else {
        offset.offset = this.options.offset
      }

      const config = {
        placement: this._getPlacement(),
        modifiers: {
          offset: offset,
          flip: { enabled: this.options.flip },
          preventOverflow: { boundariesElement: this.options.boundary }
        }
      }

      // Disable Popper.js if we have a static display.
      if (this.options.display === 'static') {
        config.modifiers.applyStyle = {
          enabled: false
        }
      }

      return config
    }

    _getPlacement() {
      return 'bottom-start'
    }
  }

  // Store dropdown instances.
  Dropdown.allDrops = []

  Dropdown.defaults = {
    drop: '[data-drop]',
    offset: 0,
    flip: true,
    openOn: 'click',
    boundary: 'scrollParent',
    reference: 'toggle',
    display: 'dynamic',
  }

  return Dropdown
})(jQuery, window.Popper)

export default Dropdown
