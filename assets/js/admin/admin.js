(function ($) {
  'use strict';

  $ = $ && $.hasOwnProperty('default') ? $['default'] : $;

  /**
   * Returns a function, that, as long as it continues to be invoked, will not
   * be triggered. The function will be called after it stops being called for
   * N milliseconds. If `immediate` is passed, trigger the function on the
   * leading edge, instead of the trailing. The function also has a property 'clear' 
   * that is a function which will clear the timer to prevent previously scheduled executions. 
   *
   * @source underscore.js
   * @see http://unscriptable.com/2009/03/20/debouncing-javascript-methods/
   * @param {Function} function to wrap
   * @param {Number} timeout in ms (`100`)
   * @param {Boolean} whether to execute at the beginning (`false`)
   * @api public
   */
  function debounce(func, wait, immediate) {
    var timeout, args, context, timestamp, result;
    if (null == wait) wait = 100;

    function later() {
      var last = Date.now() - timestamp;

      if (last < wait && last >= 0) {
        timeout = setTimeout(later, wait - last);
      } else {
        timeout = null;

        if (!immediate) {
          result = func.apply(context, args);
          context = args = null;
        }
      }
    }

    var debounced = function debounced() {
      context = this;
      args = arguments;
      timestamp = Date.now();
      var callNow = immediate && !timeout;
      if (!timeout) timeout = setTimeout(later, wait);

      if (callNow) {
        result = func.apply(context, args);
        context = args = null;
      }

      return result;
    };

    debounced.clear = function () {
      if (timeout) {
        clearTimeout(timeout);
        timeout = null;
      }
    };

    debounced.flush = function () {
      if (timeout) {
        result = func.apply(context, args);
        context = args = null;
        clearTimeout(timeout);
        timeout = null;
      }
    };

    return debounced;
  }

  debounce.debounce = debounce;
  var debounce_1 = debounce;

  var isMobile_1 = isMobile;
  var isMobile_2 = isMobile;

  function isMobile(ua) {
    if (!ua && typeof navigator != 'undefined') ua = navigator.userAgent;

    if (ua && ua.headers && typeof ua.headers['user-agent'] == 'string') {
      ua = ua.headers['user-agent'];
    }

    if (typeof ua != 'string') return false;
    return /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(ua) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(ua.substr(0, 4));
  }
  isMobile_1.isMobile = isMobile_2;

  function _typeof(obj) {
    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
      _typeof = function (obj) {
        return typeof obj;
      };
    } else {
      _typeof = function (obj) {
        return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
      };
    }

    return _typeof(obj);
  }

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
  }

  function _slicedToArray(arr, i) {
    return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest();
  }

  function _arrayWithHoles(arr) {
    if (Array.isArray(arr)) return arr;
  }

  function _iterableToArrayLimit(arr, i) {
    var _arr = [];
    var _n = true;
    var _d = false;
    var _e = undefined;

    try {
      for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
        _arr.push(_s.value);

        if (i && _arr.length === i) break;
      }
    } catch (err) {
      _d = true;
      _e = err;
    } finally {
      try {
        if (!_n && _i["return"] != null) _i["return"]();
      } finally {
        if (_d) throw _e;
      }
    }

    return _arr;
  }

  function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance");
  }

  var strictUriEncode = function strictUriEncode(str) {
    return encodeURIComponent(str).replace(/[!'()*]/g, function (x) {
      return "%".concat(x.charCodeAt(0).toString(16).toUpperCase());
    });
  };

  var token = '%[a-f0-9]{2}';
  var singleMatcher = new RegExp(token, 'gi');
  var multiMatcher = new RegExp('(' + token + ')+', 'gi');

  function decodeComponents(components, split) {
    try {
      // Try to decode the entire string first
      return decodeURIComponent(components.join(''));
    } catch (err) {// Do nothing
    }

    if (components.length === 1) {
      return components;
    }

    split = split || 1; // Split the array in 2 parts

    var left = components.slice(0, split);
    var right = components.slice(split);
    return Array.prototype.concat.call([], decodeComponents(left), decodeComponents(right));
  }

  function decode(input) {
    try {
      return decodeURIComponent(input);
    } catch (err) {
      var tokens = input.match(singleMatcher);

      for (var i = 1; i < tokens.length; i++) {
        input = decodeComponents(tokens, i).join('');
        tokens = input.match(singleMatcher);
      }

      return input;
    }
  }

  function customDecodeURIComponent(input) {
    // Keep track of all the replacements and prefill the map with the `BOM`
    var replaceMap = {
      '%FE%FF': "\uFFFD\uFFFD",
      '%FF%FE': "\uFFFD\uFFFD"
    };
    var match = multiMatcher.exec(input);

    while (match) {
      try {
        // Decode as big chunks as possible
        replaceMap[match[0]] = decodeURIComponent(match[0]);
      } catch (err) {
        var result = decode(match[0]);

        if (result !== match[0]) {
          replaceMap[match[0]] = result;
        }
      }

      match = multiMatcher.exec(input);
    } // Add `%C2` at the end of the map to make sure it does not replace the combinator before everything else


    replaceMap['%C2'] = "\uFFFD";
    var entries = Object.keys(replaceMap);

    for (var i = 0; i < entries.length; i++) {
      // Replace all decoded components
      var key = entries[i];
      input = input.replace(new RegExp(key, 'g'), replaceMap[key]);
    }

    return input;
  }

  var decodeUriComponent = function decodeUriComponent(encodedURI) {
    if (typeof encodedURI !== 'string') {
      throw new TypeError('Expected `encodedURI` to be of type `string`, got `' + _typeof(encodedURI) + '`');
    }

    try {
      encodedURI = encodedURI.replace(/\+/g, ' '); // Try the built in decoder first

      return decodeURIComponent(encodedURI);
    } catch (err) {
      // Fallback to a more advanced decoder
      return customDecodeURIComponent(encodedURI);
    }
  };

  function encoderForArrayFormat(options) {
    switch (options.arrayFormat) {
      case 'index':
        return function (key, value, index) {
          return value === null ? [encode(key, options), '[', index, ']'].join('') : [encode(key, options), '[', encode(index, options), ']=', encode(value, options)].join('');
        };

      case 'bracket':
        return function (key, value) {
          return value === null ? [encode(key, options), '[]'].join('') : [encode(key, options), '[]=', encode(value, options)].join('');
        };

      default:
        return function (key, value) {
          return value === null ? encode(key, options) : [encode(key, options), '=', encode(value, options)].join('');
        };
    }
  }

  function parserForArrayFormat(options) {
    var result;

    switch (options.arrayFormat) {
      case 'index':
        return function (key, value, accumulator) {
          result = /\[(\d*)\]$/.exec(key);
          key = key.replace(/\[\d*\]$/, '');

          if (!result) {
            accumulator[key] = value;
            return;
          }

          if (accumulator[key] === undefined) {
            accumulator[key] = {};
          }

          accumulator[key][result[1]] = value;
        };

      case 'bracket':
        return function (key, value, accumulator) {
          result = /(\[\])$/.exec(key);
          key = key.replace(/\[\]$/, '');

          if (!result) {
            accumulator[key] = value;
            return;
          }

          if (accumulator[key] === undefined) {
            accumulator[key] = [value];
            return;
          }

          accumulator[key] = [].concat(accumulator[key], value);
        };

      default:
        return function (key, value, accumulator) {
          if (accumulator[key] === undefined) {
            accumulator[key] = value;
            return;
          }

          accumulator[key] = [].concat(accumulator[key], value);
        };
    }
  }

  function encode(value, options) {
    if (options.encode) {
      return options.strict ? strictUriEncode(value) : encodeURIComponent(value);
    }

    return value;
  }

  function decode$1(value, options) {
    if (options.decode) {
      return decodeUriComponent(value);
    }

    return value;
  }

  function keysSorter(input) {
    if (Array.isArray(input)) {
      return input.sort();
    }

    if (_typeof(input) === 'object') {
      return keysSorter(Object.keys(input)).sort(function (a, b) {
        return Number(a) - Number(b);
      }).map(function (key) {
        return input[key];
      });
    }

    return input;
  }

  function extract(input) {
    var queryStart = input.indexOf('?');

    if (queryStart === -1) {
      return '';
    }

    return input.slice(queryStart + 1);
  }

  function parse(input, options) {
    options = Object.assign({
      decode: true,
      arrayFormat: 'none'
    }, options);
    var formatter = parserForArrayFormat(options); // Create an object with no prototype

    var ret = Object.create(null);

    if (typeof input !== 'string') {
      return ret;
    }

    input = input.trim().replace(/^[?#&]/, '');

    if (!input) {
      return ret;
    }

    var _iteratorNormalCompletion = true;
    var _didIteratorError = false;
    var _iteratorError = undefined;

    try {
      for (var _iterator = input.split('&')[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
        var param = _step.value;

        var _param$replace$split = param.replace(/\+/g, ' ').split('='),
            _param$replace$split2 = _slicedToArray(_param$replace$split, 2),
            key = _param$replace$split2[0],
            value = _param$replace$split2[1]; // Missing `=` should be `null`:
        // http://w3.org/TR/2012/WD-url-20120524/#collect-url-parameters


        value = value === undefined ? null : decode$1(value, options);
        formatter(decode$1(key, options), value, ret);
      }
    } catch (err) {
      _didIteratorError = true;
      _iteratorError = err;
    } finally {
      try {
        if (!_iteratorNormalCompletion && _iterator.return != null) {
          _iterator.return();
        }
      } finally {
        if (_didIteratorError) {
          throw _iteratorError;
        }
      }
    }

    return Object.keys(ret).sort().reduce(function (result, key) {
      var value = ret[key];

      if (Boolean(value) && _typeof(value) === 'object' && !Array.isArray(value)) {
        // Sort object keys, not values
        result[key] = keysSorter(value);
      } else {
        result[key] = value;
      }

      return result;
    }, Object.create(null));
  }

  var extract_1 = extract;
  var parse_1 = parse;

  var stringify = function stringify(obj, options) {
    var defaults = {
      encode: true,
      strict: true,
      arrayFormat: 'none'
    };
    options = Object.assign(defaults, options);

    if (options.sort === false) {
      options.sort = function () {};
    }

    var formatter = encoderForArrayFormat(options);
    return obj ? Object.keys(obj).sort(options.sort).map(function (key) {
      var value = obj[key];

      if (value === undefined) {
        return '';
      }

      if (value === null) {
        return encode(key, options);
      }

      if (Array.isArray(value)) {
        var result = [];
        var _iteratorNormalCompletion2 = true;
        var _didIteratorError2 = false;
        var _iteratorError2 = undefined;

        try {
          for (var _iterator2 = value.slice()[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
            var value2 = _step2.value;

            if (value2 === undefined) {
              continue;
            }

            result.push(formatter(key, value2, result.length));
          }
        } catch (err) {
          _didIteratorError2 = true;
          _iteratorError2 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion2 && _iterator2.return != null) {
              _iterator2.return();
            }
          } finally {
            if (_didIteratorError2) {
              throw _iteratorError2;
            }
          }
        }

        return result.join('&');
      }

      return encode(key, options) + '=' + encode(value, options);
    }).filter(function (x) {
      return x.length > 0;
    }).join('&') : '';
  };

  var parseUrl = function parseUrl(input, options) {
    return {
      url: input.split('?')[0] || '',
      query: parse(extract(input), options)
    };
  };

  var queryString = {
    extract: extract_1,
    parse: parse_1,
    stringify: stringify,
    parseUrl: parseUrl
  };

  var queryString$1 = /*#__PURE__*/Object.freeze({
    default: queryString,
    __moduleExports: queryString,
    extract: extract_1,
    parse: parse_1,
    stringify: stringify,
    parseUrl: parseUrl
  });

  var Utils = function ($$$1) {
    function getTransitionEndEvent() {
      var transitionEndEvent = '';
      var transitionEndEvents = {
        'WebkitTransition': 'webkitTransitionEnd',
        'MozTransition': 'transitionend',
        'OTransition': 'otransitionend',
        'transition': 'transitionend'
      };

      for (var name in transitionEndEvents) {
        if ({}.hasOwnProperty.call(transitionEndEvents, name)) {
          var tempEl = document.createElement('p');

          if (typeof tempEl.style[name] !== 'undefined') {
            transitionEndEvent = transitionEndEvents[name];
          }
        }
      }

      return transitionEndEvent;
    }

    return {
      isMobile: isMobile_1,
      debounce: debounce_1,
      queryString: queryString$1,
      TRANSITION_END: getTransitionEndEvent(),
      onTransitionEnd: function onTransitionEnd(el, callback) {
        var _this = this;

        var called = false;
        $$$1(el).one(this.TRANSITION_END, function () {
          callback();
          called = true;
        });
        setTimeout(function () {
          if (!called) $$$1(el).trigger(_this.TRANSITION_END);
        }, this.getTransitionDurationFromElement(el));
      },
      getTransitionDurationFromElement: function getTransitionDurationFromElement(element) {
        if (!element) {
          return 0;
        } // Get transition-duration of the element.


        var transitionDuration = $$$1(element).css('transition-duration');
        var floatTransitionDuration = parseFloat(transitionDuration); // Return 0 if element or transition duration is not found.

        if (!floatTransitionDuration) {
          return 0;
        } // If multiple durations are defined, take the first.


        transitionDuration = transitionDuration.split(',')[0];
        return parseFloat(transitionDuration) * 1000;
      },
      getTargetFromElement: function getTargetFromElement(element) {
        var selector = element.getAttribute('data-target');

        if (!selector || selector === '#') {
          selector = element.getAttribute('href') || '';
        }

        try {
          return document.querySelector(selector) ? selector : null;
        } catch (err) {
          return null;
        }
      }
    };
  }(jQuery);

  var Dropdown = function ($$$1, Popper) {

    var Dropdown =
    /*#__PURE__*/
    function () {
      function Dropdown(element, options) {
        _classCallCheck(this, Dropdown);

        this.element = element;
        this.options = Object.assign({}, Dropdown.defaults, options);
        this.drop = this._getDropElement();
        this.popper = null;

        if (!this.drop || typeof this.drop === 'undefined') {
          throw new Error('Drop Error: Cannot find the drop element.');
        }

        if (typeof Popper !== 'undefined' && !this.popper) {
          var referenceElement = this.element;
          this.popper = new Popper(referenceElement, this.drop, this._getPopperConfig());
        }

        this._addEventListeners();

        Dropdown.allDrops.push(this);
      }

      _createClass(Dropdown, [{
        key: "isOpened",
        value: function isOpened() {
          return this.drop.classList.contains('open');
        }
      }, {
        key: "isDisabled",
        value: function isDisabled() {
          return this.element.disabled || this.element.classList.contains('disabled');
        }
      }, {
        key: "toggle",
        value: function toggle() {
          if (this.isOpened()) {
            this.close();
          } else {
            this.open();
          }
        }
      }, {
        key: "open",
        value: function open() {
          var _this = this;

          if (this.isDisabled() || this.isOpened()) {
            return;
          }

          this.element.focus();
          this.element.setAttribute('aria-expanded', true); // If this is a touch-enabled device we add extra
          // empty mouseover listeners to the body's immediate children;
          // only needed because of broken event delegation on iOS
          // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html

          if ('ontouchstart' in document.documentElement) {
            $$$1(document.body).children().on('mouseover', null, $$$1.noop);
          }

          this.drop.classList.add('open');
          this.drop.setAttribute('aria-hidden', true);

          if (this.popper) {
            this.popper.update();
          }

          setTimeout(function () {
            _this.drop.classList.add('open--transition');
          });
        }
      }, {
        key: "close",
        value: function close() {
          var _this2 = this;

          if (this.isDisabled() || !this.isOpened()) {
            return;
          } // If this is a touch-enabled device we remove the extra
          // empty mouseover listeners we added for iOS support


          if ('ontouchstart' in document.documentElement) {
            $$$1(document.body).children().off('mouseover', null, $$$1.noop);
          }

          this.element.setAttribute('aria-expanded', false);
          this.drop.removeAttribute('aria-hidden');
          this.drop.classList.remove('open--transition');
          Utils.onTransitionEnd(this.drop, function () {
            _this2.drop.classList.remove('open');
          });
        }
      }, {
        key: "_addEventListeners",
        value: function _addEventListeners() {
          var _this3 = this;

          if (!this.options.openOn) {
            return;
          }

          if (this.options.openOn === 'always') {
            setTimeout(this.open.bind(this));
            return;
          }

          var events = this.options.openOn.split(' ');

          if (events.indexOf('click') >= 0) {
            $$$1(this.element).on('click', function (e) {
              e.preventDefault(); // e.stopPropagation();

              _this3.toggle();
            });
            $$$1(document).on('click', function (e) {
              if (!_this3.isOpened()) {
                return;
              } // Clicking inside dropdown


              if (e.target === _this3.drop || _this3.drop.contains(e.target)) {
                return;
              } // Clicking target


              if (e.target === _this3.element || _this3.element.contains(e.target)) {
                return;
              }

              _this3.close(e);
            });
          }

          if (events.indexOf('hover') >= 0) ;

          if (events.indexOf('focus') >= 0) ;
        }
      }, {
        key: "_getDropElement",
        value: function _getDropElement() {
          if (!this.drop) {
            var parent = this.element.parentNode;
            var target = Utils.getTargetFromElement(this.element);

            if (target) {
              this.drop = document.querySelector(target);
            } else {
              this.drop = parent ? parent.querySelector(this.options.drop) : null;
            }
          }

          return this.drop;
        }
      }, {
        key: "_getPopperConfig",
        value: function _getPopperConfig() {
          var _this4 = this;

          var offset = {};

          if (typeof this.options.offset === 'function') {
            offset.fn = function (data) {
              data.offsets = Object.assign({}, data.offsets, _this4.options.offset(data.offsets) || {});
              return data;
            };
          } else {
            offset.offset = this.options.offset;
          }

          var config = {
            placement: this._getPlacement(),
            modifiers: {
              offset: offset,
              flip: {
                enabled: this.options.flip
              },
              preventOverflow: {
                boundariesElement: this.options.boundary
              }
            }
          }; // Disable Popper.js if we have a static display.

          if (this.options.display === 'static') {
            config.modifiers.applyStyle = {
              enabled: false
            };
          }

          return config;
        }
      }, {
        key: "_getPlacement",
        value: function _getPlacement() {
          return 'bottom-start';
        }
      }]);

      return Dropdown;
    }(); // Store dropdown instances.


    Dropdown.allDrops = [];
    Dropdown.defaults = {
      drop: '[data-drop]',
      offset: 0,
      flip: true,
      openOn: 'click',
      boundary: 'scrollParent',
      reference: 'toggle',
      display: 'dynamic'
    };
    return Dropdown;
  }(jQuery, window.Popper);

  function rangePlugin() {
    var config = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    return function (fp) {
      var dateFormat = '',
          secondInput,
          _secondInputFocused;

      var createSecondInput = function createSecondInput() {
        if (config.input) {
          secondInput = config.input instanceof Element ? config.input : window.document.querySelector(config.input);
        } else {
          secondInput = fp._input.cloneNode();
          secondInput.removeAttribute('id');
          secondInput._flatpickr = undefined;
        }

        if (secondInput.value) {
          var parsedDate = fp.parseDate(secondInput.value);

          if (parsedDate) {
            fp.selectedDates.push(parsedDate);
          }
        }

        secondInput.setAttribute('data-fp-omit', '');

        fp._bind(secondInput, ['focus', 'click'], function () {
          if (fp.selectedDates[1]) {
            fp.latestSelectedDateObj = fp.selectedDates[1];

            fp._setHoursFromDate(fp.selectedDates[1]);

            fp.jumpToDate(fp.selectedDates[1]);
          }
          _secondInputFocused = true;
          fp.isOpen = false;
          fp.open(undefined, secondInput);
        });

        fp._bind(fp._input, ['focus', 'click'], function (e) {
          e.preventDefault();
          fp.isOpen = false;
          fp.open();
        });

        if (fp.config.allowInput) {
          fp._bind(secondInput, 'keydown', function (e) {
            if (e.key === 'Enter') {
              fp.setDate([fp.selectedDates[0], secondInput.value], true, dateFormat);
              secondInput.click();
            }
          });
        }

        if (!config.input) {
          fp._input.parentNode && fp._input.parentNode.insertBefore(secondInput, fp._input.nextSibling);
        }
      };

      var plugin = {
        onParseConfig: function onParseConfig() {
          fp.config.mode = 'range';
          dateFormat = fp.config.altInput ? fp.config.altFormat : fp.config.dateFormat;
        },
        onReady: function onReady() {
          createSecondInput();
          fp.config.ignoredFocusElements.push(secondInput);

          if (fp.config.allowInput) {
            fp._input.removeAttribute('readonly');

            secondInput.removeAttribute('readonly');
          } else {
            secondInput.setAttribute('readonly', 'readonly');
          }

          fp._bind(fp._input, 'focus', function () {
            fp.latestSelectedDateObj = fp.selectedDates[0];

            fp._setHoursFromDate(fp.selectedDates[0]);
            _secondInputFocused = false;
          });

          if (fp.config.allowInput) {
            fp._bind(fp._input, 'keydown', function (e) {
              if (e.key === 'Enter') {
                fp.setDate([fp._input.value, fp.selectedDates[1]], true, dateFormat);
              }
            });
          }

          fp.setDate(fp.selectedDates, false);
          plugin.onValueUpdate(fp.selectedDates);
        },
        onPreCalendarPosition: function onPreCalendarPosition() {
          if (_secondInputFocused) {
            fp._positionElement = secondInput;
            setTimeout(function () {
              fp._positionElement = fp._input;
            }, 0);
          }
        },
        onValueUpdate: function onValueUpdate() {
          if (!secondInput) {
            return;
          }

          var _fp$selectedDates$map = fp.selectedDates.map(function (d) {
            return fp.formatDate(d, dateFormat);
          });

          var _fp$selectedDates$map2 = _slicedToArray(_fp$selectedDates$map, 2);

          var _fp$selectedDates$map3 = _fp$selectedDates$map2[0];
          fp._input.value = _fp$selectedDates$map3 === void 0 ? '' : _fp$selectedDates$map3;
          var _fp$selectedDates$map4 = _fp$selectedDates$map2[1];
          secondInput.value = _fp$selectedDates$map4 === void 0 ? '' : _fp$selectedDates$map4;
        },
        onChange: function onChange() {
          if (!fp.selectedDates.length) {
            setTimeout(function () {
              if (fp.selectedDates.length) {
                return;
              }

              secondInput.value = '';
            }, 10);
          }

          if (_secondInputFocused) {
            setTimeout(function () {
              secondInput.focus();
            }, 0);
          }
        },
        onDestroy: function onDestroy() {
          if (!config.input) {
            secondInput.parentNode && secondInput.parentNode.removeChild(secondInput);
          }
        }
      };
      return plugin;
    };
  }

  var $$1 = jQuery;
  var plugin = window.awebooking;

  var ajaxSearch = function ajaxSearch() {
    var type = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'customers';
    var query = arguments.length > 1 ? arguments[1] : undefined;
    var callback = arguments.length > 2 ? arguments[2] : undefined;
    $$1.ajax({
      type: 'GET',
      url: plugin.route("/search/".concat(type)),
      data: {
        term: encodeURIComponent(query)
      },
      error: function error() {
        callback();
      },
      success: function success(res) {
        callback(res);
      }
    });
  };

  var initSelectize = function initSelectize(select) {
    $$1(select).selectize({
      valueField: 'id',
      labelField: 'display',
      searchField: 'display',
      dropdownParent: 'body',
      placeholder: $$1(this).data('placeholder'),
      load: function load(query, callback) {
        if (!query.length) {
          return callback();
        } else {
          ajaxSearch('customers', query, callback);
        }
      }
    });
  };

  var initSelectizeServices = function initSelectizeServices(select) {
    $$1(select).selectize({
      plugins: ['remove_button', 'drag_drop'],
      valueField: 'id',
      labelField: 'name',
      searchField: ['name', 'id'],
      dropdownParent: 'body',
      placeholder: $$1(this).data('placeholder'),
      load: function load(query, callback) {
        if (!query.length) {
          return callback();
        } else {
          ajaxSearch('services', query, callback);
        }
      }
    });
  };

  function customerSearch () {
    $$1('select.awebooking-search-customer, .selectize-search-customer .cmb2_select').each(function () {
      initSelectize(this);
    });
    $$1('.selectize-search-services').each(function () {
      initSelectizeServices(this);
    });
  }

  var commonjsGlobal = typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

  function createCommonjsModule(fn, module) {
  	return module = { exports: {} }, fn(module, module.exports), module.exports;
  }

  var accounting = createCommonjsModule(function (module, exports) {
    /*!
     * accounting.js v0.4.1
     * Copyright 2014 Open Exchange Rates
     *
     * Freely distributable under the MIT license.
     * Portions of accounting.js are inspired or borrowed from underscore.js
     *
     * Full details and documentation:
     * http://openexchangerates.github.io/accounting.js/
     */
    (function (root, undefined) {
      /* --- Setup --- */
      // Create the local library object, to be exported or referenced globally later
      var lib = {}; // Current version

      lib.version = '0.4.1';
      /* --- Exposed settings --- */
      // The library's settings configuration object. Contains default parameters for
      // currency and number formatting

      lib.settings = {
        currency: {
          symbol: "$",
          // default currency symbol is '$'
          format: "%s%v",
          // controls output: %s = symbol, %v = value (can be object, see docs)
          decimal: ".",
          // decimal point separator
          thousand: ",",
          // thousands separator
          precision: 2,
          // decimal places
          grouping: 3 // digit grouping (not implemented yet)

        },
        number: {
          precision: 0,
          // default precision on numbers is 0
          grouping: 3,
          // digit grouping (not implemented yet)
          thousand: ",",
          decimal: "."
        }
      };
      /* --- Internal Helper Methods --- */
      // Store reference to possibly-available ECMAScript 5 methods for later

      var nativeMap = Array.prototype.map,
          nativeIsArray = Array.isArray,
          toString = Object.prototype.toString;
      /**
       * Tests whether supplied parameter is a string
       * from underscore.js
       */

      function isString(obj) {
        return !!(obj === '' || obj && obj.charCodeAt && obj.substr);
      }
      /**
       * Tests whether supplied parameter is a string
       * from underscore.js, delegates to ECMA5's native Array.isArray
       */


      function isArray(obj) {
        return nativeIsArray ? nativeIsArray(obj) : toString.call(obj) === '[object Array]';
      }
      /**
       * Tests whether supplied parameter is a true object
       */


      function isObject(obj) {
        return obj && toString.call(obj) === '[object Object]';
      }
      /**
       * Extends an object with a defaults object, similar to underscore's _.defaults
       *
       * Used for abstracting parameter handling from API methods
       */


      function defaults(object, defs) {
        var key;
        object = object || {};
        defs = defs || {}; // Iterate over object non-prototype properties:

        for (key in defs) {
          if (defs.hasOwnProperty(key)) {
            // Replace values with defaults only if undefined (allow empty/zero values):
            if (object[key] == null) object[key] = defs[key];
          }
        }

        return object;
      }
      /**
       * Implementation of `Array.map()` for iteration loops
       *
       * Returns a new Array as a result of calling `iterator` on each array value.
       * Defers to native Array.map if available
       */


      function map(obj, iterator, context) {
        var results = [],
            i,
            j;
        if (!obj) return results; // Use native .map method if it exists:

        if (nativeMap && obj.map === nativeMap) return obj.map(iterator, context); // Fallback for native .map:

        for (i = 0, j = obj.length; i < j; i++) {
          results[i] = iterator.call(context, obj[i], i, obj);
        }

        return results;
      }
      /**
       * Check and normalise the value of precision (must be positive integer)
       */


      function checkPrecision(val, base) {
        val = Math.round(Math.abs(val));
        return isNaN(val) ? base : val;
      }
      /**
       * Parses a format string or object and returns format obj for use in rendering
       *
       * `format` is either a string with the default (positive) format, or object
       * containing `pos` (required), `neg` and `zero` values (or a function returning
       * either a string or object)
       *
       * Either string or format.pos must contain "%v" (value) to be valid
       */


      function checkCurrencyFormat(format) {
        var defaults = lib.settings.currency.format; // Allow function as format parameter (should return string or object):

        if (typeof format === "function") format = format(); // Format can be a string, in which case `value` ("%v") must be present:

        if (isString(format) && format.match("%v")) {
          // Create and return positive, negative and zero formats:
          return {
            pos: format,
            neg: format.replace("-", "").replace("%v", "-%v"),
            zero: format
          }; // If no format, or object is missing valid positive value, use defaults:
        } else if (!format || !format.pos || !format.pos.match("%v")) {
          // If defaults is a string, casts it to an object for faster checking next time:
          return !isString(defaults) ? defaults : lib.settings.currency.format = {
            pos: defaults,
            neg: defaults.replace("%v", "-%v"),
            zero: defaults
          };
        } // Otherwise, assume format was fine:


        return format;
      }
      /* --- API Methods --- */

      /**
       * Takes a string/array of strings, removes all formatting/cruft and returns the raw float value
       * Alias: `accounting.parse(string)`
       *
       * Decimal must be included in the regular expression to match floats (defaults to
       * accounting.settings.number.decimal), so if the number uses a non-standard decimal 
       * separator, provide it as the second argument.
       *
       * Also matches bracketed negatives (eg. "$ (1.99)" => -1.99)
       *
       * Doesn't throw any errors (`NaN`s become 0) but this may change in future
       */


      var unformat = lib.unformat = lib.parse = function (value, decimal) {
        // Recursively unformat arrays:
        if (isArray(value)) {
          return map(value, function (val) {
            return unformat(val, decimal);
          });
        } // Fails silently (need decent errors):


        value = value || 0; // Return the value as-is if it's already a number:

        if (typeof value === "number") return value; // Default decimal point comes from settings, but could be set to eg. "," in opts:

        decimal = decimal || lib.settings.number.decimal; // Build regex to strip out everything except digits, decimal point and minus sign:

        var regex = new RegExp("[^0-9-" + decimal + "]", ["g"]),
            unformatted = parseFloat(("" + value).replace(/\((.*)\)/, "-$1") // replace bracketed values with negatives
        .replace(regex, '') // strip out any cruft
        .replace(decimal, '.') // make sure decimal point is standard
        ); // This will fail silently which may cause trouble, let's wait and see:

        return !isNaN(unformatted) ? unformatted : 0;
      };
      /**
       * Implementation of toFixed() that treats floats more like decimals
       *
       * Fixes binary rounding issues (eg. (0.615).toFixed(2) === "0.61") that present
       * problems for accounting- and finance-related software.
       */


      var toFixed = lib.toFixed = function (value, precision) {
        precision = checkPrecision(precision, lib.settings.number.precision);
        var power = Math.pow(10, precision); // Multiply up by precision, round accurately, then divide and use native toFixed():

        return (Math.round(lib.unformat(value) * power) / power).toFixed(precision);
      };
      /**
       * Format a number, with comma-separated thousands and custom precision/decimal places
       * Alias: `accounting.format()`
       *
       * Localise by overriding the precision and thousand / decimal separators
       * 2nd parameter `precision` can be an object matching `settings.number`
       */


      var formatNumber = lib.formatNumber = lib.format = function (number, precision, thousand, decimal) {
        // Resursively format arrays:
        if (isArray(number)) {
          return map(number, function (val) {
            return formatNumber(val, precision, thousand, decimal);
          });
        } // Clean up number:


        number = unformat(number); // Build options object from second param (if object) or all params, extending defaults:

        var opts = defaults(isObject(precision) ? precision : {
          precision: precision,
          thousand: thousand,
          decimal: decimal
        }, lib.settings.number),
            // Clean up precision
        usePrecision = checkPrecision(opts.precision),
            // Do some calc:
        negative = number < 0 ? "-" : "",
            base = parseInt(toFixed(Math.abs(number || 0), usePrecision), 10) + "",
            mod = base.length > 3 ? base.length % 3 : 0; // Format the number:

        return negative + (mod ? base.substr(0, mod) + opts.thousand : "") + base.substr(mod).replace(/(\d{3})(?=\d)/g, "$1" + opts.thousand) + (usePrecision ? opts.decimal + toFixed(Math.abs(number), usePrecision).split('.')[1] : "");
      };
      /**
       * Format a number into currency
       *
       * Usage: accounting.formatMoney(number, symbol, precision, thousandsSep, decimalSep, format)
       * defaults: (0, "$", 2, ",", ".", "%s%v")
       *
       * Localise by overriding the symbol, precision, thousand / decimal separators and format
       * Second param can be an object matching `settings.currency` which is the easiest way.
       *
       * To do: tidy up the parameters
       */


      var formatMoney = lib.formatMoney = function (number, symbol, precision, thousand, decimal, format) {
        // Resursively format arrays:
        if (isArray(number)) {
          return map(number, function (val) {
            return formatMoney(val, symbol, precision, thousand, decimal, format);
          });
        } // Clean up number:


        number = unformat(number); // Build options object from second param (if object) or all params, extending defaults:

        var opts = defaults(isObject(symbol) ? symbol : {
          symbol: symbol,
          precision: precision,
          thousand: thousand,
          decimal: decimal,
          format: format
        }, lib.settings.currency),
            // Check format (returns object with pos, neg and zero):
        formats = checkCurrencyFormat(opts.format),
            // Choose which format to use for this value:
        useFormat = number > 0 ? formats.pos : number < 0 ? formats.neg : formats.zero; // Return with currency symbol added:

        return useFormat.replace('%s', opts.symbol).replace('%v', formatNumber(Math.abs(number), checkPrecision(opts.precision), opts.thousand, opts.decimal));
      };
      /**
       * Format a list of numbers into an accounting column, padding with whitespace
       * to line up currency symbols, thousand separators and decimals places
       *
       * List should be an array of numbers
       * Second parameter can be an object containing keys that match the params
       *
       * Returns array of accouting-formatted number strings of same length
       *
       * NB: `white-space:pre` CSS rule is required on the list container to prevent
       * browsers from collapsing the whitespace in the output strings.
       */


      lib.formatColumn = function (list, symbol, precision, thousand, decimal, format) {
        if (!list) return []; // Build options object from second param (if object) or all params, extending defaults:

        var opts = defaults(isObject(symbol) ? symbol : {
          symbol: symbol,
          precision: precision,
          thousand: thousand,
          decimal: decimal,
          format: format
        }, lib.settings.currency),
            // Check format (returns object with pos, neg and zero), only need pos for now:
        formats = checkCurrencyFormat(opts.format),
            // Whether to pad at start of string or after currency symbol:
        padAfterSymbol = formats.pos.indexOf("%s") < formats.pos.indexOf("%v") ? true : false,
            // Store value for the length of the longest string in the column:
        maxLength = 0,
            // Format the list according to options, store the length of the longest string:
        formatted = map(list, function (val, i) {
          if (isArray(val)) {
            // Recursively format columns if list is a multi-dimensional array:
            return lib.formatColumn(val, opts);
          } else {
            // Clean up the value
            val = unformat(val); // Choose which format to use for this value (pos, neg or zero):

            var useFormat = val > 0 ? formats.pos : val < 0 ? formats.neg : formats.zero,
                // Format this value, push into formatted list and save the length:
            fVal = useFormat.replace('%s', opts.symbol).replace('%v', formatNumber(Math.abs(val), checkPrecision(opts.precision), opts.thousand, opts.decimal));
            if (fVal.length > maxLength) maxLength = fVal.length;
            return fVal;
          }
        }); // Pad each number in the list and send back the column of numbers:

        return map(formatted, function (val, i) {
          // Only if this is a string (not a nested array, which would have already been padded):
          if (isString(val) && val.length < maxLength) {
            // Depending on symbol position, pad after symbol or at index 0:
            return padAfterSymbol ? val.replace(opts.symbol, opts.symbol + new Array(maxLength - val.length + 1).join(" ")) : new Array(maxLength - val.length + 1).join(" ") + val;
          }

          return val;
        });
      };
      /* --- Module Definition --- */
      // Export accounting for CommonJS. If being loaded as an AMD module, define it as such.
      // Otherwise, just add `accounting` to the global object


      {
        if (module.exports) {
          exports = module.exports = lib;
        }

        exports.accounting = lib;
      } // Root will be `window` in browser or `global` on the server:

    })(commonjsGlobal);
  });
  var accounting_1 = accounting.accounting;

  var awebooking = window.awebooking || {}; // Create the properties.

  awebooking.utils = {};
  awebooking.instances = {};
  awebooking.isMobile = isMobile_1;
  awebooking.utils.flatpickrRangePlugin = rangePlugin;
  /**
   * The admin route.
   *
   * @param  {string} route
   * @return {string}
   */

  awebooking.route = function (route) {
    return this.admin_route + route.replace(/^\//g, '');
  };
  /**
   * Show the alert dialog.
   *
   * @return {SweetAlert}
   */


  awebooking.alert = function (message) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'error';
    return swal({
      text: message,
      type: type,
      toast: true,
      buttonsStyling: false,
      showCancelButton: false,
      showConfirmButton: true,
      confirmButtonClass: 'button'
    });
  };
  /**
   * Show the confirm message.
   *
   * @return {SweetAlert}
   */


  awebooking.confirm = function (message, callback) {
    if (!window.swal) {
      return window.confirm(message || this.i18n.warning) && callback();
    }

    var confirm = window.swal({
      toast: true,
      text: message || this.i18n.warning,
      type: 'warning',
      position: 'center',
      reverseButtons: true,
      buttonsStyling: false,
      showCancelButton: true,
      cancelButtonClass: 'button',
      confirmButtonClass: 'button button-primary',
      cancelButtonText: this.i18n.cancel,
      confirmButtonText: this.i18n.ok
    });

    if (callback) {
      return confirm.then(function (result) {
        if (result.value) callback(result);
      });
    }

    return confirm;
  };
  /**
   * Create the dialog.
   *
   * @param  {string} selector
   * @return {Object}
   */


  awebooking.dialog = function (selector) {
    var $dialog = $(selector).dialog({
      modal: true,
      width: 'auto',
      height: 'auto',
      autoOpen: false,
      draggable: false,
      resizable: false,
      closeOnEscape: true,
      dialogClass: 'wp-dialog awebooking-dialog',
      position: {
        my: 'center',
        at: 'center center-15%',
        of: window
      }
    });
    $(window).resize(debounce_1(function () {
      $dialog.dialog('option', 'position', {
        my: 'center',
        at: 'center center-15%',
        of: window
      });
    }, 150));
    return $dialog;
  };
  /**
   * Send a ajax request to a route.
   *
   * @param  {String}   route
   * @param  {Object}   data
   * @param  {Function} callback
   * @return {Object}
   */


  awebooking.ajax = function (method, route, data, callback) {
    return $.ajax({
      url: awebooking.route(route),
      data: data,
      method: method,
      dataType: 'json'
    }).done(function (data) {
      if (callback) callback(data);
    }).fail(function (xhr) {
      var json = xhr.responseJSON;

      if (json && json.message) {
        awebooking.alert(json.message, 'error');
      } else {
        awebooking.alert(awebooking.i18n.error, 'error');
      }
    });
  };
  /**
   * Create a form then append to body.
   *
   * @param  {String} link   The form action.
   * @param  {String} method The form method.
   * @return {Object}
   */


  awebooking.createForm = function (action, method) {
    var $form = $('<form>', {
      'method': 'POST',
      'action': action
    });
    var hiddenInput = $('<input>', {
      'name': '_method',
      'type': 'hidden',
      'value': method
    });
    return $form.append(hiddenInput).appendTo('body');
  };
  /**
   * Format the price.
   *
   * @param amount
   * @returns {string}
   */


  awebooking.formatPrice = function (amount) {
    return accounting.formatMoney(amount, {
      format: awebooking.i18n.priceFormat,
      symbol: awebooking.i18n.currencySymbol,
      decimal: awebooking.i18n.decimalSeparator,
      thousand: awebooking.i18n.priceThousandSeparator,
      precision: awebooking.i18n.numberDecimals
    });
  };
  /**
   * Retrieves a modified URL query string.
   *
   * @param {object} args
   * @param {string} url
   */


  awebooking.utils.addQueryArgs = function (args, url) {
    if (typeof url === 'undefined') {
      url = window.location.href;
    }

    var parsed = parseUrl(url);
    var query = $.extend({}, parsed.query, args);
    return parsed.url + '?' + stringify(query, {
      sort: false
    });
  };

  $(function () {
    // Init tippy.
    if (window.tippy) {
      window.tippy('.tippy', {
        arrow: true,
        animation: 'shift-toward',
        duration: [200, 150]
      });
    } // Init the selectize.


    if ($.fn.selectize) {
      customerSearch();
      $('select.selectize, .with-selectize .cmb2_select').selectize({
        allowEmptyOption: true,
        searchField: ['value', 'text']
      });
    } // Init warning before delete.


    $('[data-method="abrs-delete"]').on('click', function (e) {
      e.preventDefault();
      var link = $(this).attr('href');
      var message = $(this).data('warning');
      awebooking.confirm(message, function () {
        awebooking.createForm(link, 'DELETE').submit();
      });
    });
    $('[data-init="abrs-dropdown"]').each(function () {
      $(this).data('abrs-dropdown', new Dropdown(this, {
        drop: '.abrs-drop'
      }));
    });
  });

}(jQuery));

//# sourceMappingURL=admin.js.map
