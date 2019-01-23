import $ from 'jquery'

/**
 * //
 *
 * @type {*}
 */
const Synchronizer = {
  val: {
    update(to) {
      this.element.val(to)
    },

    refresh() {
      return this.element.val()
    }
  },

  checkbox: {
    update(to) {
      this.element.prop('checked', to)
    },

    refresh() {
      return this.element.prop('checked')
    }
  },

  radio: {
    update(to) {
      this.element.filter(function () {
        return this.value === to
      }).prop('checked', true)
    },

    refresh() {
      return this.element.filter(':checked').val()
    }
  },

  html: {
    update(to) {
      this.element.html(to)
    },

    refresh() {
      return this.element.html()
    }
  },
}

/**
 * Cast a string to a jQuery collection if it isn't already.
 *
 * @param {string|jQuery} element
 */
function ensureElement(element) {
  return typeof element == 'string' ? $(element) : element
}

/**
 * //
 *
 * @param {*} a
 * @param {*} b
 * @return {boolean}
 */
function isEquals(a, b) {
  const underscore = window._ || window.lodash

  if (typeof underscore !== 'undefined') {
    return underscore.isEqual(a, b)
  }

  // noinspection EqualityComparisonWithCoercionJS
  return a == b
}

/**
 * An observable value that syncs with an element
 *
 * Handles inputs, selects, and textareas by default
 */
export default class Control {
  constructor(element) {
    this._value = null
    this._dirty = false

    // Store and manage the callback lists
    this.callbacks = $.Callbacks()

    this.element = ensureElement(element)
    this.events = ''

    /* synchronizer */
    let _synchronizer = Synchronizer.html

    if (this.element.is('input, select, textarea')) {
      const type = this.element.prop('type')

      this.events += ' change input'
      _synchronizer = Synchronizer.val

      // For checkbox and radio inputs.
      if (this.element.is('input') && Synchronizer.hasOwnProperty(type)) {
        _synchronizer = Synchronizer[type]
      }
    }

    // Set the value from the input.
    $.extend(this, _synchronizer)

    const _initialize = this.initialize || void 0
    if (typeof _initialize === 'function') {
      _initialize.apply(this, arguments)
    }

    // Overwire some update & refresh methods.
    const self = this
    const { update, refresh } = this

    this.update = function (to) {
      if (to !== refresh.call(self)) {
        update.apply(this, arguments)
      }
    }

    this.refresh = function () {
      self.set(refresh.call(self))
    }

    this.get = this.get.bind(this)
    this.set = this.set.bind(this)
    this._setter = this._setter.bind(this)

    // Enable two-way bindings.
    this.bind(this.update)
    this.element.bind(this.events, this.refresh)

    // Set the initial value.
    if (null === this._value) {
      this._value = refresh.call(this)
    }
  }

  refresh() {}

  update() {}

  /**
   * Get the value.
   *
   * @return {*}
   */
  get() {
    return this._value
  }

  /**
   * Set the value and trigger all bound callbacks.
   *
   * @param {*} to New value.
   */
  set(to) {
    const from = this._value

    to = this._setter.apply(this, arguments)
    to = this.validate(to)

    // Bail if the sanitized value is null or unchanged.
    if (null === to || isEquals(from, to)) {
      return this
    }

    this._value = to
    this._dirty = true

    this.callbacks.fireWith(this, [to, from])

    return this
  }

  /**
   * Validate the value and return the sanitized value.
   *
   * @param {*} value
   * @return {*}
   */
  validate(value) {
    return value
  }

  _setter(to) {
    return to
  }

  setter(callback) {
    const from = this.get()
    this._setter = callback

    // Temporarily clear value so setter can decide if it's valid.
    this._value = null
    this.set(from)

    return this
  }

  resetSetter() {
    this._setter = this.constructor.prototype._setter

    this.set(this.get())

    return this
  }

  /**
   * Bind a function to be invoked whenever the value changes.
   *
   * @param {function} args A function, or multiple functions, to add to the callback stack.
   */
  bind(...args) {
    this.callbacks.add.apply(this.callbacks, args)

    return this
  }

  /**
   * Unbind a previously bound function.
   *
   * @param {function} args A function, or multiple functions, to remove from the callback stack.
   */
  unbind(...args) {
    this.callbacks.remove.apply(this.callbacks, args)

    return this
  }

  link(...values) {
    const set = this.set

    values.forEach(value => {
      value.bind(set)
    })

    return this
  }

  unlink(...values) {
    const set = this.set

    values.forEach(value => {
      value.unbind(set)
    })

    return this
  }

  sync(...values) {
    const that = this

    values.forEach(value => {
      that.link(value)
      value.link(that)
    })

    return this
  }

  unsync(...values) {
    const that = this

    values.forEach(value => {
      that.unlink(value)
      value.unlink(that)
    })

    return this
  }
}
