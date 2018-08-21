module.export = function () {
  'use strict'

  if (!String.prototype.trim) {
    String.prototype.trim = function () {
      return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '')
    }
  }

  // Polyfill location.origin in IE
  // @see https://stackoverflow.com/a/25495161
  if (!window.location.origin) {
    window.location.origin = window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '')
  }
}
