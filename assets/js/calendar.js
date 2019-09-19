(function(e, a) { for(var i in a) e[i] = a[i]; }(this, /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 11);
/******/ })
/************************************************************************/
/******/ ({

/***/ "+51k":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getActiveElement;

function getActiveElement() {
  return typeof document !== 'undefined' && document.activeElement;
}

/***/ }),

/***/ "+D8X":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
Object.defineProperty(exports, "DIRECTIONS", {
  enumerable: true,
  get: function get() {
    return _reactWithDirection.DIRECTIONS;
  }
});
exports["default"] = void 0;

var _react = __webpack_require__("cDcd");

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _reactWithDirection = __webpack_require__("fHbK");

function detectAndCreateContext(defaultValue) {
  if (_react.createContext) {
    return (0, _react.createContext)(defaultValue);
  }

  return {
    Provider: function Provider() {
      throw new ReferenceError('WithStylesContext requires React 16.3 or later');
    },
    Consumer: function Consumer() {
      throw new ReferenceError('WithStylesContext requires React 16.3 or later');
    }
  };
}

var WithStylesContext = detectAndCreateContext({
  stylesInterface: null,
  stylesTheme: null,
  direction: null
});
WithStylesContext.Provider.propTypes = {
  stylesInterface: _propTypes["default"].object,
  // eslint-disable-line react/forbid-prop-types
  stylesTheme: _propTypes["default"].object,
  // eslint-disable-line react/forbid-prop-types
  direction: _propTypes["default"].oneOf([_reactWithDirection.DIRECTIONS.LTR, _reactWithDirection.DIRECTIONS.RTL])
};
var _default = WithStylesContext;
exports["default"] = _default;

/***/ }),

/***/ "/9aa":
/***/ (function(module, exports, __webpack_require__) {

var baseGetTag = __webpack_require__("NykK"),
    isObjectLike = __webpack_require__("ExA7");

/** `Object#toString` result references. */
var symbolTag = '[object Symbol]';

/**
 * Checks if `value` is classified as a `Symbol` primitive or object.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a symbol, else `false`.
 * @example
 *
 * _.isSymbol(Symbol.iterator);
 * // => true
 *
 * _.isSymbol('abc');
 * // => false
 */
function isSymbol(value) {
  return typeof value == 'symbol' ||
    (isObjectLike(value) && baseGetTag(value) == symbolTag);
}

module.exports = isSymbol;


/***/ }),

/***/ "/ZKw":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var define = __webpack_require__("82c2");
var bind = __webpack_require__("D3zA");

var implementation = __webpack_require__("yN6O");
var getPolyfill = __webpack_require__("22yB");
var polyfill = getPolyfill();
var shim = __webpack_require__("v3P4");

var boundFlat = bind.call(Function.call, polyfill);

define(boundFlat, {
	getPolyfill: getPolyfill,
	implementation: implementation,
	shim: shim
});

module.exports = boundFlat;


/***/ }),

/***/ "/sVA":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var toStr = Object.prototype.toString;
var hasSymbols = __webpack_require__("UVaH")();

if (hasSymbols) {
	var symToStr = Symbol.prototype.toString;
	var symStringRegex = /^Symbol\(.*\)$/;
	var isSymbolObject = function isRealSymbolObject(value) {
		if (typeof value.valueOf() !== 'symbol') {
			return false;
		}
		return symStringRegex.test(symToStr.call(value));
	};

	module.exports = function isSymbol(value) {
		if (typeof value === 'symbol') {
			return true;
		}
		if (toStr.call(value) !== '[object Symbol]') {
			return false;
		}
		try {
			return isSymbolObject(value);
		} catch (e) {
			return false;
		}
	};
} else {

	module.exports = function isSymbol(value) {
		// this environment does not support Symbols.
		return  false && false;
	};
}


/***/ }),

/***/ "/tz4":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _react = __webpack_require__("cDcd");

var _react2 = _interopRequireDefault(_react);

var _implementation = __webpack_require__("acCH");

var _implementation2 = _interopRequireDefault(_implementation);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createContext || _implementation2.default;
module.exports = exports['default'];

/***/ }),

/***/ "0+bg":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _constants = __webpack_require__("Fv1B");

var _default = _propTypes["default"].oneOf([_constants.ANCHOR_LEFT, _constants.ANCHOR_RIGHT]);

exports["default"] = _default;

/***/ }),

/***/ "030x":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports._getInterface = _getInterface;
exports._getTheme = get;
exports["default"] = void 0;
var styleInterface;
var styleTheme;
var START_MARK = 'react-with-styles.resolve.start';
var END_MARK = 'react-with-styles.resolve.end';
var MEASURE_MARK = "\uD83D\uDC69\u200D\uD83C\uDFA8 [resolve]";

function registerTheme(theme) {
  styleTheme = theme;
}

function registerInterface(interfaceToRegister) {
  styleInterface = interfaceToRegister;
}

function create(makeFromTheme, createWithDirection) {
  var styles = createWithDirection(makeFromTheme(styleTheme));
  return function () {
    return styles;
  };
}

function createLTR(makeFromTheme) {
  return create(makeFromTheme, styleInterface.createLTR || styleInterface.create);
}

function createRTL(makeFromTheme) {
  return create(makeFromTheme, styleInterface.createRTL || styleInterface.create);
}

function get() {
  return styleTheme;
}

function resolve() {
  if (false) {}

  for (var _len = arguments.length, styles = new Array(_len), _key = 0; _key < _len; _key++) {
    styles[_key] = arguments[_key];
  }

  var result = styleInterface.resolve(styles);

  if (false) {}

  return result;
}

function resolveLTR() {
  for (var _len2 = arguments.length, styles = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
    styles[_key2] = arguments[_key2];
  }

  if (styleInterface.resolveLTR) {
    return styleInterface.resolveLTR(styles);
  }

  return resolve(styles);
}

function resolveRTL() {
  for (var _len3 = arguments.length, styles = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
    styles[_key3] = arguments[_key3];
  }

  if (styleInterface.resolveRTL) {
    return styleInterface.resolveRTL(styles);
  }

  return resolve(styles);
}

function flush() {
  if (styleInterface.flush) {
    styleInterface.flush();
  }
} // Exported until we deprecate this API completely
// eslint-disable-next-line no-underscore-dangle


function _getInterface() {
  return styleInterface;
} // Exported until we deprecate this API completely


var _default = {
  registerTheme: registerTheme,
  registerInterface: registerInterface,
  create: createLTR,
  createLTR: createLTR,
  createRTL: createRTL,
  get: get,
  resolve: resolveLTR,
  resolveLTR: resolveLTR,
  resolveRTL: resolveRTL,
  flush: flush
};
exports["default"] = _default;

/***/ }),

/***/ "0Dl3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getNumberOfCalendarMonthWeeks;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

function getBlankDaysBeforeFirstDay(firstDayOfMonth, firstDayOfWeek) {
  var weekDayDiff = firstDayOfMonth.day() - firstDayOfWeek;
  return (weekDayDiff + 7) % 7;
}

function getNumberOfCalendarMonthWeeks(month) {
  var firstDayOfWeek = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : _moment["default"].localeData().firstDayOfWeek();
  var firstDayOfMonth = month.clone().startOf('month');
  var numBlankDays = getBlankDaysBeforeFirstDay(firstDayOfMonth, firstDayOfWeek);
  return Math.ceil((numBlankDays + month.daysInMonth()) / 7);
}

/***/ }),

/***/ "0XP8":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var LeftArrow = function LeftArrow(props) {
  return _react["default"].createElement("svg", props, _react["default"].createElement("path", {
    d: "M336 275L126 485h806c13 0 23 10 23 23s-10 23-23 23H126l210 210c11 11 11 21 0 32-5 5-10 7-16 7s-11-2-16-7L55 524c-11-11-11-21 0-32l249-249c21-22 53 10 32 32z"
  }));
};

LeftArrow.defaultProps = {
  focusable: "false",
  viewBox: "0 0 1000 1000"
};
var _default = LeftArrow;
exports["default"] = _default;

/***/ }),

/***/ "1+Kn":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = exports.BOTTOM_RIGHT = exports.TOP_RIGHT = exports.TOP_LEFT = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _KeyboardShortcutRow = _interopRequireDefault(__webpack_require__("zN8g"));

var _CloseButton = _interopRequireDefault(__webpack_require__("xEte"));

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var TOP_LEFT = 'top-left';
exports.TOP_LEFT = TOP_LEFT;
var TOP_RIGHT = 'top-right';
exports.TOP_RIGHT = TOP_RIGHT;
var BOTTOM_RIGHT = 'bottom-right';
exports.BOTTOM_RIGHT = BOTTOM_RIGHT;
var propTypes =  false ? undefined : {};
var defaultProps = {
  block: false,
  buttonLocation: BOTTOM_RIGHT,
  showKeyboardShortcutsPanel: false,
  openKeyboardShortcutsPanel: function openKeyboardShortcutsPanel() {},
  closeKeyboardShortcutsPanel: function closeKeyboardShortcutsPanel() {},
  phrases: _defaultPhrases.DayPickerKeyboardShortcutsPhrases,
  renderKeyboardShortcutsButton: undefined
};

function getKeyboardShortcuts(phrases) {
  return [{
    unicode: '↵',
    label: phrases.enterKey,
    action: phrases.selectFocusedDate
  }, {
    unicode: '←/→',
    label: phrases.leftArrowRightArrow,
    action: phrases.moveFocusByOneDay
  }, {
    unicode: '↑/↓',
    label: phrases.upArrowDownArrow,
    action: phrases.moveFocusByOneWeek
  }, {
    unicode: 'PgUp/PgDn',
    label: phrases.pageUpPageDown,
    action: phrases.moveFocusByOneMonth
  }, {
    unicode: 'Home/End',
    label: phrases.homeEnd,
    action: phrases.moveFocustoStartAndEndOfWeek
  }, {
    unicode: 'Esc',
    label: phrases.escape,
    action: phrases.returnFocusToInput
  }, {
    unicode: '?',
    label: phrases.questionMark,
    action: phrases.openThisPanel
  }];
}

var DayPickerKeyboardShortcuts =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(DayPickerKeyboardShortcuts, _ref);
  var _proto = DayPickerKeyboardShortcuts.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function DayPickerKeyboardShortcuts() {
    var _this;

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    _this = _ref.call.apply(_ref, [this].concat(args)) || this;
    var phrases = _this.props.phrases;
    _this.keyboardShortcuts = getKeyboardShortcuts(phrases);
    _this.onShowKeyboardShortcutsButtonClick = _this.onShowKeyboardShortcutsButtonClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setShowKeyboardShortcutsButtonRef = _this.setShowKeyboardShortcutsButtonRef.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setHideKeyboardShortcutsButtonRef = _this.setHideKeyboardShortcutsButtonRef.bind((0, _assertThisInitialized2["default"])(_this));
    _this.handleFocus = _this.handleFocus.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onKeyDown = _this.onKeyDown.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
    var phrases = this.props.phrases;

    if (nextProps.phrases !== phrases) {
      this.keyboardShortcuts = getKeyboardShortcuts(nextProps.phrases);
    }
  };

  _proto.componentDidUpdate = function componentDidUpdate() {
    this.handleFocus();
  };

  _proto.onKeyDown = function onKeyDown(e) {
    e.stopPropagation();
    var closeKeyboardShortcutsPanel = this.props.closeKeyboardShortcutsPanel; // Because the close button is the only focusable element inside of the panel, this
    // amounts to a very basic focus trap. The user can exit the panel by "pressing" the
    // close button or hitting escape

    switch (e.key) {
      case 'Escape':
        closeKeyboardShortcutsPanel();
        break;
      // do nothing - this allows the up and down arrows continue their
      // default behavior of scrolling the content of the Keyboard Shortcuts Panel
      // which is needed when only a single month is shown for instance.

      case 'ArrowUp':
      case 'ArrowDown':
        break;
      // completely block the rest of the keys that have functionality outside of this panel

      case 'Tab':
      case 'Home':
      case 'End':
      case 'PageUp':
      case 'PageDown':
      case 'ArrowLeft':
      case 'ArrowRight':
        e.preventDefault();
        break;

      default:
        break;
    }
  };

  _proto.onShowKeyboardShortcutsButtonClick = function onShowKeyboardShortcutsButtonClick() {
    var _this2 = this;

    var openKeyboardShortcutsPanel = this.props.openKeyboardShortcutsPanel; // we want to return focus to this button after closing the keyboard shortcuts panel

    openKeyboardShortcutsPanel(function () {
      _this2.showKeyboardShortcutsButton.focus();
    });
  };

  _proto.setShowKeyboardShortcutsButtonRef = function setShowKeyboardShortcutsButtonRef(ref) {
    this.showKeyboardShortcutsButton = ref;
  };

  _proto.setHideKeyboardShortcutsButtonRef = function setHideKeyboardShortcutsButtonRef(ref) {
    this.hideKeyboardShortcutsButton = ref;
  };

  _proto.handleFocus = function handleFocus() {
    if (this.hideKeyboardShortcutsButton) {
      // automatically move focus into the dialog by moving
      // to the only interactive element, the hide button
      this.hideKeyboardShortcutsButton.focus();
    }
  };

  _proto.render = function render() {
    var _this$props = this.props,
        block = _this$props.block,
        buttonLocation = _this$props.buttonLocation,
        css = _this$props.css,
        showKeyboardShortcutsPanel = _this$props.showKeyboardShortcutsPanel,
        closeKeyboardShortcutsPanel = _this$props.closeKeyboardShortcutsPanel,
        styles = _this$props.styles,
        phrases = _this$props.phrases,
        renderKeyboardShortcutsButton = _this$props.renderKeyboardShortcutsButton;
    var toggleButtonText = showKeyboardShortcutsPanel ? phrases.hideKeyboardShortcutsPanel : phrases.showKeyboardShortcutsPanel;
    var bottomRight = buttonLocation === BOTTOM_RIGHT;
    var topRight = buttonLocation === TOP_RIGHT;
    var topLeft = buttonLocation === TOP_LEFT;
    return _react["default"].createElement("div", null, renderKeyboardShortcutsButton && renderKeyboardShortcutsButton({
      // passing in context-specific props
      ref: this.setShowKeyboardShortcutsButtonRef,
      onClick: this.onShowKeyboardShortcutsButtonClick,
      ariaLabel: toggleButtonText
    }), renderKeyboardShortcutsButton || _react["default"].createElement("button", (0, _extends2["default"])({
      ref: this.setShowKeyboardShortcutsButtonRef
    }, css(styles.DayPickerKeyboardShortcuts_buttonReset, styles.DayPickerKeyboardShortcuts_show, bottomRight && styles.DayPickerKeyboardShortcuts_show__bottomRight, topRight && styles.DayPickerKeyboardShortcuts_show__topRight, topLeft && styles.DayPickerKeyboardShortcuts_show__topLeft), {
      type: "button",
      "aria-label": toggleButtonText,
      onClick: this.onShowKeyboardShortcutsButtonClick,
      onMouseUp: function onMouseUp(e) {
        e.currentTarget.blur();
      }
    }), _react["default"].createElement("span", css(styles.DayPickerKeyboardShortcuts_showSpan, bottomRight && styles.DayPickerKeyboardShortcuts_showSpan__bottomRight, topRight && styles.DayPickerKeyboardShortcuts_showSpan__topRight, topLeft && styles.DayPickerKeyboardShortcuts_showSpan__topLeft), "?")), showKeyboardShortcutsPanel && _react["default"].createElement("div", (0, _extends2["default"])({}, css(styles.DayPickerKeyboardShortcuts_panel), {
      role: "dialog",
      "aria-labelledby": "DayPickerKeyboardShortcuts_title",
      "aria-describedby": "DayPickerKeyboardShortcuts_description"
    }), _react["default"].createElement("div", (0, _extends2["default"])({}, css(styles.DayPickerKeyboardShortcuts_title), {
      id: "DayPickerKeyboardShortcuts_title"
    }), phrases.keyboardShortcuts), _react["default"].createElement("button", (0, _extends2["default"])({
      ref: this.setHideKeyboardShortcutsButtonRef
    }, css(styles.DayPickerKeyboardShortcuts_buttonReset, styles.DayPickerKeyboardShortcuts_close), {
      type: "button",
      tabIndex: "0",
      "aria-label": phrases.hideKeyboardShortcutsPanel,
      onClick: closeKeyboardShortcutsPanel,
      onKeyDown: this.onKeyDown
    }), _react["default"].createElement(_CloseButton["default"], css(styles.DayPickerKeyboardShortcuts_closeSvg))), _react["default"].createElement("ul", (0, _extends2["default"])({}, css(styles.DayPickerKeyboardShortcuts_list), {
      id: "DayPickerKeyboardShortcuts_description"
    }), this.keyboardShortcuts.map(function (_ref2) {
      var unicode = _ref2.unicode,
          label = _ref2.label,
          action = _ref2.action;
      return _react["default"].createElement(_KeyboardShortcutRow["default"], {
        key: label,
        unicode: unicode,
        label: label,
        action: action,
        block: block
      });
    }))));
  };

  return DayPickerKeyboardShortcuts;
}(_react["default"].PureComponent || _react["default"].Component);

DayPickerKeyboardShortcuts.propTypes =  false ? undefined : {};
DayPickerKeyboardShortcuts.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref3) {
  var _ref3$reactDates = _ref3.reactDates,
      color = _ref3$reactDates.color,
      font = _ref3$reactDates.font,
      zIndex = _ref3$reactDates.zIndex;
  return {
    DayPickerKeyboardShortcuts_buttonReset: {
      background: 'none',
      border: 0,
      borderRadius: 0,
      color: 'inherit',
      font: 'inherit',
      lineHeight: 'normal',
      overflow: 'visible',
      padding: 0,
      cursor: 'pointer',
      fontSize: font.size,
      ':active': {
        outline: 'none'
      }
    },
    DayPickerKeyboardShortcuts_show: {
      width: 33,
      height: 26,
      position: 'absolute',
      zIndex: zIndex + 2,
      '::before': {
        content: '""',
        display: 'block',
        position: 'absolute'
      }
    },
    DayPickerKeyboardShortcuts_show__bottomRight: {
      bottom: 0,
      right: 0,
      '::before': {
        borderTop: '26px solid transparent',
        borderRight: "33px solid ".concat(color.core.primary),
        bottom: 0,
        right: 0
      },
      ':hover::before': {
        borderRight: "33px solid ".concat(color.core.primary_dark)
      }
    },
    DayPickerKeyboardShortcuts_show__topRight: {
      top: 0,
      right: 0,
      '::before': {
        borderBottom: '26px solid transparent',
        borderRight: "33px solid ".concat(color.core.primary),
        top: 0,
        right: 0
      },
      ':hover::before': {
        borderRight: "33px solid ".concat(color.core.primary_dark)
      }
    },
    DayPickerKeyboardShortcuts_show__topLeft: {
      top: 0,
      left: 0,
      '::before': {
        borderBottom: '26px solid transparent',
        borderLeft: "33px solid ".concat(color.core.primary),
        top: 0,
        left: 0
      },
      ':hover::before': {
        borderLeft: "33px solid ".concat(color.core.primary_dark)
      }
    },
    DayPickerKeyboardShortcuts_showSpan: {
      color: color.core.white,
      position: 'absolute'
    },
    DayPickerKeyboardShortcuts_showSpan__bottomRight: {
      bottom: 0,
      right: 5
    },
    DayPickerKeyboardShortcuts_showSpan__topRight: {
      top: 1,
      right: 5
    },
    DayPickerKeyboardShortcuts_showSpan__topLeft: {
      top: 1,
      left: 5
    },
    DayPickerKeyboardShortcuts_panel: {
      overflow: 'auto',
      background: color.background,
      border: "1px solid ".concat(color.core.border),
      borderRadius: 2,
      position: 'absolute',
      top: 0,
      bottom: 0,
      right: 0,
      left: 0,
      zIndex: zIndex + 2,
      padding: 22,
      margin: 33,
      textAlign: 'left' // TODO: investigate use of text-align throughout the library

    },
    DayPickerKeyboardShortcuts_title: {
      fontSize: 16,
      fontWeight: 'bold',
      margin: 0
    },
    DayPickerKeyboardShortcuts_list: {
      listStyle: 'none',
      padding: 0,
      fontSize: font.size
    },
    DayPickerKeyboardShortcuts_close: {
      position: 'absolute',
      right: 22,
      top: 22,
      zIndex: zIndex + 2,
      ':active': {
        outline: 'none'
      }
    },
    DayPickerKeyboardShortcuts_closeSvg: {
      height: 15,
      width: 15,
      fill: color.core.grayLighter,
      ':hover': {
        fill: color.core.grayLight
      },
      ':focus': {
        fill: color.core.grayLight
      }
    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(DayPickerKeyboardShortcuts);

exports["default"] = _default;

/***/ }),

/***/ 11:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("G+CP");


/***/ }),

/***/ "16Al":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = __webpack_require__("WbBG");

function emptyFunction() {}
function emptyFunctionWithReset() {}
emptyFunctionWithReset.resetWarningCache = emptyFunction;

module.exports = function() {
  function shim(props, propName, componentName, location, propFullName, secret) {
    if (secret === ReactPropTypesSecret) {
      // It is still safe when called from React.
      return;
    }
    var err = new Error(
      'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
      'Use PropTypes.checkPropTypes() to call them. ' +
      'Read more at http://fb.me/use-check-prop-types'
    );
    err.name = 'Invariant Violation';
    throw err;
  };
  shim.isRequired = shim;
  function getShim() {
    return shim;
  };
  // Important!
  // Keep this list in sync with production version in `./factoryWithTypeCheckers.js`.
  var ReactPropTypes = {
    array: shim,
    bool: shim,
    func: shim,
    number: shim,
    object: shim,
    string: shim,
    symbol: shim,

    any: shim,
    arrayOf: getShim,
    element: shim,
    elementType: shim,
    instanceOf: getShim,
    node: shim,
    objectOf: getShim,
    oneOf: getShim,
    oneOfType: getShim,
    shape: getShim,
    exact: getShim,

    checkPropTypes: emptyFunctionWithReset,
    resetWarningCache: emptyFunction
  };

  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ "17x9":
/***/ (function(module, exports, __webpack_require__) {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (false) { var throwOnDirectAccess, ReactIs; } else {
  // By explicitly using `prop-types` you are opting into new production behavior.
  // http://fb.me/prop-types-in-prod
  module.exports = __webpack_require__("16Al")();
}


/***/ }),

/***/ "1KsK":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var toStr = Object.prototype.toString;

module.exports = function isArguments(value) {
	var str = toStr.call(value);
	var isArgs = str === '[object Arguments]';
	if (!isArgs) {
		isArgs = str !== '[object Array]' &&
			value !== null &&
			typeof value === 'object' &&
			typeof value.length === 'number' &&
			value.length >= 0 &&
			toStr.call(value.callee) === '[object Function]';
	}
	return isArgs;
};


/***/ }),

/***/ "1RUG":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var has = __webpack_require__("oNNP");
var toPrimitive = __webpack_require__("i1el");
var keys = __webpack_require__("1seS");

var GetIntrinsic = __webpack_require__("6ayh");

var $TypeError = GetIntrinsic('%TypeError%');
var $SyntaxError = GetIntrinsic('%SyntaxError%');
var $Array = GetIntrinsic('%Array%');
var $String = GetIntrinsic('%String%');
var $Object = GetIntrinsic('%Object%');
var $Number = GetIntrinsic('%Number%');
var $Symbol = GetIntrinsic('%Symbol%', true);
var $RegExp = GetIntrinsic('%RegExp%');

var hasSymbols = !!$Symbol;

var assertRecord = __webpack_require__("xG2L");
var $isNaN = __webpack_require__("IFfy");
var $isFinite = __webpack_require__("xhJ2");
var MAX_SAFE_INTEGER = $Number.MAX_SAFE_INTEGER || Math.pow(2, 53) - 1;

var assign = __webpack_require__("DGeY");
var sign = __webpack_require__("WXWk");
var mod = __webpack_require__("u1Mj");
var isPrimitive = __webpack_require__("E1iy");
var parseInteger = parseInt;
var bind = __webpack_require__("D3zA");
var arraySlice = bind.call(Function.call, $Array.prototype.slice);
var strSlice = bind.call(Function.call, $String.prototype.slice);
var isBinary = bind.call(Function.call, $RegExp.prototype.test, /^0b[01]+$/i);
var isOctal = bind.call(Function.call, $RegExp.prototype.test, /^0o[0-7]+$/i);
var regexExec = bind.call(Function.call, $RegExp.prototype.exec);
var nonWS = ['\u0085', '\u200b', '\ufffe'].join('');
var nonWSregex = new $RegExp('[' + nonWS + ']', 'g');
var hasNonWS = bind.call(Function.call, $RegExp.prototype.test, nonWSregex);
var invalidHexLiteral = /^[-+]0x[0-9a-f]+$/i;
var isInvalidHexLiteral = bind.call(Function.call, $RegExp.prototype.test, invalidHexLiteral);
var $charCodeAt = bind.call(Function.call, $String.prototype.charCodeAt);

var toStr = bind.call(Function.call, Object.prototype.toString);

var $NumberValueOf = bind.call(Function.call, GetIntrinsic('%NumberPrototype%').valueOf);
var $BooleanValueOf = bind.call(Function.call, GetIntrinsic('%BooleanPrototype%').valueOf);
var $StringValueOf = bind.call(Function.call, GetIntrinsic('%StringPrototype%').valueOf);
var $DateValueOf = bind.call(Function.call, GetIntrinsic('%DatePrototype%').valueOf);

var $floor = Math.floor;
var $abs = Math.abs;

var $ObjectCreate = Object.create;
var $gOPD = $Object.getOwnPropertyDescriptor;

var $isExtensible = $Object.isExtensible;

var $defineProperty = $Object.defineProperty;

// whitespace from: http://es5.github.io/#x15.5.4.20
// implementation from https://github.com/es-shims/es5-shim/blob/v3.4.0/es5-shim.js#L1304-L1324
var ws = [
	'\x09\x0A\x0B\x0C\x0D\x20\xA0\u1680\u180E\u2000\u2001\u2002\u2003',
	'\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028',
	'\u2029\uFEFF'
].join('');
var trimRegex = new RegExp('(^[' + ws + ']+)|([' + ws + ']+$)', 'g');
var replace = bind.call(Function.call, $String.prototype.replace);
var trim = function (value) {
	return replace(value, trimRegex, '');
};

var ES5 = __webpack_require__("DwGB");

var hasRegExpMatcher = __webpack_require__("2Nju");

// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-abstract-operations
var ES6 = assign(assign({}, ES5), {

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-call-f-v-args
	Call: function Call(F, V) {
		var args = arguments.length > 2 ? arguments[2] : [];
		if (!this.IsCallable(F)) {
			throw new $TypeError(F + ' is not a function');
		}
		return F.apply(V, args);
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-toprimitive
	ToPrimitive: toPrimitive,

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-toboolean
	// ToBoolean: ES5.ToBoolean,

	// https://ecma-international.org/ecma-262/6.0/#sec-tonumber
	ToNumber: function ToNumber(argument) {
		var value = isPrimitive(argument) ? argument : toPrimitive(argument, $Number);
		if (typeof value === 'symbol') {
			throw new $TypeError('Cannot convert a Symbol value to a number');
		}
		if (typeof value === 'string') {
			if (isBinary(value)) {
				return this.ToNumber(parseInteger(strSlice(value, 2), 2));
			} else if (isOctal(value)) {
				return this.ToNumber(parseInteger(strSlice(value, 2), 8));
			} else if (hasNonWS(value) || isInvalidHexLiteral(value)) {
				return NaN;
			} else {
				var trimmed = trim(value);
				if (trimmed !== value) {
					return this.ToNumber(trimmed);
				}
			}
		}
		return $Number(value);
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tointeger
	// ToInteger: ES5.ToNumber,

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-toint32
	// ToInt32: ES5.ToInt32,

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-touint32
	// ToUint32: ES5.ToUint32,

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-toint16
	ToInt16: function ToInt16(argument) {
		var int16bit = this.ToUint16(argument);
		return int16bit >= 0x8000 ? int16bit - 0x10000 : int16bit;
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-touint16
	// ToUint16: ES5.ToUint16,

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-toint8
	ToInt8: function ToInt8(argument) {
		var int8bit = this.ToUint8(argument);
		return int8bit >= 0x80 ? int8bit - 0x100 : int8bit;
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-touint8
	ToUint8: function ToUint8(argument) {
		var number = this.ToNumber(argument);
		if ($isNaN(number) || number === 0 || !$isFinite(number)) { return 0; }
		var posInt = sign(number) * $floor($abs(number));
		return mod(posInt, 0x100);
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-touint8clamp
	ToUint8Clamp: function ToUint8Clamp(argument) {
		var number = this.ToNumber(argument);
		if ($isNaN(number) || number <= 0) { return 0; }
		if (number >= 0xFF) { return 0xFF; }
		var f = $floor(argument);
		if (f + 0.5 < number) { return f + 1; }
		if (number < f + 0.5) { return f; }
		if (f % 2 !== 0) { return f + 1; }
		return f;
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tostring
	ToString: function ToString(argument) {
		if (typeof argument === 'symbol') {
			throw new $TypeError('Cannot convert a Symbol value to a string');
		}
		return $String(argument);
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-toobject
	ToObject: function ToObject(value) {
		this.RequireObjectCoercible(value);
		return $Object(value);
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-topropertykey
	ToPropertyKey: function ToPropertyKey(argument) {
		var key = this.ToPrimitive(argument, $String);
		return typeof key === 'symbol' ? key : this.ToString(key);
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength
	ToLength: function ToLength(argument) {
		var len = this.ToInteger(argument);
		if (len <= 0) { return 0; } // includes converting -0 to +0
		if (len > MAX_SAFE_INTEGER) { return MAX_SAFE_INTEGER; }
		return len;
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-canonicalnumericindexstring
	CanonicalNumericIndexString: function CanonicalNumericIndexString(argument) {
		if (toStr(argument) !== '[object String]') {
			throw new $TypeError('must be a string');
		}
		if (argument === '-0') { return -0; }
		var n = this.ToNumber(argument);
		if (this.SameValue(this.ToString(n), argument)) { return n; }
		return void 0;
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-requireobjectcoercible
	RequireObjectCoercible: ES5.CheckObjectCoercible,

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-isarray
	IsArray: $Array.isArray || function IsArray(argument) {
		return toStr(argument) === '[object Array]';
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-iscallable
	// IsCallable: ES5.IsCallable,

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-isconstructor
	IsConstructor: function IsConstructor(argument) {
		return typeof argument === 'function' && !!argument.prototype; // unfortunately there's no way to truly check this without try/catch `new argument`
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-isextensible-o
	IsExtensible: Object.preventExtensions
		? function IsExtensible(obj) {
			if (isPrimitive(obj)) {
				return false;
			}
			return $isExtensible(obj);
		}
		: function isExtensible(obj) { return true; }, // eslint-disable-line no-unused-vars

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-isinteger
	IsInteger: function IsInteger(argument) {
		if (typeof argument !== 'number' || $isNaN(argument) || !$isFinite(argument)) {
			return false;
		}
		var abs = $abs(argument);
		return $floor(abs) === abs;
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-ispropertykey
	IsPropertyKey: function IsPropertyKey(argument) {
		return typeof argument === 'string' || typeof argument === 'symbol';
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-isregexp
	IsRegExp: function IsRegExp(argument) {
		if (!argument || typeof argument !== 'object') {
			return false;
		}
		if (hasSymbols) {
			var isRegExp = argument[$Symbol.match];
			if (typeof isRegExp !== 'undefined') {
				return ES5.ToBoolean(isRegExp);
			}
		}
		return hasRegExpMatcher(argument);
	},

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-samevalue
	// SameValue: ES5.SameValue,

	// https://people.mozilla.org/~jorendorff/es6-draft.html#sec-samevaluezero
	SameValueZero: function SameValueZero(x, y) {
		return (x === y) || ($isNaN(x) && $isNaN(y));
	},

	/**
	 * 7.3.2 GetV (V, P)
	 * 1. Assert: IsPropertyKey(P) is true.
	 * 2. Let O be ToObject(V).
	 * 3. ReturnIfAbrupt(O).
	 * 4. Return O.[[Get]](P, V).
	 */
	GetV: function GetV(V, P) {
		// 7.3.2.1
		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('Assertion failed: IsPropertyKey(P) is not true');
		}

		// 7.3.2.2-3
		var O = this.ToObject(V);

		// 7.3.2.4
		return O[P];
	},

	/**
	 * 7.3.9 - https://ecma-international.org/ecma-262/6.0/#sec-getmethod
	 * 1. Assert: IsPropertyKey(P) is true.
	 * 2. Let func be GetV(O, P).
	 * 3. ReturnIfAbrupt(func).
	 * 4. If func is either undefined or null, return undefined.
	 * 5. If IsCallable(func) is false, throw a TypeError exception.
	 * 6. Return func.
	 */
	GetMethod: function GetMethod(O, P) {
		// 7.3.9.1
		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('Assertion failed: IsPropertyKey(P) is not true');
		}

		// 7.3.9.2
		var func = this.GetV(O, P);

		// 7.3.9.4
		if (func == null) {
			return void 0;
		}

		// 7.3.9.5
		if (!this.IsCallable(func)) {
			throw new $TypeError(P + 'is not a function');
		}

		// 7.3.9.6
		return func;
	},

	/**
	 * 7.3.1 Get (O, P) - https://ecma-international.org/ecma-262/6.0/#sec-get-o-p
	 * 1. Assert: Type(O) is Object.
	 * 2. Assert: IsPropertyKey(P) is true.
	 * 3. Return O.[[Get]](P, O).
	 */
	Get: function Get(O, P) {
		// 7.3.1.1
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(O) is not Object');
		}
		// 7.3.1.2
		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('Assertion failed: IsPropertyKey(P) is not true');
		}
		// 7.3.1.3
		return O[P];
	},

	Type: function Type(x) {
		if (typeof x === 'symbol') {
			return 'Symbol';
		}
		return ES5.Type(x);
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-speciesconstructor
	SpeciesConstructor: function SpeciesConstructor(O, defaultConstructor) {
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(O) is not Object');
		}
		var C = O.constructor;
		if (typeof C === 'undefined') {
			return defaultConstructor;
		}
		if (this.Type(C) !== 'Object') {
			throw new $TypeError('O.constructor is not an Object');
		}
		var S = hasSymbols && $Symbol.species ? C[$Symbol.species] : void 0;
		if (S == null) {
			return defaultConstructor;
		}
		if (this.IsConstructor(S)) {
			return S;
		}
		throw new $TypeError('no constructor found');
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-completepropertydescriptor
	CompletePropertyDescriptor: function CompletePropertyDescriptor(Desc) {
		assertRecord(this, 'Property Descriptor', 'Desc', Desc);

		if (this.IsGenericDescriptor(Desc) || this.IsDataDescriptor(Desc)) {
			if (!has(Desc, '[[Value]]')) {
				Desc['[[Value]]'] = void 0;
			}
			if (!has(Desc, '[[Writable]]')) {
				Desc['[[Writable]]'] = false;
			}
		} else {
			if (!has(Desc, '[[Get]]')) {
				Desc['[[Get]]'] = void 0;
			}
			if (!has(Desc, '[[Set]]')) {
				Desc['[[Set]]'] = void 0;
			}
		}
		if (!has(Desc, '[[Enumerable]]')) {
			Desc['[[Enumerable]]'] = false;
		}
		if (!has(Desc, '[[Configurable]]')) {
			Desc['[[Configurable]]'] = false;
		}
		return Desc;
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-set-o-p-v-throw
	Set: function Set(O, P, V, Throw) {
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('O must be an Object');
		}
		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('P must be a Property Key');
		}
		if (this.Type(Throw) !== 'Boolean') {
			throw new $TypeError('Throw must be a Boolean');
		}
		if (Throw) {
			O[P] = V;
			return true;
		} else {
			try {
				O[P] = V;
			} catch (e) {
				return false;
			}
		}
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-hasownproperty
	HasOwnProperty: function HasOwnProperty(O, P) {
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('O must be an Object');
		}
		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('P must be a Property Key');
		}
		return has(O, P);
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-hasproperty
	HasProperty: function HasProperty(O, P) {
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('O must be an Object');
		}
		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('P must be a Property Key');
		}
		return P in O;
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-isconcatspreadable
	IsConcatSpreadable: function IsConcatSpreadable(O) {
		if (this.Type(O) !== 'Object') {
			return false;
		}
		if (hasSymbols && typeof $Symbol.isConcatSpreadable === 'symbol') {
			var spreadable = this.Get(O, Symbol.isConcatSpreadable);
			if (typeof spreadable !== 'undefined') {
				return this.ToBoolean(spreadable);
			}
		}
		return this.IsArray(O);
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-invoke
	Invoke: function Invoke(O, P) {
		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('P must be a Property Key');
		}
		var argumentsList = arraySlice(arguments, 2);
		var func = this.GetV(O, P);
		return this.Call(func, O, argumentsList);
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-getiterator
	GetIterator: function GetIterator(obj, method) {
		if (!hasSymbols) {
			throw new SyntaxError('ES.GetIterator depends on native iterator support.');
		}

		var actualMethod = method;
		if (arguments.length < 2) {
			actualMethod = this.GetMethod(obj, $Symbol.iterator);
		}
		var iterator = this.Call(actualMethod, obj);
		if (this.Type(iterator) !== 'Object') {
			throw new $TypeError('iterator must return an object');
		}

		return iterator;
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-iteratornext
	IteratorNext: function IteratorNext(iterator, value) {
		var result = this.Invoke(iterator, 'next', arguments.length < 2 ? [] : [value]);
		if (this.Type(result) !== 'Object') {
			throw new $TypeError('iterator next must return an object');
		}
		return result;
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-iteratorcomplete
	IteratorComplete: function IteratorComplete(iterResult) {
		if (this.Type(iterResult) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(iterResult) is not Object');
		}
		return this.ToBoolean(this.Get(iterResult, 'done'));
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-iteratorvalue
	IteratorValue: function IteratorValue(iterResult) {
		if (this.Type(iterResult) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(iterResult) is not Object');
		}
		return this.Get(iterResult, 'value');
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-iteratorstep
	IteratorStep: function IteratorStep(iterator) {
		var result = this.IteratorNext(iterator);
		var done = this.IteratorComplete(result);
		return done === true ? false : result;
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-iteratorclose
	IteratorClose: function IteratorClose(iterator, completion) {
		if (this.Type(iterator) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(iterator) is not Object');
		}
		if (!this.IsCallable(completion)) {
			throw new $TypeError('Assertion failed: completion is not a thunk for a Completion Record');
		}
		var completionThunk = completion;

		var iteratorReturn = this.GetMethod(iterator, 'return');

		if (typeof iteratorReturn === 'undefined') {
			return completionThunk();
		}

		var completionRecord;
		try {
			var innerResult = this.Call(iteratorReturn, iterator, []);
		} catch (e) {
			// if we hit here, then "e" is the innerResult completion that needs re-throwing

			// if the completion is of type "throw", this will throw.
			completionRecord = completionThunk();
			completionThunk = null; // ensure it's not called twice.

			// if not, then return the innerResult completion
			throw e;
		}
		completionRecord = completionThunk(); // if innerResult worked, then throw if the completion does
		completionThunk = null; // ensure it's not called twice.

		if (this.Type(innerResult) !== 'Object') {
			throw new $TypeError('iterator .return must return an object');
		}

		return completionRecord;
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-createiterresultobject
	CreateIterResultObject: function CreateIterResultObject(value, done) {
		if (this.Type(done) !== 'Boolean') {
			throw new $TypeError('Assertion failed: Type(done) is not Boolean');
		}
		return {
			value: value,
			done: done
		};
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-regexpexec
	RegExpExec: function RegExpExec(R, S) {
		if (this.Type(R) !== 'Object') {
			throw new $TypeError('R must be an Object');
		}
		if (this.Type(S) !== 'String') {
			throw new $TypeError('S must be a String');
		}
		var exec = this.Get(R, 'exec');
		if (this.IsCallable(exec)) {
			var result = this.Call(exec, R, [S]);
			if (result === null || this.Type(result) === 'Object') {
				return result;
			}
			throw new $TypeError('"exec" method must return `null` or an Object');
		}
		return regexExec(R, S);
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-arrayspeciescreate
	ArraySpeciesCreate: function ArraySpeciesCreate(originalArray, length) {
		if (!this.IsInteger(length) || length < 0) {
			throw new $TypeError('Assertion failed: length must be an integer >= 0');
		}
		var len = length === 0 ? 0 : length;
		var C;
		var isArray = this.IsArray(originalArray);
		if (isArray) {
			C = this.Get(originalArray, 'constructor');
			// TODO: figure out how to make a cross-realm normal Array, a same-realm Array
			// if (this.IsConstructor(C)) {
			// 	if C is another realm's Array, C = undefined
			// 	Object.getPrototypeOf(Object.getPrototypeOf(Object.getPrototypeOf(Array))) === null ?
			// }
			if (this.Type(C) === 'Object' && hasSymbols && $Symbol.species) {
				C = this.Get(C, $Symbol.species);
				if (C === null) {
					C = void 0;
				}
			}
		}
		if (typeof C === 'undefined') {
			return $Array(len);
		}
		if (!this.IsConstructor(C)) {
			throw new $TypeError('C must be a constructor');
		}
		return new C(len); // this.Construct(C, len);
	},

	CreateDataProperty: function CreateDataProperty(O, P, V) {
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(O) is not Object');
		}
		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('Assertion failed: IsPropertyKey(P) is not true');
		}
		var oldDesc = $gOPD(O, P);
		var extensible = oldDesc || (typeof $isExtensible !== 'function' || $isExtensible(O));
		var immutable = oldDesc && (!oldDesc.writable || !oldDesc.configurable);
		if (immutable || !extensible) {
			return false;
		}
		var newDesc = {
			configurable: true,
			enumerable: true,
			value: V,
			writable: true
		};
		$defineProperty(O, P, newDesc);
		return true;
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-createdatapropertyorthrow
	CreateDataPropertyOrThrow: function CreateDataPropertyOrThrow(O, P, V) {
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(O) is not Object');
		}
		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('Assertion failed: IsPropertyKey(P) is not true');
		}
		var success = this.CreateDataProperty(O, P, V);
		if (!success) {
			throw new $TypeError('unable to create data property');
		}
		return success;
	},

	// https://www.ecma-international.org/ecma-262/6.0/#sec-objectcreate
	ObjectCreate: function ObjectCreate(proto, internalSlotsList) {
		if (proto !== null && this.Type(proto) !== 'Object') {
			throw new $TypeError('Assertion failed: proto must be null or an object');
		}
		var slots = arguments.length < 2 ? [] : internalSlotsList;
		if (slots.length > 0) {
			throw new $SyntaxError('es-abstract does not yet support internal slots');
		}

		if (proto === null && !$ObjectCreate) {
			throw new $SyntaxError('native Object.create support is required to create null objects');
		}

		return $ObjectCreate(proto);
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-advancestringindex
	AdvanceStringIndex: function AdvanceStringIndex(S, index, unicode) {
		if (this.Type(S) !== 'String') {
			throw new $TypeError('S must be a String');
		}
		if (!this.IsInteger(index) || index < 0 || index > MAX_SAFE_INTEGER) {
			throw new $TypeError('Assertion failed: length must be an integer >= 0 and <= 2**53');
		}
		if (this.Type(unicode) !== 'Boolean') {
			throw new $TypeError('Assertion failed: unicode must be a Boolean');
		}
		if (!unicode) {
			return index + 1;
		}
		var length = S.length;
		if ((index + 1) >= length) {
			return index + 1;
		}

		var first = $charCodeAt(S, index);
		if (first < 0xD800 || first > 0xDBFF) {
			return index + 1;
		}

		var second = $charCodeAt(S, index + 1);
		if (second < 0xDC00 || second > 0xDFFF) {
			return index + 1;
		}

		return index + 2;
	},

	// https://www.ecma-international.org/ecma-262/6.0/#sec-createmethodproperty
	CreateMethodProperty: function CreateMethodProperty(O, P, V) {
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(O) is not Object');
		}

		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('Assertion failed: IsPropertyKey(P) is not true');
		}

		var newDesc = {
			configurable: true,
			enumerable: false,
			value: V,
			writable: true
		};
		return !!$defineProperty(O, P, newDesc);
	},

	// https://www.ecma-international.org/ecma-262/6.0/#sec-definepropertyorthrow
	DefinePropertyOrThrow: function DefinePropertyOrThrow(O, P, desc) {
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(O) is not Object');
		}

		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('Assertion failed: IsPropertyKey(P) is not true');
		}

		return !!$defineProperty(O, P, desc);
	},

	// https://www.ecma-international.org/ecma-262/6.0/#sec-deletepropertyorthrow
	DeletePropertyOrThrow: function DeletePropertyOrThrow(O, P) {
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(O) is not Object');
		}

		if (!this.IsPropertyKey(P)) {
			throw new $TypeError('Assertion failed: IsPropertyKey(P) is not true');
		}

		var success = delete O[P];
		if (!success) {
			throw new TypeError('Attempt to delete property failed.');
		}
		return success;
	},

	// https://www.ecma-international.org/ecma-262/6.0/#sec-enumerableownnames
	EnumerableOwnNames: function EnumerableOwnNames(O) {
		if (this.Type(O) !== 'Object') {
			throw new $TypeError('Assertion failed: Type(O) is not Object');
		}

		return keys(O);
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-properties-of-the-number-prototype-object
	thisNumberValue: function thisNumberValue(value) {
		if (this.Type(value) === 'Number') {
			return value;
		}

		return $NumberValueOf(value);
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-properties-of-the-boolean-prototype-object
	thisBooleanValue: function thisBooleanValue(value) {
		if (this.Type(value) === 'Boolean') {
			return value;
		}

		return $BooleanValueOf(value);
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-properties-of-the-string-prototype-object
	thisStringValue: function thisStringValue(value) {
		if (this.Type(value) === 'String') {
			return value;
		}

		return $StringValueOf(value);
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-properties-of-the-date-prototype-object
	thisTimeValue: function thisTimeValue(value) {
		return $DateValueOf(value);
	}
});

delete ES6.CheckObjectCoercible; // renamed in ES6 to RequireObjectCoercible

module.exports = ES6;


/***/ }),

/***/ "1TsT":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "addEventListener", function() { return addEventListener; });
var CAN_USE_DOM = !!(typeof window !== 'undefined' && window.document && window.document.createElement);

// Adapted from Modernizr
// https://github.com/Modernizr/Modernizr/blob/acb3f0d9/feature-detects/dom/passiveeventlisteners.js#L26-L37
function testPassiveEventListeners() {
  if (!CAN_USE_DOM) {
    return false;
  }

  if (!window.addEventListener || !window.removeEventListener || !Object.defineProperty) {
    return false;
  }

  var supportsPassiveOption = false;
  try {
    var opts = Object.defineProperty({}, 'passive', {
      // eslint-disable-next-line getter-return
      get: function () {
        function get() {
          supportsPassiveOption = true;
        }

        return get;
      }()
    });
    var noop = function noop() {};
    window.addEventListener('testPassiveEventSupport', noop, opts);
    window.removeEventListener('testPassiveEventSupport', noop, opts);
  } catch (e) {
    // do nothing
  }

  return supportsPassiveOption;
}

var memoized = void 0;

function canUsePassiveEventListeners() {
  if (memoized === undefined) {
    memoized = testPassiveEventListeners();
  }
  return memoized;
}

function normalizeEventOptions(eventOptions) {
  if (!eventOptions) {
    return undefined;
  }

  if (!canUsePassiveEventListeners()) {
    // If the browser does not support the passive option, then it is expecting
    // a boolean for the options argument to specify whether it should use
    // capture or not. In more modern browsers, this is passed via the `capture`
    // option, so let's just hoist that value up.
    return !!eventOptions.capture;
  }

  return eventOptions;
}

/* eslint-disable no-bitwise */

/**
 * Generate a unique key for any set of event options
 */
function eventOptionsKey(normalizedEventOptions) {
  if (!normalizedEventOptions) {
    return 0;
  }

  // If the browser does not support passive event listeners, the normalized
  // event options will be a boolean.
  if (normalizedEventOptions === true) {
    return 100;
  }

  // At this point, the browser supports passive event listeners, so we expect
  // the event options to be an object with possible properties of capture,
  // passive, and once.
  //
  // We want to consistently return the same value, regardless of the order of
  // these properties, so let's use binary maths to assign each property to a
  // bit, and then add those together (with an offset to account for the
  // booleans at the beginning of this function).
  var capture = normalizedEventOptions.capture << 0;
  var passive = normalizedEventOptions.passive << 1;
  var once = normalizedEventOptions.once << 2;
  return capture + passive + once;
}

function ensureCanMutateNextEventHandlers(eventHandlers) {
  if (eventHandlers.handlers === eventHandlers.nextHandlers) {
    // eslint-disable-next-line no-param-reassign
    eventHandlers.nextHandlers = eventHandlers.handlers.slice();
  }
}

function TargetEventHandlers(target) {
  this.target = target;
  this.events = {};
}

TargetEventHandlers.prototype.getEventHandlers = function () {
  function getEventHandlers(eventName, options) {
    var key = String(eventName) + ' ' + String(eventOptionsKey(options));

    if (!this.events[key]) {
      this.events[key] = {
        handlers: [],
        handleEvent: undefined
      };
      this.events[key].nextHandlers = this.events[key].handlers;
    }

    return this.events[key];
  }

  return getEventHandlers;
}();

TargetEventHandlers.prototype.handleEvent = function () {
  function handleEvent(eventName, options, event) {
    var eventHandlers = this.getEventHandlers(eventName, options);
    eventHandlers.handlers = eventHandlers.nextHandlers;
    eventHandlers.handlers.forEach(function (handler) {
      if (handler) {
        // We need to check for presence here because a handler function may
        // cause later handlers to get removed. This can happen if you for
        // instance have a waypoint that unmounts another waypoint as part of an
        // onEnter/onLeave handler.
        handler(event);
      }
    });
  }

  return handleEvent;
}();

TargetEventHandlers.prototype.add = function () {
  function add(eventName, listener, options) {
    var _this = this;

    // options has already been normalized at this point.
    var eventHandlers = this.getEventHandlers(eventName, options);

    ensureCanMutateNextEventHandlers(eventHandlers);

    if (eventHandlers.nextHandlers.length === 0) {
      eventHandlers.handleEvent = this.handleEvent.bind(this, eventName, options);

      this.target.addEventListener(eventName, eventHandlers.handleEvent, options);
    }

    eventHandlers.nextHandlers.push(listener);

    var isSubscribed = true;
    var unsubscribe = function () {
      function unsubscribe() {
        if (!isSubscribed) {
          return;
        }

        isSubscribed = false;

        ensureCanMutateNextEventHandlers(eventHandlers);
        var index = eventHandlers.nextHandlers.indexOf(listener);
        eventHandlers.nextHandlers.splice(index, 1);

        if (eventHandlers.nextHandlers.length === 0) {
          // All event handlers have been removed, so we want to remove the event
          // listener from the target node.

          if (_this.target) {
            // There can be a race condition where the target may no longer exist
            // when this function is called, e.g. when a React component is
            // unmounting. Guarding against this prevents the following error:
            //
            //   Cannot read property 'removeEventListener' of undefined
            _this.target.removeEventListener(eventName, eventHandlers.handleEvent, options);
          }

          eventHandlers.handleEvent = undefined;
        }
      }

      return unsubscribe;
    }();
    return unsubscribe;
  }

  return add;
}();

var EVENT_HANDLERS_KEY = '__consolidated_events_handlers__';

// eslint-disable-next-line import/prefer-default-export
function addEventListener(target, eventName, listener, options) {
  if (!target[EVENT_HANDLERS_KEY]) {
    // eslint-disable-next-line no-param-reassign
    target[EVENT_HANDLERS_KEY] = new TargetEventHandlers(target);
  }
  var normalizedEventOptions = normalizeEventOptions(options);
  return target[EVENT_HANDLERS_KEY].add(eventName, listener, normalizedEventOptions);
}




/***/ }),

/***/ "1seS":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


// modified from https://github.com/es-shims/es5-shim
var has = Object.prototype.hasOwnProperty;
var toStr = Object.prototype.toString;
var slice = Array.prototype.slice;
var isArgs = __webpack_require__("1KsK");
var isEnumerable = Object.prototype.propertyIsEnumerable;
var hasDontEnumBug = !isEnumerable.call({ toString: null }, 'toString');
var hasProtoEnumBug = isEnumerable.call(function () {}, 'prototype');
var dontEnums = [
	'toString',
	'toLocaleString',
	'valueOf',
	'hasOwnProperty',
	'isPrototypeOf',
	'propertyIsEnumerable',
	'constructor'
];
var equalsConstructorPrototype = function (o) {
	var ctor = o.constructor;
	return ctor && ctor.prototype === o;
};
var excludedKeys = {
	$applicationCache: true,
	$console: true,
	$external: true,
	$frame: true,
	$frameElement: true,
	$frames: true,
	$innerHeight: true,
	$innerWidth: true,
	$outerHeight: true,
	$outerWidth: true,
	$pageXOffset: true,
	$pageYOffset: true,
	$parent: true,
	$scrollLeft: true,
	$scrollTop: true,
	$scrollX: true,
	$scrollY: true,
	$self: true,
	$webkitIndexedDB: true,
	$webkitStorageInfo: true,
	$window: true
};
var hasAutomationEqualityBug = (function () {
	/* global window */
	if (typeof window === 'undefined') { return false; }
	for (var k in window) {
		try {
			if (!excludedKeys['$' + k] && has.call(window, k) && window[k] !== null && typeof window[k] === 'object') {
				try {
					equalsConstructorPrototype(window[k]);
				} catch (e) {
					return true;
				}
			}
		} catch (e) {
			return true;
		}
	}
	return false;
}());
var equalsConstructorPrototypeIfNotBuggy = function (o) {
	/* global window */
	if (typeof window === 'undefined' || !hasAutomationEqualityBug) {
		return equalsConstructorPrototype(o);
	}
	try {
		return equalsConstructorPrototype(o);
	} catch (e) {
		return false;
	}
};

var keysShim = function keys(object) {
	var isObject = object !== null && typeof object === 'object';
	var isFunction = toStr.call(object) === '[object Function]';
	var isArguments = isArgs(object);
	var isString = isObject && toStr.call(object) === '[object String]';
	var theKeys = [];

	if (!isObject && !isFunction && !isArguments) {
		throw new TypeError('Object.keys called on a non-object');
	}

	var skipProto = hasProtoEnumBug && isFunction;
	if (isString && object.length > 0 && !has.call(object, 0)) {
		for (var i = 0; i < object.length; ++i) {
			theKeys.push(String(i));
		}
	}

	if (isArguments && object.length > 0) {
		for (var j = 0; j < object.length; ++j) {
			theKeys.push(String(j));
		}
	} else {
		for (var name in object) {
			if (!(skipProto && name === 'prototype') && has.call(object, name)) {
				theKeys.push(String(name));
			}
		}
	}

	if (hasDontEnumBug) {
		var skipConstructor = equalsConstructorPrototypeIfNotBuggy(object);

		for (var k = 0; k < dontEnums.length; ++k) {
			if (!(skipConstructor && dontEnums[k] === 'constructor') && has.call(object, dontEnums[k])) {
				theKeys.push(dontEnums[k]);
			}
		}
	}
	return theKeys;
};

keysShim.shim = function shimObjectKeys() {
	if (Object.keys) {
		var keysWorksWithArguments = (function () {
			// Safari 5.0 bug
			return (Object.keys(arguments) || '').length === 2;
		}(1, 2));
		if (!keysWorksWithArguments) {
			var originalKeys = Object.keys;
			Object.keys = function keys(object) { // eslint-disable-line func-name-matching
				if (isArgs(object)) {
					return originalKeys(slice.call(object));
				} else {
					return originalKeys(object);
				}
			};
		}
	} else {
		Object.keys = keysShim;
	}
	return Object.keys || keysShim;
};

module.exports = keysShim;


/***/ }),

/***/ "22yB":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var implementation = __webpack_require__("yN6O");

module.exports = function getPolyfill() {
	return Array.prototype.flat || implementation;
};


/***/ }),

/***/ "27z4":
/***/ (function(module, exports) {

(function() { module.exports = this["Popper"]; }());

/***/ }),

/***/ "284h":
/***/ (function(module, exports) {

function _interopRequireWildcard(obj) {
  if (obj && obj.__esModule) {
    return obj;
  } else {
    var newObj = {};

    if (obj != null) {
      for (var key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
          var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {};

          if (desc.get || desc.set) {
            Object.defineProperty(newObj, key, desc);
          } else {
            newObj[key] = obj[key];
          }
        }
      }
    }

    newObj["default"] = obj;
    return newObj;
  }
}

module.exports = _interopRequireWildcard;

/***/ }),

/***/ "2Nju":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var has = __webpack_require__("oNNP");
var regexExec = RegExp.prototype.exec;
var gOPD = Object.getOwnPropertyDescriptor;

var tryRegexExecCall = function tryRegexExec(value) {
	try {
		var lastIndex = value.lastIndex;
		value.lastIndex = 0;

		regexExec.call(value);
		return true;
	} catch (e) {
		return false;
	} finally {
		value.lastIndex = lastIndex;
	}
};
var toStr = Object.prototype.toString;
var regexClass = '[object RegExp]';
var hasToStringTag = typeof Symbol === 'function' && typeof Symbol.toStringTag === 'symbol';

module.exports = function isRegex(value) {
	if (!value || typeof value !== 'object') {
		return false;
	}
	if (!hasToStringTag) {
		return toStr.call(value) === regexClass;
	}

	var descriptor = gOPD(value, 'lastIndex');
	var hasLastIndexDataProperty = descriptor && has(descriptor, 'value');
	if (!hasLastIndexDataProperty) {
		return false;
	}

	return tryRegexExecCall(value);
};


/***/ }),

/***/ "2Q00":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = CalendarWeek;

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _CalendarDay = _interopRequireDefault(__webpack_require__("N3k4"));

var _CustomizableCalendarDay = _interopRequireDefault(__webpack_require__("GET3"));

var propTypes =  false ? undefined : {};

function CalendarWeek(_ref) {
  var children = _ref.children;
  return _react["default"].createElement("tr", null, children);
}

CalendarWeek.propTypes =  false ? undefined : {};

/***/ }),

/***/ "2S2E":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _constants = __webpack_require__("Fv1B");

var _default = _propTypes["default"].oneOf(_constants.WEEKDAYS);

exports["default"] = _default;

/***/ }),

/***/ "2W6z":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



/**
 * Similar to invariant but only logs a warning if the condition is not met.
 * This can be used to log issues in development environments in critical
 * paths. Removing the logging code for production environments will keep the
 * same logic and follow the same code paths.
 */

var __DEV__ = "production" !== 'production';

var warning = function() {};

if (__DEV__) {
  var printWarning = function printWarning(format, args) {
    var len = arguments.length;
    args = new Array(len > 1 ? len - 1 : 0);
    for (var key = 1; key < len; key++) {
      args[key - 1] = arguments[key];
    }
    var argIndex = 0;
    var message = 'Warning: ' +
      format.replace(/%s/g, function() {
        return args[argIndex++];
      });
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) {}
  }

  warning = function(condition, format, args) {
    var len = arguments.length;
    args = new Array(len > 2 ? len - 2 : 0);
    for (var key = 2; key < len; key++) {
      args[key - 2] = arguments[key];
    }
    if (format === undefined) {
      throw new Error(
          '`warning(condition, format, ...args)` requires a warning ' +
          'message argument'
      );
    }
    if (!condition) {
      printWarning.apply(null, [format].concat(args));
    }
  };
}

module.exports = warning;


/***/ }),

/***/ "2WTt":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = noflip;
var NOFLIP = '/* @noflip */'; // Appends a noflip comment to a style rule in order to prevent it from being automatically
// flipped in RTL contexts. This should be used only in situations where the style must remain
// unflipped regardless of direction context. See: https://github.com/kentcdodds/rtl-css-js#usage

function noflip(value) {
  if (typeof value === 'number') return "".concat(value, "px ").concat(NOFLIP);
  if (typeof value === 'string') return "".concat(value, " ").concat(NOFLIP);
  throw new TypeError('noflip expects a string or a number');
}

/***/ }),

/***/ "2gq3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var ES2015 = __webpack_require__("1RUG");
var assign = __webpack_require__("DGeY");

var ES2016 = assign(assign({}, ES2015), {
	// https://github.com/tc39/ecma262/pull/60
	SameValueNonNumber: function SameValueNonNumber(x, y) {
		if (typeof x === 'number' || typeof x !== typeof y) {
			throw new TypeError('SameValueNonNumber requires two non-number values of the same type.');
		}
		return this.SameValue(x, y);
	}
});

module.exports = ES2016;


/***/ }),

/***/ "2mcs":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */



var emptyFunction = __webpack_require__("ohE5");

/**
 * Similar to invariant but only logs a warning if the condition is not met.
 * This can be used to log issues in development environments in critical
 * paths. Removing the logging code for production environments will keep the
 * same logic and follow the same code paths.
 */

var warning = emptyFunction;

if (false) { var printWarning; }

module.exports = warning;

/***/ }),

/***/ "2mql":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Copyright 2015, Yahoo! Inc.
 * Copyrights licensed under the New BSD License. See the accompanying LICENSE file for terms.
 */
var ReactIs = __webpack_require__("TOwV");
var REACT_STATICS = {
    childContextTypes: true,
    contextType: true,
    contextTypes: true,
    defaultProps: true,
    displayName: true,
    getDefaultProps: true,
    getDerivedStateFromError: true,
    getDerivedStateFromProps: true,
    mixins: true,
    propTypes: true,
    type: true
};

var KNOWN_STATICS = {
    name: true,
    length: true,
    prototype: true,
    caller: true,
    callee: true,
    arguments: true,
    arity: true
};

var FORWARD_REF_STATICS = {
    '$$typeof': true,
    render: true,
    defaultProps: true,
    displayName: true,
    propTypes: true
};

var MEMO_STATICS = {
    '$$typeof': true,
    compare: true,
    defaultProps: true,
    displayName: true,
    propTypes: true,
    type: true
};

var TYPE_STATICS = {};
TYPE_STATICS[ReactIs.ForwardRef] = FORWARD_REF_STATICS;

function getStatics(component) {
    if (ReactIs.isMemo(component)) {
        return MEMO_STATICS;
    }
    return TYPE_STATICS[component['$$typeof']] || REACT_STATICS;
}

var defineProperty = Object.defineProperty;
var getOwnPropertyNames = Object.getOwnPropertyNames;
var getOwnPropertySymbols = Object.getOwnPropertySymbols;
var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
var getPrototypeOf = Object.getPrototypeOf;
var objectPrototype = Object.prototype;

function hoistNonReactStatics(targetComponent, sourceComponent, blacklist) {
    if (typeof sourceComponent !== 'string') {
        // don't hoist over string (html) components

        if (objectPrototype) {
            var inheritedComponent = getPrototypeOf(sourceComponent);
            if (inheritedComponent && inheritedComponent !== objectPrototype) {
                hoistNonReactStatics(targetComponent, inheritedComponent, blacklist);
            }
        }

        var keys = getOwnPropertyNames(sourceComponent);

        if (getOwnPropertySymbols) {
            keys = keys.concat(getOwnPropertySymbols(sourceComponent));
        }

        var targetStatics = getStatics(targetComponent);
        var sourceStatics = getStatics(sourceComponent);

        for (var i = 0; i < keys.length; ++i) {
            var key = keys[i];
            if (!KNOWN_STATICS[key] && !(blacklist && blacklist[key]) && !(sourceStatics && sourceStatics[key]) && !(targetStatics && targetStatics[key])) {
                var descriptor = getOwnPropertyDescriptor(sourceComponent, key);
                try {
                    // Avoid failures from read-only properties
                    defineProperty(targetComponent, key, descriptor);
                } catch (e) {}
            }
        }

        return targetComponent;
    }

    return targetComponent;
}

module.exports = hoistNonReactStatics;


/***/ }),

/***/ "3HjQ":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

// This function takes an array of styles and separates them into styles that
// are handled by Aphrodite and inline styles.
function separateStyles(stylesArray) {
  var classNames = []; // Since determining if an Object is empty requires collecting all of its
  // keys, and we want the best performance in this code because we are in the
  // render path, we are going to do a little bookkeeping ourselves.

  var hasInlineStyles = false;
  var inlineStyles = {}; // This is run on potentially every node in the tree when rendering, where
  // performance is critical. Normally we would prefer using `forEach`, but
  // old-fashioned for loops are faster so that's what we have chosen here.

  for (var i = 0; i < stylesArray.length; i++) {
    // eslint-disable-line no-plusplus
    var style = stylesArray[i]; // If this  style is falsy, we just want to disregard it. This allows for
    // syntax like:
    //
    //   css(isFoo && styles.foo)

    if (style) {
      if (typeof style === 'string') {
        classNames.push(style);
      } else {
        Object.assign(inlineStyles, style);
        hasInlineStyles = true;
      }
    }
  }

  return {
    classNames: classNames,
    hasInlineStyles: hasInlineStyles,
    inlineStyles: inlineStyles
  };
}

var _default = separateStyles;
exports["default"] = _default;

/***/ }),

/***/ "3gBW":
/***/ (function(module, exports, __webpack_require__) {

// eslint-disable-next-line import/no-unresolved
module.exports = __webpack_require__("50qU");


/***/ }),

/***/ "4CzF":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = exports.PureSingleDatePicker = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _reactWithStyles = __webpack_require__("TG4+");

var _reactPortal = __webpack_require__("7OqA");

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _consolidatedEvents = __webpack_require__("1TsT");

var _isTouchDevice = _interopRequireDefault(__webpack_require__("LTAC"));

var _reactOutsideClickHandler = _interopRequireDefault(__webpack_require__("3gBW"));

var _SingleDatePickerShape = _interopRequireDefault(__webpack_require__("TUgy"));

var _defaultPhrases = __webpack_require__("vV+G");

var _getResponsiveContainerStyles = _interopRequireDefault(__webpack_require__("w0iJ"));

var _getDetachedContainerStyles = _interopRequireDefault(__webpack_require__("yR3C"));

var _getInputHeight = _interopRequireDefault(__webpack_require__("hgME"));

var _isInclusivelyAfterDay = _interopRequireDefault(__webpack_require__("rit9"));

var _disableScroll2 = _interopRequireDefault(__webpack_require__("zec9"));

var _noflip = _interopRequireDefault(__webpack_require__("2WTt"));

var _SingleDatePickerInputController = _interopRequireDefault(__webpack_require__("NiDq"));

var _DayPickerSingleDateController = _interopRequireDefault(__webpack_require__("Xtko"));

var _CloseButton = _interopRequireDefault(__webpack_require__("xEte"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  // required props for a functional interactive SingleDatePicker
  date: null,
  focused: false,
  // input related props
  id: 'date',
  placeholder: 'Date',
  ariaLabel: undefined,
  disabled: false,
  required: false,
  readOnly: false,
  screenReaderInputMessage: '',
  showClearDate: false,
  showDefaultInputIcon: false,
  inputIconPosition: _constants.ICON_BEFORE_POSITION,
  customInputIcon: null,
  customCloseIcon: null,
  noBorder: false,
  block: false,
  small: false,
  regular: false,
  verticalSpacing: _constants.DEFAULT_VERTICAL_SPACING,
  keepFocusOnInput: false,
  // calendar presentation and interaction related props
  orientation: _constants.HORIZONTAL_ORIENTATION,
  anchorDirection: _constants.ANCHOR_LEFT,
  openDirection: _constants.OPEN_DOWN,
  horizontalMargin: 0,
  withPortal: false,
  withFullScreenPortal: false,
  appendToBody: false,
  disableScroll: false,
  initialVisibleMonth: null,
  firstDayOfWeek: null,
  numberOfMonths: 2,
  keepOpenOnDateSelect: false,
  reopenPickerOnClearDate: false,
  renderCalendarInfo: null,
  calendarInfoPosition: _constants.INFO_POSITION_BOTTOM,
  hideKeyboardShortcutsPanel: false,
  daySize: _constants.DAY_SIZE,
  isRTL: false,
  verticalHeight: null,
  transitionDuration: undefined,
  horizontalMonthPadding: 13,
  // navigation related props
  navPrev: null,
  navNext: null,
  onPrevMonthClick: function onPrevMonthClick() {},
  onNextMonthClick: function onNextMonthClick() {},
  onClose: function onClose() {},
  // month presentation and interaction related props
  renderMonthText: null,
  // day presentation and interaction related props
  renderCalendarDay: undefined,
  renderDayContents: null,
  renderMonthElement: null,
  enableOutsideDays: false,
  isDayBlocked: function isDayBlocked() {
    return false;
  },
  isOutsideRange: function isOutsideRange(day) {
    return !(0, _isInclusivelyAfterDay["default"])(day, (0, _moment["default"])());
  },
  isDayHighlighted: function isDayHighlighted() {},
  // internationalization props
  displayFormat: function displayFormat() {
    return _moment["default"].localeData().longDateFormat('L');
  },
  monthFormat: 'MMMM YYYY',
  weekDayFormat: 'dd',
  phrases: _defaultPhrases.SingleDatePickerPhrases,
  dayAriaLabelFormat: undefined
};

var SingleDatePicker =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(SingleDatePicker, _ref);
  var _proto = SingleDatePicker.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function SingleDatePicker(props) {
    var _this;

    _this = _ref.call(this, props) || this;
    _this.isTouchDevice = false;
    _this.state = {
      dayPickerContainerStyles: {},
      isDayPickerFocused: false,
      isInputFocused: false,
      showKeyboardShortcuts: false
    };
    _this.onFocusOut = _this.onFocusOut.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onOutsideClick = _this.onOutsideClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onInputFocus = _this.onInputFocus.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onDayPickerFocus = _this.onDayPickerFocus.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onDayPickerBlur = _this.onDayPickerBlur.bind((0, _assertThisInitialized2["default"])(_this));
    _this.showKeyboardShortcutsPanel = _this.showKeyboardShortcutsPanel.bind((0, _assertThisInitialized2["default"])(_this));
    _this.responsivizePickerPosition = _this.responsivizePickerPosition.bind((0, _assertThisInitialized2["default"])(_this));
    _this.disableScroll = _this.disableScroll.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setDayPickerContainerRef = _this.setDayPickerContainerRef.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setContainerRef = _this.setContainerRef.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }
  /* istanbul ignore next */


  _proto.componentDidMount = function componentDidMount() {
    this.removeResizeEventListener = (0, _consolidatedEvents.addEventListener)(window, 'resize', this.responsivizePickerPosition, {
      passive: true
    });
    this.responsivizePickerPosition();
    this.disableScroll();
    var focused = this.props.focused;

    if (focused) {
      this.setState({
        isInputFocused: true
      });
    }

    this.isTouchDevice = (0, _isTouchDevice["default"])();
  };

  _proto.componentDidUpdate = function componentDidUpdate(prevProps) {
    var focused = this.props.focused;

    if (!prevProps.focused && focused) {
      this.responsivizePickerPosition();
      this.disableScroll();
    } else if (prevProps.focused && !focused) {
      if (this.enableScroll) this.enableScroll();
    }
  }
  /* istanbul ignore next */
  ;

  _proto.componentWillUnmount = function componentWillUnmount() {
    if (this.removeResizeEventListener) this.removeResizeEventListener();
    if (this.removeFocusOutEventListener) this.removeFocusOutEventListener();
    if (this.enableScroll) this.enableScroll();
  };

  _proto.onOutsideClick = function onOutsideClick(event) {
    var _this$props = this.props,
        focused = _this$props.focused,
        onFocusChange = _this$props.onFocusChange,
        onClose = _this$props.onClose,
        date = _this$props.date,
        appendToBody = _this$props.appendToBody;
    if (!focused) return;
    if (appendToBody && this.dayPickerContainer.contains(event.target)) return;
    this.setState({
      isInputFocused: false,
      isDayPickerFocused: false,
      showKeyboardShortcuts: false
    });
    onFocusChange({
      focused: false
    });
    onClose({
      date: date
    });
  };

  _proto.onInputFocus = function onInputFocus(_ref2) {
    var focused = _ref2.focused;
    var _this$props2 = this.props,
        onFocusChange = _this$props2.onFocusChange,
        readOnly = _this$props2.readOnly,
        withPortal = _this$props2.withPortal,
        withFullScreenPortal = _this$props2.withFullScreenPortal,
        keepFocusOnInput = _this$props2.keepFocusOnInput;

    if (focused) {
      var withAnyPortal = withPortal || withFullScreenPortal;
      var moveFocusToDayPicker = withAnyPortal || readOnly && !keepFocusOnInput || this.isTouchDevice && !keepFocusOnInput;

      if (moveFocusToDayPicker) {
        this.onDayPickerFocus();
      } else {
        this.onDayPickerBlur();
      }
    }

    onFocusChange({
      focused: focused
    });
  };

  _proto.onDayPickerFocus = function onDayPickerFocus() {
    this.setState({
      isInputFocused: false,
      isDayPickerFocused: true,
      showKeyboardShortcuts: false
    });
  };

  _proto.onDayPickerBlur = function onDayPickerBlur() {
    this.setState({
      isInputFocused: true,
      isDayPickerFocused: false,
      showKeyboardShortcuts: false
    });
  };

  _proto.onFocusOut = function onFocusOut(e) {
    var onFocusChange = this.props.onFocusChange; // In cases where **relatedTarget** is not null, it points to the right
    // element here. However, in cases where it is null (such as clicking on a
    // specific day) or it is **document.body** (IE11), the appropriate value is **event.target**.
    //
    // We handle both situations here by using the ` || ` operator to fallback
    // to *event.target** when **relatedTarget** is not provided.

    var relatedTarget = e.relatedTarget === document.body ? e.target : e.relatedTarget || e.target;
    if (this.dayPickerContainer.contains(relatedTarget)) return;
    onFocusChange({
      focused: false
    });
  };

  _proto.setDayPickerContainerRef = function setDayPickerContainerRef(ref) {
    if (ref === this.dayPickerContainer) return;
    this.removeEventListeners();
    this.dayPickerContainer = ref;
    if (!ref) return;
    this.addEventListeners();
  };

  _proto.setContainerRef = function setContainerRef(ref) {
    this.container = ref;
  };

  _proto.addEventListeners = function addEventListeners() {
    // We manually set event because React has not implemented onFocusIn/onFocusOut.
    // Keep an eye on https://github.com/facebook/react/issues/6410 for updates
    // We use "blur w/ useCapture param" vs "onfocusout" for FF browser support
    this.removeFocusOutEventListener = (0, _consolidatedEvents.addEventListener)(this.dayPickerContainer, 'focusout', this.onFocusOut);
  };

  _proto.removeEventListeners = function removeEventListeners() {
    if (this.removeFocusOutEventListener) this.removeFocusOutEventListener();
  };

  _proto.disableScroll = function disableScroll() {
    var _this$props3 = this.props,
        appendToBody = _this$props3.appendToBody,
        propDisableScroll = _this$props3.disableScroll,
        focused = _this$props3.focused;
    if (!appendToBody && !propDisableScroll) return;
    if (!focused) return; // Disable scroll for every ancestor of this <SingleDatePicker> up to the
    // document level. This ensures the input and the picker never move. Other
    // sibling elements or the picker itself can scroll.

    this.enableScroll = (0, _disableScroll2["default"])(this.container);
  }
  /* istanbul ignore next */
  ;

  _proto.responsivizePickerPosition = function responsivizePickerPosition() {
    // It's possible the portal props have been changed in response to window resizes
    // So let's ensure we reset this back to the base state each time
    this.setState({
      dayPickerContainerStyles: {}
    });
    var _this$props4 = this.props,
        openDirection = _this$props4.openDirection,
        anchorDirection = _this$props4.anchorDirection,
        horizontalMargin = _this$props4.horizontalMargin,
        withPortal = _this$props4.withPortal,
        withFullScreenPortal = _this$props4.withFullScreenPortal,
        appendToBody = _this$props4.appendToBody,
        focused = _this$props4.focused;
    var dayPickerContainerStyles = this.state.dayPickerContainerStyles;

    if (!focused) {
      return;
    }

    var isAnchoredLeft = anchorDirection === _constants.ANCHOR_LEFT;

    if (!withPortal && !withFullScreenPortal) {
      var containerRect = this.dayPickerContainer.getBoundingClientRect();
      var currentOffset = dayPickerContainerStyles[anchorDirection] || 0;
      var containerEdge = isAnchoredLeft ? containerRect[_constants.ANCHOR_RIGHT] : containerRect[_constants.ANCHOR_LEFT];
      this.setState({
        dayPickerContainerStyles: _objectSpread({}, (0, _getResponsiveContainerStyles["default"])(anchorDirection, currentOffset, containerEdge, horizontalMargin), {}, appendToBody && (0, _getDetachedContainerStyles["default"])(openDirection, anchorDirection, this.container))
      });
    }
  };

  _proto.showKeyboardShortcutsPanel = function showKeyboardShortcutsPanel() {
    this.setState({
      isInputFocused: false,
      isDayPickerFocused: true,
      showKeyboardShortcuts: true
    });
  };

  _proto.maybeRenderDayPickerWithPortal = function maybeRenderDayPickerWithPortal() {
    var _this$props5 = this.props,
        focused = _this$props5.focused,
        withPortal = _this$props5.withPortal,
        withFullScreenPortal = _this$props5.withFullScreenPortal,
        appendToBody = _this$props5.appendToBody;

    if (!focused) {
      return null;
    }

    if (withPortal || withFullScreenPortal || appendToBody) {
      return _react["default"].createElement(_reactPortal.Portal, null, this.renderDayPicker());
    }

    return this.renderDayPicker();
  };

  _proto.renderDayPicker = function renderDayPicker() {
    var _this$props6 = this.props,
        anchorDirection = _this$props6.anchorDirection,
        css = _this$props6.css,
        openDirection = _this$props6.openDirection,
        onDateChange = _this$props6.onDateChange,
        date = _this$props6.date,
        onFocusChange = _this$props6.onFocusChange,
        focused = _this$props6.focused,
        enableOutsideDays = _this$props6.enableOutsideDays,
        numberOfMonths = _this$props6.numberOfMonths,
        orientation = _this$props6.orientation,
        monthFormat = _this$props6.monthFormat,
        navPrev = _this$props6.navPrev,
        navNext = _this$props6.navNext,
        onPrevMonthClick = _this$props6.onPrevMonthClick,
        onNextMonthClick = _this$props6.onNextMonthClick,
        onClose = _this$props6.onClose,
        withPortal = _this$props6.withPortal,
        withFullScreenPortal = _this$props6.withFullScreenPortal,
        keepOpenOnDateSelect = _this$props6.keepOpenOnDateSelect,
        initialVisibleMonth = _this$props6.initialVisibleMonth,
        renderMonthText = _this$props6.renderMonthText,
        renderCalendarDay = _this$props6.renderCalendarDay,
        renderDayContents = _this$props6.renderDayContents,
        renderCalendarInfo = _this$props6.renderCalendarInfo,
        renderMonthElement = _this$props6.renderMonthElement,
        calendarInfoPosition = _this$props6.calendarInfoPosition,
        hideKeyboardShortcutsPanel = _this$props6.hideKeyboardShortcutsPanel,
        firstDayOfWeek = _this$props6.firstDayOfWeek,
        customCloseIcon = _this$props6.customCloseIcon,
        phrases = _this$props6.phrases,
        dayAriaLabelFormat = _this$props6.dayAriaLabelFormat,
        daySize = _this$props6.daySize,
        isRTL = _this$props6.isRTL,
        isOutsideRange = _this$props6.isOutsideRange,
        isDayBlocked = _this$props6.isDayBlocked,
        isDayHighlighted = _this$props6.isDayHighlighted,
        weekDayFormat = _this$props6.weekDayFormat,
        styles = _this$props6.styles,
        verticalHeight = _this$props6.verticalHeight,
        transitionDuration = _this$props6.transitionDuration,
        verticalSpacing = _this$props6.verticalSpacing,
        horizontalMonthPadding = _this$props6.horizontalMonthPadding,
        small = _this$props6.small,
        reactDates = _this$props6.theme.reactDates;
    var _this$state = this.state,
        dayPickerContainerStyles = _this$state.dayPickerContainerStyles,
        isDayPickerFocused = _this$state.isDayPickerFocused,
        showKeyboardShortcuts = _this$state.showKeyboardShortcuts;
    var onOutsideClick = !withFullScreenPortal && withPortal ? this.onOutsideClick : undefined;

    var closeIcon = customCloseIcon || _react["default"].createElement(_CloseButton["default"], null);

    var inputHeight = (0, _getInputHeight["default"])(reactDates, small);
    var withAnyPortal = withPortal || withFullScreenPortal;
    return _react["default"].createElement("div", (0, _extends2["default"])({
      // eslint-disable-line jsx-a11y/no-static-element-interactions
      ref: this.setDayPickerContainerRef
    }, css(styles.SingleDatePicker_picker, anchorDirection === _constants.ANCHOR_LEFT && styles.SingleDatePicker_picker__directionLeft, anchorDirection === _constants.ANCHOR_RIGHT && styles.SingleDatePicker_picker__directionRight, openDirection === _constants.OPEN_DOWN && styles.SingleDatePicker_picker__openDown, openDirection === _constants.OPEN_UP && styles.SingleDatePicker_picker__openUp, !withAnyPortal && openDirection === _constants.OPEN_DOWN && {
      top: inputHeight + verticalSpacing
    }, !withAnyPortal && openDirection === _constants.OPEN_UP && {
      bottom: inputHeight + verticalSpacing
    }, orientation === _constants.HORIZONTAL_ORIENTATION && styles.SingleDatePicker_picker__horizontal, orientation === _constants.VERTICAL_ORIENTATION && styles.SingleDatePicker_picker__vertical, withAnyPortal && styles.SingleDatePicker_picker__portal, withFullScreenPortal && styles.SingleDatePicker_picker__fullScreenPortal, isRTL && styles.SingleDatePicker_picker__rtl, dayPickerContainerStyles), {
      onClick: onOutsideClick
    }), _react["default"].createElement(_DayPickerSingleDateController["default"], {
      date: date,
      onDateChange: onDateChange,
      onFocusChange: onFocusChange,
      orientation: orientation,
      enableOutsideDays: enableOutsideDays,
      numberOfMonths: numberOfMonths,
      monthFormat: monthFormat,
      withPortal: withAnyPortal,
      focused: focused,
      keepOpenOnDateSelect: keepOpenOnDateSelect,
      hideKeyboardShortcutsPanel: hideKeyboardShortcutsPanel,
      initialVisibleMonth: initialVisibleMonth,
      navPrev: navPrev,
      navNext: navNext,
      onPrevMonthClick: onPrevMonthClick,
      onNextMonthClick: onNextMonthClick,
      onClose: onClose,
      renderMonthText: renderMonthText,
      renderCalendarDay: renderCalendarDay,
      renderDayContents: renderDayContents,
      renderCalendarInfo: renderCalendarInfo,
      renderMonthElement: renderMonthElement,
      calendarInfoPosition: calendarInfoPosition,
      isFocused: isDayPickerFocused,
      showKeyboardShortcuts: showKeyboardShortcuts,
      onBlur: this.onDayPickerBlur,
      phrases: phrases,
      dayAriaLabelFormat: dayAriaLabelFormat,
      daySize: daySize,
      isRTL: isRTL,
      isOutsideRange: isOutsideRange,
      isDayBlocked: isDayBlocked,
      isDayHighlighted: isDayHighlighted,
      firstDayOfWeek: firstDayOfWeek,
      weekDayFormat: weekDayFormat,
      verticalHeight: verticalHeight,
      transitionDuration: transitionDuration,
      horizontalMonthPadding: horizontalMonthPadding
    }), withFullScreenPortal && _react["default"].createElement("button", (0, _extends2["default"])({}, css(styles.SingleDatePicker_closeButton), {
      "aria-label": phrases.closeDatePicker,
      type: "button",
      onClick: this.onOutsideClick
    }), _react["default"].createElement("div", css(styles.SingleDatePicker_closeButton_svg), closeIcon)));
  };

  _proto.render = function render() {
    var _this$props7 = this.props,
        css = _this$props7.css,
        id = _this$props7.id,
        placeholder = _this$props7.placeholder,
        ariaLabel = _this$props7.ariaLabel,
        disabled = _this$props7.disabled,
        focused = _this$props7.focused,
        required = _this$props7.required,
        readOnly = _this$props7.readOnly,
        openDirection = _this$props7.openDirection,
        showClearDate = _this$props7.showClearDate,
        showDefaultInputIcon = _this$props7.showDefaultInputIcon,
        inputIconPosition = _this$props7.inputIconPosition,
        customCloseIcon = _this$props7.customCloseIcon,
        customInputIcon = _this$props7.customInputIcon,
        date = _this$props7.date,
        onDateChange = _this$props7.onDateChange,
        displayFormat = _this$props7.displayFormat,
        phrases = _this$props7.phrases,
        withPortal = _this$props7.withPortal,
        withFullScreenPortal = _this$props7.withFullScreenPortal,
        screenReaderInputMessage = _this$props7.screenReaderInputMessage,
        isRTL = _this$props7.isRTL,
        noBorder = _this$props7.noBorder,
        block = _this$props7.block,
        small = _this$props7.small,
        regular = _this$props7.regular,
        verticalSpacing = _this$props7.verticalSpacing,
        reopenPickerOnClearDate = _this$props7.reopenPickerOnClearDate,
        keepOpenOnDateSelect = _this$props7.keepOpenOnDateSelect,
        styles = _this$props7.styles,
        isOutsideRange = _this$props7.isOutsideRange;
    var isInputFocused = this.state.isInputFocused;
    var enableOutsideClick = !withPortal && !withFullScreenPortal;
    var hideFang = verticalSpacing < _constants.FANG_HEIGHT_PX;

    var input = _react["default"].createElement(_SingleDatePickerInputController["default"], {
      id: id,
      placeholder: placeholder,
      ariaLabel: ariaLabel,
      focused: focused,
      isFocused: isInputFocused,
      disabled: disabled,
      required: required,
      readOnly: readOnly,
      openDirection: openDirection,
      showCaret: !withPortal && !withFullScreenPortal && !hideFang,
      showClearDate: showClearDate,
      showDefaultInputIcon: showDefaultInputIcon,
      inputIconPosition: inputIconPosition,
      isOutsideRange: isOutsideRange,
      customCloseIcon: customCloseIcon,
      customInputIcon: customInputIcon,
      date: date,
      onDateChange: onDateChange,
      displayFormat: displayFormat,
      onFocusChange: this.onInputFocus,
      onKeyDownArrowDown: this.onDayPickerFocus,
      onKeyDownQuestionMark: this.showKeyboardShortcutsPanel,
      screenReaderMessage: screenReaderInputMessage,
      phrases: phrases,
      isRTL: isRTL,
      noBorder: noBorder,
      block: block,
      small: small,
      regular: regular,
      verticalSpacing: verticalSpacing,
      reopenPickerOnClearDate: reopenPickerOnClearDate,
      keepOpenOnDateSelect: keepOpenOnDateSelect
    }, this.maybeRenderDayPickerWithPortal());

    return _react["default"].createElement("div", (0, _extends2["default"])({
      ref: this.setContainerRef
    }, css(styles.SingleDatePicker, block && styles.SingleDatePicker__block)), enableOutsideClick && _react["default"].createElement(_reactOutsideClickHandler["default"], {
      onOutsideClick: this.onOutsideClick
    }, input), enableOutsideClick || input);
  };

  return SingleDatePicker;
}(_react["default"].PureComponent || _react["default"].Component);

exports.PureSingleDatePicker = SingleDatePicker;
SingleDatePicker.propTypes =  false ? undefined : {};
SingleDatePicker.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref3) {
  var _ref3$reactDates = _ref3.reactDates,
      color = _ref3$reactDates.color,
      zIndex = _ref3$reactDates.zIndex;
  return {
    SingleDatePicker: {
      position: 'relative',
      display: 'inline-block'
    },
    SingleDatePicker__block: {
      display: 'block'
    },
    SingleDatePicker_picker: {
      zIndex: zIndex + 1,
      backgroundColor: color.background,
      position: 'absolute'
    },
    SingleDatePicker_picker__rtl: {
      direction: (0, _noflip["default"])('rtl')
    },
    SingleDatePicker_picker__directionLeft: {
      left: (0, _noflip["default"])(0)
    },
    SingleDatePicker_picker__directionRight: {
      right: (0, _noflip["default"])(0)
    },
    SingleDatePicker_picker__portal: {
      backgroundColor: 'rgba(0, 0, 0, 0.3)',
      position: 'fixed',
      top: 0,
      left: (0, _noflip["default"])(0),
      height: '100%',
      width: '100%'
    },
    SingleDatePicker_picker__fullScreenPortal: {
      backgroundColor: color.background
    },
    SingleDatePicker_closeButton: {
      background: 'none',
      border: 0,
      color: 'inherit',
      font: 'inherit',
      lineHeight: 'normal',
      overflow: 'visible',
      cursor: 'pointer',
      position: 'absolute',
      top: 0,
      right: (0, _noflip["default"])(0),
      padding: 15,
      zIndex: zIndex + 2,
      ':hover': {
        color: "darken(".concat(color.core.grayLighter, ", 10%)"),
        textDecoration: 'none'
      },
      ':focus': {
        color: "darken(".concat(color.core.grayLighter, ", 10%)"),
        textDecoration: 'none'
      }
    },
    SingleDatePicker_closeButton_svg: {
      height: 15,
      width: 15,
      fill: color.core.grayLighter
    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(SingleDatePicker);

exports["default"] = _default;

/***/ }),

/***/ "4cSd":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var define = __webpack_require__("82c2");

var implementation = __webpack_require__("rQy3");
var getPolyfill = __webpack_require__("xoj2");
var shim = __webpack_require__("ib7Q");

var polyfill = getPolyfill();

define(polyfill, {
	getPolyfill: getPolyfill,
	implementation: implementation,
	shim: shim
});

module.exports = polyfill;


/***/ }),

/***/ "50qU":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = __webpack_require__("cDcd");

var _react2 = _interopRequireDefault(_react);

var _propTypes = __webpack_require__("17x9");

var _propTypes2 = _interopRequireDefault(_propTypes);

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _consolidatedEvents = __webpack_require__("1TsT");

var _object = __webpack_require__("4cSd");

var _object2 = _interopRequireDefault(_object);

var _document = __webpack_require__("n1Y7");

var _document2 = _interopRequireDefault(_document);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var DISPLAY = {
  BLOCK: 'block',
  FLEX: 'flex',
  INLINE_BLOCK: 'inline-block'
};

var propTypes = (0, _airbnbPropTypes.forbidExtraProps)({
  children: _propTypes2['default'].node.isRequired,
  onOutsideClick: _propTypes2['default'].func.isRequired,
  disabled: _propTypes2['default'].bool,
  useCapture: _propTypes2['default'].bool,
  display: _propTypes2['default'].oneOf((0, _object2['default'])(DISPLAY))
});

var defaultProps = {
  disabled: false,

  // `useCapture` is set to true by default so that a `stopPropagation` in the
  // children will not prevent all outside click handlers from firing - maja
  useCapture: true,
  display: DISPLAY.BLOCK
};

var OutsideClickHandler = function (_React$Component) {
  _inherits(OutsideClickHandler, _React$Component);

  function OutsideClickHandler() {
    var _ref;

    _classCallCheck(this, OutsideClickHandler);

    for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    var _this = _possibleConstructorReturn(this, (_ref = OutsideClickHandler.__proto__ || Object.getPrototypeOf(OutsideClickHandler)).call.apply(_ref, [this].concat(args)));

    _this.onMouseDown = _this.onMouseDown.bind(_this);
    _this.onMouseUp = _this.onMouseUp.bind(_this);
    _this.setChildNodeRef = _this.setChildNodeRef.bind(_this);
    return _this;
  }

  _createClass(OutsideClickHandler, [{
    key: 'componentDidMount',
    value: function () {
      function componentDidMount() {
        var _props = this.props,
            disabled = _props.disabled,
            useCapture = _props.useCapture;


        if (!disabled) this.addMouseDownEventListener(useCapture);
      }

      return componentDidMount;
    }()
  }, {
    key: 'componentWillReceiveProps',
    value: function () {
      function componentWillReceiveProps(_ref2) {
        var disabled = _ref2.disabled,
            useCapture = _ref2.useCapture;
        var prevDisabled = this.props.disabled;

        if (prevDisabled !== disabled) {
          if (disabled) {
            this.removeEventListeners();
          } else {
            this.addMouseDownEventListener(useCapture);
          }
        }
      }

      return componentWillReceiveProps;
    }()
  }, {
    key: 'componentWillUnmount',
    value: function () {
      function componentWillUnmount() {
        this.removeEventListeners();
      }

      return componentWillUnmount;
    }()

    // Use mousedown/mouseup to enforce that clicks remain outside the root's
    // descendant tree, even when dragged. This should also get triggered on
    // touch devices.

  }, {
    key: 'onMouseDown',
    value: function () {
      function onMouseDown(e) {
        var useCapture = this.props.useCapture;


        var isDescendantOfRoot = this.childNode && (0, _document2['default'])(this.childNode, e.target);
        if (!isDescendantOfRoot) {
          if (this.removeMouseUp) {
            this.removeMouseUp();
            this.removeMouseUp = null;
          }
          this.removeMouseUp = (0, _consolidatedEvents.addEventListener)(document, 'mouseup', this.onMouseUp, { capture: useCapture });
        }
      }

      return onMouseDown;
    }()

    // Use mousedown/mouseup to enforce that clicks remain outside the root's
    // descendant tree, even when dragged. This should also get triggered on
    // touch devices.

  }, {
    key: 'onMouseUp',
    value: function () {
      function onMouseUp(e) {
        var onOutsideClick = this.props.onOutsideClick;


        var isDescendantOfRoot = this.childNode && (0, _document2['default'])(this.childNode, e.target);
        if (this.removeMouseUp) {
          this.removeMouseUp();
          this.removeMouseUp = null;
        }

        if (!isDescendantOfRoot) {
          onOutsideClick(e);
        }
      }

      return onMouseUp;
    }()
  }, {
    key: 'setChildNodeRef',
    value: function () {
      function setChildNodeRef(ref) {
        this.childNode = ref;
      }

      return setChildNodeRef;
    }()
  }, {
    key: 'addMouseDownEventListener',
    value: function () {
      function addMouseDownEventListener(useCapture) {
        this.removeMouseDown = (0, _consolidatedEvents.addEventListener)(document, 'mousedown', this.onMouseDown, { capture: useCapture });
      }

      return addMouseDownEventListener;
    }()
  }, {
    key: 'removeEventListeners',
    value: function () {
      function removeEventListeners() {
        if (this.removeMouseDown) this.removeMouseDown();
        if (this.removeMouseUp) this.removeMouseUp();
      }

      return removeEventListeners;
    }()
  }, {
    key: 'render',
    value: function () {
      function render() {
        var _props2 = this.props,
            children = _props2.children,
            display = _props2.display;


        return _react2['default'].createElement(
          'div',
          {
            ref: this.setChildNodeRef,
            style: display !== DISPLAY.BLOCK && (0, _object2['default'])(DISPLAY).includes(display) ? { display: display } : undefined
          },
          children
        );
      }

      return render;
    }()
  }]);

  return OutsideClickHandler;
}(_react2['default'].Component);

exports['default'] = OutsideClickHandler;


OutsideClickHandler.propTypes = propTypes;
OutsideClickHandler.defaultProps = defaultProps;

/***/ }),

/***/ "5Fvu":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _constants = __webpack_require__("Fv1B");

var _default = _propTypes["default"].oneOfType([_propTypes["default"].bool, _propTypes["default"].oneOf([_constants.START_DATE, _constants.END_DATE])]);

exports["default"] = _default;

/***/ }),

/***/ "5bN9":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _constants = __webpack_require__("Fv1B");

var _default = _propTypes["default"].oneOf([_constants.START_DATE, _constants.END_DATE]);

exports["default"] = _default;

/***/ }),

/***/ "5f7n":
/***/ (function(module, exports, __webpack_require__) {

// eslint-disable-next-line import/no-unresolved
module.exports = __webpack_require__("Fv1B");


/***/ }),

/***/ "5yQQ":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var implementation = __webpack_require__("nRDI");

module.exports = function getPolyfill() {
	if (typeof document !== 'undefined') {
		if (document.contains) {
			return document.contains;
		}
		if (document.body && document.body.contains) {
			return document.body.contains;
		}
	}
	return implementation;
};


/***/ }),

/***/ "6HWY":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isNextMonth;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _isSameMonth = _interopRequireDefault(__webpack_require__("ulUS"));

function isNextMonth(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false;
  return (0, _isSameMonth["default"])(a.clone().add(1, 'month'), b);
}

/***/ }),

/***/ "6ayh":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/* globals
	Set,
	Map,
	WeakSet,
	WeakMap,

	Promise,

	Symbol,
	Proxy,

	Atomics,
	SharedArrayBuffer,

	ArrayBuffer,
	DataView,
	Uint8Array,
	Float32Array,
	Float64Array,
	Int8Array,
	Int16Array,
	Int32Array,
	Uint8ClampedArray,
	Uint16Array,
	Uint32Array,
*/

var undefined; // eslint-disable-line no-shadow-restricted-names

var ThrowTypeError = Object.getOwnPropertyDescriptor
	? (function () { return Object.getOwnPropertyDescriptor(arguments, 'callee').get; }())
	: function () { throw new TypeError(); };

var hasSymbols = typeof Symbol === 'function' && typeof Symbol.iterator === 'symbol';

var getProto = Object.getPrototypeOf || function (x) { return x.__proto__; }; // eslint-disable-line no-proto

var generator; // = function * () {};
var generatorFunction = generator ? getProto(generator) : undefined;
var asyncFn; // async function() {};
var asyncFunction = asyncFn ? asyncFn.constructor : undefined;
var asyncGen; // async function * () {};
var asyncGenFunction = asyncGen ? getProto(asyncGen) : undefined;
var asyncGenIterator = asyncGen ? asyncGen() : undefined;

var TypedArray = typeof Uint8Array === 'undefined' ? undefined : getProto(Uint8Array);

var INTRINSICS = {
	'$ %Array%': Array,
	'$ %ArrayBuffer%': typeof ArrayBuffer === 'undefined' ? undefined : ArrayBuffer,
	'$ %ArrayBufferPrototype%': typeof ArrayBuffer === 'undefined' ? undefined : ArrayBuffer.prototype,
	'$ %ArrayIteratorPrototype%': hasSymbols ? getProto([][Symbol.iterator]()) : undefined,
	'$ %ArrayPrototype%': Array.prototype,
	'$ %ArrayProto_entries%': Array.prototype.entries,
	'$ %ArrayProto_forEach%': Array.prototype.forEach,
	'$ %ArrayProto_keys%': Array.prototype.keys,
	'$ %ArrayProto_values%': Array.prototype.values,
	'$ %AsyncFromSyncIteratorPrototype%': undefined,
	'$ %AsyncFunction%': asyncFunction,
	'$ %AsyncFunctionPrototype%': asyncFunction ? asyncFunction.prototype : undefined,
	'$ %AsyncGenerator%': asyncGen ? getProto(asyncGenIterator) : undefined,
	'$ %AsyncGeneratorFunction%': asyncGenFunction,
	'$ %AsyncGeneratorPrototype%': asyncGenFunction ? asyncGenFunction.prototype : undefined,
	'$ %AsyncIteratorPrototype%': asyncGenIterator && hasSymbols && Symbol.asyncIterator ? asyncGenIterator[Symbol.asyncIterator]() : undefined,
	'$ %Atomics%': typeof Atomics === 'undefined' ? undefined : Atomics,
	'$ %Boolean%': Boolean,
	'$ %BooleanPrototype%': Boolean.prototype,
	'$ %DataView%': typeof DataView === 'undefined' ? undefined : DataView,
	'$ %DataViewPrototype%': typeof DataView === 'undefined' ? undefined : DataView.prototype,
	'$ %Date%': Date,
	'$ %DatePrototype%': Date.prototype,
	'$ %decodeURI%': decodeURI,
	'$ %decodeURIComponent%': decodeURIComponent,
	'$ %encodeURI%': encodeURI,
	'$ %encodeURIComponent%': encodeURIComponent,
	'$ %Error%': Error,
	'$ %ErrorPrototype%': Error.prototype,
	'$ %eval%': eval, // eslint-disable-line no-eval
	'$ %EvalError%': EvalError,
	'$ %EvalErrorPrototype%': EvalError.prototype,
	'$ %Float32Array%': typeof Float32Array === 'undefined' ? undefined : Float32Array,
	'$ %Float32ArrayPrototype%': typeof Float32Array === 'undefined' ? undefined : Float32Array.prototype,
	'$ %Float64Array%': typeof Float64Array === 'undefined' ? undefined : Float64Array,
	'$ %Float64ArrayPrototype%': typeof Float64Array === 'undefined' ? undefined : Float64Array.prototype,
	'$ %Function%': Function,
	'$ %FunctionPrototype%': Function.prototype,
	'$ %Generator%': generator ? getProto(generator()) : undefined,
	'$ %GeneratorFunction%': generatorFunction,
	'$ %GeneratorPrototype%': generatorFunction ? generatorFunction.prototype : undefined,
	'$ %Int8Array%': typeof Int8Array === 'undefined' ? undefined : Int8Array,
	'$ %Int8ArrayPrototype%': typeof Int8Array === 'undefined' ? undefined : Int8Array.prototype,
	'$ %Int16Array%': typeof Int16Array === 'undefined' ? undefined : Int16Array,
	'$ %Int16ArrayPrototype%': typeof Int16Array === 'undefined' ? undefined : Int8Array.prototype,
	'$ %Int32Array%': typeof Int32Array === 'undefined' ? undefined : Int32Array,
	'$ %Int32ArrayPrototype%': typeof Int32Array === 'undefined' ? undefined : Int32Array.prototype,
	'$ %isFinite%': isFinite,
	'$ %isNaN%': isNaN,
	'$ %IteratorPrototype%': hasSymbols ? getProto(getProto([][Symbol.iterator]())) : undefined,
	'$ %JSON%': JSON,
	'$ %JSONParse%': JSON.parse,
	'$ %Map%': typeof Map === 'undefined' ? undefined : Map,
	'$ %MapIteratorPrototype%': typeof Map === 'undefined' || !hasSymbols ? undefined : getProto(new Map()[Symbol.iterator]()),
	'$ %MapPrototype%': typeof Map === 'undefined' ? undefined : Map.prototype,
	'$ %Math%': Math,
	'$ %Number%': Number,
	'$ %NumberPrototype%': Number.prototype,
	'$ %Object%': Object,
	'$ %ObjectPrototype%': Object.prototype,
	'$ %ObjProto_toString%': Object.prototype.toString,
	'$ %ObjProto_valueOf%': Object.prototype.valueOf,
	'$ %parseFloat%': parseFloat,
	'$ %parseInt%': parseInt,
	'$ %Promise%': typeof Promise === 'undefined' ? undefined : Promise,
	'$ %PromisePrototype%': typeof Promise === 'undefined' ? undefined : Promise.prototype,
	'$ %PromiseProto_then%': typeof Promise === 'undefined' ? undefined : Promise.prototype.then,
	'$ %Promise_all%': typeof Promise === 'undefined' ? undefined : Promise.all,
	'$ %Promise_reject%': typeof Promise === 'undefined' ? undefined : Promise.reject,
	'$ %Promise_resolve%': typeof Promise === 'undefined' ? undefined : Promise.resolve,
	'$ %Proxy%': typeof Proxy === 'undefined' ? undefined : Proxy,
	'$ %RangeError%': RangeError,
	'$ %RangeErrorPrototype%': RangeError.prototype,
	'$ %ReferenceError%': ReferenceError,
	'$ %ReferenceErrorPrototype%': ReferenceError.prototype,
	'$ %Reflect%': typeof Reflect === 'undefined' ? undefined : Reflect,
	'$ %RegExp%': RegExp,
	'$ %RegExpPrototype%': RegExp.prototype,
	'$ %Set%': typeof Set === 'undefined' ? undefined : Set,
	'$ %SetIteratorPrototype%': typeof Set === 'undefined' || !hasSymbols ? undefined : getProto(new Set()[Symbol.iterator]()),
	'$ %SetPrototype%': typeof Set === 'undefined' ? undefined : Set.prototype,
	'$ %SharedArrayBuffer%': typeof SharedArrayBuffer === 'undefined' ? undefined : SharedArrayBuffer,
	'$ %SharedArrayBufferPrototype%': typeof SharedArrayBuffer === 'undefined' ? undefined : SharedArrayBuffer.prototype,
	'$ %String%': String,
	'$ %StringIteratorPrototype%': hasSymbols ? getProto(''[Symbol.iterator]()) : undefined,
	'$ %StringPrototype%': String.prototype,
	'$ %Symbol%': hasSymbols ? Symbol : undefined,
	'$ %SymbolPrototype%': hasSymbols ? Symbol.prototype : undefined,
	'$ %SyntaxError%': SyntaxError,
	'$ %SyntaxErrorPrototype%': SyntaxError.prototype,
	'$ %ThrowTypeError%': ThrowTypeError,
	'$ %TypedArray%': TypedArray,
	'$ %TypedArrayPrototype%': TypedArray ? TypedArray.prototype : undefined,
	'$ %TypeError%': TypeError,
	'$ %TypeErrorPrototype%': TypeError.prototype,
	'$ %Uint8Array%': typeof Uint8Array === 'undefined' ? undefined : Uint8Array,
	'$ %Uint8ArrayPrototype%': typeof Uint8Array === 'undefined' ? undefined : Uint8Array.prototype,
	'$ %Uint8ClampedArray%': typeof Uint8ClampedArray === 'undefined' ? undefined : Uint8ClampedArray,
	'$ %Uint8ClampedArrayPrototype%': typeof Uint8ClampedArray === 'undefined' ? undefined : Uint8ClampedArray.prototype,
	'$ %Uint16Array%': typeof Uint16Array === 'undefined' ? undefined : Uint16Array,
	'$ %Uint16ArrayPrototype%': typeof Uint16Array === 'undefined' ? undefined : Uint16Array.prototype,
	'$ %Uint32Array%': typeof Uint32Array === 'undefined' ? undefined : Uint32Array,
	'$ %Uint32ArrayPrototype%': typeof Uint32Array === 'undefined' ? undefined : Uint32Array.prototype,
	'$ %URIError%': URIError,
	'$ %URIErrorPrototype%': URIError.prototype,
	'$ %WeakMap%': typeof WeakMap === 'undefined' ? undefined : WeakMap,
	'$ %WeakMapPrototype%': typeof WeakMap === 'undefined' ? undefined : WeakMap.prototype,
	'$ %WeakSet%': typeof WeakSet === 'undefined' ? undefined : WeakSet,
	'$ %WeakSetPrototype%': typeof WeakSet === 'undefined' ? undefined : WeakSet.prototype
};

module.exports = function GetIntrinsic(name, allowMissing) {
	if (arguments.length > 1 && typeof allowMissing !== 'boolean') {
		throw new TypeError('"allowMissing" argument must be a boolean');
	}

	var key = '$ ' + name;
	if (!(key in INTRINSICS)) {
		throw new SyntaxError('intrinsic ' + name + ' does not exist!');
	}

	// istanbul ignore if // hopefully this is impossible to test :-)
	if (typeof INTRINSICS[key] === 'undefined' && !allowMissing) {
		throw new TypeError('intrinsic ' + name + ' exists, but is not available. Please file an issue!');
	}
	return INTRINSICS[key];
};


/***/ }),

/***/ "7OqA":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external "ReactDOM"
var external_ReactDOM_ = __webpack_require__("faye");
var external_ReactDOM_default = /*#__PURE__*/__webpack_require__.n(external_ReactDOM_);

// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__("cDcd");
var external_React_default = /*#__PURE__*/__webpack_require__.n(external_React_);

// EXTERNAL MODULE: ./node_modules/prop-types/index.js
var prop_types = __webpack_require__("17x9");
var prop_types_default = /*#__PURE__*/__webpack_require__.n(prop_types);

// CONCATENATED MODULE: ./node_modules/react-portal/es/utils.js
var canUseDOM = !!(typeof window !== 'undefined' && window.document && window.document.createElement);
// CONCATENATED MODULE: ./node_modules/react-portal/es/Portal.js
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }






var Portal_Portal = function (_React$Component) {
  _inherits(Portal, _React$Component);

  function Portal() {
    _classCallCheck(this, Portal);

    return _possibleConstructorReturn(this, (Portal.__proto__ || Object.getPrototypeOf(Portal)).apply(this, arguments));
  }

  _createClass(Portal, [{
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      if (this.defaultNode) {
        document.body.removeChild(this.defaultNode);
      }
      this.defaultNode = null;
    }
  }, {
    key: 'render',
    value: function render() {
      if (!canUseDOM) {
        return null;
      }
      if (!this.props.node && !this.defaultNode) {
        this.defaultNode = document.createElement('div');
        document.body.appendChild(this.defaultNode);
      }
      return external_ReactDOM_default.a.createPortal(this.props.children, this.props.node || this.defaultNode);
    }
  }]);

  return Portal;
}(external_React_default.a.Component);

Portal_Portal.propTypes = {
  children: prop_types_default.a.node.isRequired,
  node: prop_types_default.a.any
};

/* harmony default export */ var es_Portal = (Portal_Portal);
// CONCATENATED MODULE: ./node_modules/react-portal/es/LegacyPortal.js
var LegacyPortal_createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function LegacyPortal_classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function LegacyPortal_possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function LegacyPortal_inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

// This file is a fallback for a consumer who is not yet on React 16
// as createPortal was introduced in React 16





var LegacyPortal_Portal = function (_React$Component) {
  LegacyPortal_inherits(Portal, _React$Component);

  function Portal() {
    LegacyPortal_classCallCheck(this, Portal);

    return LegacyPortal_possibleConstructorReturn(this, (Portal.__proto__ || Object.getPrototypeOf(Portal)).apply(this, arguments));
  }

  LegacyPortal_createClass(Portal, [{
    key: 'componentDidMount',
    value: function componentDidMount() {
      this.renderPortal();
    }
  }, {
    key: 'componentDidUpdate',
    value: function componentDidUpdate(props) {
      this.renderPortal();
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      external_ReactDOM_default.a.unmountComponentAtNode(this.defaultNode || this.props.node);
      if (this.defaultNode) {
        document.body.removeChild(this.defaultNode);
      }
      this.defaultNode = null;
      this.portal = null;
    }
  }, {
    key: 'renderPortal',
    value: function renderPortal(props) {
      if (!this.props.node && !this.defaultNode) {
        this.defaultNode = document.createElement('div');
        document.body.appendChild(this.defaultNode);
      }

      var children = this.props.children;
      // https://gist.github.com/jimfb/d99e0678e9da715ccf6454961ef04d1b
      if (typeof this.props.children.type === 'function') {
        children = external_React_default.a.cloneElement(this.props.children);
      }

      this.portal = external_ReactDOM_default.a.unstable_renderSubtreeIntoContainer(this, children, this.props.node || this.defaultNode);
    }
  }, {
    key: 'render',
    value: function render() {
      return null;
    }
  }]);

  return Portal;
}(external_React_default.a.Component);

/* harmony default export */ var LegacyPortal = (LegacyPortal_Portal);


LegacyPortal_Portal.propTypes = {
  children: prop_types_default.a.node.isRequired,
  node: prop_types_default.a.any
};
// CONCATENATED MODULE: ./node_modules/react-portal/es/PortalCompat.js





var PortalCompat_Portal = void 0;

if (external_ReactDOM_default.a.createPortal) {
  PortalCompat_Portal = es_Portal;
} else {
  PortalCompat_Portal = LegacyPortal;
}

/* harmony default export */ var PortalCompat = (PortalCompat_Portal);
// CONCATENATED MODULE: ./node_modules/react-portal/es/PortalWithState.js
var PortalWithState_createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function PortalWithState_classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function PortalWithState_possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function PortalWithState_inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }





var KEYCODES = {
  ESCAPE: 27
};

var PortalWithState_PortalWithState = function (_React$Component) {
  PortalWithState_inherits(PortalWithState, _React$Component);

  function PortalWithState(props) {
    PortalWithState_classCallCheck(this, PortalWithState);

    var _this = PortalWithState_possibleConstructorReturn(this, (PortalWithState.__proto__ || Object.getPrototypeOf(PortalWithState)).call(this, props));

    _this.portalNode = null;
    _this.state = { active: !!props.defaultOpen };
    _this.openPortal = _this.openPortal.bind(_this);
    _this.closePortal = _this.closePortal.bind(_this);
    _this.wrapWithPortal = _this.wrapWithPortal.bind(_this);
    _this.handleOutsideMouseClick = _this.handleOutsideMouseClick.bind(_this);
    _this.handleKeydown = _this.handleKeydown.bind(_this);
    return _this;
  }

  PortalWithState_createClass(PortalWithState, [{
    key: 'componentDidMount',
    value: function componentDidMount() {
      if (this.props.closeOnEsc) {
        document.addEventListener('keydown', this.handleKeydown);
      }
      if (this.props.closeOnOutsideClick) {
        document.addEventListener('click', this.handleOutsideMouseClick);
      }
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      if (this.props.closeOnEsc) {
        document.removeEventListener('keydown', this.handleKeydown);
      }
      if (this.props.closeOnOutsideClick) {
        document.removeEventListener('click', this.handleOutsideMouseClick);
      }
    }
  }, {
    key: 'openPortal',
    value: function openPortal(e) {
      if (this.state.active) {
        return;
      }
      if (e && e.nativeEvent) {
        e.nativeEvent.stopImmediatePropagation();
      }
      this.setState({ active: true }, this.props.onOpen);
    }
  }, {
    key: 'closePortal',
    value: function closePortal() {
      if (!this.state.active) {
        return;
      }
      this.setState({ active: false }, this.props.onClose);
    }
  }, {
    key: 'wrapWithPortal',
    value: function wrapWithPortal(children) {
      var _this2 = this;

      if (!this.state.active) {
        return null;
      }
      return external_React_default.a.createElement(
        PortalCompat,
        {
          node: this.props.node,
          key: 'react-portal',
          ref: function ref(portalNode) {
            return _this2.portalNode = portalNode;
          }
        },
        children
      );
    }
  }, {
    key: 'handleOutsideMouseClick',
    value: function handleOutsideMouseClick(e) {
      if (!this.state.active) {
        return;
      }
      var root = this.portalNode.props.node || this.portalNode.defaultNode;
      if (!root || root.contains(e.target) || e.button && e.button !== 0) {
        return;
      }
      this.closePortal();
    }
  }, {
    key: 'handleKeydown',
    value: function handleKeydown(e) {
      if (e.keyCode === KEYCODES.ESCAPE && this.state.active) {
        this.closePortal();
      }
    }
  }, {
    key: 'render',
    value: function render() {
      return this.props.children({
        openPortal: this.openPortal,
        closePortal: this.closePortal,
        portal: this.wrapWithPortal,
        isOpen: this.state.active
      });
    }
  }]);

  return PortalWithState;
}(external_React_default.a.Component);

PortalWithState_PortalWithState.propTypes = {
  children: prop_types_default.a.func.isRequired,
  defaultOpen: prop_types_default.a.bool,
  node: prop_types_default.a.any,
  closeOnEsc: prop_types_default.a.bool,
  closeOnOutsideClick: prop_types_default.a.bool,
  onOpen: prop_types_default.a.func,
  onClose: prop_types_default.a.func
};

PortalWithState_PortalWithState.defaultProps = {
  onOpen: function onOpen() {},
  onClose: function onClose() {}
};

/* harmony default export */ var es_PortalWithState = (PortalWithState_PortalWithState);
// CONCATENATED MODULE: ./node_modules/react-portal/es/index.js
/* concated harmony reexport Portal */__webpack_require__.d(__webpack_exports__, "Portal", function() { return PortalCompat; });
/* concated harmony reexport PortalWithState */__webpack_require__.d(__webpack_exports__, "PortalWithState", function() { return es_PortalWithState; });





/***/ }),

/***/ "82c2":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var keys = __webpack_require__("1seS");
var hasSymbols = typeof Symbol === 'function' && typeof Symbol('foo') === 'symbol';

var toStr = Object.prototype.toString;
var concat = Array.prototype.concat;
var origDefineProperty = Object.defineProperty;

var isFunction = function (fn) {
	return typeof fn === 'function' && toStr.call(fn) === '[object Function]';
};

var arePropertyDescriptorsSupported = function () {
	var obj = {};
	try {
		origDefineProperty(obj, 'x', { enumerable: false, value: obj });
		// eslint-disable-next-line no-unused-vars, no-restricted-syntax
		for (var _ in obj) { // jscs:ignore disallowUnusedVariables
			return false;
		}
		return obj.x === obj;
	} catch (e) { /* this is IE 8. */
		return false;
	}
};
var supportsDescriptors = origDefineProperty && arePropertyDescriptorsSupported();

var defineProperty = function (object, name, value, predicate) {
	if (name in object && (!isFunction(predicate) || !predicate())) {
		return;
	}
	if (supportsDescriptors) {
		origDefineProperty(object, name, {
			configurable: true,
			enumerable: false,
			value: value,
			writable: true
		});
	} else {
		object[name] = value;
	}
};

var defineProperties = function (object, map) {
	var predicates = arguments.length > 2 ? arguments[2] : {};
	var props = keys(map);
	if (hasSymbols) {
		props = concat.call(props, Object.getOwnPropertySymbols(map));
	}
	for (var i = 0; i < props.length; i += 1) {
		defineProperty(object, props[i], map[props[i]], predicates[props[i]]);
	}
};

defineProperties.supportsDescriptors = !!supportsDescriptors;

module.exports = defineProperties;


/***/ }),

/***/ "8OQS":
/***/ (function(module, exports) {

function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}

module.exports = _objectWithoutPropertiesLoose;

/***/ }),

/***/ "8oxB":
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),

/***/ "9VuS":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = exports.PureDateRangePicker = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _reactWithStyles = __webpack_require__("TG4+");

var _reactPortal = __webpack_require__("7OqA");

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _consolidatedEvents = __webpack_require__("1TsT");

var _isTouchDevice = _interopRequireDefault(__webpack_require__("LTAC"));

var _reactOutsideClickHandler = _interopRequireDefault(__webpack_require__("3gBW"));

var _DateRangePickerShape = _interopRequireDefault(__webpack_require__("yLoW"));

var _defaultPhrases = __webpack_require__("vV+G");

var _getResponsiveContainerStyles = _interopRequireDefault(__webpack_require__("w0iJ"));

var _getDetachedContainerStyles = _interopRequireDefault(__webpack_require__("yR3C"));

var _getInputHeight = _interopRequireDefault(__webpack_require__("hgME"));

var _isInclusivelyAfterDay = _interopRequireDefault(__webpack_require__("rit9"));

var _disableScroll2 = _interopRequireDefault(__webpack_require__("zec9"));

var _noflip = _interopRequireDefault(__webpack_require__("2WTt"));

var _DateRangePickerInputController = _interopRequireDefault(__webpack_require__("nmTR"));

var _DayPickerRangeController = _interopRequireDefault(__webpack_require__("CDAp"));

var _CloseButton = _interopRequireDefault(__webpack_require__("xEte"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  // required props for a functional interactive DateRangePicker
  startDate: null,
  endDate: null,
  focusedInput: null,
  // input related props
  startDatePlaceholderText: 'Start Date',
  endDatePlaceholderText: 'End Date',
  startDateAriaLabel: undefined,
  endDateAriaLabel: undefined,
  startDateOffset: undefined,
  endDateOffset: undefined,
  disabled: false,
  required: false,
  readOnly: false,
  screenReaderInputMessage: '',
  showClearDates: false,
  showDefaultInputIcon: false,
  inputIconPosition: _constants.ICON_BEFORE_POSITION,
  customInputIcon: null,
  customArrowIcon: null,
  customCloseIcon: null,
  noBorder: false,
  block: false,
  small: false,
  regular: false,
  keepFocusOnInput: false,
  // calendar presentation and interaction related props
  renderMonthText: null,
  orientation: _constants.HORIZONTAL_ORIENTATION,
  anchorDirection: _constants.ANCHOR_LEFT,
  openDirection: _constants.OPEN_DOWN,
  horizontalMargin: 0,
  withPortal: false,
  withFullScreenPortal: false,
  appendToBody: false,
  disableScroll: false,
  initialVisibleMonth: null,
  numberOfMonths: 2,
  keepOpenOnDateSelect: false,
  reopenPickerOnClearDates: false,
  renderCalendarInfo: null,
  calendarInfoPosition: _constants.INFO_POSITION_BOTTOM,
  hideKeyboardShortcutsPanel: false,
  daySize: _constants.DAY_SIZE,
  isRTL: false,
  firstDayOfWeek: null,
  verticalHeight: null,
  transitionDuration: undefined,
  verticalSpacing: _constants.DEFAULT_VERTICAL_SPACING,
  horizontalMonthPadding: undefined,
  // navigation related props
  navPrev: null,
  navNext: null,
  onPrevMonthClick: function onPrevMonthClick() {},
  onNextMonthClick: function onNextMonthClick() {},
  onClose: function onClose() {},
  // day presentation and interaction related props
  renderCalendarDay: undefined,
  renderDayContents: null,
  renderMonthElement: null,
  minimumNights: 1,
  enableOutsideDays: false,
  isDayBlocked: function isDayBlocked() {
    return false;
  },
  isOutsideRange: function isOutsideRange(day) {
    return !(0, _isInclusivelyAfterDay["default"])(day, (0, _moment["default"])());
  },
  isDayHighlighted: function isDayHighlighted() {
    return false;
  },
  // internationalization
  displayFormat: function displayFormat() {
    return _moment["default"].localeData().longDateFormat('L');
  },
  monthFormat: 'MMMM YYYY',
  weekDayFormat: 'dd',
  phrases: _defaultPhrases.DateRangePickerPhrases,
  dayAriaLabelFormat: undefined
};

var DateRangePicker =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(DateRangePicker, _ref);
  var _proto = DateRangePicker.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function DateRangePicker(props) {
    var _this;

    _this = _ref.call(this, props) || this;
    _this.state = {
      dayPickerContainerStyles: {},
      isDateRangePickerInputFocused: false,
      isDayPickerFocused: false,
      showKeyboardShortcuts: false
    };
    _this.isTouchDevice = false;
    _this.onOutsideClick = _this.onOutsideClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onDateRangePickerInputFocus = _this.onDateRangePickerInputFocus.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onDayPickerFocus = _this.onDayPickerFocus.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onDayPickerFocusOut = _this.onDayPickerFocusOut.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onDayPickerBlur = _this.onDayPickerBlur.bind((0, _assertThisInitialized2["default"])(_this));
    _this.showKeyboardShortcutsPanel = _this.showKeyboardShortcutsPanel.bind((0, _assertThisInitialized2["default"])(_this));
    _this.responsivizePickerPosition = _this.responsivizePickerPosition.bind((0, _assertThisInitialized2["default"])(_this));
    _this.disableScroll = _this.disableScroll.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setDayPickerContainerRef = _this.setDayPickerContainerRef.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setContainerRef = _this.setContainerRef.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.componentDidMount = function componentDidMount() {
    this.removeEventListener = (0, _consolidatedEvents.addEventListener)(window, 'resize', this.responsivizePickerPosition, {
      passive: true
    });
    this.responsivizePickerPosition();
    this.disableScroll();
    var focusedInput = this.props.focusedInput;

    if (focusedInput) {
      this.setState({
        isDateRangePickerInputFocused: true
      });
    }

    this.isTouchDevice = (0, _isTouchDevice["default"])();
  };

  _proto.componentDidUpdate = function componentDidUpdate(prevProps) {
    var focusedInput = this.props.focusedInput;

    if (!prevProps.focusedInput && focusedInput && this.isOpened()) {
      // The date picker just changed from being closed to being open.
      this.responsivizePickerPosition();
      this.disableScroll();
    } else if (prevProps.focusedInput && !focusedInput && !this.isOpened()) {
      // The date picker just changed from being open to being closed.
      if (this.enableScroll) this.enableScroll();
    }
  };

  _proto.componentWillUnmount = function componentWillUnmount() {
    this.removeDayPickerEventListeners();
    if (this.removeEventListener) this.removeEventListener();
    if (this.enableScroll) this.enableScroll();
  };

  _proto.onOutsideClick = function onOutsideClick(event) {
    var _this$props = this.props,
        onFocusChange = _this$props.onFocusChange,
        onClose = _this$props.onClose,
        startDate = _this$props.startDate,
        endDate = _this$props.endDate,
        appendToBody = _this$props.appendToBody;
    if (!this.isOpened()) return;
    if (appendToBody && this.dayPickerContainer.contains(event.target)) return;
    this.setState({
      isDateRangePickerInputFocused: false,
      isDayPickerFocused: false,
      showKeyboardShortcuts: false
    });
    onFocusChange(null);
    onClose({
      startDate: startDate,
      endDate: endDate
    });
  };

  _proto.onDateRangePickerInputFocus = function onDateRangePickerInputFocus(focusedInput) {
    var _this$props2 = this.props,
        onFocusChange = _this$props2.onFocusChange,
        readOnly = _this$props2.readOnly,
        withPortal = _this$props2.withPortal,
        withFullScreenPortal = _this$props2.withFullScreenPortal,
        keepFocusOnInput = _this$props2.keepFocusOnInput;

    if (focusedInput) {
      var withAnyPortal = withPortal || withFullScreenPortal;
      var moveFocusToDayPicker = withAnyPortal || readOnly && !keepFocusOnInput || this.isTouchDevice && !keepFocusOnInput;

      if (moveFocusToDayPicker) {
        this.onDayPickerFocus();
      } else {
        this.onDayPickerBlur();
      }
    }

    onFocusChange(focusedInput);
  };

  _proto.onDayPickerFocus = function onDayPickerFocus() {
    var _this$props3 = this.props,
        focusedInput = _this$props3.focusedInput,
        onFocusChange = _this$props3.onFocusChange;
    if (!focusedInput) onFocusChange(_constants.START_DATE);
    this.setState({
      isDateRangePickerInputFocused: false,
      isDayPickerFocused: true,
      showKeyboardShortcuts: false
    });
  };

  _proto.onDayPickerFocusOut = function onDayPickerFocusOut(event) {
    // In cases where **relatedTarget** is not null, it points to the right
    // element here. However, in cases where it is null (such as clicking on a
    // specific day) or it is **document.body** (IE11), the appropriate value is **event.target**.
    //
    // We handle both situations here by using the ` || ` operator to fallback
    // to *event.target** when **relatedTarget** is not provided.
    var relatedTarget = event.relatedTarget === document.body ? event.target : event.relatedTarget || event.target;
    if (this.dayPickerContainer.contains(relatedTarget)) return;
    this.onOutsideClick(event);
  };

  _proto.onDayPickerBlur = function onDayPickerBlur() {
    this.setState({
      isDateRangePickerInputFocused: true,
      isDayPickerFocused: false,
      showKeyboardShortcuts: false
    });
  };

  _proto.setDayPickerContainerRef = function setDayPickerContainerRef(ref) {
    if (ref === this.dayPickerContainer) return;
    if (this.dayPickerContainer) this.removeDayPickerEventListeners();
    this.dayPickerContainer = ref;
    if (!ref) return;
    this.addDayPickerEventListeners();
  };

  _proto.setContainerRef = function setContainerRef(ref) {
    this.container = ref;
  };

  _proto.addDayPickerEventListeners = function addDayPickerEventListeners() {
    // NOTE: We are using a manual event listener here, because React doesn't
    // provide FocusOut, while blur and keydown don't provide the information
    // needed in order to know whether we have left focus or not.
    //
    // For reference, this issue is further described here:
    // - https://github.com/facebook/react/issues/6410
    this.removeDayPickerFocusOut = (0, _consolidatedEvents.addEventListener)(this.dayPickerContainer, 'focusout', this.onDayPickerFocusOut);
  };

  _proto.removeDayPickerEventListeners = function removeDayPickerEventListeners() {
    if (this.removeDayPickerFocusOut) this.removeDayPickerFocusOut();
  };

  _proto.isOpened = function isOpened() {
    var focusedInput = this.props.focusedInput;
    return focusedInput === _constants.START_DATE || focusedInput === _constants.END_DATE;
  };

  _proto.disableScroll = function disableScroll() {
    var _this$props4 = this.props,
        appendToBody = _this$props4.appendToBody,
        propDisableScroll = _this$props4.disableScroll;
    if (!appendToBody && !propDisableScroll) return;
    if (!this.isOpened()) return; // Disable scroll for every ancestor of this DateRangePicker up to the
    // document level. This ensures the input and the picker never move. Other
    // sibling elements or the picker itself can scroll.

    this.enableScroll = (0, _disableScroll2["default"])(this.container);
  };

  _proto.responsivizePickerPosition = function responsivizePickerPosition() {
    // It's possible the portal props have been changed in response to window resizes
    // So let's ensure we reset this back to the base state each time
    var dayPickerContainerStyles = this.state.dayPickerContainerStyles;

    if (Object.keys(dayPickerContainerStyles).length > 0) {
      this.setState({
        dayPickerContainerStyles: {}
      });
    }

    if (!this.isOpened()) {
      return;
    }

    var _this$props5 = this.props,
        openDirection = _this$props5.openDirection,
        anchorDirection = _this$props5.anchorDirection,
        horizontalMargin = _this$props5.horizontalMargin,
        withPortal = _this$props5.withPortal,
        withFullScreenPortal = _this$props5.withFullScreenPortal,
        appendToBody = _this$props5.appendToBody;
    var isAnchoredLeft = anchorDirection === _constants.ANCHOR_LEFT;

    if (!withPortal && !withFullScreenPortal) {
      var containerRect = this.dayPickerContainer.getBoundingClientRect();
      var currentOffset = dayPickerContainerStyles[anchorDirection] || 0;
      var containerEdge = isAnchoredLeft ? containerRect[_constants.ANCHOR_RIGHT] : containerRect[_constants.ANCHOR_LEFT];
      this.setState({
        dayPickerContainerStyles: _objectSpread({}, (0, _getResponsiveContainerStyles["default"])(anchorDirection, currentOffset, containerEdge, horizontalMargin), {}, appendToBody && (0, _getDetachedContainerStyles["default"])(openDirection, anchorDirection, this.container))
      });
    }
  };

  _proto.showKeyboardShortcutsPanel = function showKeyboardShortcutsPanel() {
    this.setState({
      isDateRangePickerInputFocused: false,
      isDayPickerFocused: true,
      showKeyboardShortcuts: true
    });
  };

  _proto.maybeRenderDayPickerWithPortal = function maybeRenderDayPickerWithPortal() {
    var _this$props6 = this.props,
        withPortal = _this$props6.withPortal,
        withFullScreenPortal = _this$props6.withFullScreenPortal,
        appendToBody = _this$props6.appendToBody;

    if (!this.isOpened()) {
      return null;
    }

    if (withPortal || withFullScreenPortal || appendToBody) {
      return _react["default"].createElement(_reactPortal.Portal, null, this.renderDayPicker());
    }

    return this.renderDayPicker();
  };

  _proto.renderDayPicker = function renderDayPicker() {
    var _this$props7 = this.props,
        anchorDirection = _this$props7.anchorDirection,
        css = _this$props7.css,
        openDirection = _this$props7.openDirection,
        isDayBlocked = _this$props7.isDayBlocked,
        isDayHighlighted = _this$props7.isDayHighlighted,
        isOutsideRange = _this$props7.isOutsideRange,
        numberOfMonths = _this$props7.numberOfMonths,
        orientation = _this$props7.orientation,
        monthFormat = _this$props7.monthFormat,
        renderMonthText = _this$props7.renderMonthText,
        navPrev = _this$props7.navPrev,
        navNext = _this$props7.navNext,
        onPrevMonthClick = _this$props7.onPrevMonthClick,
        onNextMonthClick = _this$props7.onNextMonthClick,
        onDatesChange = _this$props7.onDatesChange,
        onFocusChange = _this$props7.onFocusChange,
        withPortal = _this$props7.withPortal,
        withFullScreenPortal = _this$props7.withFullScreenPortal,
        daySize = _this$props7.daySize,
        enableOutsideDays = _this$props7.enableOutsideDays,
        focusedInput = _this$props7.focusedInput,
        startDate = _this$props7.startDate,
        startDateOffset = _this$props7.startDateOffset,
        endDate = _this$props7.endDate,
        endDateOffset = _this$props7.endDateOffset,
        minimumNights = _this$props7.minimumNights,
        keepOpenOnDateSelect = _this$props7.keepOpenOnDateSelect,
        renderCalendarDay = _this$props7.renderCalendarDay,
        renderDayContents = _this$props7.renderDayContents,
        renderCalendarInfo = _this$props7.renderCalendarInfo,
        renderMonthElement = _this$props7.renderMonthElement,
        calendarInfoPosition = _this$props7.calendarInfoPosition,
        firstDayOfWeek = _this$props7.firstDayOfWeek,
        initialVisibleMonth = _this$props7.initialVisibleMonth,
        hideKeyboardShortcutsPanel = _this$props7.hideKeyboardShortcutsPanel,
        customCloseIcon = _this$props7.customCloseIcon,
        onClose = _this$props7.onClose,
        phrases = _this$props7.phrases,
        dayAriaLabelFormat = _this$props7.dayAriaLabelFormat,
        isRTL = _this$props7.isRTL,
        weekDayFormat = _this$props7.weekDayFormat,
        styles = _this$props7.styles,
        verticalHeight = _this$props7.verticalHeight,
        transitionDuration = _this$props7.transitionDuration,
        verticalSpacing = _this$props7.verticalSpacing,
        horizontalMonthPadding = _this$props7.horizontalMonthPadding,
        small = _this$props7.small,
        disabled = _this$props7.disabled,
        reactDates = _this$props7.theme.reactDates;
    var _this$state = this.state,
        dayPickerContainerStyles = _this$state.dayPickerContainerStyles,
        isDayPickerFocused = _this$state.isDayPickerFocused,
        showKeyboardShortcuts = _this$state.showKeyboardShortcuts;
    var onOutsideClick = !withFullScreenPortal && withPortal ? this.onOutsideClick : undefined;

    var initialVisibleMonthThunk = initialVisibleMonth || function () {
      return startDate || endDate || (0, _moment["default"])();
    };

    var closeIcon = customCloseIcon || _react["default"].createElement(_CloseButton["default"], css(styles.DateRangePicker_closeButton_svg));

    var inputHeight = (0, _getInputHeight["default"])(reactDates, small);
    var withAnyPortal = withPortal || withFullScreenPortal;
    return _react["default"].createElement("div", (0, _extends2["default"])({
      // eslint-disable-line jsx-a11y/no-static-element-interactions
      ref: this.setDayPickerContainerRef
    }, css(styles.DateRangePicker_picker, anchorDirection === _constants.ANCHOR_LEFT && styles.DateRangePicker_picker__directionLeft, anchorDirection === _constants.ANCHOR_RIGHT && styles.DateRangePicker_picker__directionRight, orientation === _constants.HORIZONTAL_ORIENTATION && styles.DateRangePicker_picker__horizontal, orientation === _constants.VERTICAL_ORIENTATION && styles.DateRangePicker_picker__vertical, !withAnyPortal && openDirection === _constants.OPEN_DOWN && {
      top: inputHeight + verticalSpacing
    }, !withAnyPortal && openDirection === _constants.OPEN_UP && {
      bottom: inputHeight + verticalSpacing
    }, withAnyPortal && styles.DateRangePicker_picker__portal, withFullScreenPortal && styles.DateRangePicker_picker__fullScreenPortal, isRTL && styles.DateRangePicker_picker__rtl, dayPickerContainerStyles), {
      onClick: onOutsideClick
    }), _react["default"].createElement(_DayPickerRangeController["default"], {
      orientation: orientation,
      enableOutsideDays: enableOutsideDays,
      numberOfMonths: numberOfMonths,
      onPrevMonthClick: onPrevMonthClick,
      onNextMonthClick: onNextMonthClick,
      onDatesChange: onDatesChange,
      onFocusChange: onFocusChange,
      onClose: onClose,
      focusedInput: focusedInput,
      startDate: startDate,
      startDateOffset: startDateOffset,
      endDate: endDate,
      endDateOffset: endDateOffset,
      monthFormat: monthFormat,
      renderMonthText: renderMonthText,
      withPortal: withAnyPortal,
      daySize: daySize,
      initialVisibleMonth: initialVisibleMonthThunk,
      hideKeyboardShortcutsPanel: hideKeyboardShortcutsPanel,
      navPrev: navPrev,
      navNext: navNext,
      minimumNights: minimumNights,
      isOutsideRange: isOutsideRange,
      isDayHighlighted: isDayHighlighted,
      isDayBlocked: isDayBlocked,
      keepOpenOnDateSelect: keepOpenOnDateSelect,
      renderCalendarDay: renderCalendarDay,
      renderDayContents: renderDayContents,
      renderCalendarInfo: renderCalendarInfo,
      renderMonthElement: renderMonthElement,
      calendarInfoPosition: calendarInfoPosition,
      isFocused: isDayPickerFocused,
      showKeyboardShortcuts: showKeyboardShortcuts,
      onBlur: this.onDayPickerBlur,
      phrases: phrases,
      dayAriaLabelFormat: dayAriaLabelFormat,
      isRTL: isRTL,
      firstDayOfWeek: firstDayOfWeek,
      weekDayFormat: weekDayFormat,
      verticalHeight: verticalHeight,
      transitionDuration: transitionDuration,
      disabled: disabled,
      horizontalMonthPadding: horizontalMonthPadding
    }), withFullScreenPortal && _react["default"].createElement("button", (0, _extends2["default"])({}, css(styles.DateRangePicker_closeButton), {
      type: "button",
      onClick: this.onOutsideClick,
      "aria-label": phrases.closeDatePicker
    }), closeIcon));
  };

  _proto.render = function render() {
    var _this$props8 = this.props,
        css = _this$props8.css,
        startDate = _this$props8.startDate,
        startDateId = _this$props8.startDateId,
        startDatePlaceholderText = _this$props8.startDatePlaceholderText,
        startDateAriaLabel = _this$props8.startDateAriaLabel,
        endDate = _this$props8.endDate,
        endDateId = _this$props8.endDateId,
        endDatePlaceholderText = _this$props8.endDatePlaceholderText,
        endDateAriaLabel = _this$props8.endDateAriaLabel,
        focusedInput = _this$props8.focusedInput,
        screenReaderInputMessage = _this$props8.screenReaderInputMessage,
        showClearDates = _this$props8.showClearDates,
        showDefaultInputIcon = _this$props8.showDefaultInputIcon,
        inputIconPosition = _this$props8.inputIconPosition,
        customInputIcon = _this$props8.customInputIcon,
        customArrowIcon = _this$props8.customArrowIcon,
        customCloseIcon = _this$props8.customCloseIcon,
        disabled = _this$props8.disabled,
        required = _this$props8.required,
        readOnly = _this$props8.readOnly,
        openDirection = _this$props8.openDirection,
        phrases = _this$props8.phrases,
        isOutsideRange = _this$props8.isOutsideRange,
        minimumNights = _this$props8.minimumNights,
        withPortal = _this$props8.withPortal,
        withFullScreenPortal = _this$props8.withFullScreenPortal,
        displayFormat = _this$props8.displayFormat,
        reopenPickerOnClearDates = _this$props8.reopenPickerOnClearDates,
        keepOpenOnDateSelect = _this$props8.keepOpenOnDateSelect,
        onDatesChange = _this$props8.onDatesChange,
        onClose = _this$props8.onClose,
        isRTL = _this$props8.isRTL,
        noBorder = _this$props8.noBorder,
        block = _this$props8.block,
        verticalSpacing = _this$props8.verticalSpacing,
        small = _this$props8.small,
        regular = _this$props8.regular,
        styles = _this$props8.styles;
    var isDateRangePickerInputFocused = this.state.isDateRangePickerInputFocused;
    var enableOutsideClick = !withPortal && !withFullScreenPortal;
    var hideFang = verticalSpacing < _constants.FANG_HEIGHT_PX;

    var input = _react["default"].createElement(_DateRangePickerInputController["default"], {
      startDate: startDate,
      startDateId: startDateId,
      startDatePlaceholderText: startDatePlaceholderText,
      isStartDateFocused: focusedInput === _constants.START_DATE,
      startDateAriaLabel: startDateAriaLabel,
      endDate: endDate,
      endDateId: endDateId,
      endDatePlaceholderText: endDatePlaceholderText,
      isEndDateFocused: focusedInput === _constants.END_DATE,
      endDateAriaLabel: endDateAriaLabel,
      displayFormat: displayFormat,
      showClearDates: showClearDates,
      showCaret: !withPortal && !withFullScreenPortal && !hideFang,
      showDefaultInputIcon: showDefaultInputIcon,
      inputIconPosition: inputIconPosition,
      customInputIcon: customInputIcon,
      customArrowIcon: customArrowIcon,
      customCloseIcon: customCloseIcon,
      disabled: disabled,
      required: required,
      readOnly: readOnly,
      openDirection: openDirection,
      reopenPickerOnClearDates: reopenPickerOnClearDates,
      keepOpenOnDateSelect: keepOpenOnDateSelect,
      isOutsideRange: isOutsideRange,
      minimumNights: minimumNights,
      withFullScreenPortal: withFullScreenPortal,
      onDatesChange: onDatesChange,
      onFocusChange: this.onDateRangePickerInputFocus,
      onKeyDownArrowDown: this.onDayPickerFocus,
      onKeyDownQuestionMark: this.showKeyboardShortcutsPanel,
      onClose: onClose,
      phrases: phrases,
      screenReaderMessage: screenReaderInputMessage,
      isFocused: isDateRangePickerInputFocused,
      isRTL: isRTL,
      noBorder: noBorder,
      block: block,
      small: small,
      regular: regular,
      verticalSpacing: verticalSpacing
    }, this.maybeRenderDayPickerWithPortal());

    return _react["default"].createElement("div", (0, _extends2["default"])({
      ref: this.setContainerRef
    }, css(styles.DateRangePicker, block && styles.DateRangePicker__block)), enableOutsideClick && _react["default"].createElement(_reactOutsideClickHandler["default"], {
      onOutsideClick: this.onOutsideClick
    }, input), enableOutsideClick || input);
  };

  return DateRangePicker;
}(_react["default"].PureComponent || _react["default"].Component);

exports.PureDateRangePicker = DateRangePicker;
DateRangePicker.propTypes =  false ? undefined : {};
DateRangePicker.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref2) {
  var _ref2$reactDates = _ref2.reactDates,
      color = _ref2$reactDates.color,
      zIndex = _ref2$reactDates.zIndex;
  return {
    DateRangePicker: {
      position: 'relative',
      display: 'inline-block'
    },
    DateRangePicker__block: {
      display: 'block'
    },
    DateRangePicker_picker: {
      zIndex: zIndex + 1,
      backgroundColor: color.background,
      position: 'absolute'
    },
    DateRangePicker_picker__rtl: {
      direction: (0, _noflip["default"])('rtl')
    },
    DateRangePicker_picker__directionLeft: {
      left: (0, _noflip["default"])(0)
    },
    DateRangePicker_picker__directionRight: {
      right: (0, _noflip["default"])(0)
    },
    DateRangePicker_picker__portal: {
      backgroundColor: 'rgba(0, 0, 0, 0.3)',
      position: 'fixed',
      top: 0,
      left: (0, _noflip["default"])(0),
      height: '100%',
      width: '100%'
    },
    DateRangePicker_picker__fullScreenPortal: {
      backgroundColor: color.background
    },
    DateRangePicker_closeButton: {
      background: 'none',
      border: 0,
      color: 'inherit',
      font: 'inherit',
      lineHeight: 'normal',
      overflow: 'visible',
      cursor: 'pointer',
      position: 'absolute',
      top: 0,
      right: (0, _noflip["default"])(0),
      padding: 15,
      zIndex: zIndex + 2,
      ':hover': {
        color: "darken(".concat(color.core.grayLighter, ", 10%)"),
        textDecoration: 'none'
      },
      ':focus': {
        color: "darken(".concat(color.core.grayLighter, ", 10%)"),
        textDecoration: 'none'
      }
    },
    DateRangePicker_closeButton_svg: {
      height: 15,
      width: 15,
      fill: color.core.grayLighter
    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(DateRangePicker);

exports["default"] = _default;

/***/ }),

/***/ "9gmn":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var ChevronUp = function ChevronUp(props) {
  return _react["default"].createElement("svg", props, _react["default"].createElement("path", {
    d: "M32 713l453-453c11-11 21-11 32 0l453 453c5 5 7 10 7 16 0 13-10 23-22 23-7 0-12-2-16-7L501 309 64 745c-4 5-9 7-15 7-7 0-12-2-17-7-9-11-9-21 0-32z"
  }));
};

ChevronUp.defaultProps = {
  focusable: "false",
  viewBox: "0 0 1000 1000"
};
var _default = ChevronUp;
exports["default"] = _default;

/***/ }),

/***/ "9pTB":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

var define = __webpack_require__("82c2");
var isSymbol = __webpack_require__("/sVA");

var globalKey = '__ global cache key __';
/* istanbul ignore else */
// eslint-disable-next-line no-restricted-properties
if (typeof Symbol === 'function' && isSymbol(Symbol('foo')) && typeof Symbol['for'] === 'function') {
	// eslint-disable-next-line no-restricted-properties
	globalKey = Symbol['for'](globalKey);
}

var trueThunk = function () {
	return true;
};

var ensureCache = function ensureCache() {
	if (!global[globalKey]) {
		var properties = {};
		properties[globalKey] = {};
		var predicates = {};
		predicates[globalKey] = trueThunk;
		define(global, properties, predicates);
	}
	return global[globalKey];
};

var cache = ensureCache();

var isPrimitive = function isPrimitive(val) {
	return val === null || (typeof val !== 'object' && typeof val !== 'function');
};

var getPrimitiveKey = function getPrimitiveKey(val) {
	if (isSymbol(val)) {
		return Symbol.prototype.valueOf.call(val);
	}
	return typeof val + ' | ' + String(val);
};

var requirePrimitiveKey = function requirePrimitiveKey(val) {
	if (!isPrimitive(val)) {
		throw new TypeError('key must not be an object');
	}
};

var globalCache = {
	clear: function clear() {
		delete global[globalKey];
		cache = ensureCache();
	},

	'delete': function deleteKey(key) {
		requirePrimitiveKey(key);
		delete cache[getPrimitiveKey(key)];
		return !globalCache.has(key);
	},

	get: function get(key) {
		requirePrimitiveKey(key);
		return cache[getPrimitiveKey(key)];
	},

	has: function has(key) {
		requirePrimitiveKey(key);
		return getPrimitiveKey(key) in cache;
	},

	set: function set(key, value) {
		requirePrimitiveKey(key);
		var primitiveKey = getPrimitiveKey(key);
		var props = {};
		props[primitiveKey] = value;
		var predicates = {};
		predicates[primitiveKey] = trueThunk;
		define(cache, props, predicates);
		return globalCache.has(key);
	},

	setIfMissingThenGet: function setIfMissingThenGet(key, valueThunk) {
		if (globalCache.has(key)) {
			return globalCache.get(key);
		}
		var item = valueThunk();
		globalCache.set(key, item);
		return item;
	}
};

module.exports = globalCache;

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("yLpj")))

/***/ }),

/***/ "AP2z":
/***/ (function(module, exports, __webpack_require__) {

var Symbol = __webpack_require__("nmnc");

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */
var nativeObjectToString = objectProto.toString;

/** Built-in value references. */
var symToStringTag = Symbol ? Symbol.toStringTag : undefined;

/**
 * A specialized version of `baseGetTag` which ignores `Symbol.toStringTag` values.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the raw `toStringTag`.
 */
function getRawTag(value) {
  var isOwn = hasOwnProperty.call(value, symToStringTag),
      tag = value[symToStringTag];

  try {
    value[symToStringTag] = undefined;
    var unmasked = true;
  } catch (e) {}

  var result = nativeObjectToString.call(value);
  if (unmasked) {
    if (isOwn) {
      value[symToStringTag] = tag;
    } else {
      delete value[symToStringTag];
    }
  }
  return result;
}

module.exports = getRawTag;


/***/ }),

/***/ "Ae65":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getCalendarDaySettings;

var _getPhrase = _interopRequireDefault(__webpack_require__("oOcr"));

var _constants = __webpack_require__("Fv1B");

function isSelected(modifiers) {
  return modifiers.has('selected') || modifiers.has('selected-span') || modifiers.has('selected-start') || modifiers.has('selected-end');
}

function shouldUseDefaultCursor(modifiers) {
  return modifiers.has('blocked-minimum-nights') || modifiers.has('blocked-calendar') || modifiers.has('blocked-out-of-range');
}

function isHoveredSpan(modifiers) {
  if (isSelected(modifiers)) return false;
  return modifiers.has('hovered-span') || modifiers.has('after-hovered-start');
}

function getAriaLabel(phrases, modifiers, day, ariaLabelFormat) {
  var chooseAvailableDate = phrases.chooseAvailableDate,
      dateIsUnavailable = phrases.dateIsUnavailable,
      dateIsSelected = phrases.dateIsSelected,
      dateIsSelectedAsStartDate = phrases.dateIsSelectedAsStartDate,
      dateIsSelectedAsEndDate = phrases.dateIsSelectedAsEndDate;
  var formattedDate = {
    date: day.format(ariaLabelFormat)
  };

  if (modifiers.has('selected-start') && dateIsSelectedAsStartDate) {
    return (0, _getPhrase["default"])(dateIsSelectedAsStartDate, formattedDate);
  }

  if (modifiers.has('selected-end') && dateIsSelectedAsEndDate) {
    return (0, _getPhrase["default"])(dateIsSelectedAsEndDate, formattedDate);
  }

  if (isSelected(modifiers) && dateIsSelected) {
    return (0, _getPhrase["default"])(dateIsSelected, formattedDate);
  }

  if (modifiers.has(_constants.BLOCKED_MODIFIER)) {
    return (0, _getPhrase["default"])(dateIsUnavailable, formattedDate);
  }

  return (0, _getPhrase["default"])(chooseAvailableDate, formattedDate);
}

function getCalendarDaySettings(day, ariaLabelFormat, daySize, modifiers, phrases) {
  return {
    ariaLabel: getAriaLabel(phrases, modifiers, day, ariaLabelFormat),
    hoveredSpan: isHoveredSpan(modifiers),
    isOutsideRange: modifiers.has('blocked-out-of-range'),
    selected: isSelected(modifiers),
    useDefaultCursor: shouldUseDefaultCursor(modifiers),
    daySizeStyles: {
      width: daySize,
      height: daySize - 1
    }
  };
}

/***/ }),

/***/ "AoOu":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = exports.withStylesPropTypes = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var withStylesPropTypes = {
  styles: _propTypes["default"].object.isRequired,
  theme: _propTypes["default"].object.isRequired,
  css: _propTypes["default"].func.isRequired
};
exports.withStylesPropTypes = withStylesPropTypes;
var _default = withStylesPropTypes;
exports["default"] = _default;

/***/ }),

/***/ "Bh6R":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _constants = __webpack_require__("Fv1B");

var _default = _propTypes["default"].oneOf([_constants.ICON_BEFORE_POSITION, _constants.ICON_AFTER_POSITION]);

exports["default"] = _default;

/***/ }),

/***/ "Bnag":
/***/ (function(module, exports) {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}

module.exports = _nonIterableSpread;

/***/ }),

/***/ "CDAp":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _slicedToArray2 = _interopRequireDefault(__webpack_require__("J4zp"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _reactMomentProptypes = _interopRequireDefault(__webpack_require__("XGBb"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _object = _interopRequireDefault(__webpack_require__("4cSd"));

var _isTouchDevice = _interopRequireDefault(__webpack_require__("LTAC"));

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _isInclusivelyAfterDay = _interopRequireDefault(__webpack_require__("rit9"));

var _isNextDay = _interopRequireDefault(__webpack_require__("YAW8"));

var _isSameDay = _interopRequireDefault(__webpack_require__("pRvc"));

var _isAfterDay = _interopRequireDefault(__webpack_require__("Nho6"));

var _isBeforeDay = _interopRequireDefault(__webpack_require__("h6xH"));

var _getVisibleDays = _interopRequireDefault(__webpack_require__("u5Fq"));

var _isDayVisible = _interopRequireDefault(__webpack_require__("IgE5"));

var _getSelectedDateOffset = _interopRequireDefault(__webpack_require__("JORM"));

var _toISODateString = _interopRequireDefault(__webpack_require__("pYxT"));

var _modifiers = __webpack_require__("kpKt");

var _DisabledShape = _interopRequireDefault(__webpack_require__("5Fvu"));

var _FocusedInputShape = _interopRequireDefault(__webpack_require__("5bN9"));

var _ScrollableOrientationShape = _interopRequireDefault(__webpack_require__("aE6U"));

var _DayOfWeekShape = _interopRequireDefault(__webpack_require__("2S2E"));

var _CalendarInfoPositionShape = _interopRequireDefault(__webpack_require__("oR9Z"));

var _constants = __webpack_require__("Fv1B");

var _DayPicker = _interopRequireDefault(__webpack_require__("Nloh"));

var _getPooledMoment = _interopRequireDefault(__webpack_require__("gtaZ"));

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  startDate: undefined,
  // TODO: use null
  endDate: undefined,
  // TODO: use null
  minDate: null,
  maxDate: null,
  onDatesChange: function onDatesChange() {},
  startDateOffset: undefined,
  endDateOffset: undefined,
  focusedInput: null,
  onFocusChange: function onFocusChange() {},
  onClose: function onClose() {},
  keepOpenOnDateSelect: false,
  minimumNights: 1,
  disabled: false,
  isOutsideRange: function isOutsideRange() {},
  isDayBlocked: function isDayBlocked() {},
  isDayHighlighted: function isDayHighlighted() {},
  getMinNightsForHoverDate: function getMinNightsForHoverDate() {},
  // DayPicker props
  renderMonthText: null,
  enableOutsideDays: false,
  numberOfMonths: 1,
  orientation: _constants.HORIZONTAL_ORIENTATION,
  withPortal: false,
  hideKeyboardShortcutsPanel: false,
  initialVisibleMonth: null,
  daySize: _constants.DAY_SIZE,
  navPrev: null,
  navNext: null,
  noNavButtons: false,
  onPrevMonthClick: function onPrevMonthClick() {},
  onNextMonthClick: function onNextMonthClick() {},
  onOutsideClick: function onOutsideClick() {},
  renderCalendarDay: undefined,
  renderDayContents: null,
  renderCalendarInfo: null,
  renderMonthElement: null,
  renderKeyboardShortcutsButton: undefined,
  calendarInfoPosition: _constants.INFO_POSITION_BOTTOM,
  firstDayOfWeek: null,
  verticalHeight: null,
  noBorder: false,
  transitionDuration: undefined,
  verticalBorderSpacing: undefined,
  horizontalMonthPadding: 13,
  // accessibility
  onBlur: function onBlur() {},
  isFocused: false,
  showKeyboardShortcuts: false,
  onTab: function onTab() {},
  onShiftTab: function onShiftTab() {},
  // i18n
  monthFormat: 'MMMM YYYY',
  weekDayFormat: 'dd',
  phrases: _defaultPhrases.DayPickerPhrases,
  dayAriaLabelFormat: undefined,
  isRTL: false
};

var getChooseAvailableDatePhrase = function getChooseAvailableDatePhrase(phrases, focusedInput) {
  if (focusedInput === _constants.START_DATE) {
    return phrases.chooseAvailableStartDate;
  }

  if (focusedInput === _constants.END_DATE) {
    return phrases.chooseAvailableEndDate;
  }

  return phrases.chooseAvailableDate;
};

var DayPickerRangeController =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(DayPickerRangeController, _ref);
  var _proto = DayPickerRangeController.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function DayPickerRangeController(props) {
    var _this;

    _this = _ref.call(this, props) || this;
    _this.isTouchDevice = (0, _isTouchDevice["default"])();
    _this.today = (0, _moment["default"])();
    _this.modifiers = {
      today: function today(day) {
        return _this.isToday(day);
      },
      blocked: function blocked(day) {
        return _this.isBlocked(day);
      },
      'blocked-calendar': function blockedCalendar(day) {
        return props.isDayBlocked(day);
      },
      'blocked-out-of-range': function blockedOutOfRange(day) {
        return props.isOutsideRange(day);
      },
      'highlighted-calendar': function highlightedCalendar(day) {
        return props.isDayHighlighted(day);
      },
      valid: function valid(day) {
        return !_this.isBlocked(day);
      },
      'selected-start': function selectedStart(day) {
        return _this.isStartDate(day);
      },
      'selected-end': function selectedEnd(day) {
        return _this.isEndDate(day);
      },
      'blocked-minimum-nights': function blockedMinimumNights(day) {
        return _this.doesNotMeetMinimumNights(day);
      },
      'selected-span': function selectedSpan(day) {
        return _this.isInSelectedSpan(day);
      },
      'last-in-range': function lastInRange(day) {
        return _this.isLastInRange(day);
      },
      hovered: function hovered(day) {
        return _this.isHovered(day);
      },
      'hovered-span': function hoveredSpan(day) {
        return _this.isInHoveredSpan(day);
      },
      'hovered-offset': function hoveredOffset(day) {
        return _this.isInHoveredSpan(day);
      },
      'after-hovered-start': function afterHoveredStart(day) {
        return _this.isDayAfterHoveredStartDate(day);
      },
      'first-day-of-week': function firstDayOfWeek(day) {
        return _this.isFirstDayOfWeek(day);
      },
      'last-day-of-week': function lastDayOfWeek(day) {
        return _this.isLastDayOfWeek(day);
      },
      'hovered-start-first-possible-end': function hoveredStartFirstPossibleEnd(day, hoverDate) {
        return _this.isFirstPossibleEndDateForHoveredStartDate(day, hoverDate);
      },
      'hovered-start-blocked-minimum-nights': function hoveredStartBlockedMinimumNights(day, hoverDate) {
        return _this.doesNotMeetMinNightsForHoveredStartDate(day, hoverDate);
      }
    };

    var _this$getStateForNewM = _this.getStateForNewMonth(props),
        currentMonth = _this$getStateForNewM.currentMonth,
        visibleDays = _this$getStateForNewM.visibleDays; // initialize phrases
    // set the appropriate CalendarDay phrase based on focusedInput


    var chooseAvailableDate = getChooseAvailableDatePhrase(props.phrases, props.focusedInput);
    _this.state = {
      hoverDate: null,
      currentMonth: currentMonth,
      phrases: _objectSpread({}, props.phrases, {
        chooseAvailableDate: chooseAvailableDate
      }),
      visibleDays: visibleDays,
      disablePrev: _this.shouldDisableMonthNavigation(props.minDate, currentMonth),
      disableNext: _this.shouldDisableMonthNavigation(props.maxDate, currentMonth)
    };
    _this.onDayClick = _this.onDayClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onDayMouseEnter = _this.onDayMouseEnter.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onDayMouseLeave = _this.onDayMouseLeave.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onPrevMonthClick = _this.onPrevMonthClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onNextMonthClick = _this.onNextMonthClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onMonthChange = _this.onMonthChange.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onYearChange = _this.onYearChange.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onMultiplyScrollableMonths = _this.onMultiplyScrollableMonths.bind((0, _assertThisInitialized2["default"])(_this));
    _this.getFirstFocusableDay = _this.getFirstFocusableDay.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
    var _this2 = this;

    var startDate = nextProps.startDate,
        endDate = nextProps.endDate,
        focusedInput = nextProps.focusedInput,
        getMinNightsForHoverDate = nextProps.getMinNightsForHoverDate,
        minimumNights = nextProps.minimumNights,
        isOutsideRange = nextProps.isOutsideRange,
        isDayBlocked = nextProps.isDayBlocked,
        isDayHighlighted = nextProps.isDayHighlighted,
        phrases = nextProps.phrases,
        initialVisibleMonth = nextProps.initialVisibleMonth,
        numberOfMonths = nextProps.numberOfMonths,
        enableOutsideDays = nextProps.enableOutsideDays;
    var _this$props = this.props,
        prevStartDate = _this$props.startDate,
        prevEndDate = _this$props.endDate,
        prevFocusedInput = _this$props.focusedInput,
        prevMinimumNights = _this$props.minimumNights,
        prevIsOutsideRange = _this$props.isOutsideRange,
        prevIsDayBlocked = _this$props.isDayBlocked,
        prevIsDayHighlighted = _this$props.isDayHighlighted,
        prevPhrases = _this$props.phrases,
        prevInitialVisibleMonth = _this$props.initialVisibleMonth,
        prevNumberOfMonths = _this$props.numberOfMonths,
        prevEnableOutsideDays = _this$props.enableOutsideDays;
    var hoverDate = this.state.hoverDate;
    var visibleDays = this.state.visibleDays;
    var recomputeOutsideRange = false;
    var recomputeDayBlocked = false;
    var recomputeDayHighlighted = false;

    if (isOutsideRange !== prevIsOutsideRange) {
      this.modifiers['blocked-out-of-range'] = function (day) {
        return isOutsideRange(day);
      };

      recomputeOutsideRange = true;
    }

    if (isDayBlocked !== prevIsDayBlocked) {
      this.modifiers['blocked-calendar'] = function (day) {
        return isDayBlocked(day);
      };

      recomputeDayBlocked = true;
    }

    if (isDayHighlighted !== prevIsDayHighlighted) {
      this.modifiers['highlighted-calendar'] = function (day) {
        return isDayHighlighted(day);
      };

      recomputeDayHighlighted = true;
    }

    var recomputePropModifiers = recomputeOutsideRange || recomputeDayBlocked || recomputeDayHighlighted;
    var didStartDateChange = startDate !== prevStartDate;
    var didEndDateChange = endDate !== prevEndDate;
    var didFocusChange = focusedInput !== prevFocusedInput;

    if (numberOfMonths !== prevNumberOfMonths || enableOutsideDays !== prevEnableOutsideDays || initialVisibleMonth !== prevInitialVisibleMonth && !prevFocusedInput && didFocusChange) {
      var newMonthState = this.getStateForNewMonth(nextProps);
      var currentMonth = newMonthState.currentMonth;
      visibleDays = newMonthState.visibleDays;
      this.setState({
        currentMonth: currentMonth,
        visibleDays: visibleDays
      });
    }

    var modifiers = {};

    if (didStartDateChange) {
      modifiers = this.deleteModifier(modifiers, prevStartDate, 'selected-start');
      modifiers = this.addModifier(modifiers, startDate, 'selected-start');

      if (prevStartDate) {
        var startSpan = prevStartDate.clone().add(1, 'day');
        var endSpan = prevStartDate.clone().add(prevMinimumNights + 1, 'days');
        modifiers = this.deleteModifierFromRange(modifiers, startSpan, endSpan, 'after-hovered-start');
      }
    }

    if (didEndDateChange) {
      modifiers = this.deleteModifier(modifiers, prevEndDate, 'selected-end');
      modifiers = this.addModifier(modifiers, endDate, 'selected-end');
    }

    if (didStartDateChange || didEndDateChange) {
      if (prevStartDate && prevEndDate) {
        modifiers = this.deleteModifierFromRange(modifiers, prevStartDate, prevEndDate.clone().add(1, 'day'), 'selected-span');
      }

      if (startDate && endDate) {
        modifiers = this.deleteModifierFromRange(modifiers, startDate, endDate.clone().add(1, 'day'), 'hovered-span');
        modifiers = this.addModifierToRange(modifiers, startDate.clone().add(1, 'day'), endDate, 'selected-span');
      }
    }

    if (!this.isTouchDevice && didStartDateChange && startDate && !endDate) {
      var _startSpan = startDate.clone().add(1, 'day');

      var _endSpan = startDate.clone().add(minimumNights + 1, 'days');

      modifiers = this.addModifierToRange(modifiers, _startSpan, _endSpan, 'after-hovered-start');
    }

    if (prevMinimumNights > 0) {
      if (didFocusChange || didStartDateChange || minimumNights !== prevMinimumNights) {
        var _startSpan2 = prevStartDate || this.today;

        modifiers = this.deleteModifierFromRange(modifiers, _startSpan2, _startSpan2.clone().add(prevMinimumNights, 'days'), 'blocked-minimum-nights');
        modifiers = this.deleteModifierFromRange(modifiers, _startSpan2, _startSpan2.clone().add(prevMinimumNights, 'days'), 'blocked');
      }
    }

    if (didFocusChange || recomputePropModifiers) {
      (0, _object["default"])(visibleDays).forEach(function (days) {
        Object.keys(days).forEach(function (day) {
          var momentObj = (0, _getPooledMoment["default"])(day);
          var isBlocked = false;

          if (didFocusChange || recomputeOutsideRange) {
            if (isOutsideRange(momentObj)) {
              modifiers = _this2.addModifier(modifiers, momentObj, 'blocked-out-of-range');
              isBlocked = true;
            } else {
              modifiers = _this2.deleteModifier(modifiers, momentObj, 'blocked-out-of-range');
            }
          }

          if (didFocusChange || recomputeDayBlocked) {
            if (isDayBlocked(momentObj)) {
              modifiers = _this2.addModifier(modifiers, momentObj, 'blocked-calendar');
              isBlocked = true;
            } else {
              modifiers = _this2.deleteModifier(modifiers, momentObj, 'blocked-calendar');
            }
          }

          if (isBlocked) {
            modifiers = _this2.addModifier(modifiers, momentObj, 'blocked');
          } else {
            modifiers = _this2.deleteModifier(modifiers, momentObj, 'blocked');
          }

          if (didFocusChange || recomputeDayHighlighted) {
            if (isDayHighlighted(momentObj)) {
              modifiers = _this2.addModifier(modifiers, momentObj, 'highlighted-calendar');
            } else {
              modifiers = _this2.deleteModifier(modifiers, momentObj, 'highlighted-calendar');
            }
          }
        });
      });
    }

    if (!this.isTouchDevice && didFocusChange && hoverDate && !this.isBlocked(hoverDate)) {
      var minNightsForHoverDate = getMinNightsForHoverDate(hoverDate);

      if (minNightsForHoverDate > 0 && focusedInput === _constants.END_DATE) {
        modifiers = this.deleteModifierFromRange(modifiers, hoverDate.clone().add(1, 'days'), hoverDate.clone().add(minNightsForHoverDate, 'days'), 'hovered-start-blocked-minimum-nights');
        modifiers = this.deleteModifier(modifiers, hoverDate.clone().add(minNightsForHoverDate, 'days'), 'hovered-start-first-possible-end');
      }

      if (minNightsForHoverDate > 0 && focusedInput === _constants.START_DATE) {
        modifiers = this.addModifierToRange(modifiers, hoverDate.clone().add(1, 'days'), hoverDate.clone().add(minNightsForHoverDate, 'days'), 'hovered-start-blocked-minimum-nights');
        modifiers = this.addModifier(modifiers, hoverDate.clone().add(minNightsForHoverDate, 'days'), 'hovered-start-first-possible-end');
      }
    }

    if (minimumNights > 0 && startDate && focusedInput === _constants.END_DATE) {
      modifiers = this.addModifierToRange(modifiers, startDate, startDate.clone().add(minimumNights, 'days'), 'blocked-minimum-nights');
      modifiers = this.addModifierToRange(modifiers, startDate, startDate.clone().add(minimumNights, 'days'), 'blocked');
    }

    var today = (0, _moment["default"])();

    if (!(0, _isSameDay["default"])(this.today, today)) {
      modifiers = this.deleteModifier(modifiers, this.today, 'today');
      modifiers = this.addModifier(modifiers, today, 'today');
      this.today = today;
    }

    if (Object.keys(modifiers).length > 0) {
      this.setState({
        visibleDays: _objectSpread({}, visibleDays, {}, modifiers)
      });
    }

    if (didFocusChange || phrases !== prevPhrases) {
      // set the appropriate CalendarDay phrase based on focusedInput
      var chooseAvailableDate = getChooseAvailableDatePhrase(phrases, focusedInput);
      this.setState({
        phrases: _objectSpread({}, phrases, {
          chooseAvailableDate: chooseAvailableDate
        })
      });
    }
  };

  _proto.onDayClick = function onDayClick(day, e) {
    var _this$props2 = this.props,
        keepOpenOnDateSelect = _this$props2.keepOpenOnDateSelect,
        minimumNights = _this$props2.minimumNights,
        onBlur = _this$props2.onBlur,
        focusedInput = _this$props2.focusedInput,
        onFocusChange = _this$props2.onFocusChange,
        onClose = _this$props2.onClose,
        onDatesChange = _this$props2.onDatesChange,
        startDateOffset = _this$props2.startDateOffset,
        endDateOffset = _this$props2.endDateOffset,
        disabled = _this$props2.disabled;
    if (e) e.preventDefault();
    if (this.isBlocked(day)) return;
    var _this$props3 = this.props,
        startDate = _this$props3.startDate,
        endDate = _this$props3.endDate;

    if (startDateOffset || endDateOffset) {
      startDate = (0, _getSelectedDateOffset["default"])(startDateOffset, day);
      endDate = (0, _getSelectedDateOffset["default"])(endDateOffset, day);

      if (this.isBlocked(startDate) || this.isBlocked(endDate)) {
        return;
      }

      onDatesChange({
        startDate: startDate,
        endDate: endDate
      });

      if (!keepOpenOnDateSelect) {
        onFocusChange(null);
        onClose({
          startDate: startDate,
          endDate: endDate
        });
      }
    } else if (focusedInput === _constants.START_DATE) {
      var lastAllowedStartDate = endDate && endDate.clone().subtract(minimumNights, 'days');
      var isStartDateAfterEndDate = (0, _isBeforeDay["default"])(lastAllowedStartDate, day) || (0, _isAfterDay["default"])(startDate, endDate);
      var isEndDateDisabled = disabled === _constants.END_DATE;

      if (!isEndDateDisabled || !isStartDateAfterEndDate) {
        startDate = day;

        if (isStartDateAfterEndDate) {
          endDate = null;
        }
      }

      onDatesChange({
        startDate: startDate,
        endDate: endDate
      });

      if (isEndDateDisabled && !isStartDateAfterEndDate) {
        onFocusChange(null);
        onClose({
          startDate: startDate,
          endDate: endDate
        });
      } else if (!isEndDateDisabled) {
        onFocusChange(_constants.END_DATE);
      }
    } else if (focusedInput === _constants.END_DATE) {
      var firstAllowedEndDate = startDate && startDate.clone().add(minimumNights, 'days');

      if (!startDate) {
        endDate = day;
        onDatesChange({
          startDate: startDate,
          endDate: endDate
        });
        onFocusChange(_constants.START_DATE);
      } else if ((0, _isInclusivelyAfterDay["default"])(day, firstAllowedEndDate)) {
        endDate = day;
        onDatesChange({
          startDate: startDate,
          endDate: endDate
        });

        if (!keepOpenOnDateSelect) {
          onFocusChange(null);
          onClose({
            startDate: startDate,
            endDate: endDate
          });
        }
      } else if (disabled !== _constants.START_DATE) {
        startDate = day;
        endDate = null;
        onDatesChange({
          startDate: startDate,
          endDate: endDate
        });
      } else {
        onDatesChange({
          startDate: startDate,
          endDate: endDate
        });
      }
    } else {
      onDatesChange({
        startDate: startDate,
        endDate: endDate
      });
    }

    onBlur();
  };

  _proto.onDayMouseEnter = function onDayMouseEnter(day) {
    /* eslint react/destructuring-assignment: 1 */
    if (this.isTouchDevice) return;
    var _this$props4 = this.props,
        startDate = _this$props4.startDate,
        endDate = _this$props4.endDate,
        focusedInput = _this$props4.focusedInput,
        getMinNightsForHoverDate = _this$props4.getMinNightsForHoverDate,
        minimumNights = _this$props4.minimumNights,
        startDateOffset = _this$props4.startDateOffset,
        endDateOffset = _this$props4.endDateOffset;
    var _this$state = this.state,
        hoverDate = _this$state.hoverDate,
        visibleDays = _this$state.visibleDays,
        dateOffset = _this$state.dateOffset;
    var nextDateOffset = null;

    if (focusedInput) {
      var hasOffset = startDateOffset || endDateOffset;
      var modifiers = {};

      if (hasOffset) {
        var start = (0, _getSelectedDateOffset["default"])(startDateOffset, day);
        var end = (0, _getSelectedDateOffset["default"])(endDateOffset, day, function (rangeDay) {
          return rangeDay.add(1, 'day');
        });
        nextDateOffset = {
          start: start,
          end: end
        }; // eslint-disable-next-line react/destructuring-assignment

        if (dateOffset && dateOffset.start && dateOffset.end) {
          modifiers = this.deleteModifierFromRange(modifiers, dateOffset.start, dateOffset.end, 'hovered-offset');
        }

        modifiers = this.addModifierToRange(modifiers, start, end, 'hovered-offset');
      }

      if (!hasOffset) {
        modifiers = this.deleteModifier(modifiers, hoverDate, 'hovered');
        modifiers = this.addModifier(modifiers, day, 'hovered');

        if (startDate && !endDate && focusedInput === _constants.END_DATE) {
          if ((0, _isAfterDay["default"])(hoverDate, startDate)) {
            var endSpan = hoverDate.clone().add(1, 'day');
            modifiers = this.deleteModifierFromRange(modifiers, startDate, endSpan, 'hovered-span');
          }

          if (!this.isBlocked(day) && (0, _isAfterDay["default"])(day, startDate)) {
            var _endSpan2 = day.clone().add(1, 'day');

            modifiers = this.addModifierToRange(modifiers, startDate, _endSpan2, 'hovered-span');
          }
        }

        if (!startDate && endDate && focusedInput === _constants.START_DATE) {
          if ((0, _isBeforeDay["default"])(hoverDate, endDate)) {
            modifiers = this.deleteModifierFromRange(modifiers, hoverDate, endDate, 'hovered-span');
          }

          if (!this.isBlocked(day) && (0, _isBeforeDay["default"])(day, endDate)) {
            modifiers = this.addModifierToRange(modifiers, day, endDate, 'hovered-span');
          }
        }

        if (startDate) {
          var startSpan = startDate.clone().add(1, 'day');

          var _endSpan3 = startDate.clone().add(minimumNights + 1, 'days');

          modifiers = this.deleteModifierFromRange(modifiers, startSpan, _endSpan3, 'after-hovered-start');

          if ((0, _isSameDay["default"])(day, startDate)) {
            var newStartSpan = startDate.clone().add(1, 'day');
            var newEndSpan = startDate.clone().add(minimumNights + 1, 'days');
            modifiers = this.addModifierToRange(modifiers, newStartSpan, newEndSpan, 'after-hovered-start');
          }
        }

        if (hoverDate && !this.isBlocked(hoverDate)) {
          var minNightsForPrevHoverDate = getMinNightsForHoverDate(hoverDate);

          if (minNightsForPrevHoverDate > 0 && focusedInput === _constants.START_DATE) {
            modifiers = this.deleteModifierFromRange(modifiers, hoverDate.clone().add(1, 'days'), hoverDate.clone().add(minNightsForPrevHoverDate, 'days'), 'hovered-start-blocked-minimum-nights');
            modifiers = this.deleteModifier(modifiers, hoverDate.clone().add(minNightsForPrevHoverDate, 'days'), 'hovered-start-first-possible-end');
          }
        }

        if (!this.isBlocked(day)) {
          var minNightsForHoverDate = getMinNightsForHoverDate(day);

          if (minNightsForHoverDate > 0 && focusedInput === _constants.START_DATE) {
            modifiers = this.addModifierToRange(modifiers, day.clone().add(1, 'days'), day.clone().add(minNightsForHoverDate, 'days'), 'hovered-start-blocked-minimum-nights');
            modifiers = this.addModifier(modifiers, day.clone().add(minNightsForHoverDate, 'days'), 'hovered-start-first-possible-end');
          }
        }
      }

      this.setState({
        hoverDate: day,
        dateOffset: nextDateOffset,
        visibleDays: _objectSpread({}, visibleDays, {}, modifiers)
      });
    }
  };

  _proto.onDayMouseLeave = function onDayMouseLeave(day) {
    var _this$props5 = this.props,
        startDate = _this$props5.startDate,
        endDate = _this$props5.endDate,
        focusedInput = _this$props5.focusedInput,
        getMinNightsForHoverDate = _this$props5.getMinNightsForHoverDate,
        minimumNights = _this$props5.minimumNights;
    var _this$state2 = this.state,
        hoverDate = _this$state2.hoverDate,
        visibleDays = _this$state2.visibleDays,
        dateOffset = _this$state2.dateOffset;
    if (this.isTouchDevice || !hoverDate) return;
    var modifiers = {};
    modifiers = this.deleteModifier(modifiers, hoverDate, 'hovered');

    if (dateOffset) {
      modifiers = this.deleteModifierFromRange(modifiers, dateOffset.start, dateOffset.end, 'hovered-offset');
    }

    if (startDate && !endDate && (0, _isAfterDay["default"])(hoverDate, startDate)) {
      var endSpan = hoverDate.clone().add(1, 'day');
      modifiers = this.deleteModifierFromRange(modifiers, startDate, endSpan, 'hovered-span');
    }

    if (!startDate && endDate && (0, _isAfterDay["default"])(endDate, hoverDate)) {
      modifiers = this.deleteModifierFromRange(modifiers, hoverDate, endDate, 'hovered-span');
    }

    if (startDate && (0, _isSameDay["default"])(day, startDate)) {
      var startSpan = startDate.clone().add(1, 'day');

      var _endSpan4 = startDate.clone().add(minimumNights + 1, 'days');

      modifiers = this.deleteModifierFromRange(modifiers, startSpan, _endSpan4, 'after-hovered-start');
    }

    if (!this.isBlocked(hoverDate)) {
      var minNightsForHoverDate = getMinNightsForHoverDate(hoverDate);

      if (minNightsForHoverDate > 0 && focusedInput === _constants.START_DATE) {
        modifiers = this.deleteModifierFromRange(modifiers, hoverDate.clone().add(1, 'days'), hoverDate.clone().add(minNightsForHoverDate, 'days'), 'hovered-start-blocked-minimum-nights');
        modifiers = this.deleteModifier(modifiers, hoverDate.clone().add(minNightsForHoverDate, 'days'), 'hovered-start-first-possible-end');
      }
    }

    this.setState({
      hoverDate: null,
      visibleDays: _objectSpread({}, visibleDays, {}, modifiers)
    });
  };

  _proto.onPrevMonthClick = function onPrevMonthClick() {
    var _this$props6 = this.props,
        enableOutsideDays = _this$props6.enableOutsideDays,
        maxDate = _this$props6.maxDate,
        minDate = _this$props6.minDate,
        numberOfMonths = _this$props6.numberOfMonths,
        onPrevMonthClick = _this$props6.onPrevMonthClick;
    var _this$state3 = this.state,
        currentMonth = _this$state3.currentMonth,
        visibleDays = _this$state3.visibleDays;
    var newVisibleDays = {};
    Object.keys(visibleDays).sort().slice(0, numberOfMonths + 1).forEach(function (month) {
      newVisibleDays[month] = visibleDays[month];
    });
    var prevMonth = currentMonth.clone().subtract(2, 'months');
    var prevMonthVisibleDays = (0, _getVisibleDays["default"])(prevMonth, 1, enableOutsideDays, true);
    var newCurrentMonth = currentMonth.clone().subtract(1, 'month');
    this.setState({
      currentMonth: newCurrentMonth,
      disablePrev: this.shouldDisableMonthNavigation(minDate, newCurrentMonth),
      disableNext: this.shouldDisableMonthNavigation(maxDate, newCurrentMonth),
      visibleDays: _objectSpread({}, newVisibleDays, {}, this.getModifiers(prevMonthVisibleDays))
    }, function () {
      onPrevMonthClick(newCurrentMonth.clone());
    });
  };

  _proto.onNextMonthClick = function onNextMonthClick() {
    var _this$props7 = this.props,
        enableOutsideDays = _this$props7.enableOutsideDays,
        maxDate = _this$props7.maxDate,
        minDate = _this$props7.minDate,
        numberOfMonths = _this$props7.numberOfMonths,
        onNextMonthClick = _this$props7.onNextMonthClick;
    var _this$state4 = this.state,
        currentMonth = _this$state4.currentMonth,
        visibleDays = _this$state4.visibleDays;
    var newVisibleDays = {};
    Object.keys(visibleDays).sort().slice(1).forEach(function (month) {
      newVisibleDays[month] = visibleDays[month];
    });
    var nextMonth = currentMonth.clone().add(numberOfMonths + 1, 'month');
    var nextMonthVisibleDays = (0, _getVisibleDays["default"])(nextMonth, 1, enableOutsideDays, true);
    var newCurrentMonth = currentMonth.clone().add(1, 'month');
    this.setState({
      currentMonth: newCurrentMonth,
      disablePrev: this.shouldDisableMonthNavigation(minDate, newCurrentMonth),
      disableNext: this.shouldDisableMonthNavigation(maxDate, newCurrentMonth),
      visibleDays: _objectSpread({}, newVisibleDays, {}, this.getModifiers(nextMonthVisibleDays))
    }, function () {
      onNextMonthClick(newCurrentMonth.clone());
    });
  };

  _proto.onMonthChange = function onMonthChange(newMonth) {
    var _this$props8 = this.props,
        numberOfMonths = _this$props8.numberOfMonths,
        enableOutsideDays = _this$props8.enableOutsideDays,
        orientation = _this$props8.orientation;
    var withoutTransitionMonths = orientation === _constants.VERTICAL_SCROLLABLE;
    var newVisibleDays = (0, _getVisibleDays["default"])(newMonth, numberOfMonths, enableOutsideDays, withoutTransitionMonths);
    this.setState({
      currentMonth: newMonth.clone(),
      visibleDays: this.getModifiers(newVisibleDays)
    });
  };

  _proto.onYearChange = function onYearChange(newMonth) {
    var _this$props9 = this.props,
        numberOfMonths = _this$props9.numberOfMonths,
        enableOutsideDays = _this$props9.enableOutsideDays,
        orientation = _this$props9.orientation;
    var withoutTransitionMonths = orientation === _constants.VERTICAL_SCROLLABLE;
    var newVisibleDays = (0, _getVisibleDays["default"])(newMonth, numberOfMonths, enableOutsideDays, withoutTransitionMonths);
    this.setState({
      currentMonth: newMonth.clone(),
      visibleDays: this.getModifiers(newVisibleDays)
    });
  };

  _proto.onMultiplyScrollableMonths = function onMultiplyScrollableMonths() {
    var _this$props10 = this.props,
        numberOfMonths = _this$props10.numberOfMonths,
        enableOutsideDays = _this$props10.enableOutsideDays;
    var _this$state5 = this.state,
        currentMonth = _this$state5.currentMonth,
        visibleDays = _this$state5.visibleDays;
    var numberOfVisibleMonths = Object.keys(visibleDays).length;
    var nextMonth = currentMonth.clone().add(numberOfVisibleMonths, 'month');
    var newVisibleDays = (0, _getVisibleDays["default"])(nextMonth, numberOfMonths, enableOutsideDays, true);
    this.setState({
      visibleDays: _objectSpread({}, visibleDays, {}, this.getModifiers(newVisibleDays))
    });
  };

  _proto.getFirstFocusableDay = function getFirstFocusableDay(newMonth) {
    var _this3 = this;

    var _this$props11 = this.props,
        startDate = _this$props11.startDate,
        endDate = _this$props11.endDate,
        focusedInput = _this$props11.focusedInput,
        minimumNights = _this$props11.minimumNights,
        numberOfMonths = _this$props11.numberOfMonths;
    var focusedDate = newMonth.clone().startOf('month');

    if (focusedInput === _constants.START_DATE && startDate) {
      focusedDate = startDate.clone();
    } else if (focusedInput === _constants.END_DATE && !endDate && startDate) {
      focusedDate = startDate.clone().add(minimumNights, 'days');
    } else if (focusedInput === _constants.END_DATE && endDate) {
      focusedDate = endDate.clone();
    }

    if (this.isBlocked(focusedDate)) {
      var days = [];
      var lastVisibleDay = newMonth.clone().add(numberOfMonths - 1, 'months').endOf('month');
      var currentDay = focusedDate.clone();

      while (!(0, _isAfterDay["default"])(currentDay, lastVisibleDay)) {
        currentDay = currentDay.clone().add(1, 'day');
        days.push(currentDay);
      }

      var viableDays = days.filter(function (day) {
        return !_this3.isBlocked(day);
      });

      if (viableDays.length > 0) {
        var _viableDays = (0, _slicedToArray2["default"])(viableDays, 1);

        focusedDate = _viableDays[0];
      }
    }

    return focusedDate;
  };

  _proto.getModifiers = function getModifiers(visibleDays) {
    var _this4 = this;

    var modifiers = {};
    Object.keys(visibleDays).forEach(function (month) {
      modifiers[month] = {};
      visibleDays[month].forEach(function (day) {
        modifiers[month][(0, _toISODateString["default"])(day)] = _this4.getModifiersForDay(day);
      });
    });
    return modifiers;
  };

  _proto.getModifiersForDay = function getModifiersForDay(day) {
    var _this5 = this;

    return new Set(Object.keys(this.modifiers).filter(function (modifier) {
      return _this5.modifiers[modifier](day);
    }));
  };

  _proto.getStateForNewMonth = function getStateForNewMonth(nextProps) {
    var _this6 = this;

    var initialVisibleMonth = nextProps.initialVisibleMonth,
        numberOfMonths = nextProps.numberOfMonths,
        enableOutsideDays = nextProps.enableOutsideDays,
        orientation = nextProps.orientation,
        startDate = nextProps.startDate;
    var initialVisibleMonthThunk = initialVisibleMonth || (startDate ? function () {
      return startDate;
    } : function () {
      return _this6.today;
    });
    var currentMonth = initialVisibleMonthThunk();
    var withoutTransitionMonths = orientation === _constants.VERTICAL_SCROLLABLE;
    var visibleDays = this.getModifiers((0, _getVisibleDays["default"])(currentMonth, numberOfMonths, enableOutsideDays, withoutTransitionMonths));
    return {
      currentMonth: currentMonth,
      visibleDays: visibleDays
    };
  };

  _proto.shouldDisableMonthNavigation = function shouldDisableMonthNavigation(date, visibleMonth) {
    if (!date) return false;
    var _this$props12 = this.props,
        numberOfMonths = _this$props12.numberOfMonths,
        enableOutsideDays = _this$props12.enableOutsideDays;
    return (0, _isDayVisible["default"])(date, visibleMonth, numberOfMonths, enableOutsideDays);
  };

  _proto.addModifier = function addModifier(updatedDays, day, modifier) {
    return (0, _modifiers.addModifier)(updatedDays, day, modifier, this.props, this.state);
  };

  _proto.addModifierToRange = function addModifierToRange(updatedDays, start, end, modifier) {
    var days = updatedDays;
    var spanStart = start.clone();

    while ((0, _isBeforeDay["default"])(spanStart, end)) {
      days = this.addModifier(days, spanStart, modifier);
      spanStart = spanStart.clone().add(1, 'day');
    }

    return days;
  };

  _proto.deleteModifier = function deleteModifier(updatedDays, day, modifier) {
    return (0, _modifiers.deleteModifier)(updatedDays, day, modifier, this.props, this.state);
  };

  _proto.deleteModifierFromRange = function deleteModifierFromRange(updatedDays, start, end, modifier) {
    var days = updatedDays;
    var spanStart = start.clone();

    while ((0, _isBeforeDay["default"])(spanStart, end)) {
      days = this.deleteModifier(days, spanStart, modifier);
      spanStart = spanStart.clone().add(1, 'day');
    }

    return days;
  };

  _proto.doesNotMeetMinimumNights = function doesNotMeetMinimumNights(day) {
    var _this$props13 = this.props,
        startDate = _this$props13.startDate,
        isOutsideRange = _this$props13.isOutsideRange,
        focusedInput = _this$props13.focusedInput,
        minimumNights = _this$props13.minimumNights;
    if (focusedInput !== _constants.END_DATE) return false;

    if (startDate) {
      var dayDiff = day.diff(startDate.clone().startOf('day').hour(12), 'days');
      return dayDiff < minimumNights && dayDiff >= 0;
    }

    return isOutsideRange((0, _moment["default"])(day).subtract(minimumNights, 'days'));
  };

  _proto.doesNotMeetMinNightsForHoveredStartDate = function doesNotMeetMinNightsForHoveredStartDate(day, hoverDate) {
    var _this$props14 = this.props,
        focusedInput = _this$props14.focusedInput,
        getMinNightsForHoverDate = _this$props14.getMinNightsForHoverDate;
    if (focusedInput !== _constants.END_DATE) return false;

    if (hoverDate && !this.isBlocked(hoverDate)) {
      var minNights = getMinNightsForHoverDate(hoverDate);
      var dayDiff = day.diff(hoverDate.clone().startOf('day').hour(12), 'days');
      return dayDiff < minNights && dayDiff >= 0;
    }

    return false;
  };

  _proto.isDayAfterHoveredStartDate = function isDayAfterHoveredStartDate(day) {
    var _this$props15 = this.props,
        startDate = _this$props15.startDate,
        endDate = _this$props15.endDate,
        minimumNights = _this$props15.minimumNights;

    var _ref2 = this.state || {},
        hoverDate = _ref2.hoverDate;

    return !!startDate && !endDate && !this.isBlocked(day) && (0, _isNextDay["default"])(hoverDate, day) && minimumNights > 0 && (0, _isSameDay["default"])(hoverDate, startDate);
  };

  _proto.isEndDate = function isEndDate(day) {
    var endDate = this.props.endDate;
    return (0, _isSameDay["default"])(day, endDate);
  };

  _proto.isHovered = function isHovered(day) {
    var _ref3 = this.state || {},
        hoverDate = _ref3.hoverDate;

    var focusedInput = this.props.focusedInput;
    return !!focusedInput && (0, _isSameDay["default"])(day, hoverDate);
  };

  _proto.isInHoveredSpan = function isInHoveredSpan(day) {
    var _this$props16 = this.props,
        startDate = _this$props16.startDate,
        endDate = _this$props16.endDate;

    var _ref4 = this.state || {},
        hoverDate = _ref4.hoverDate;

    var isForwardRange = !!startDate && !endDate && (day.isBetween(startDate, hoverDate) || (0, _isSameDay["default"])(hoverDate, day));
    var isBackwardRange = !!endDate && !startDate && (day.isBetween(hoverDate, endDate) || (0, _isSameDay["default"])(hoverDate, day));
    var isValidDayHovered = hoverDate && !this.isBlocked(hoverDate);
    return (isForwardRange || isBackwardRange) && isValidDayHovered;
  };

  _proto.isInSelectedSpan = function isInSelectedSpan(day) {
    var _this$props17 = this.props,
        startDate = _this$props17.startDate,
        endDate = _this$props17.endDate;
    return day.isBetween(startDate, endDate, 'days');
  };

  _proto.isLastInRange = function isLastInRange(day) {
    var endDate = this.props.endDate;
    return this.isInSelectedSpan(day) && (0, _isNextDay["default"])(day, endDate);
  };

  _proto.isStartDate = function isStartDate(day) {
    var startDate = this.props.startDate;
    return (0, _isSameDay["default"])(day, startDate);
  };

  _proto.isBlocked = function isBlocked(day) {
    var _this$props18 = this.props,
        isDayBlocked = _this$props18.isDayBlocked,
        isOutsideRange = _this$props18.isOutsideRange;
    return isDayBlocked(day) || isOutsideRange(day) || this.doesNotMeetMinimumNights(day);
  };

  _proto.isToday = function isToday(day) {
    return (0, _isSameDay["default"])(day, this.today);
  };

  _proto.isFirstDayOfWeek = function isFirstDayOfWeek(day) {
    var firstDayOfWeek = this.props.firstDayOfWeek;
    return day.day() === (firstDayOfWeek || _moment["default"].localeData().firstDayOfWeek());
  };

  _proto.isLastDayOfWeek = function isLastDayOfWeek(day) {
    var firstDayOfWeek = this.props.firstDayOfWeek;
    return day.day() === ((firstDayOfWeek || _moment["default"].localeData().firstDayOfWeek()) + 6) % 7;
  };

  _proto.isFirstPossibleEndDateForHoveredStartDate = function isFirstPossibleEndDateForHoveredStartDate(day, hoverDate) {
    var _this$props19 = this.props,
        focusedInput = _this$props19.focusedInput,
        getMinNightsForHoverDate = _this$props19.getMinNightsForHoverDate;
    if (focusedInput !== _constants.END_DATE || !hoverDate || this.isBlocked(hoverDate)) return false;
    var minNights = getMinNightsForHoverDate(hoverDate);
    var firstAvailableEndDate = hoverDate.clone().add(minNights, 'days');
    return (0, _isSameDay["default"])(day, firstAvailableEndDate);
  };

  _proto.render = function render() {
    var _this$props20 = this.props,
        numberOfMonths = _this$props20.numberOfMonths,
        orientation = _this$props20.orientation,
        monthFormat = _this$props20.monthFormat,
        renderMonthText = _this$props20.renderMonthText,
        navPrev = _this$props20.navPrev,
        navNext = _this$props20.navNext,
        noNavButtons = _this$props20.noNavButtons,
        onOutsideClick = _this$props20.onOutsideClick,
        withPortal = _this$props20.withPortal,
        enableOutsideDays = _this$props20.enableOutsideDays,
        firstDayOfWeek = _this$props20.firstDayOfWeek,
        renderKeyboardShortcutsButton = _this$props20.renderKeyboardShortcutsButton,
        hideKeyboardShortcutsPanel = _this$props20.hideKeyboardShortcutsPanel,
        daySize = _this$props20.daySize,
        focusedInput = _this$props20.focusedInput,
        renderCalendarDay = _this$props20.renderCalendarDay,
        renderDayContents = _this$props20.renderDayContents,
        renderCalendarInfo = _this$props20.renderCalendarInfo,
        renderMonthElement = _this$props20.renderMonthElement,
        calendarInfoPosition = _this$props20.calendarInfoPosition,
        onBlur = _this$props20.onBlur,
        onShiftTab = _this$props20.onShiftTab,
        onTab = _this$props20.onTab,
        isFocused = _this$props20.isFocused,
        showKeyboardShortcuts = _this$props20.showKeyboardShortcuts,
        isRTL = _this$props20.isRTL,
        weekDayFormat = _this$props20.weekDayFormat,
        dayAriaLabelFormat = _this$props20.dayAriaLabelFormat,
        verticalHeight = _this$props20.verticalHeight,
        noBorder = _this$props20.noBorder,
        transitionDuration = _this$props20.transitionDuration,
        verticalBorderSpacing = _this$props20.verticalBorderSpacing,
        horizontalMonthPadding = _this$props20.horizontalMonthPadding;
    var _this$state6 = this.state,
        currentMonth = _this$state6.currentMonth,
        phrases = _this$state6.phrases,
        visibleDays = _this$state6.visibleDays,
        disablePrev = _this$state6.disablePrev,
        disableNext = _this$state6.disableNext;
    return _react["default"].createElement(_DayPicker["default"], {
      orientation: orientation,
      enableOutsideDays: enableOutsideDays,
      modifiers: visibleDays,
      numberOfMonths: numberOfMonths,
      onDayClick: this.onDayClick,
      onDayMouseEnter: this.onDayMouseEnter,
      onDayMouseLeave: this.onDayMouseLeave,
      onPrevMonthClick: this.onPrevMonthClick,
      onNextMonthClick: this.onNextMonthClick,
      onMonthChange: this.onMonthChange,
      onTab: onTab,
      onShiftTab: onShiftTab,
      onYearChange: this.onYearChange,
      onMultiplyScrollableMonths: this.onMultiplyScrollableMonths,
      monthFormat: monthFormat,
      renderMonthText: renderMonthText,
      withPortal: withPortal,
      hidden: !focusedInput,
      initialVisibleMonth: function initialVisibleMonth() {
        return currentMonth;
      },
      daySize: daySize,
      onOutsideClick: onOutsideClick,
      disablePrev: disablePrev,
      disableNext: disableNext,
      navPrev: navPrev,
      navNext: navNext,
      noNavButtons: noNavButtons,
      renderCalendarDay: renderCalendarDay,
      renderDayContents: renderDayContents,
      renderCalendarInfo: renderCalendarInfo,
      renderMonthElement: renderMonthElement,
      renderKeyboardShortcutsButton: renderKeyboardShortcutsButton,
      calendarInfoPosition: calendarInfoPosition,
      firstDayOfWeek: firstDayOfWeek,
      hideKeyboardShortcutsPanel: hideKeyboardShortcutsPanel,
      isFocused: isFocused,
      getFirstFocusableDay: this.getFirstFocusableDay,
      onBlur: onBlur,
      showKeyboardShortcuts: showKeyboardShortcuts,
      phrases: phrases,
      isRTL: isRTL,
      weekDayFormat: weekDayFormat,
      dayAriaLabelFormat: dayAriaLabelFormat,
      verticalHeight: verticalHeight,
      verticalBorderSpacing: verticalBorderSpacing,
      noBorder: noBorder,
      transitionDuration: transitionDuration,
      horizontalMonthPadding: horizontalMonthPadding
    });
  };

  return DayPickerRangeController;
}(_react["default"].PureComponent || _react["default"].Component);

exports["default"] = DayPickerRangeController;
DayPickerRangeController.propTypes =  false ? undefined : {};
DayPickerRangeController.defaultProps = defaultProps;

/***/ }),

/***/ "D3zA":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var implementation = __webpack_require__("aI7X");

module.exports = Function.prototype.bind || implementation;


/***/ }),

/***/ "DGeY":
/***/ (function(module, exports, __webpack_require__) {

var bind = __webpack_require__("D3zA");
var has = bind.call(Function.call, Object.prototype.hasOwnProperty);

var $assign = Object.assign;

module.exports = function assign(target, source) {
	if ($assign) {
		return $assign(target, source);
	}

	for (var key in source) {
		if (has(source, key)) {
			target[key] = source[key];
		}
	}
	return target;
};


/***/ }),

/***/ "DHWS":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var ChevronDown = function ChevronDown(props) {
  return _react["default"].createElement("svg", props, _react["default"].createElement("path", {
    d: "M968 289L514 741c-11 11-21 11-32 0L29 289c-4-5-6-11-6-16 0-13 10-23 23-23 6 0 11 2 15 7l437 436 438-436c4-5 9-7 16-7 6 0 11 2 16 7 9 10 9 21 0 32z"
  }));
};

ChevronDown.defaultProps = {
  focusable: "false",
  viewBox: "0 0 1000 1000"
};
var _default = ChevronDown;
exports["default"] = _default;

/***/ }),

/***/ "DciD":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


function noop() {
  return null;
}

noop.isRequired = noop;

function noopThunk() {
  return noop;
}

module.exports = {
  and: noopThunk,
  between: noopThunk,
  booleanSome: noopThunk,
  childrenHavePropXorChildren: noopThunk,
  childrenOf: noopThunk,
  childrenOfType: noopThunk,
  childrenSequenceOf: noopThunk,
  componentWithName: noopThunk,
  disallowedIf: noopThunk,
  elementType: noopThunk,
  empty: noopThunk,
  explicitNull: noopThunk,
  forbidExtraProps: Object,
  integer: noopThunk,
  keysOf: noopThunk,
  mutuallyExclusiveProps: noopThunk,
  mutuallyExclusiveTrueProps: noopThunk,
  nChildren: noopThunk,
  nonNegativeInteger: noop,
  nonNegativeNumber: noopThunk,
  numericString: noopThunk,
  object: noopThunk,
  or: noopThunk,
  range: noopThunk,
  ref: noopThunk,
  requiredBy: noopThunk,
  restrictedProp: noopThunk,
  sequenceOf: noopThunk,
  shape: noopThunk,
  stringEndsWith: noopThunk,
  stringStartsWith: noopThunk,
  uniqueArray: noopThunk,
  uniqueArrayOf: noopThunk,
  valuesOf: noopThunk,
  withShape: noopThunk
};
//# sourceMappingURL=index.js.map

/***/ }),

/***/ "Ddh5":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.perfStart = perfStart;
exports.perfEnd = perfEnd;
exports["default"] = withPerf;

function perfStart(startMark) {
  if (typeof performance !== 'undefined' && performance.mark !== undefined && typeof performance.clearMarks === 'function' && startMark) {
    performance.clearMarks(startMark);
    performance.mark(startMark);
  }
}

function perfEnd(startMark, endMark, measureName) {
  if (typeof performance !== 'undefined' && performance.mark !== undefined && typeof performance.clearMarks === 'function') {
    performance.clearMarks(endMark);
    performance.mark(endMark);
    performance.measure(measureName, startMark, endMark);
    performance.clearMarks(measureName);
  }
}

function withPerf(methodName) {
  var startMark = "react-with-styles.".concat(methodName, ".start");
  var endMark = "react-with-styles.".concat(methodName, ".end");
  var measureName = "\uD83D\uDC69\u200D\uD83C\uDFA8 [".concat(methodName, "]");
  return function (fn) {
    return function () {
      if (false) {}

      var result = fn.apply(void 0, arguments);

      if (false) {}

      return result;
    };
  };
}

/***/ }),

/***/ "DmXP":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var getDay = Date.prototype.getDay;
var tryDateObject = function tryDateObject(value) {
	try {
		getDay.call(value);
		return true;
	} catch (e) {
		return false;
	}
};

var toStr = Object.prototype.toString;
var dateClass = '[object Date]';
var hasToStringTag = typeof Symbol === 'function' && typeof Symbol.toStringTag === 'symbol';

module.exports = function isDateObject(value) {
	if (typeof value !== 'object' || value === null) { return false; }
	return hasToStringTag ? tryDateObject(value) : toStr.call(value) === dateClass;
};


/***/ }),

/***/ "Dv0f":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

var _throttle = _interopRequireDefault(__webpack_require__("DzJC"));

var _isTouchDevice = _interopRequireDefault(__webpack_require__("LTAC"));

var _noflip = _interopRequireDefault(__webpack_require__("2WTt"));

var _getInputHeight = _interopRequireDefault(__webpack_require__("hgME"));

var _OpenDirectionShape = _interopRequireDefault(__webpack_require__("iuLr"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var FANG_PATH_TOP = "M0,".concat(_constants.FANG_HEIGHT_PX, " ").concat(_constants.FANG_WIDTH_PX, ",").concat(_constants.FANG_HEIGHT_PX, " ").concat(_constants.FANG_WIDTH_PX / 2, ",0z");
var FANG_STROKE_TOP = "M0,".concat(_constants.FANG_HEIGHT_PX, " ").concat(_constants.FANG_WIDTH_PX / 2, ",0 ").concat(_constants.FANG_WIDTH_PX, ",").concat(_constants.FANG_HEIGHT_PX);
var FANG_PATH_BOTTOM = "M0,0 ".concat(_constants.FANG_WIDTH_PX, ",0 ").concat(_constants.FANG_WIDTH_PX / 2, ",").concat(_constants.FANG_HEIGHT_PX, "z");
var FANG_STROKE_BOTTOM = "M0,0 ".concat(_constants.FANG_WIDTH_PX / 2, ",").concat(_constants.FANG_HEIGHT_PX, " ").concat(_constants.FANG_WIDTH_PX, ",0");
var propTypes =  false ? undefined : {};
var defaultProps = {
  placeholder: 'Select Date',
  displayValue: '',
  ariaLabel: undefined,
  screenReaderMessage: '',
  focused: false,
  disabled: false,
  required: false,
  readOnly: null,
  openDirection: _constants.OPEN_DOWN,
  showCaret: false,
  verticalSpacing: _constants.DEFAULT_VERTICAL_SPACING,
  small: false,
  block: false,
  regular: false,
  onChange: function onChange() {},
  onFocus: function onFocus() {},
  onKeyDownShiftTab: function onKeyDownShiftTab() {},
  onKeyDownTab: function onKeyDownTab() {},
  onKeyDownArrowDown: function onKeyDownArrowDown() {},
  onKeyDownQuestionMark: function onKeyDownQuestionMark() {},
  // accessibility
  isFocused: false
};

var DateInput =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(DateInput, _ref);
  var _proto = DateInput.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function DateInput(props) {
    var _this;

    _this = _ref.call(this, props) || this;
    _this.state = {
      dateString: '',
      isTouchDevice: false
    };
    _this.onChange = _this.onChange.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onKeyDown = _this.onKeyDown.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setInputRef = _this.setInputRef.bind((0, _assertThisInitialized2["default"])(_this));
    _this.throttledKeyDown = (0, _throttle["default"])(_this.onFinalKeyDown, 300, {
      trailing: false
    });
    return _this;
  }

  _proto.componentDidMount = function componentDidMount() {
    this.setState({
      isTouchDevice: (0, _isTouchDevice["default"])()
    });
  };

  _proto.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
    var dateString = this.state.dateString;

    if (dateString && nextProps.displayValue) {
      this.setState({
        dateString: ''
      });
    }
  };

  _proto.componentDidUpdate = function componentDidUpdate(prevProps) {
    var _this$props = this.props,
        focused = _this$props.focused,
        isFocused = _this$props.isFocused;
    if (prevProps.focused === focused && prevProps.isFocused === isFocused) return;

    if (focused && isFocused) {
      this.inputRef.focus();
    }
  };

  _proto.onChange = function onChange(e) {
    var _this$props2 = this.props,
        onChange = _this$props2.onChange,
        onKeyDownQuestionMark = _this$props2.onKeyDownQuestionMark;
    var dateString = e.target.value; // In Safari, onKeyDown does not consistently fire ahead of onChange. As a result, we need to
    // special case the `?` key so that it always triggers the appropriate callback, instead of
    // modifying the input value

    if (dateString[dateString.length - 1] === '?') {
      onKeyDownQuestionMark(e);
    } else {
      this.setState({
        dateString: dateString
      }, function () {
        return onChange(dateString);
      });
    }
  };

  _proto.onKeyDown = function onKeyDown(e) {
    e.stopPropagation();

    if (!_constants.MODIFIER_KEY_NAMES.has(e.key)) {
      this.throttledKeyDown(e);
    }
  };

  _proto.onFinalKeyDown = function onFinalKeyDown(e) {
    var _this$props3 = this.props,
        onKeyDownShiftTab = _this$props3.onKeyDownShiftTab,
        onKeyDownTab = _this$props3.onKeyDownTab,
        onKeyDownArrowDown = _this$props3.onKeyDownArrowDown,
        onKeyDownQuestionMark = _this$props3.onKeyDownQuestionMark;
    var key = e.key;

    if (key === 'Tab') {
      if (e.shiftKey) {
        onKeyDownShiftTab(e);
      } else {
        onKeyDownTab(e);
      }
    } else if (key === 'ArrowDown') {
      onKeyDownArrowDown(e);
    } else if (key === '?') {
      e.preventDefault();
      onKeyDownQuestionMark(e);
    }
  };

  _proto.setInputRef = function setInputRef(ref) {
    this.inputRef = ref;
  };

  _proto.render = function render() {
    var _this$state = this.state,
        dateString = _this$state.dateString,
        isTouch = _this$state.isTouchDevice;
    var _this$props4 = this.props,
        id = _this$props4.id,
        placeholder = _this$props4.placeholder,
        ariaLabel = _this$props4.ariaLabel,
        displayValue = _this$props4.displayValue,
        screenReaderMessage = _this$props4.screenReaderMessage,
        focused = _this$props4.focused,
        showCaret = _this$props4.showCaret,
        onFocus = _this$props4.onFocus,
        disabled = _this$props4.disabled,
        required = _this$props4.required,
        readOnly = _this$props4.readOnly,
        openDirection = _this$props4.openDirection,
        verticalSpacing = _this$props4.verticalSpacing,
        small = _this$props4.small,
        regular = _this$props4.regular,
        block = _this$props4.block,
        styles = _this$props4.styles,
        reactDates = _this$props4.theme.reactDates,
        css = _this$props4.css;
    var value = dateString || displayValue || '';
    var screenReaderMessageId = "DateInput__screen-reader-message-".concat(id);
    var withFang = showCaret && focused;
    var inputHeight = (0, _getInputHeight["default"])(reactDates, small);
    return _react["default"].createElement("div", css(styles.DateInput, small && styles.DateInput__small, block && styles.DateInput__block, withFang && styles.DateInput__withFang, disabled && styles.DateInput__disabled, withFang && openDirection === _constants.OPEN_DOWN && styles.DateInput__openDown, withFang && openDirection === _constants.OPEN_UP && styles.DateInput__openUp), _react["default"].createElement("input", (0, _extends2["default"])({}, css(styles.DateInput_input, small && styles.DateInput_input__small, regular && styles.DateInput_input__regular, readOnly && styles.DateInput_input__readOnly, focused && styles.DateInput_input__focused, disabled && styles.DateInput_input__disabled), {
      "aria-label": ariaLabel === undefined ? placeholder : ariaLabel,
      type: "text",
      id: id,
      name: id,
      ref: this.setInputRef,
      value: value,
      onChange: this.onChange,
      onKeyDown: this.onKeyDown,
      onFocus: onFocus,
      placeholder: placeholder,
      autoComplete: "off",
      disabled: disabled,
      readOnly: typeof readOnly === 'boolean' ? readOnly : isTouch,
      required: required,
      "aria-describedby": screenReaderMessage && screenReaderMessageId
    })), withFang && _react["default"].createElement("svg", (0, _extends2["default"])({
      role: "presentation",
      focusable: "false"
    }, css(styles.DateInput_fang, openDirection === _constants.OPEN_DOWN && {
      top: inputHeight + verticalSpacing - _constants.FANG_HEIGHT_PX - 1
    }, openDirection === _constants.OPEN_UP && {
      bottom: inputHeight + verticalSpacing - _constants.FANG_HEIGHT_PX - 1
    })), _react["default"].createElement("path", (0, _extends2["default"])({}, css(styles.DateInput_fangShape), {
      d: openDirection === _constants.OPEN_DOWN ? FANG_PATH_TOP : FANG_PATH_BOTTOM
    })), _react["default"].createElement("path", (0, _extends2["default"])({}, css(styles.DateInput_fangStroke), {
      d: openDirection === _constants.OPEN_DOWN ? FANG_STROKE_TOP : FANG_STROKE_BOTTOM
    }))), screenReaderMessage && _react["default"].createElement("p", (0, _extends2["default"])({}, css(styles.DateInput_screenReaderMessage), {
      id: screenReaderMessageId
    }), screenReaderMessage));
  };

  return DateInput;
}(_react["default"].PureComponent || _react["default"].Component);

DateInput.propTypes =  false ? undefined : {};
DateInput.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref2) {
  var _ref2$reactDates = _ref2.reactDates,
      border = _ref2$reactDates.border,
      color = _ref2$reactDates.color,
      sizing = _ref2$reactDates.sizing,
      spacing = _ref2$reactDates.spacing,
      font = _ref2$reactDates.font,
      zIndex = _ref2$reactDates.zIndex;
  return {
    DateInput: {
      margin: 0,
      padding: spacing.inputPadding,
      background: color.background,
      position: 'relative',
      display: 'inline-block',
      width: sizing.inputWidth,
      verticalAlign: 'middle'
    },
    DateInput__small: {
      width: sizing.inputWidth_small
    },
    DateInput__block: {
      width: '100%'
    },
    DateInput__disabled: {
      background: color.disabled,
      color: color.textDisabled
    },
    DateInput_input: {
      fontWeight: 200,
      fontSize: font.input.size,
      lineHeight: font.input.lineHeight,
      color: color.text,
      backgroundColor: color.background,
      width: '100%',
      padding: "".concat(spacing.displayTextPaddingVertical, "px ").concat(spacing.displayTextPaddingHorizontal, "px"),
      paddingTop: spacing.displayTextPaddingTop,
      paddingBottom: spacing.displayTextPaddingBottom,
      paddingLeft: (0, _noflip["default"])(spacing.displayTextPaddingLeft),
      paddingRight: (0, _noflip["default"])(spacing.displayTextPaddingRight),
      border: border.input.border,
      borderTop: border.input.borderTop,
      borderRight: (0, _noflip["default"])(border.input.borderRight),
      borderBottom: border.input.borderBottom,
      borderLeft: (0, _noflip["default"])(border.input.borderLeft),
      borderRadius: border.input.borderRadius
    },
    DateInput_input__small: {
      fontSize: font.input.size_small,
      lineHeight: font.input.lineHeight_small,
      letterSpacing: font.input.letterSpacing_small,
      padding: "".concat(spacing.displayTextPaddingVertical_small, "px ").concat(spacing.displayTextPaddingHorizontal_small, "px"),
      paddingTop: spacing.displayTextPaddingTop_small,
      paddingBottom: spacing.displayTextPaddingBottom_small,
      paddingLeft: (0, _noflip["default"])(spacing.displayTextPaddingLeft_small),
      paddingRight: (0, _noflip["default"])(spacing.displayTextPaddingRight_small)
    },
    DateInput_input__regular: {
      fontWeight: 'auto'
    },
    DateInput_input__readOnly: {
      userSelect: 'none'
    },
    DateInput_input__focused: {
      outline: border.input.outlineFocused,
      background: color.backgroundFocused,
      border: border.input.borderFocused,
      borderTop: border.input.borderTopFocused,
      borderRight: (0, _noflip["default"])(border.input.borderRightFocused),
      borderBottom: border.input.borderBottomFocused,
      borderLeft: (0, _noflip["default"])(border.input.borderLeftFocused)
    },
    DateInput_input__disabled: {
      background: color.disabled,
      fontStyle: font.input.styleDisabled
    },
    DateInput_screenReaderMessage: {
      border: 0,
      clip: 'rect(0, 0, 0, 0)',
      height: 1,
      margin: -1,
      overflow: 'hidden',
      padding: 0,
      position: 'absolute',
      width: 1
    },
    DateInput_fang: {
      position: 'absolute',
      width: _constants.FANG_WIDTH_PX,
      height: _constants.FANG_HEIGHT_PX,
      left: 22,
      // TODO: should be noflip wrapped and handled by an isRTL prop
      zIndex: zIndex + 2
    },
    DateInput_fangShape: {
      fill: color.background
    },
    DateInput_fangStroke: {
      stroke: color.core.border,
      fill: 'transparent'
    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(DateInput);

exports["default"] = _default;

/***/ }),

/***/ "DwGB":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var GetIntrinsic = __webpack_require__("6ayh");

var $Object = GetIntrinsic('%Object%');
var $TypeError = GetIntrinsic('%TypeError%');
var $String = GetIntrinsic('%String%');

var assertRecord = __webpack_require__("xG2L");
var $isNaN = __webpack_require__("IFfy");
var $isFinite = __webpack_require__("xhJ2");

var sign = __webpack_require__("WXWk");
var mod = __webpack_require__("u1Mj");

var IsCallable = __webpack_require__("IdCN");
var toPrimitive = __webpack_require__("Lxf3");

var has = __webpack_require__("oNNP");

// https://es5.github.io/#x9
var ES5 = {
	ToPrimitive: toPrimitive,

	ToBoolean: function ToBoolean(value) {
		return !!value;
	},
	ToNumber: function ToNumber(value) {
		return +value; // eslint-disable-line no-implicit-coercion
	},
	ToInteger: function ToInteger(value) {
		var number = this.ToNumber(value);
		if ($isNaN(number)) { return 0; }
		if (number === 0 || !$isFinite(number)) { return number; }
		return sign(number) * Math.floor(Math.abs(number));
	},
	ToInt32: function ToInt32(x) {
		return this.ToNumber(x) >> 0;
	},
	ToUint32: function ToUint32(x) {
		return this.ToNumber(x) >>> 0;
	},
	ToUint16: function ToUint16(value) {
		var number = this.ToNumber(value);
		if ($isNaN(number) || number === 0 || !$isFinite(number)) { return 0; }
		var posInt = sign(number) * Math.floor(Math.abs(number));
		return mod(posInt, 0x10000);
	},
	ToString: function ToString(value) {
		return $String(value);
	},
	ToObject: function ToObject(value) {
		this.CheckObjectCoercible(value);
		return $Object(value);
	},
	CheckObjectCoercible: function CheckObjectCoercible(value, optMessage) {
		/* jshint eqnull:true */
		if (value == null) {
			throw new $TypeError(optMessage || 'Cannot call method on ' + value);
		}
		return value;
	},
	IsCallable: IsCallable,
	SameValue: function SameValue(x, y) {
		if (x === y) { // 0 === -0, but they are not identical.
			if (x === 0) { return 1 / x === 1 / y; }
			return true;
		}
		return $isNaN(x) && $isNaN(y);
	},

	// https://www.ecma-international.org/ecma-262/5.1/#sec-8
	Type: function Type(x) {
		if (x === null) {
			return 'Null';
		}
		if (typeof x === 'undefined') {
			return 'Undefined';
		}
		if (typeof x === 'function' || typeof x === 'object') {
			return 'Object';
		}
		if (typeof x === 'number') {
			return 'Number';
		}
		if (typeof x === 'boolean') {
			return 'Boolean';
		}
		if (typeof x === 'string') {
			return 'String';
		}
	},

	// https://ecma-international.org/ecma-262/6.0/#sec-property-descriptor-specification-type
	IsPropertyDescriptor: function IsPropertyDescriptor(Desc) {
		if (this.Type(Desc) !== 'Object') {
			return false;
		}
		var allowed = {
			'[[Configurable]]': true,
			'[[Enumerable]]': true,
			'[[Get]]': true,
			'[[Set]]': true,
			'[[Value]]': true,
			'[[Writable]]': true
		};

		for (var key in Desc) { // eslint-disable-line
			if (has(Desc, key) && !allowed[key]) {
				return false;
			}
		}

		var isData = has(Desc, '[[Value]]');
		var IsAccessor = has(Desc, '[[Get]]') || has(Desc, '[[Set]]');
		if (isData && IsAccessor) {
			throw new $TypeError('Property Descriptors may not be both accessor and data descriptors');
		}
		return true;
	},

	// https://ecma-international.org/ecma-262/5.1/#sec-8.10.1
	IsAccessorDescriptor: function IsAccessorDescriptor(Desc) {
		if (typeof Desc === 'undefined') {
			return false;
		}

		assertRecord(this, 'Property Descriptor', 'Desc', Desc);

		if (!has(Desc, '[[Get]]') && !has(Desc, '[[Set]]')) {
			return false;
		}

		return true;
	},

	// https://ecma-international.org/ecma-262/5.1/#sec-8.10.2
	IsDataDescriptor: function IsDataDescriptor(Desc) {
		if (typeof Desc === 'undefined') {
			return false;
		}

		assertRecord(this, 'Property Descriptor', 'Desc', Desc);

		if (!has(Desc, '[[Value]]') && !has(Desc, '[[Writable]]')) {
			return false;
		}

		return true;
	},

	// https://ecma-international.org/ecma-262/5.1/#sec-8.10.3
	IsGenericDescriptor: function IsGenericDescriptor(Desc) {
		if (typeof Desc === 'undefined') {
			return false;
		}

		assertRecord(this, 'Property Descriptor', 'Desc', Desc);

		if (!this.IsAccessorDescriptor(Desc) && !this.IsDataDescriptor(Desc)) {
			return true;
		}

		return false;
	},

	// https://ecma-international.org/ecma-262/5.1/#sec-8.10.4
	FromPropertyDescriptor: function FromPropertyDescriptor(Desc) {
		if (typeof Desc === 'undefined') {
			return Desc;
		}

		assertRecord(this, 'Property Descriptor', 'Desc', Desc);

		if (this.IsDataDescriptor(Desc)) {
			return {
				value: Desc['[[Value]]'],
				writable: !!Desc['[[Writable]]'],
				enumerable: !!Desc['[[Enumerable]]'],
				configurable: !!Desc['[[Configurable]]']
			};
		} else if (this.IsAccessorDescriptor(Desc)) {
			return {
				get: Desc['[[Get]]'],
				set: Desc['[[Set]]'],
				enumerable: !!Desc['[[Enumerable]]'],
				configurable: !!Desc['[[Configurable]]']
			};
		} else {
			throw new $TypeError('FromPropertyDescriptor must be called with a fully populated Property Descriptor');
		}
	},

	// https://ecma-international.org/ecma-262/5.1/#sec-8.10.5
	ToPropertyDescriptor: function ToPropertyDescriptor(Obj) {
		if (this.Type(Obj) !== 'Object') {
			throw new $TypeError('ToPropertyDescriptor requires an object');
		}

		var desc = {};
		if (has(Obj, 'enumerable')) {
			desc['[[Enumerable]]'] = this.ToBoolean(Obj.enumerable);
		}
		if (has(Obj, 'configurable')) {
			desc['[[Configurable]]'] = this.ToBoolean(Obj.configurable);
		}
		if (has(Obj, 'value')) {
			desc['[[Value]]'] = Obj.value;
		}
		if (has(Obj, 'writable')) {
			desc['[[Writable]]'] = this.ToBoolean(Obj.writable);
		}
		if (has(Obj, 'get')) {
			var getter = Obj.get;
			if (typeof getter !== 'undefined' && !this.IsCallable(getter)) {
				throw new TypeError('getter must be a function');
			}
			desc['[[Get]]'] = getter;
		}
		if (has(Obj, 'set')) {
			var setter = Obj.set;
			if (typeof setter !== 'undefined' && !this.IsCallable(setter)) {
				throw new $TypeError('setter must be a function');
			}
			desc['[[Set]]'] = setter;
		}

		if ((has(desc, '[[Get]]') || has(desc, '[[Set]]')) && (has(desc, '[[Value]]') || has(desc, '[[Writable]]'))) {
			throw new $TypeError('Invalid property descriptor. Cannot both specify accessors and a value or writable attribute');
		}
		return desc;
	}
};

module.exports = ES5;


/***/ }),

/***/ "DzJC":
/***/ (function(module, exports, __webpack_require__) {

var debounce = __webpack_require__("sEfC"),
    isObject = __webpack_require__("GoyQ");

/** Error message constants. */
var FUNC_ERROR_TEXT = 'Expected a function';

/**
 * Creates a throttled function that only invokes `func` at most once per
 * every `wait` milliseconds. The throttled function comes with a `cancel`
 * method to cancel delayed `func` invocations and a `flush` method to
 * immediately invoke them. Provide `options` to indicate whether `func`
 * should be invoked on the leading and/or trailing edge of the `wait`
 * timeout. The `func` is invoked with the last arguments provided to the
 * throttled function. Subsequent calls to the throttled function return the
 * result of the last `func` invocation.
 *
 * **Note:** If `leading` and `trailing` options are `true`, `func` is
 * invoked on the trailing edge of the timeout only if the throttled function
 * is invoked more than once during the `wait` timeout.
 *
 * If `wait` is `0` and `leading` is `false`, `func` invocation is deferred
 * until to the next tick, similar to `setTimeout` with a timeout of `0`.
 *
 * See [David Corbacho's article](https://css-tricks.com/debouncing-throttling-explained-examples/)
 * for details over the differences between `_.throttle` and `_.debounce`.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Function
 * @param {Function} func The function to throttle.
 * @param {number} [wait=0] The number of milliseconds to throttle invocations to.
 * @param {Object} [options={}] The options object.
 * @param {boolean} [options.leading=true]
 *  Specify invoking on the leading edge of the timeout.
 * @param {boolean} [options.trailing=true]
 *  Specify invoking on the trailing edge of the timeout.
 * @returns {Function} Returns the new throttled function.
 * @example
 *
 * // Avoid excessively updating the position while scrolling.
 * jQuery(window).on('scroll', _.throttle(updatePosition, 100));
 *
 * // Invoke `renewToken` when the click event is fired, but not more than once every 5 minutes.
 * var throttled = _.throttle(renewToken, 300000, { 'trailing': false });
 * jQuery(element).on('click', throttled);
 *
 * // Cancel the trailing throttled invocation.
 * jQuery(window).on('popstate', throttled.cancel);
 */
function throttle(func, wait, options) {
  var leading = true,
      trailing = true;

  if (typeof func != 'function') {
    throw new TypeError(FUNC_ERROR_TEXT);
  }
  if (isObject(options)) {
    leading = 'leading' in options ? !!options.leading : leading;
    trailing = 'trailing' in options ? !!options.trailing : trailing;
  }
  return debounce(func, wait, {
    'leading': leading,
    'maxWait': wait,
    'trailing': trailing
  });
}

module.exports = throttle;


/***/ }),

/***/ "E1iy":
/***/ (function(module, exports) {

module.exports = function isPrimitive(value) {
	return value === null || (typeof value !== 'function' && typeof value !== 'object');
};


/***/ }),

/***/ "EbDI":
/***/ (function(module, exports) {

function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

module.exports = _iterableToArray;

/***/ }),

/***/ "ExA7":
/***/ (function(module, exports) {

/**
 * Checks if `value` is object-like. A value is object-like if it's not `null`
 * and has a `typeof` result of "object".
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 * @example
 *
 * _.isObjectLike({});
 * // => true
 *
 * _.isObjectLike([1, 2, 3]);
 * // => true
 *
 * _.isObjectLike(_.noop);
 * // => false
 *
 * _.isObjectLike(null);
 * // => false
 */
function isObjectLike(value) {
  return value != null && typeof value == 'object';
}

module.exports = isObjectLike;


/***/ }),

/***/ "F7ZS":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getCalendarMonthWeeks;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _constants = __webpack_require__("Fv1B");

function getCalendarMonthWeeks(month, enableOutsideDays) {
  var firstDayOfWeek = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : _moment["default"].localeData().firstDayOfWeek();

  if (!_moment["default"].isMoment(month) || !month.isValid()) {
    throw new TypeError('`month` must be a valid moment object');
  }

  if (_constants.WEEKDAYS.indexOf(firstDayOfWeek) === -1) {
    throw new TypeError('`firstDayOfWeek` must be an integer between 0 and 6');
  } // set utc offset to get correct dates in future (when timezone changes)


  var firstOfMonth = month.clone().startOf('month').hour(12);
  var lastOfMonth = month.clone().endOf('month').hour(12); // calculate the exact first and last days to fill the entire matrix
  // (considering days outside month)

  var prevDays = (firstOfMonth.day() + 7 - firstDayOfWeek) % 7;
  var nextDays = (firstDayOfWeek + 6 - lastOfMonth.day()) % 7;
  var firstDay = firstOfMonth.clone().subtract(prevDays, 'day');
  var lastDay = lastOfMonth.clone().add(nextDays, 'day');
  var totalDays = lastDay.diff(firstDay, 'days') + 1;
  var currentDay = firstDay.clone();
  var weeksInMonth = [];

  for (var i = 0; i < totalDays; i += 1) {
    if (i % 7 === 0) {
      weeksInMonth.push([]);
    }

    var day = null;

    if (i >= prevDays && i < totalDays - nextDays || enableOutsideDays) {
      day = currentDay.clone();
    }

    weeksInMonth[weeksInMonth.length - 1].push(day);
    currentDay.add(1, 'day');
  }

  return weeksInMonth;
}

/***/ }),

/***/ "FpZJ":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/* eslint complexity: [2, 17], max-statements: [2, 33] */
module.exports = function hasSymbols() {
	if (typeof Symbol !== 'function' || typeof Object.getOwnPropertySymbols !== 'function') { return false; }
	if (typeof Symbol.iterator === 'symbol') { return true; }

	var obj = {};
	var sym = Symbol('test');
	var symObj = Object(sym);
	if (typeof sym === 'string') { return false; }

	if (Object.prototype.toString.call(sym) !== '[object Symbol]') { return false; }
	if (Object.prototype.toString.call(symObj) !== '[object Symbol]') { return false; }

	// temp disabled per https://github.com/ljharb/object.assign/issues/17
	// if (sym instanceof Symbol) { return false; }
	// temp disabled per https://github.com/WebReflection/get-own-property-symbols/issues/4
	// if (!(symObj instanceof Symbol)) { return false; }

	// if (typeof Symbol.prototype.toString !== 'function') { return false; }
	// if (String(sym) !== Symbol.prototype.toString.call(sym)) { return false; }

	var symVal = 42;
	obj[sym] = symVal;
	for (sym in obj) { return false; } // eslint-disable-line no-restricted-syntax
	if (typeof Object.keys === 'function' && Object.keys(obj).length !== 0) { return false; }

	if (typeof Object.getOwnPropertyNames === 'function' && Object.getOwnPropertyNames(obj).length !== 0) { return false; }

	var syms = Object.getOwnPropertySymbols(obj);
	if (syms.length !== 1 || syms[0] !== sym) { return false; }

	if (!Object.prototype.propertyIsEnumerable.call(obj, sym)) { return false; }

	if (typeof Object.getOwnPropertyDescriptor === 'function') {
		var descriptor = Object.getOwnPropertyDescriptor(obj, sym);
		if (descriptor.value !== symVal || descriptor.enumerable !== true) { return false; }
	}

	return true;
};


/***/ }),

/***/ "Fv1B":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.MODIFIER_KEY_NAMES = exports.DEFAULT_VERTICAL_SPACING = exports.FANG_HEIGHT_PX = exports.FANG_WIDTH_PX = exports.WEEKDAYS = exports.BLOCKED_MODIFIER = exports.DAY_SIZE = exports.OPEN_UP = exports.OPEN_DOWN = exports.ANCHOR_RIGHT = exports.ANCHOR_LEFT = exports.INFO_POSITION_AFTER = exports.INFO_POSITION_BEFORE = exports.INFO_POSITION_BOTTOM = exports.INFO_POSITION_TOP = exports.ICON_AFTER_POSITION = exports.ICON_BEFORE_POSITION = exports.VERTICAL_SCROLLABLE = exports.VERTICAL_ORIENTATION = exports.HORIZONTAL_ORIENTATION = exports.END_DATE = exports.START_DATE = exports.ISO_MONTH_FORMAT = exports.ISO_FORMAT = exports.DISPLAY_FORMAT = void 0;
var DISPLAY_FORMAT = 'L';
exports.DISPLAY_FORMAT = DISPLAY_FORMAT;
var ISO_FORMAT = 'YYYY-MM-DD';
exports.ISO_FORMAT = ISO_FORMAT;
var ISO_MONTH_FORMAT = 'YYYY-MM'; // TODO delete this line of dead code on next breaking change

exports.ISO_MONTH_FORMAT = ISO_MONTH_FORMAT;
var START_DATE = 'startDate';
exports.START_DATE = START_DATE;
var END_DATE = 'endDate';
exports.END_DATE = END_DATE;
var HORIZONTAL_ORIENTATION = 'horizontal';
exports.HORIZONTAL_ORIENTATION = HORIZONTAL_ORIENTATION;
var VERTICAL_ORIENTATION = 'vertical';
exports.VERTICAL_ORIENTATION = VERTICAL_ORIENTATION;
var VERTICAL_SCROLLABLE = 'verticalScrollable';
exports.VERTICAL_SCROLLABLE = VERTICAL_SCROLLABLE;
var ICON_BEFORE_POSITION = 'before';
exports.ICON_BEFORE_POSITION = ICON_BEFORE_POSITION;
var ICON_AFTER_POSITION = 'after';
exports.ICON_AFTER_POSITION = ICON_AFTER_POSITION;
var INFO_POSITION_TOP = 'top';
exports.INFO_POSITION_TOP = INFO_POSITION_TOP;
var INFO_POSITION_BOTTOM = 'bottom';
exports.INFO_POSITION_BOTTOM = INFO_POSITION_BOTTOM;
var INFO_POSITION_BEFORE = 'before';
exports.INFO_POSITION_BEFORE = INFO_POSITION_BEFORE;
var INFO_POSITION_AFTER = 'after';
exports.INFO_POSITION_AFTER = INFO_POSITION_AFTER;
var ANCHOR_LEFT = 'left';
exports.ANCHOR_LEFT = ANCHOR_LEFT;
var ANCHOR_RIGHT = 'right';
exports.ANCHOR_RIGHT = ANCHOR_RIGHT;
var OPEN_DOWN = 'down';
exports.OPEN_DOWN = OPEN_DOWN;
var OPEN_UP = 'up';
exports.OPEN_UP = OPEN_UP;
var DAY_SIZE = 39;
exports.DAY_SIZE = DAY_SIZE;
var BLOCKED_MODIFIER = 'blocked';
exports.BLOCKED_MODIFIER = BLOCKED_MODIFIER;
var WEEKDAYS = [0, 1, 2, 3, 4, 5, 6];
exports.WEEKDAYS = WEEKDAYS;
var FANG_WIDTH_PX = 20;
exports.FANG_WIDTH_PX = FANG_WIDTH_PX;
var FANG_HEIGHT_PX = 10;
exports.FANG_HEIGHT_PX = FANG_HEIGHT_PX;
var DEFAULT_VERTICAL_SPACING = 22;
exports.DEFAULT_VERTICAL_SPACING = DEFAULT_VERTICAL_SPACING;
var MODIFIER_KEY_NAMES = new Set(['Shift', 'Control', 'Alt', 'Meta']);
exports.MODIFIER_KEY_NAMES = MODIFIER_KEY_NAMES;

/***/ }),

/***/ "G+CP":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__("cDcd");
var external_React_default = /*#__PURE__*/__webpack_require__.n(external_React_);

// EXTERNAL MODULE: external "ReactDOM"
var external_ReactDOM_ = __webpack_require__("faye");
var external_ReactDOM_default = /*#__PURE__*/__webpack_require__.n(external_ReactDOM_);

// EXTERNAL MODULE: ./node_modules/react-outside-click-handler/index.js
var react_outside_click_handler = __webpack_require__("3gBW");
var react_outside_click_handler_default = /*#__PURE__*/__webpack_require__.n(react_outside_click_handler);

// EXTERNAL MODULE: external "moment"
var external_moment_ = __webpack_require__("wy2R");
var external_moment_default = /*#__PURE__*/__webpack_require__.n(external_moment_);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js
var objectWithoutPropertiesLoose = __webpack_require__("8OQS");
var objectWithoutPropertiesLoose_default = /*#__PURE__*/__webpack_require__.n(objectWithoutPropertiesLoose);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/extends.js
var helpers_extends = __webpack_require__("pVnL");
var extends_default = /*#__PURE__*/__webpack_require__.n(helpers_extends);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/inheritsLoose.js
var inheritsLoose = __webpack_require__("VbXa");
var inheritsLoose_default = /*#__PURE__*/__webpack_require__.n(inheritsLoose);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/assertThisInitialized.js
var assertThisInitialized = __webpack_require__("PJYZ");
var assertThisInitialized_default = /*#__PURE__*/__webpack_require__.n(assertThisInitialized);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/defineProperty.js
var defineProperty = __webpack_require__("lSNA");
var defineProperty_default = /*#__PURE__*/__webpack_require__.n(defineProperty);

// EXTERNAL MODULE: external "Popper"
var external_Popper_ = __webpack_require__("27z4");
var external_Popper_default = /*#__PURE__*/__webpack_require__.n(external_Popper_);

// EXTERNAL MODULE: ./node_modules/create-react-context/lib/index.js
var lib = __webpack_require__("/tz4");
var lib_default = /*#__PURE__*/__webpack_require__.n(lib);

// CONCATENATED MODULE: ./node_modules/react-popper/lib/esm/Manager.js






var ManagerContext = lib_default()({
  setReferenceNode: undefined,
  referenceNode: undefined
});

var Manager_Manager =
/*#__PURE__*/
function (_React$Component) {
  inheritsLoose_default()(Manager, _React$Component);

  function Manager() {
    var _this;

    _this = _React$Component.call(this) || this;

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "setReferenceNode", function (referenceNode) {
      if (!referenceNode || _this.state.context.referenceNode === referenceNode) {
        return;
      }

      _this.setState(function (_ref) {
        var context = _ref.context;
        return {
          context: extends_default()({}, context, {
            referenceNode: referenceNode
          })
        };
      });
    });

    _this.state = {
      context: {
        setReferenceNode: _this.setReferenceNode,
        referenceNode: undefined
      }
    };
    return _this;
  }

  var _proto = Manager.prototype;

  _proto.render = function render() {
    return external_React_["createElement"](ManagerContext.Provider, {
      value: this.state.context
    }, this.props.children);
  };

  return Manager;
}(external_React_["Component"]);


// CONCATENATED MODULE: ./node_modules/react-popper/lib/esm/utils.js
/**
 * Takes an argument and if it's an array, returns the first item in the array,
 * otherwise returns the argument. Used for Preact compatibility.
 */
var unwrapArray = function unwrapArray(arg) {
  return Array.isArray(arg) ? arg[0] : arg;
};
/**
 * Takes a maybe-undefined function and arbitrary args and invokes the function
 * only if it is defined.
 */

var safeInvoke = function safeInvoke(fn) {
  if (typeof fn === "function") {
    for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      args[_key - 1] = arguments[_key];
    }

    return fn.apply(void 0, args);
  }
};
// CONCATENATED MODULE: ./node_modules/react-popper/lib/esm/Popper.js









var initialStyle = {
  position: 'absolute',
  top: 0,
  left: 0,
  opacity: 0,
  pointerEvents: 'none'
};
var initialArrowStyle = {};
var Popper_InnerPopper =
/*#__PURE__*/
function (_React$Component) {
  inheritsLoose_default()(InnerPopper, _React$Component);

  function InnerPopper() {
    var _this;

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    _this = _React$Component.call.apply(_React$Component, [this].concat(args)) || this;

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "state", {
      data: undefined,
      placement: undefined
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "popperInstance", void 0);

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "popperNode", null);

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "arrowNode", null);

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "setPopperNode", function (popperNode) {
      if (!popperNode || _this.popperNode === popperNode) return;
      safeInvoke(_this.props.innerRef, popperNode);
      _this.popperNode = popperNode;

      _this.updatePopperInstance();
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "setArrowNode", function (arrowNode) {
      _this.arrowNode = arrowNode;
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "updateStateModifier", {
      enabled: true,
      order: 900,
      fn: function fn(data) {
        var placement = data.placement;

        _this.setState({
          data: data,
          placement: placement
        });

        return data;
      }
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "getOptions", function () {
      return {
        placement: _this.props.placement,
        eventsEnabled: _this.props.eventsEnabled,
        positionFixed: _this.props.positionFixed,
        modifiers: extends_default()({}, _this.props.modifiers, {
          arrow: extends_default()({}, _this.props.modifiers && _this.props.modifiers.arrow, {
            enabled: !!_this.arrowNode,
            element: _this.arrowNode
          }),
          applyStyle: {
            enabled: false
          },
          updateStateModifier: _this.updateStateModifier
        })
      };
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "getPopperStyle", function () {
      return !_this.popperNode || !_this.state.data ? initialStyle : extends_default()({
        position: _this.state.data.offsets.popper.position
      }, _this.state.data.styles);
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "getPopperPlacement", function () {
      return !_this.state.data ? undefined : _this.state.placement;
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "getArrowStyle", function () {
      return !_this.arrowNode || !_this.state.data ? initialArrowStyle : _this.state.data.arrowStyles;
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "getOutOfBoundariesState", function () {
      return _this.state.data ? _this.state.data.hide : undefined;
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "destroyPopperInstance", function () {
      if (!_this.popperInstance) return;

      _this.popperInstance.destroy();

      _this.popperInstance = null;
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "updatePopperInstance", function () {
      _this.destroyPopperInstance();

      var _assertThisInitialize = assertThisInitialized_default()(assertThisInitialized_default()(_this)),
          popperNode = _assertThisInitialize.popperNode;

      var referenceElement = _this.props.referenceElement;
      if (!referenceElement || !popperNode) return;
      _this.popperInstance = new external_Popper_default.a(referenceElement, popperNode, _this.getOptions());
    });

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "scheduleUpdate", function () {
      if (_this.popperInstance) {
        _this.popperInstance.scheduleUpdate();
      }
    });

    return _this;
  }

  var _proto = InnerPopper.prototype;

  _proto.componentDidUpdate = function componentDidUpdate(prevProps, prevState) {
    // If the Popper.js options have changed, update the instance (destroy + create)
    if (this.props.placement !== prevProps.placement || this.props.referenceElement !== prevProps.referenceElement || this.props.positionFixed !== prevProps.positionFixed) {
      this.updatePopperInstance();
    } else if (this.props.eventsEnabled !== prevProps.eventsEnabled && this.popperInstance) {
      this.props.eventsEnabled ? this.popperInstance.enableEventListeners() : this.popperInstance.disableEventListeners();
    } // A placement difference in state means popper determined a new placement
    // apart from the props value. By the time the popper element is rendered with
    // the new position Popper has already measured it, if the place change triggers
    // a size change it will result in a misaligned popper. So we schedule an update to be sure.


    if (prevState.placement !== this.state.placement) {
      this.scheduleUpdate();
    }
  };

  _proto.componentWillUnmount = function componentWillUnmount() {
    safeInvoke(this.props.innerRef, null);
    this.destroyPopperInstance();
  };

  _proto.render = function render() {
    return unwrapArray(this.props.children)({
      ref: this.setPopperNode,
      style: this.getPopperStyle(),
      placement: this.getPopperPlacement(),
      outOfBoundaries: this.getOutOfBoundariesState(),
      scheduleUpdate: this.scheduleUpdate,
      arrowProps: {
        ref: this.setArrowNode,
        style: this.getArrowStyle()
      }
    });
  };

  return InnerPopper;
}(external_React_["Component"]);

defineProperty_default()(Popper_InnerPopper, "defaultProps", {
  placement: 'bottom',
  eventsEnabled: true,
  referenceElement: undefined,
  positionFixed: false
});

var placements = external_Popper_default.a.placements;

function Popper(_ref) {
  var referenceElement = _ref.referenceElement,
      props = objectWithoutPropertiesLoose_default()(_ref, ["referenceElement"]);

  return external_React_["createElement"](ManagerContext.Consumer, null, function (_ref2) {
    var referenceNode = _ref2.referenceNode;
    return external_React_["createElement"](Popper_InnerPopper, extends_default()({
      referenceElement: referenceElement !== undefined ? referenceElement : referenceNode
    }, props));
  });
}
// EXTERNAL MODULE: ./node_modules/warning/warning.js
var warning = __webpack_require__("2W6z");
var warning_default = /*#__PURE__*/__webpack_require__.n(warning);

// CONCATENATED MODULE: ./node_modules/react-popper/lib/esm/Reference.js









var Reference_InnerReference =
/*#__PURE__*/
function (_React$Component) {
  inheritsLoose_default()(InnerReference, _React$Component);

  function InnerReference() {
    var _this;

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    _this = _React$Component.call.apply(_React$Component, [this].concat(args)) || this;

    defineProperty_default()(assertThisInitialized_default()(assertThisInitialized_default()(_this)), "refHandler", function (node) {
      safeInvoke(_this.props.innerRef, node);
      safeInvoke(_this.props.setReferenceNode, node);
    });

    return _this;
  }

  var _proto = InnerReference.prototype;

  _proto.render = function render() {
    warning_default()(Boolean(this.props.setReferenceNode), '`Reference` should not be used outside of a `Manager` component.');
    return unwrapArray(this.props.children)({
      ref: this.refHandler
    });
  };

  return InnerReference;
}(external_React_["Component"]);

function Reference(props) {
  return external_React_["createElement"](ManagerContext.Consumer, null, function (_ref) {
    var setReferenceNode = _ref.setReferenceNode;
    return external_React_["createElement"](Reference_InnerReference, extends_default()({
      setReferenceNode: setReferenceNode
    }, props));
  });
}
// CONCATENATED MODULE: ./node_modules/react-popper/lib/esm/index.js
// Public components



 // Public types
// EXTERNAL MODULE: ./node_modules/react-dates/initialize.js
var initialize = __webpack_require__("GG7f");

// EXTERNAL MODULE: ./node_modules/react-dates/lib/utils/isBeforeDay.js
var isBeforeDay = __webpack_require__("h6xH");
var isBeforeDay_default = /*#__PURE__*/__webpack_require__.n(isBeforeDay);

// EXTERNAL MODULE: ./node_modules/react-dates/lib/utils/toMomentObject.js
var toMomentObject = __webpack_require__("WmS1");
var toMomentObject_default = /*#__PURE__*/__webpack_require__.n(toMomentObject);

// EXTERNAL MODULE: ./node_modules/react-dates/lib/utils/toLocalizedDateString.js
var toLocalizedDateString = __webpack_require__("UyxP");
var toLocalizedDateString_default = /*#__PURE__*/__webpack_require__.n(toLocalizedDateString);

// EXTERNAL MODULE: ./node_modules/react-dates/index.js
var react_dates = __webpack_require__("qNGI");

// EXTERNAL MODULE: ./node_modules/react-dates/constants.js
var constants = __webpack_require__("5f7n");

// CONCATENATED MODULE: ./assets/babel/calendar.jsx
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "DatePicker", function() { return calendar_DatePicker; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createDatePicker", function() { return createDatePicker; });
function _typeof2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
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

function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  }

  return _assertThisInitialized(self);
}

function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) _setPrototypeOf(subClass, superClass);
}

function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}












var defaultProps = {
  form: null,
  // required props for a functional interactive DateRangePicker
  startDate: null,
  endDate: null,
  focusedInput: null,
  minimumNights: 1,
  maximumNights: 0,
  minimumDateRange: 0,
  maximumDateRange: 0,
  disablePastDates: true,
  disableFutureDates: false,
  isRTL: false,
  withFullScreenPortal: false,
  displayFormat: 'DD MM YY',
  // calendar presentation and interaction related props
  numberOfMonths: 2,
  disabled: false,
  keepOpenOnDateSelect: false,
  isDayBlocked: function isDayBlocked() {
    return false;
  },
  isDayHighlighted: function isDayHighlighted() {
    return false;
  },
  isOutsideRange: function isOutsideRange(day) {
    return !Object(react_dates["isInclusivelyAfterDay"])(day, external_moment_default()());
  },
  endDateOffset: undefined,
  getMinNightsForHoverDate: undefined,
  // Internationalization
  monthFormat: 'MMMM YYYY',
  weekDayFormat: 'dd',
  onFocus: function onFocus() {},
  onChange: function onChange() {},
  onClose: function onClose() {}
};
var calendar_DatePicker =
/*#__PURE__*/
function (_React$Component) {
  _inherits(DatePicker, _React$Component);

  function DatePicker(props) {
    var _this;

    _classCallCheck(this, DatePicker);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(DatePicker).call(this, props));

    if (typeof window.Popper === 'undefined') {
      throw new TypeError('The Calendar require Popper.js (https://popper.js.org/)');
    }

    _this.form = props.form;
    var elements = _this.form.elements;
    _this.state = {
      focusedInput: null,
      startDate: toMomentObject_default()(elements['check_in'].get()),
      endDate: toMomentObject_default()(elements['check_out'].get()),
      isDayPickerFocused: false
    };
    _this.onDatesChange = _this.onDatesChange.bind(_assertThisInitialized(_this));
    _this.onFocusChange = _this.onFocusChange.bind(_assertThisInitialized(_this));
    _this.onOutsideClick = _this.onOutsideClick.bind(_assertThisInitialized(_this));
    _this.onDayPickerBlur = _this.onDayPickerBlur.bind(_assertThisInitialized(_this));
    _this.isOutsideRange = _this.isOutsideRange.bind(_assertThisInitialized(_this));
    return _this;
  }

  _createClass(DatePicker, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.getFormElement('check_in_alt').on('change', this._onStartDateChange.bind(this)).on('focus', this._onStartDateFocus.bind(this)).on('keydown', this._clearInputValues.bind(this, 'startDate'));
      this.getFormElement('check_out_alt').on('focus', this._onEndDateFocus.bind(this)).on('change', this._onEndDateChange.bind(this)).on('keydown', this._clearInputValues.bind(this, 'endDate'));
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      this.getFormElement('check_in_alt').off('change focus keydown');
      this.getFormElement('check_out_alt').off('change focus keydown');
    }
  }, {
    key: "open",
    value: function open(focusedInput) {
      this.onFocusChange(focusedInput ? focusedInput : constants["START_DATE"]);
    }
  }, {
    key: "close",
    value: function close() {
      this.onFocusChange(null);
    }
  }, {
    key: "onFocusChange",
    value: function onFocusChange(focusedInput) {
      this.setState({
        focusedInput: focusedInput
      }, this._onInputFocus.bind(this, focusedInput));
    }
  }, {
    key: "_onInputFocus",
    value: function _onInputFocus(focusedInput) {
      var onFocus = this.props.onFocus;

      if (focusedInput === constants["START_DATE"] && !this.getFormElement('check_in_alt').is(':focus')) {
        this.getFormElement('check_in_alt')[0].focus();
      } else if (focusedInput === constants["END_DATE"] && !this.getFormElement('check_out_alt').is(':focus')) {
        this.getFormElement('check_out_alt')[0].focus();
      }

      onFocus(focusedInput);
    }
  }, {
    key: "onDatesChange",
    value: function onDatesChange(_ref) {
      var _this2 = this;

      var startDate = _ref.startDate,
          endDate = _ref.endDate;
      var onChange = this.props.onChange;
      this.setState({
        startDate: startDate,
        endDate: endDate
      }, function () {
        var elements = _this2.form.elements;
        elements['check_in'].set(startDate ? startDate.format(constants["ISO_FORMAT"]) : '');
        elements['check_out'].set(endDate ? endDate.format(constants["ISO_FORMAT"]) : '');
        onChange({
          startDate: startDate,
          endDate: endDate
        });
      });
    }
  }, {
    key: "clearDates",
    value: function clearDates() {
      var reopenPickerOnClearDates = this.props.reopenPickerOnClearDates;
      this.onDatesChange({
        startDate: null,
        endDate: null
      });

      if (reopenPickerOnClearDates) {
        this.onFocusChange(constants["START_DATE"]);
      }
    }
  }, {
    key: "isOpened",
    value: function isOpened() {
      var focusedInput = this.state.focusedInput;
      return focusedInput === constants["START_DATE"] || focusedInput === constants["END_DATE"];
    }
  }, {
    key: "_onStartDateChange",
    value: function _onStartDateChange(e) {
      var startDateString = e.target.value;
      var endDate = this.state.endDate;
      var _this$props = this.props,
          disabled = _this$props.disabled,
          minimumNights = _this$props.minimumNights,
          isOutsideRange = _this$props.isOutsideRange;
      var startDate = toMomentObject_default()(startDateString, this.getDisplayFormat());
      console.log(startDate);
      var isEndDateBeforeStartDate = startDate && isBeforeDay_default()(endDate, startDate.clone().add(minimumNights, 'days'));
      var isStartDateValid = startDate && !isOutsideRange(startDate) && !(disabled === constants["END_DATE"] && isEndDateBeforeStartDate);

      if (isStartDateValid) {
        if (isEndDateBeforeStartDate) {
          endDate = null;
        }

        this.onDatesChange({
          startDate: startDate,
          endDate: endDate
        });
        this.onFocusChange(constants["END_DATE"]);
      } else {
        this.onDatesChange({
          startDate: null,
          endDate: endDate
        });
      }
    }
  }, {
    key: "_onEndDateChange",
    value: function _onEndDateChange(e) {
      var endDateString = e.target.value;
      var startDate = this.state.startDate;
      var _this$props2 = this.props,
          minimumNights = _this$props2.minimumNights,
          isOutsideRange = _this$props2.isOutsideRange,
          keepOpenOnDateSelect = _this$props2.keepOpenOnDateSelect;
      var endDate = toMomentObject_default()(endDateString, this.getDisplayFormat());
      var isEndDateValid = endDate && !isOutsideRange(endDate) && !(startDate && isBeforeDay_default()(endDate, startDate.clone().add(minimumNights, 'days')));

      if (isEndDateValid) {
        this.onDatesChange({
          startDate: startDate,
          endDate: endDate
        });
        if (!keepOpenOnDateSelect) this.onClearFocus();
      } else {
        this.onDatesChange({
          startDate: startDate,
          endDate: null
        });
      }
    }
  }, {
    key: "_onStartDateFocus",
    value: function _onStartDateFocus() {
      var _this$props3 = this.props,
          disabled = _this$props3.disabled,
          focusedInput = _this$props3.focusedInput;

      if (this.isOpened() && focusedInput === constants["START_DATE"]) {
        return;
      }

      if (!disabled || disabled === constants["END_DATE"]) {
        this.onFocusChange(constants["START_DATE"]);
      }
    }
  }, {
    key: "_onEndDateFocus",
    value: function _onEndDateFocus() {
      var _this$state = this.state,
          startDate = _this$state.startDate,
          focusedInput = _this$state.focusedInput;
      var _this$props4 = this.props,
          disabled = _this$props4.disabled,
          withFullScreenPortal = _this$props4.withFullScreenPortal;

      if (this.isOpened() && focusedInput === constants["END_DATE"]) {
        return;
      } // When the datepicker is full screen, we never want to focus the end date first
      // because there's no indication that that is the case once the datepicker is open and it
      // might confuse the user


      if (!startDate && withFullScreenPortal && (!disabled || disabled === constants["END_DATE"])) {
        this.onFocusChange(constants["START_DATE"]);
      } else if (!disabled || disabled === constants["START_DATE"]) {
        this.onFocusChange(constants["END_DATE"]);
      }
    }
  }, {
    key: "_clearInputValues",
    value: function _clearInputValues(type, event) {
      if (event.which === 8 || event.which === 46) {
        if (type === 'endDate') {
          this.onDatesChange({
            startDate: this.state.startDate,
            endDate: null
          });
        } else {
          this.clearDates();
        }
      }
    }
  }, {
    key: "onClearFocus",
    value: function onClearFocus() {
      this.close();
    }
  }, {
    key: "getDateString",
    value: function getDateString(date) {
      var displayFormat = this.getDisplayFormat();

      if (date && displayFormat) {
        return date && date.format(displayFormat);
      }

      return toLocalizedDateString_default()(date);
    }
  }, {
    key: "getDisplayFormat",
    value: function getDisplayFormat() {
      var displayFormat = this.props.displayFormat;
      return typeof displayFormat === 'string' ? displayFormat : displayFormat();
    }
    /**
     * Gets a DOM element in the form.
     *
     * @param  {string} name
     * @return {jQuery}
     */

  }, {
    key: "getFormElement",
    value: function getFormElement(name) {
      var elements = this.form.elements;

      if (elements.hasOwnProperty(name)) {
        return elements[name].element;
      }
    }
  }, {
    key: "onOutsideClick",
    value: function onOutsideClick(e) {
      if (!this.isOpened()) {
        return;
      }

      if (this._isClickOnInputs(e)) {
        return;
      }

      this.setState({
        isDayPickerFocused: false
      });
      this.close();
    }
  }, {
    key: "_isClickOnInputs",
    value: function _isClickOnInputs(e) {
      var inputs = [this.getFormElement('check_in'), this.getFormElement('check_out'), this.getFormElement('check_in_alt'), this.getFormElement('check_out_alt')];
      var clicked = false;
      inputs.forEach(function (element) {
        if (element[0] && element[0].contains(e.target)) {
          clicked = true;
        }
      });
      return clicked;
    }
  }, {
    key: "onDayPickerFocus",
    value: function onDayPickerFocus() {
      /*const { focusedInput, onFocusChange } = this.props
      if (!focusedInput) onFocusChange(START_DATE)
       this.setState({
        isDayPickerFocused: true,
      })*/
    }
  }, {
    key: "onDayPickerBlur",
    value: function onDayPickerBlur() {
      this.setState({
        isDayPickerFocused: false
      });
    }
  }, {
    key: "getReferenceElement",
    value: function getReferenceElement(focusedInput) {
      var numberOfMonths = this.props.numberOfMonths;

      if (!focusedInput) {
        return;
      }

      var referenceElement = focusedInput === constants["START_DATE"] ? this.getFormElement('check_in_alt') : this.getFormElement('check_out_alt');

      if (numberOfMonths > 1 && referenceElement.closest('.abrs-searchbox__group-wrap').length > 0) {
        return referenceElement.closest('.abrs-searchbox__group')[0];
      }

      return referenceElement.closest('.abrs-searchbox__box')[0];
    }
  }, {
    key: "isOutsideRange",
    value: function isOutsideRange(day) {
      var _this$state2 = this.state,
          startDate = _this$state2.startDate,
          focusedInput = _this$state2.focusedInput;
      var _this$props5 = this.props,
          disablePastDates = _this$props5.disablePastDates,
          disableFutureDates = _this$props5.disableFutureDates,
          maximumNights = _this$props5.maximumNights,
          isOutsideRange = _this$props5.isOutsideRange,
          minimumDateRange = _this$props5.minimumDateRange,
          maximumDateRange = _this$props5.maximumDateRange;
      var today = external_moment_default()();

      if (startDate && focusedInput === 'endDate' && day.isBefore(startDate, 'day')) {
        return true;
      }

      if (disablePastDates && today.isAfter(day, 'day')) {
        return true;
      }

      if (disableFutureDates && today.isBefore(day, 'day')) {
        return true;
      }

      if (maximumNights && startDate) {
        var maxDate = startDate.clone().add(maximumNights, 'day');

        if (focusedInput === 'endDate' && maxDate.isBefore(day)) {
          return true;
        }
      }

      if (minimumDateRange > 0 || maximumDateRange > 0) {
        return !Object(react_dates["isInclusivelyAfterDay"])(day, today.clone().add(minimumDateRange, 'days')) || maximumDateRange && Object(react_dates["isInclusivelyAfterDay"])(day, today.clone().add(maximumDateRange, 'days'));
      }

      if (isOutsideRange) {
        return isOutsideRange(day);
      }

      return false;
    }
  }, {
    key: "render",
    value: function render() {
      var _this3 = this;

      var _this$state3 = this.state,
          focusedInput = _this$state3.focusedInput,
          startDate = _this$state3.startDate,
          endDate = _this$state3.endDate,
          isDayPickerFocused = _this$state3.isDayPickerFocused;
      var _this$props6 = this.props,
          disabled = _this$props6.disabled,
          numberOfMonths = _this$props6.numberOfMonths,
          minimumNights = _this$props6.minimumNights,
          keepOpenOnDateSelect = _this$props6.keepOpenOnDateSelect,
          isDayBlocked = _this$props6.isDayBlocked,
          isDayHighlighted = _this$props6.isDayHighlighted,
          isRTL = _this$props6.isRTL,
          endDateOffset = _this$props6.endDateOffset,
          getMinNightsForHoverDate = _this$props6.getMinNightsForHoverDate;
      var modifiers = {
        preventOverflow: {
          enabled: false
        },
        hide: {
          enabled: false
        },
        offset: {
          offset: 0
        },
        flip: {
          behavior: 'flip'
        }
      };
      return focusedInput && external_React_default.a.createElement(react_outside_click_handler_default.a, {
        onOutsideClick: this.onOutsideClick
      }, external_React_default.a.createElement(Popper, {
        placement: "bottom-end",
        referenceElement: this.getReferenceElement(focusedInput),
        modifiers: modifiers
      }, function (_ref2) {
        var ref = _ref2.ref,
            style = _ref2.style,
            placement = _ref2.placement,
            arrowProps = _ref2.arrowProps;
        return external_React_default.a.createElement("div", {
          className: "DayPickerPopper",
          ref: ref,
          style: style,
          "data-placement": placement
        }, external_React_default.a.createElement("div", {
          className: "DayPickerPopper__Arrow",
          ref: arrowProps.ref,
          style: arrowProps.style
        }), external_React_default.a.createElement(react_dates["DayPickerRangeController"], {
          isRTL: isRTL,
          endDate: endDate,
          startDate: startDate,
          focusedInput: focusedInput,
          onDatesChange: _this3.onDatesChange,
          onFocusChange: _this3.onFocusChange,
          disabled: disabled,
          numberOfMonths: numberOfMonths,
          minimumNights: minimumNights,
          keepOpenOnDateSelect: keepOpenOnDateSelect,
          isDayBlocked: isDayBlocked,
          isDayHighlighted: isDayHighlighted,
          isOutsideRange: _this3.isOutsideRange,
          isFocused: isDayPickerFocused,
          onBlur: _this3.onDayPickerBlur,
          hideKeyboardShortcutsPanel: true,
          endDateOffset: endDateOffset,
          getMinNightsForHoverDate: getMinNightsForHoverDate
        }));
      }));
    }
  }]);

  return DatePicker;
}(external_React_default.a.Component);
calendar_DatePicker.defaultProps = defaultProps;
function createDatePicker(searchform, props) {
  var root = searchform.getRootElement().querySelector('.abrs-searchbox__dates');
  return external_ReactDOM_default.a.render(external_React_default.a.createElement(calendar_DatePicker, _extends({
    form: searchform
  }, props)), root);
}

(function () {
  window.createReactDatePicker = createDatePicker;
})();

/***/ }),

/***/ "GET3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = exports.PureCustomizableCalendarDay = exports.selectedStyles = exports.lastInRangeStyles = exports.selectedSpanStyles = exports.hoveredSpanStyles = exports.blockedOutOfRangeStyles = exports.blockedCalendarStyles = exports.blockedMinNightsStyles = exports.highlightedCalendarStyles = exports.outsideStyles = exports.defaultStyles = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _reactMomentProptypes = _interopRequireDefault(__webpack_require__("XGBb"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _raf = _interopRequireDefault(__webpack_require__("xEkU"));

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _getCalendarDaySettings = _interopRequireDefault(__webpack_require__("Ae65"));

var _constants = __webpack_require__("Fv1B");

var _DefaultTheme = _interopRequireDefault(__webpack_require__("xOhs"));

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var color = _DefaultTheme["default"].reactDates.color;

function getStyles(stylesObj, isHovered) {
  if (!stylesObj) return null;
  var hover = stylesObj.hover;

  if (isHovered && hover) {
    return hover;
  }

  return stylesObj;
}

var DayStyleShape =  false ? undefined : {};
var propTypes =  false ? undefined : {};
var defaultStyles = {
  border: "1px solid ".concat(color.core.borderLight),
  color: color.text,
  background: color.background,
  hover: {
    background: color.core.borderLight,
    border: "1px solid ".concat(color.core.borderLight),
    color: 'inherit'
  }
};
exports.defaultStyles = defaultStyles;
var outsideStyles = {
  background: color.outside.backgroundColor,
  border: 0,
  color: color.outside.color
};
exports.outsideStyles = outsideStyles;
var highlightedCalendarStyles = {
  background: color.highlighted.backgroundColor,
  color: color.highlighted.color,
  hover: {
    background: color.highlighted.backgroundColor_hover,
    color: color.highlighted.color_active
  }
};
exports.highlightedCalendarStyles = highlightedCalendarStyles;
var blockedMinNightsStyles = {
  background: color.minimumNights.backgroundColor,
  border: "1px solid ".concat(color.minimumNights.borderColor),
  color: color.minimumNights.color,
  hover: {
    background: color.minimumNights.backgroundColor_hover,
    color: color.minimumNights.color_active
  }
};
exports.blockedMinNightsStyles = blockedMinNightsStyles;
var blockedCalendarStyles = {
  background: color.blocked_calendar.backgroundColor,
  border: "1px solid ".concat(color.blocked_calendar.borderColor),
  color: color.blocked_calendar.color,
  hover: {
    background: color.blocked_calendar.backgroundColor_hover,
    border: "1px solid ".concat(color.blocked_calendar.borderColor),
    color: color.blocked_calendar.color_active
  }
};
exports.blockedCalendarStyles = blockedCalendarStyles;
var blockedOutOfRangeStyles = {
  background: color.blocked_out_of_range.backgroundColor,
  border: "1px solid ".concat(color.blocked_out_of_range.borderColor),
  color: color.blocked_out_of_range.color,
  hover: {
    background: color.blocked_out_of_range.backgroundColor_hover,
    border: "1px solid ".concat(color.blocked_out_of_range.borderColor),
    color: color.blocked_out_of_range.color_active
  }
};
exports.blockedOutOfRangeStyles = blockedOutOfRangeStyles;
var hoveredSpanStyles = {
  background: color.hoveredSpan.backgroundColor,
  border: "1px double ".concat(color.hoveredSpan.borderColor),
  color: color.hoveredSpan.color,
  hover: {
    background: color.hoveredSpan.backgroundColor_hover,
    border: "1px double ".concat(color.hoveredSpan.borderColor),
    color: color.hoveredSpan.color_active
  }
};
exports.hoveredSpanStyles = hoveredSpanStyles;
var selectedSpanStyles = {
  background: color.selectedSpan.backgroundColor,
  border: "1px double ".concat(color.selectedSpan.borderColor),
  color: color.selectedSpan.color,
  hover: {
    background: color.selectedSpan.backgroundColor_hover,
    border: "1px double ".concat(color.selectedSpan.borderColor),
    color: color.selectedSpan.color_active
  }
};
exports.selectedSpanStyles = selectedSpanStyles;
var lastInRangeStyles = {};
exports.lastInRangeStyles = lastInRangeStyles;
var selectedStyles = {
  background: color.selected.backgroundColor,
  border: "1px double ".concat(color.selected.borderColor),
  color: color.selected.color,
  hover: {
    background: color.selected.backgroundColor_hover,
    border: "1px double ".concat(color.selected.borderColor),
    color: color.selected.color_active
  }
};
exports.selectedStyles = selectedStyles;
var defaultProps = {
  day: (0, _moment["default"])(),
  daySize: _constants.DAY_SIZE,
  isOutsideDay: false,
  modifiers: new Set(),
  isFocused: false,
  tabIndex: -1,
  onDayClick: function onDayClick() {},
  onDayMouseEnter: function onDayMouseEnter() {},
  onDayMouseLeave: function onDayMouseLeave() {},
  renderDayContents: null,
  ariaLabelFormat: 'dddd, LL',
  // style defaults
  defaultStyles: defaultStyles,
  outsideStyles: outsideStyles,
  todayStyles: {},
  highlightedCalendarStyles: highlightedCalendarStyles,
  blockedMinNightsStyles: blockedMinNightsStyles,
  blockedCalendarStyles: blockedCalendarStyles,
  blockedOutOfRangeStyles: blockedOutOfRangeStyles,
  hoveredSpanStyles: hoveredSpanStyles,
  selectedSpanStyles: selectedSpanStyles,
  lastInRangeStyles: lastInRangeStyles,
  selectedStyles: selectedStyles,
  selectedStartStyles: {},
  selectedEndStyles: {},
  afterHoveredStartStyles: {},
  firstDayOfWeekStyles: {},
  lastDayOfWeekStyles: {},
  hoveredStartFirstPossibleEndStyles: {},
  hoveredStartBlockedMinNightsStyles: {},
  // internationalization
  phrases: _defaultPhrases.CalendarDayPhrases
};

var CustomizableCalendarDay =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(CustomizableCalendarDay, _ref);
  var _proto = CustomizableCalendarDay.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function CustomizableCalendarDay() {
    var _this;

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    _this = _ref.call.apply(_ref, [this].concat(args)) || this;
    _this.state = {
      isHovered: false
    };
    _this.setButtonRef = _this.setButtonRef.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.componentDidUpdate = function componentDidUpdate(prevProps) {
    var _this2 = this;

    var _this$props = this.props,
        isFocused = _this$props.isFocused,
        tabIndex = _this$props.tabIndex;

    if (tabIndex === 0) {
      if (isFocused || tabIndex !== prevProps.tabIndex) {
        (0, _raf["default"])(function () {
          if (_this2.buttonRef) {
            _this2.buttonRef.focus();
          }
        });
      }
    }
  };

  _proto.onDayClick = function onDayClick(day, e) {
    var onDayClick = this.props.onDayClick;
    onDayClick(day, e);
  };

  _proto.onDayMouseEnter = function onDayMouseEnter(day, e) {
    var onDayMouseEnter = this.props.onDayMouseEnter;
    this.setState({
      isHovered: true
    });
    onDayMouseEnter(day, e);
  };

  _proto.onDayMouseLeave = function onDayMouseLeave(day, e) {
    var onDayMouseLeave = this.props.onDayMouseLeave;
    this.setState({
      isHovered: false
    });
    onDayMouseLeave(day, e);
  };

  _proto.onKeyDown = function onKeyDown(day, e) {
    var onDayClick = this.props.onDayClick;
    var key = e.key;

    if (key === 'Enter' || key === ' ') {
      onDayClick(day, e);
    }
  };

  _proto.setButtonRef = function setButtonRef(ref) {
    this.buttonRef = ref;
  };

  _proto.render = function render() {
    var _this3 = this;

    var _this$props2 = this.props,
        css = _this$props2.css,
        day = _this$props2.day,
        ariaLabelFormat = _this$props2.ariaLabelFormat,
        daySize = _this$props2.daySize,
        isOutsideDay = _this$props2.isOutsideDay,
        modifiers = _this$props2.modifiers,
        tabIndex = _this$props2.tabIndex,
        renderDayContents = _this$props2.renderDayContents,
        styles = _this$props2.styles,
        phrases = _this$props2.phrases,
        defaultStylesWithHover = _this$props2.defaultStyles,
        outsideStylesWithHover = _this$props2.outsideStyles,
        todayStylesWithHover = _this$props2.todayStyles,
        firstDayOfWeekStylesWithHover = _this$props2.firstDayOfWeekStyles,
        lastDayOfWeekStylesWithHover = _this$props2.lastDayOfWeekStyles,
        highlightedCalendarStylesWithHover = _this$props2.highlightedCalendarStyles,
        blockedMinNightsStylesWithHover = _this$props2.blockedMinNightsStyles,
        blockedCalendarStylesWithHover = _this$props2.blockedCalendarStyles,
        blockedOutOfRangeStylesWithHover = _this$props2.blockedOutOfRangeStyles,
        hoveredSpanStylesWithHover = _this$props2.hoveredSpanStyles,
        selectedSpanStylesWithHover = _this$props2.selectedSpanStyles,
        lastInRangeStylesWithHover = _this$props2.lastInRangeStyles,
        selectedStylesWithHover = _this$props2.selectedStyles,
        selectedStartStylesWithHover = _this$props2.selectedStartStyles,
        selectedEndStylesWithHover = _this$props2.selectedEndStyles,
        afterHoveredStartStylesWithHover = _this$props2.afterHoveredStartStyles,
        hoveredStartFirstPossibleEndStylesWithHover = _this$props2.hoveredStartFirstPossibleEndStyles,
        hoveredStartBlockedMinNightsStylesWithHover = _this$props2.hoveredStartBlockedMinNightsStyles;
    var isHovered = this.state.isHovered;
    if (!day) return _react["default"].createElement("td", null);

    var _getCalendarDaySettin = (0, _getCalendarDaySettings["default"])(day, ariaLabelFormat, daySize, modifiers, phrases),
        daySizeStyles = _getCalendarDaySettin.daySizeStyles,
        useDefaultCursor = _getCalendarDaySettin.useDefaultCursor,
        selected = _getCalendarDaySettin.selected,
        hoveredSpan = _getCalendarDaySettin.hoveredSpan,
        isOutsideRange = _getCalendarDaySettin.isOutsideRange,
        ariaLabel = _getCalendarDaySettin.ariaLabel;

    return _react["default"].createElement("td", (0, _extends2["default"])({}, css(styles.CalendarDay, useDefaultCursor && styles.CalendarDay__defaultCursor, daySizeStyles, getStyles(defaultStylesWithHover, isHovered), isOutsideDay && getStyles(outsideStylesWithHover, isHovered), modifiers.has('today') && getStyles(todayStylesWithHover, isHovered), modifiers.has('first-day-of-week') && getStyles(firstDayOfWeekStylesWithHover, isHovered), modifiers.has('last-day-of-week') && getStyles(lastDayOfWeekStylesWithHover, isHovered), modifiers.has('hovered-start-first-possible-end') && getStyles(hoveredStartFirstPossibleEndStylesWithHover, isHovered), modifiers.has('hovered-start-blocked-minimum-nights') && getStyles(hoveredStartBlockedMinNightsStylesWithHover, isHovered), modifiers.has('highlighted-calendar') && getStyles(highlightedCalendarStylesWithHover, isHovered), modifiers.has('blocked-minimum-nights') && getStyles(blockedMinNightsStylesWithHover, isHovered), modifiers.has('blocked-calendar') && getStyles(blockedCalendarStylesWithHover, isHovered), hoveredSpan && getStyles(hoveredSpanStylesWithHover, isHovered), modifiers.has('after-hovered-start') && getStyles(afterHoveredStartStylesWithHover, isHovered), modifiers.has('selected-span') && getStyles(selectedSpanStylesWithHover, isHovered), modifiers.has('last-in-range') && getStyles(lastInRangeStylesWithHover, isHovered), selected && getStyles(selectedStylesWithHover, isHovered), modifiers.has('selected-start') && getStyles(selectedStartStylesWithHover, isHovered), modifiers.has('selected-end') && getStyles(selectedEndStylesWithHover, isHovered), isOutsideRange && getStyles(blockedOutOfRangeStylesWithHover, isHovered)), {
      role: "button" // eslint-disable-line jsx-a11y/no-noninteractive-element-to-interactive-role
      ,
      ref: this.setButtonRef,
      "aria-disabled": modifiers.has('blocked'),
      "aria-label": ariaLabel,
      onMouseEnter: function onMouseEnter(e) {
        _this3.onDayMouseEnter(day, e);
      },
      onMouseLeave: function onMouseLeave(e) {
        _this3.onDayMouseLeave(day, e);
      },
      onMouseUp: function onMouseUp(e) {
        e.currentTarget.blur();
      },
      onClick: function onClick(e) {
        _this3.onDayClick(day, e);
      },
      onKeyDown: function onKeyDown(e) {
        _this3.onKeyDown(day, e);
      },
      tabIndex: tabIndex
    }), renderDayContents ? renderDayContents(day, modifiers) : day.format('D'));
  };

  return CustomizableCalendarDay;
}(_react["default"].PureComponent || _react["default"].Component);

exports.PureCustomizableCalendarDay = CustomizableCalendarDay;
CustomizableCalendarDay.propTypes =  false ? undefined : {};
CustomizableCalendarDay.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref2) {
  var font = _ref2.reactDates.font;
  return {
    CalendarDay: {
      boxSizing: 'border-box',
      cursor: 'pointer',
      fontSize: font.size,
      textAlign: 'center',
      ':active': {
        outline: 0
      }
    },
    CalendarDay__defaultCursor: {
      cursor: 'default'
    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(CustomizableCalendarDay);

exports["default"] = _default;

/***/ }),

/***/ "GG7f":
/***/ (function(module, exports, __webpack_require__) {

// eslint-disable-next-line import/no-unresolved
__webpack_require__("H24B");


/***/ }),

/***/ "Gn0q":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var define = __webpack_require__("82c2");
var getPolyfill = __webpack_require__("5yQQ");

module.exports = function shimContains() {
	var polyfill = getPolyfill();
	if (typeof document !== 'undefined') {
		define(
			document,
			{ contains: polyfill },
			{ contains: function () { return document.contains !== polyfill; } }
		);
		if (typeof Element !== 'undefined') {
			define(
				Element.prototype,
				{ contains: polyfill },
				{ contains: function () { return Element.prototype.contains !== polyfill; } }
			);
		}
	}
	return polyfill;
};


/***/ }),

/***/ "GoyQ":
/***/ (function(module, exports) {

/**
 * Checks if `value` is the
 * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
 * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(_.noop);
 * // => true
 *
 * _.isObject(null);
 * // => false
 */
function isObject(value) {
  var type = typeof value;
  return value != null && (type == 'object' || type == 'function');
}

module.exports = isObject;


/***/ }),

/***/ "H24B":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

var _registerCSSInterfaceWithDefaultTheme = _interopRequireDefault(__webpack_require__("TUyu"));

(0, _registerCSSInterfaceWithDefaultTheme["default"])();

/***/ }),

/***/ "HUEL":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _noflip = _interopRequireDefault(__webpack_require__("2WTt"));

var _DateInput = _interopRequireDefault(__webpack_require__("Dv0f"));

var _IconPositionShape = _interopRequireDefault(__webpack_require__("Bh6R"));

var _CloseButton = _interopRequireDefault(__webpack_require__("xEte"));

var _CalendarIcon = _interopRequireDefault(__webpack_require__("LfrC"));

var _OpenDirectionShape = _interopRequireDefault(__webpack_require__("iuLr"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  children: null,
  placeholder: 'Select Date',
  ariaLabel: undefined,
  displayValue: '',
  screenReaderMessage: '',
  focused: false,
  isFocused: false,
  disabled: false,
  required: false,
  readOnly: false,
  openDirection: _constants.OPEN_DOWN,
  showCaret: false,
  showClearDate: false,
  showDefaultInputIcon: false,
  inputIconPosition: _constants.ICON_BEFORE_POSITION,
  customCloseIcon: null,
  customInputIcon: null,
  isRTL: false,
  noBorder: false,
  block: false,
  small: false,
  regular: false,
  verticalSpacing: undefined,
  onChange: function onChange() {},
  onClearDate: function onClearDate() {},
  onFocus: function onFocus() {},
  onKeyDownShiftTab: function onKeyDownShiftTab() {},
  onKeyDownTab: function onKeyDownTab() {},
  onKeyDownArrowDown: function onKeyDownArrowDown() {},
  onKeyDownQuestionMark: function onKeyDownQuestionMark() {},
  // i18n
  phrases: _defaultPhrases.SingleDatePickerInputPhrases
};

function SingleDatePickerInput(_ref) {
  var id = _ref.id,
      children = _ref.children,
      css = _ref.css,
      placeholder = _ref.placeholder,
      ariaLabel = _ref.ariaLabel,
      displayValue = _ref.displayValue,
      focused = _ref.focused,
      isFocused = _ref.isFocused,
      disabled = _ref.disabled,
      required = _ref.required,
      readOnly = _ref.readOnly,
      showCaret = _ref.showCaret,
      showClearDate = _ref.showClearDate,
      showDefaultInputIcon = _ref.showDefaultInputIcon,
      inputIconPosition = _ref.inputIconPosition,
      phrases = _ref.phrases,
      onClearDate = _ref.onClearDate,
      onChange = _ref.onChange,
      onFocus = _ref.onFocus,
      onKeyDownShiftTab = _ref.onKeyDownShiftTab,
      onKeyDownTab = _ref.onKeyDownTab,
      onKeyDownArrowDown = _ref.onKeyDownArrowDown,
      onKeyDownQuestionMark = _ref.onKeyDownQuestionMark,
      screenReaderMessage = _ref.screenReaderMessage,
      customCloseIcon = _ref.customCloseIcon,
      customInputIcon = _ref.customInputIcon,
      openDirection = _ref.openDirection,
      isRTL = _ref.isRTL,
      noBorder = _ref.noBorder,
      block = _ref.block,
      small = _ref.small,
      regular = _ref.regular,
      verticalSpacing = _ref.verticalSpacing,
      styles = _ref.styles;

  var calendarIcon = customInputIcon || _react["default"].createElement(_CalendarIcon["default"], css(styles.SingleDatePickerInput_calendarIcon_svg));

  var closeIcon = customCloseIcon || _react["default"].createElement(_CloseButton["default"], css(styles.SingleDatePickerInput_clearDate_svg, small && styles.SingleDatePickerInput_clearDate_svg__small));

  var screenReaderText = screenReaderMessage || phrases.keyboardForwardNavigationInstructions;

  var inputIcon = (showDefaultInputIcon || customInputIcon !== null) && _react["default"].createElement("button", (0, _extends2["default"])({}, css(styles.SingleDatePickerInput_calendarIcon), {
    type: "button",
    disabled: disabled,
    "aria-label": phrases.focusStartDate,
    onClick: onFocus
  }), calendarIcon);

  return _react["default"].createElement("div", css(styles.SingleDatePickerInput, disabled && styles.SingleDatePickerInput__disabled, isRTL && styles.SingleDatePickerInput__rtl, !noBorder && styles.SingleDatePickerInput__withBorder, block && styles.SingleDatePickerInput__block, showClearDate && styles.SingleDatePickerInput__showClearDate), inputIconPosition === _constants.ICON_BEFORE_POSITION && inputIcon, _react["default"].createElement(_DateInput["default"], {
    id: id,
    placeholder: placeholder,
    ariaLabel: ariaLabel,
    displayValue: displayValue,
    screenReaderMessage: screenReaderText,
    focused: focused,
    isFocused: isFocused,
    disabled: disabled,
    required: required,
    readOnly: readOnly,
    showCaret: showCaret,
    onChange: onChange,
    onFocus: onFocus,
    onKeyDownShiftTab: onKeyDownShiftTab,
    onKeyDownTab: onKeyDownTab,
    onKeyDownArrowDown: onKeyDownArrowDown,
    onKeyDownQuestionMark: onKeyDownQuestionMark,
    openDirection: openDirection,
    verticalSpacing: verticalSpacing,
    small: small,
    regular: regular,
    block: block
  }), children, showClearDate && _react["default"].createElement("button", (0, _extends2["default"])({}, css(styles.SingleDatePickerInput_clearDate, small && styles.SingleDatePickerInput_clearDate__small, !customCloseIcon && styles.SingleDatePickerInput_clearDate__default, !displayValue && styles.SingleDatePickerInput_clearDate__hide), {
    type: "button",
    "aria-label": phrases.clearDate,
    disabled: disabled,
    onClick: onClearDate
  }), closeIcon), inputIconPosition === _constants.ICON_AFTER_POSITION && inputIcon);
}

SingleDatePickerInput.propTypes =  false ? undefined : {};
SingleDatePickerInput.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref2) {
  var _ref2$reactDates = _ref2.reactDates,
      border = _ref2$reactDates.border,
      color = _ref2$reactDates.color;
  return {
    SingleDatePickerInput: {
      display: 'inline-block',
      backgroundColor: color.background
    },
    SingleDatePickerInput__withBorder: {
      borderColor: color.border,
      borderWidth: border.pickerInput.borderWidth,
      borderStyle: border.pickerInput.borderStyle,
      borderRadius: border.pickerInput.borderRadius
    },
    SingleDatePickerInput__rtl: {
      direction: (0, _noflip["default"])('rtl')
    },
    SingleDatePickerInput__disabled: {
      backgroundColor: color.disabled
    },
    SingleDatePickerInput__block: {
      display: 'block'
    },
    SingleDatePickerInput__showClearDate: {
      paddingRight: 30 // TODO: should be noflip wrapped and handled by an isRTL prop

    },
    SingleDatePickerInput_clearDate: {
      background: 'none',
      border: 0,
      color: 'inherit',
      font: 'inherit',
      lineHeight: 'normal',
      overflow: 'visible',
      cursor: 'pointer',
      padding: 10,
      margin: '0 10px 0 5px',
      // TODO: should be noflip wrapped and handled by an isRTL prop
      position: 'absolute',
      right: 0,
      // TODO: should be noflip wrapped and handled by an isRTL prop
      top: '50%',
      transform: 'translateY(-50%)'
    },
    SingleDatePickerInput_clearDate__default: {
      ':focus': {
        background: color.core.border,
        borderRadius: '50%'
      },
      ':hover': {
        background: color.core.border,
        borderRadius: '50%'
      }
    },
    SingleDatePickerInput_clearDate__small: {
      padding: 6
    },
    SingleDatePickerInput_clearDate__hide: {
      visibility: 'hidden'
    },
    SingleDatePickerInput_clearDate_svg: {
      fill: color.core.grayLight,
      height: 12,
      width: 15,
      verticalAlign: 'middle'
    },
    SingleDatePickerInput_clearDate_svg__small: {
      height: 9
    },
    SingleDatePickerInput_calendarIcon: {
      background: 'none',
      border: 0,
      color: 'inherit',
      font: 'inherit',
      lineHeight: 'normal',
      overflow: 'visible',
      cursor: 'pointer',
      display: 'inline-block',
      verticalAlign: 'middle',
      padding: 10,
      margin: '0 5px 0 10px' // TODO: should be noflip wrapped and handled by an isRTL prop

    },
    SingleDatePickerInput_calendarIcon_svg: {
      fill: color.core.grayLight,
      height: 15,
      width: 14,
      verticalAlign: 'middle'
    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(SingleDatePickerInput);

exports["default"] = _default;

/***/ }),

/***/ "HWoq":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _object = __webpack_require__("4cSd");

var _object2 = _interopRequireDefault(_object);

var _propTypes = __webpack_require__("17x9");

var _propTypes2 = _interopRequireDefault(_propTypes);

var _constants = __webpack_require__("QEu6");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

exports['default'] = _propTypes2['default'].oneOf((0, _object2['default'])(_constants.DIRECTIONS));

/***/ }),

/***/ "Hi9J":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var define = __webpack_require__("82c2");
var bind = __webpack_require__("D3zA");

var implementation = __webpack_require__("l4Qr");
var getPolyfill = __webpack_require__("gaAG");
var shim = __webpack_require__("NUgV");

var bound = bind.call(Function.call, implementation);

define(bound, {
	getPolyfill: getPolyfill,
	implementation: implementation,
	shim: shim
});

module.exports = bound;


/***/ }),

/***/ "Hsqg":
/***/ (function(module, exports, __webpack_require__) {

module.exports =  true ? __webpack_require__("DciD") : undefined;

//# sourceMappingURL=index.js.map

/***/ }),

/***/ "IFfy":
/***/ (function(module, exports) {

module.exports = Number.isNaN || function isNaN(a) {
	return a !== a;
};


/***/ }),

/***/ "IdCN":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var fnToStr = Function.prototype.toString;

var constructorRegex = /^\s*class\b/;
var isES6ClassFn = function isES6ClassFunction(value) {
	try {
		var fnStr = fnToStr.call(value);
		return constructorRegex.test(fnStr);
	} catch (e) {
		return false; // not a function
	}
};

var tryFunctionObject = function tryFunctionToStr(value) {
	try {
		if (isES6ClassFn(value)) { return false; }
		fnToStr.call(value);
		return true;
	} catch (e) {
		return false;
	}
};
var toStr = Object.prototype.toString;
var fnClass = '[object Function]';
var genClass = '[object GeneratorFunction]';
var hasToStringTag = typeof Symbol === 'function' && typeof Symbol.toStringTag === 'symbol';

module.exports = function isCallable(value) {
	if (!value) { return false; }
	if (typeof value !== 'function' && typeof value !== 'object') { return false; }
	if (typeof value === 'function' && !value.prototype) { return true; }
	if (hasToStringTag) { return tryFunctionObject(value); }
	if (isES6ClassFn(value)) { return false; }
	var strClass = toStr.call(value);
	return strClass === fnClass || strClass === genClass;
};


/***/ }),

/***/ "IgE5":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isDayVisible;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _isBeforeDay = _interopRequireDefault(__webpack_require__("h6xH"));

var _isAfterDay = _interopRequireDefault(__webpack_require__("Nho6"));

var _toISOMonthString = _interopRequireDefault(__webpack_require__("jenk"));

var startCacheOutsideDays = new Map();
var endCacheOutsideDays = new Map();
var startCacheInsideDays = new Map();
var endCacheInsideDays = new Map();

function isDayVisible(day, month, numberOfMonths, enableOutsideDays) {
  if (!_moment["default"].isMoment(day)) return false; // Cloning is a little expensive, so we want to do it as little as possible.

  var startKey = (0, _toISOMonthString["default"])(month); // eslint-disable-next-line prefer-template

  var endKey = startKey + '+' + numberOfMonths;

  if (enableOutsideDays) {
    if (!startCacheOutsideDays.has(startKey)) {
      startCacheOutsideDays.set(startKey, month.clone().startOf('month').startOf('week'));
    }

    if ((0, _isBeforeDay["default"])(day, startCacheOutsideDays.get(startKey))) return false;

    if (!endCacheOutsideDays.has(endKey)) {
      endCacheOutsideDays.set(endKey, month.clone().endOf('week').add(numberOfMonths - 1, 'months').endOf('month').endOf('week'));
    }

    return !(0, _isAfterDay["default"])(day, endCacheOutsideDays.get(endKey));
  } // !enableOutsideDays


  if (!startCacheInsideDays.has(startKey)) {
    startCacheInsideDays.set(startKey, month.clone().startOf('month'));
  }

  if ((0, _isBeforeDay["default"])(day, startCacheInsideDays.get(startKey))) return false;

  if (!endCacheInsideDays.has(endKey)) {
    endCacheInsideDays.set(endKey, month.clone().add(numberOfMonths - 1, 'months').endOf('month'));
  }

  return !(0, _isAfterDay["default"])(day, endCacheInsideDays.get(endKey));
}

/***/ }),

/***/ "Ijbi":
/***/ (function(module, exports) {

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}

module.exports = _arrayWithoutHoles;

/***/ }),

/***/ "J4zp":
/***/ (function(module, exports, __webpack_require__) {

var arrayWithHoles = __webpack_require__("wTVA");

var iterableToArrayLimit = __webpack_require__("m0LI");

var nonIterableRest = __webpack_require__("wkBT");

function _slicedToArray(arr, i) {
  return arrayWithHoles(arr) || iterableToArrayLimit(arr, i) || nonIterableRest();
}

module.exports = _slicedToArray;

/***/ }),

/***/ "J7JS":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _toConsumableArray2 = _interopRequireDefault(__webpack_require__("RIqP"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _default = (0, _airbnbPropTypes.and)([_propTypes["default"].instanceOf(Set), function modifiers(props, propName) {
  for (var _len = arguments.length, rest = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    rest[_key - 2] = arguments[_key];
  }

  var propValue = props[propName];
  var firstError;
  (0, _toConsumableArray2["default"])(propValue).some(function (v, i) {
    var _PropTypes$string;

    var fakePropName = "".concat(propName, ": index ").concat(i);
    firstError = (_PropTypes$string = _propTypes["default"].string).isRequired.apply(_PropTypes$string, [(0, _defineProperty2["default"])({}, fakePropName, v), fakePropName].concat(rest));
    return firstError != null;
  });
  return firstError == null ? null : firstError;
}], 'Modifiers (Set of Strings)');

exports["default"] = _default;

/***/ }),

/***/ "JORM":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getSelectedDateOffset;

var defaultModifier = function defaultModifier(day) {
  return day;
};

function getSelectedDateOffset(fn, day) {
  var modifier = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : defaultModifier;
  if (!fn) return day;
  return modifier(fn(day.clone()));
}

/***/ }),

/***/ "JQAW":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var functionsHaveNames = function functionsHaveNames() {
	return typeof function f() {}.name === 'string';
};

var gOPD = Object.getOwnPropertyDescriptor;

functionsHaveNames.functionsHaveConfigurableNames = function functionsHaveConfigurableNames() {
	return functionsHaveNames() && gOPD && !!gOPD(function () {}, 'name').configurable;
};

module.exports = functionsHaveNames;


/***/ }),

/***/ "KfNM":
/***/ (function(module, exports) {

/** Used for built-in method references. */
var objectProto = Object.prototype;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */
var nativeObjectToString = objectProto.toString;

/**
 * Converts `value` to a string using `Object.prototype.toString`.
 *
 * @private
 * @param {*} value The value to convert.
 * @returns {string} Returns the converted string.
 */
function objectToString(value) {
  return nativeObjectToString.call(value);
}

module.exports = objectToString;


/***/ }),

/***/ "Kz5y":
/***/ (function(module, exports, __webpack_require__) {

var freeGlobal = __webpack_require__("WFqU");

/** Detect free variable `self`. */
var freeSelf = typeof self == 'object' && self && self.Object === Object && self;

/** Used as a reference to the global object. */
var root = freeGlobal || freeSelf || Function('return this')();

module.exports = root;


/***/ }),

/***/ "LTAC":
/***/ (function(module, exports) {

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports['default'] = isTouchDevice;
function isTouchDevice() {
  return !!(typeof window !== 'undefined' && ('ontouchstart' in window || window.DocumentTouch && typeof document !== 'undefined' && document instanceof window.DocumentTouch)) || !!(typeof navigator !== 'undefined' && (navigator.maxTouchPoints || navigator.msMaxTouchPoints));
}
module.exports = exports['default'];

/***/ }),

/***/ "LfrC":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var CalendarIcon = function CalendarIcon(props) {
  return _react["default"].createElement("svg", props, _react["default"].createElement("path", {
    d: "m107 1393h241v-241h-241zm295 0h268v-241h-268zm-295-295h241v-268h-241zm295 0h268v-268h-268zm-295-321h241v-241h-241zm616 616h268v-241h-268zm-321-616h268v-241h-268zm643 616h241v-241h-241zm-322-295h268v-268h-268zm-294-723v-241c0-7-3-14-8-19-6-5-12-8-19-8h-54c-7 0-13 3-19 8-5 5-8 12-8 19v241c0 7 3 14 8 19 6 5 12 8 19 8h54c7 0 13-3 19-8 5-5 8-12 8-19zm616 723h241v-268h-241zm-322-321h268v-241h-268zm322 0h241v-241h-241zm27-402v-241c0-7-3-14-8-19-6-5-12-8-19-8h-54c-7 0-13 3-19 8-5 5-8 12-8 19v241c0 7 3 14 8 19 6 5 12 8 19 8h54c7 0 13-3 19-8 5-5 8-12 8-19zm321-54v1072c0 29-11 54-32 75s-46 32-75 32h-1179c-29 0-54-11-75-32s-32-46-32-75v-1072c0-29 11-54 32-75s46-32 75-32h107v-80c0-37 13-68 40-95s57-39 94-39h54c37 0 68 13 95 39 26 26 39 58 39 95v80h321v-80c0-37 13-69 40-95 26-26 57-39 94-39h54c37 0 68 13 94 39s40 58 40 95v80h107c29 0 54 11 75 32s32 46 32 75z"
  }));
};

CalendarIcon.defaultProps = {
  focusable: "false",
  viewBox: "0 0 1393.1 1500"
};
var _default = CalendarIcon;
exports["default"] = _default;

/***/ }),

/***/ "Lxf3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var toStr = Object.prototype.toString;

var isPrimitive = __webpack_require__("Teho");

var isCallable = __webpack_require__("IdCN");

// http://ecma-international.org/ecma-262/5.1/#sec-8.12.8
var ES5internalSlots = {
	'[[DefaultValue]]': function (O) {
		var actualHint;
		if (arguments.length > 1) {
			actualHint = arguments[1];
		} else {
			actualHint = toStr.call(O) === '[object Date]' ? String : Number;
		}

		if (actualHint === String || actualHint === Number) {
			var methods = actualHint === String ? ['toString', 'valueOf'] : ['valueOf', 'toString'];
			var value, i;
			for (i = 0; i < methods.length; ++i) {
				if (isCallable(O[methods[i]])) {
					value = O[methods[i]]();
					if (isPrimitive(value)) {
						return value;
					}
				}
			}
			throw new TypeError('No default value');
		}
		throw new TypeError('invalid [[DefaultValue]] hint supplied');
	}
};

// http://ecma-international.org/ecma-262/5.1/#sec-9.1
module.exports = function ToPrimitive(input) {
	if (isPrimitive(input)) {
		return input;
	}
	if (arguments.length > 1) {
		return ES5internalSlots['[[DefaultValue]]'](input, arguments[1]);
	}
	return ES5internalSlots['[[DefaultValue]]'](input);
};


/***/ }),

/***/ "N3k4":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = exports.PureCalendarDay = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _reactMomentProptypes = _interopRequireDefault(__webpack_require__("XGBb"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _raf = _interopRequireDefault(__webpack_require__("xEkU"));

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _getCalendarDaySettings = _interopRequireDefault(__webpack_require__("Ae65"));

var _ModifiersShape = _interopRequireDefault(__webpack_require__("J7JS"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  day: (0, _moment["default"])(),
  daySize: _constants.DAY_SIZE,
  isOutsideDay: false,
  modifiers: new Set(),
  isFocused: false,
  tabIndex: -1,
  onDayClick: function onDayClick() {},
  onDayMouseEnter: function onDayMouseEnter() {},
  onDayMouseLeave: function onDayMouseLeave() {},
  renderDayContents: null,
  ariaLabelFormat: 'dddd, LL',
  // internationalization
  phrases: _defaultPhrases.CalendarDayPhrases
};

var CalendarDay =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(CalendarDay, _ref);
  var _proto = CalendarDay.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function CalendarDay() {
    var _this;

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    _this = _ref.call.apply(_ref, [this].concat(args)) || this;
    _this.setButtonRef = _this.setButtonRef.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.componentDidUpdate = function componentDidUpdate(prevProps) {
    var _this2 = this;

    var _this$props = this.props,
        isFocused = _this$props.isFocused,
        tabIndex = _this$props.tabIndex;

    if (tabIndex === 0) {
      if (isFocused || tabIndex !== prevProps.tabIndex) {
        (0, _raf["default"])(function () {
          if (_this2.buttonRef) {
            _this2.buttonRef.focus();
          }
        });
      }
    }
  };

  _proto.onDayClick = function onDayClick(day, e) {
    var onDayClick = this.props.onDayClick;
    onDayClick(day, e);
  };

  _proto.onDayMouseEnter = function onDayMouseEnter(day, e) {
    var onDayMouseEnter = this.props.onDayMouseEnter;
    onDayMouseEnter(day, e);
  };

  _proto.onDayMouseLeave = function onDayMouseLeave(day, e) {
    var onDayMouseLeave = this.props.onDayMouseLeave;
    onDayMouseLeave(day, e);
  };

  _proto.onKeyDown = function onKeyDown(day, e) {
    var onDayClick = this.props.onDayClick;
    var key = e.key;

    if (key === 'Enter' || key === ' ') {
      onDayClick(day, e);
    }
  };

  _proto.setButtonRef = function setButtonRef(ref) {
    this.buttonRef = ref;
  };

  _proto.render = function render() {
    var _this3 = this;

    var _this$props2 = this.props,
        day = _this$props2.day,
        ariaLabelFormat = _this$props2.ariaLabelFormat,
        daySize = _this$props2.daySize,
        isOutsideDay = _this$props2.isOutsideDay,
        modifiers = _this$props2.modifiers,
        renderDayContents = _this$props2.renderDayContents,
        tabIndex = _this$props2.tabIndex,
        styles = _this$props2.styles,
        phrases = _this$props2.phrases,
        css = _this$props2.css;
    if (!day) return _react["default"].createElement("td", null);

    var _getCalendarDaySettin = (0, _getCalendarDaySettings["default"])(day, ariaLabelFormat, daySize, modifiers, phrases),
        daySizeStyles = _getCalendarDaySettin.daySizeStyles,
        useDefaultCursor = _getCalendarDaySettin.useDefaultCursor,
        selected = _getCalendarDaySettin.selected,
        hoveredSpan = _getCalendarDaySettin.hoveredSpan,
        isOutsideRange = _getCalendarDaySettin.isOutsideRange,
        ariaLabel = _getCalendarDaySettin.ariaLabel;

    return _react["default"].createElement("td", (0, _extends2["default"])({}, css(styles.CalendarDay, useDefaultCursor && styles.CalendarDay__defaultCursor, styles.CalendarDay__default, isOutsideDay && styles.CalendarDay__outside, modifiers.has('today') && styles.CalendarDay__today, modifiers.has('first-day-of-week') && styles.CalendarDay__firstDayOfWeek, modifiers.has('last-day-of-week') && styles.CalendarDay__lastDayOfWeek, modifiers.has('hovered-offset') && styles.CalendarDay__hovered_offset, modifiers.has('hovered-start-first-possible-end') && styles.CalendarDay__hovered_start_first_possible_end, modifiers.has('hovered-start-blocked-minimum-nights') && styles.CalendarDay__hovered_start_blocked_min_nights, modifiers.has('highlighted-calendar') && styles.CalendarDay__highlighted_calendar, modifiers.has('blocked-minimum-nights') && styles.CalendarDay__blocked_minimum_nights, modifiers.has('blocked-calendar') && styles.CalendarDay__blocked_calendar, hoveredSpan && styles.CalendarDay__hovered_span, modifiers.has('selected-span') && styles.CalendarDay__selected_span, modifiers.has('selected-start') && styles.CalendarDay__selected_start, modifiers.has('selected-end') && styles.CalendarDay__selected_end, selected && !modifiers.has('selected-span') && styles.CalendarDay__selected, isOutsideRange && styles.CalendarDay__blocked_out_of_range, daySizeStyles), {
      role: "button" // eslint-disable-line jsx-a11y/no-noninteractive-element-to-interactive-role
      ,
      ref: this.setButtonRef,
      "aria-disabled": modifiers.has('blocked'),
      "aria-label": ariaLabel,
      onMouseEnter: function onMouseEnter(e) {
        _this3.onDayMouseEnter(day, e);
      },
      onMouseLeave: function onMouseLeave(e) {
        _this3.onDayMouseLeave(day, e);
      },
      onMouseUp: function onMouseUp(e) {
        e.currentTarget.blur();
      },
      onClick: function onClick(e) {
        _this3.onDayClick(day, e);
      },
      onKeyDown: function onKeyDown(e) {
        _this3.onKeyDown(day, e);
      },
      tabIndex: tabIndex
    }), renderDayContents ? renderDayContents(day, modifiers) : day.format('D'));
  };

  return CalendarDay;
}(_react["default"].PureComponent || _react["default"].Component);

exports.PureCalendarDay = CalendarDay;
CalendarDay.propTypes =  false ? undefined : {};
CalendarDay.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref2) {
  var _ref2$reactDates = _ref2.reactDates,
      color = _ref2$reactDates.color,
      font = _ref2$reactDates.font;
  return {
    CalendarDay: {
      boxSizing: 'border-box',
      cursor: 'pointer',
      fontSize: font.size,
      textAlign: 'center',
      ':active': {
        outline: 0
      }
    },
    CalendarDay__defaultCursor: {
      cursor: 'default'
    },
    CalendarDay__default: {
      border: "1px solid ".concat(color.core.borderLight),
      color: color.text,
      background: color.background,
      ':hover': {
        background: color.core.borderLight,
        border: "1px solid ".concat(color.core.borderLight),
        color: 'inherit'
      }
    },
    CalendarDay__hovered_offset: {
      background: color.core.borderBright,
      border: "1px double ".concat(color.core.borderLight),
      color: 'inherit'
    },
    CalendarDay__outside: {
      border: 0,
      background: color.outside.backgroundColor,
      color: color.outside.color,
      ':hover': {
        border: 0
      }
    },
    CalendarDay__blocked_minimum_nights: {
      background: color.minimumNights.backgroundColor,
      border: "1px solid ".concat(color.minimumNights.borderColor),
      color: color.minimumNights.color,
      ':hover': {
        background: color.minimumNights.backgroundColor_hover,
        color: color.minimumNights.color_active
      },
      ':active': {
        background: color.minimumNights.backgroundColor_active,
        color: color.minimumNights.color_active
      }
    },
    CalendarDay__highlighted_calendar: {
      background: color.highlighted.backgroundColor,
      color: color.highlighted.color,
      ':hover': {
        background: color.highlighted.backgroundColor_hover,
        color: color.highlighted.color_active
      },
      ':active': {
        background: color.highlighted.backgroundColor_active,
        color: color.highlighted.color_active
      }
    },
    CalendarDay__selected_span: {
      background: color.selectedSpan.backgroundColor,
      border: "1px double ".concat(color.selectedSpan.borderColor),
      color: color.selectedSpan.color,
      ':hover': {
        background: color.selectedSpan.backgroundColor_hover,
        border: "1px double ".concat(color.selectedSpan.borderColor),
        color: color.selectedSpan.color_active
      },
      ':active': {
        background: color.selectedSpan.backgroundColor_active,
        border: "1px double ".concat(color.selectedSpan.borderColor),
        color: color.selectedSpan.color_active
      }
    },
    CalendarDay__selected: {
      background: color.selected.backgroundColor,
      border: "1px double ".concat(color.selected.borderColor),
      color: color.selected.color,
      ':hover': {
        background: color.selected.backgroundColor_hover,
        border: "1px double ".concat(color.selected.borderColor),
        color: color.selected.color_active
      },
      ':active': {
        background: color.selected.backgroundColor_active,
        border: "1px double ".concat(color.selected.borderColor),
        color: color.selected.color_active
      }
    },
    CalendarDay__hovered_span: {
      background: color.hoveredSpan.backgroundColor,
      border: "1px double ".concat(color.hoveredSpan.borderColor),
      color: color.hoveredSpan.color,
      ':hover': {
        background: color.hoveredSpan.backgroundColor_hover,
        border: "1px double ".concat(color.hoveredSpan.borderColor),
        color: color.hoveredSpan.color_active
      },
      ':active': {
        background: color.hoveredSpan.backgroundColor_active,
        border: "1px double ".concat(color.hoveredSpan.borderColor),
        color: color.hoveredSpan.color_active
      }
    },
    CalendarDay__blocked_calendar: {
      background: color.blocked_calendar.backgroundColor,
      border: "1px solid ".concat(color.blocked_calendar.borderColor),
      color: color.blocked_calendar.color,
      ':hover': {
        background: color.blocked_calendar.backgroundColor_hover,
        border: "1px solid ".concat(color.blocked_calendar.borderColor),
        color: color.blocked_calendar.color_active
      },
      ':active': {
        background: color.blocked_calendar.backgroundColor_active,
        border: "1px solid ".concat(color.blocked_calendar.borderColor),
        color: color.blocked_calendar.color_active
      }
    },
    CalendarDay__blocked_out_of_range: {
      background: color.blocked_out_of_range.backgroundColor,
      border: "1px solid ".concat(color.blocked_out_of_range.borderColor),
      color: color.blocked_out_of_range.color,
      ':hover': {
        background: color.blocked_out_of_range.backgroundColor_hover,
        border: "1px solid ".concat(color.blocked_out_of_range.borderColor),
        color: color.blocked_out_of_range.color_active
      },
      ':active': {
        background: color.blocked_out_of_range.backgroundColor_active,
        border: "1px solid ".concat(color.blocked_out_of_range.borderColor),
        color: color.blocked_out_of_range.color_active
      }
    },
    CalendarDay__hovered_start_first_possible_end: {
      background: color.core.borderLighter,
      border: "1px double ".concat(color.core.borderLighter)
    },
    CalendarDay__hovered_start_blocked_min_nights: {
      background: color.core.borderLighter,
      border: "1px double ".concat(color.core.borderLight)
    },
    CalendarDay__selected_start: {},
    CalendarDay__selected_end: {},
    CalendarDay__today: {},
    CalendarDay__firstDayOfWeek: {},
    CalendarDay__lastDayOfWeek: {}
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(CalendarDay);

exports["default"] = _default;

/***/ }),

/***/ "NE9H":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

exports['default'] = shallowEqual;

var _objectIs = __webpack_require__("bbcx");

var _objectIs2 = _interopRequireDefault(_objectIs);

var _has = __webpack_require__("oNNP");

var _has2 = _interopRequireDefault(_has);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

// adapted from https://github.com/facebook/react/blob/144328fe81719e916b946e22660479e31561bb0b/packages/shared/shallowEqual.js#L36-L68
function shallowEqual(objA, objB) {
  if ((0, _objectIs2['default'])(objA, objB)) {
    return true;
  }

  if (!objA || !objB || (typeof objA === 'undefined' ? 'undefined' : _typeof(objA)) !== 'object' || (typeof objB === 'undefined' ? 'undefined' : _typeof(objB)) !== 'object') {
    return false;
  }

  var keysA = Object.keys(objA);
  var keysB = Object.keys(objB);

  if (keysA.length !== keysB.length) {
    return false;
  }

  keysA.sort();
  keysB.sort();

  // Test for A's keys different from B.
  for (var i = 0; i < keysA.length; i += 1) {
    if (!(0, _has2['default'])(objB, keysA[i]) || !(0, _objectIs2['default'])(objA[keysA[i]], objB[keysA[i]])) {
      return false;
    }
  }

  return true;
}
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIi4uL3NyYy9pbmRleC5qcyJdLCJuYW1lcyI6WyJzaGFsbG93RXF1YWwiLCJvYmpBIiwib2JqQiIsImtleXNBIiwiT2JqZWN0Iiwia2V5cyIsImtleXNCIiwibGVuZ3RoIiwic29ydCIsImkiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7O3FCQUl3QkEsWTs7QUFKeEI7Ozs7QUFDQTs7Ozs7O0FBRUE7QUFDZSxTQUFTQSxZQUFULENBQXNCQyxJQUF0QixFQUE0QkMsSUFBNUIsRUFBa0M7QUFDL0MsTUFBSSwyQkFBR0QsSUFBSCxFQUFTQyxJQUFULENBQUosRUFBb0I7QUFDbEIsV0FBTyxJQUFQO0FBQ0Q7O0FBRUQsTUFBSSxDQUFDRCxJQUFELElBQVMsQ0FBQ0MsSUFBVixJQUFrQixRQUFPRCxJQUFQLHlDQUFPQSxJQUFQLE9BQWdCLFFBQWxDLElBQThDLFFBQU9DLElBQVAseUNBQU9BLElBQVAsT0FBZ0IsUUFBbEUsRUFBNEU7QUFDMUUsV0FBTyxLQUFQO0FBQ0Q7O0FBRUQsTUFBTUMsUUFBUUMsT0FBT0MsSUFBUCxDQUFZSixJQUFaLENBQWQ7QUFDQSxNQUFNSyxRQUFRRixPQUFPQyxJQUFQLENBQVlILElBQVosQ0FBZDs7QUFFQSxNQUFJQyxNQUFNSSxNQUFOLEtBQWlCRCxNQUFNQyxNQUEzQixFQUFtQztBQUNqQyxXQUFPLEtBQVA7QUFDRDs7QUFFREosUUFBTUssSUFBTjtBQUNBRixRQUFNRSxJQUFOOztBQUVBO0FBQ0EsT0FBSyxJQUFJQyxJQUFJLENBQWIsRUFBZ0JBLElBQUlOLE1BQU1JLE1BQTFCLEVBQWtDRSxLQUFLLENBQXZDLEVBQTBDO0FBQ3hDLFFBQUksQ0FBQyxzQkFBSVAsSUFBSixFQUFVQyxNQUFNTSxDQUFOLENBQVYsQ0FBRCxJQUF3QixDQUFDLDJCQUFHUixLQUFLRSxNQUFNTSxDQUFOLENBQUwsQ0FBSCxFQUFtQlAsS0FBS0MsTUFBTU0sQ0FBTixDQUFMLENBQW5CLENBQTdCLEVBQWlFO0FBQy9ELGFBQU8sS0FBUDtBQUNEO0FBQ0Y7O0FBRUQsU0FBTyxJQUFQO0FBQ0QiLCJmaWxlIjoiaW5kZXguanMiLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgaXMgZnJvbSAnb2JqZWN0LWlzJztcbmltcG9ydCBoYXMgZnJvbSAnaGFzJztcblxuLy8gYWRhcHRlZCBmcm9tIGh0dHBzOi8vZ2l0aHViLmNvbS9mYWNlYm9vay9yZWFjdC9ibG9iLzE0NDMyOGZlODE3MTllOTE2Yjk0NmUyMjY2MDQ3OWUzMTU2MWJiMGIvcGFja2FnZXMvc2hhcmVkL3NoYWxsb3dFcXVhbC5qcyNMMzYtTDY4XG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBzaGFsbG93RXF1YWwob2JqQSwgb2JqQikge1xuICBpZiAoaXMob2JqQSwgb2JqQikpIHtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxuXG4gIGlmICghb2JqQSB8fCAhb2JqQiB8fCB0eXBlb2Ygb2JqQSAhPT0gJ29iamVjdCcgfHwgdHlwZW9mIG9iakIgIT09ICdvYmplY3QnKSB7XG4gICAgcmV0dXJuIGZhbHNlO1xuICB9XG5cbiAgY29uc3Qga2V5c0EgPSBPYmplY3Qua2V5cyhvYmpBKTtcbiAgY29uc3Qga2V5c0IgPSBPYmplY3Qua2V5cyhvYmpCKTtcblxuICBpZiAoa2V5c0EubGVuZ3RoICE9PSBrZXlzQi5sZW5ndGgpIHtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cblxuICBrZXlzQS5zb3J0KCk7XG4gIGtleXNCLnNvcnQoKTtcblxuICAvLyBUZXN0IGZvciBBJ3Mga2V5cyBkaWZmZXJlbnQgZnJvbSBCLlxuICBmb3IgKGxldCBpID0gMDsgaSA8IGtleXNBLmxlbmd0aDsgaSArPSAxKSB7XG4gICAgaWYgKCFoYXMob2JqQiwga2V5c0FbaV0pIHx8ICFpcyhvYmpBW2tleXNBW2ldXSwgb2JqQltrZXlzQVtpXV0pKSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIHRydWU7XG59XG4iXX0=
//# sourceMappingURL=index.js.map

/***/ }),

/***/ "NUgV":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var supportsDescriptors = __webpack_require__("82c2").supportsDescriptors;
var functionsHaveNames = __webpack_require__("JQAW")();
var getPolyfill = __webpack_require__("gaAG");
var defineProperty = Object.defineProperty;
var TypeErr = TypeError;

module.exports = function shimName() {
	var polyfill = getPolyfill();
	if (functionsHaveNames) {
		return polyfill;
	}
	if (!supportsDescriptors) {
		throw new TypeErr('Shimming Function.prototype.name support requires ES5 property descriptor support.');
	}
	var functionProto = Function.prototype;
	defineProperty(functionProto, 'name', {
		configurable: true,
		enumerable: false,
		get: function () {
			var name = polyfill.call(this);
			if (this !== functionProto) {
				defineProperty(this, 'name', {
					configurable: true,
					enumerable: false,
					value: name,
					writable: false
				});
			}
			return name;
		}
	});
	return polyfill;
};


/***/ }),

/***/ "Nho6":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isAfterDay;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _isBeforeDay = _interopRequireDefault(__webpack_require__("h6xH"));

var _isSameDay = _interopRequireDefault(__webpack_require__("pRvc"));

function isAfterDay(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false;
  return !(0, _isBeforeDay["default"])(a, b) && !(0, _isSameDay["default"])(a, b);
}

/***/ }),

/***/ "NiDq":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _reactMomentProptypes = _interopRequireDefault(__webpack_require__("XGBb"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _OpenDirectionShape = _interopRequireDefault(__webpack_require__("iuLr"));

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _SingleDatePickerInput = _interopRequireDefault(__webpack_require__("HUEL"));

var _IconPositionShape = _interopRequireDefault(__webpack_require__("Bh6R"));

var _DisabledShape = _interopRequireDefault(__webpack_require__("5Fvu"));

var _toMomentObject = _interopRequireDefault(__webpack_require__("WmS1"));

var _toLocalizedDateString = _interopRequireDefault(__webpack_require__("UyxP"));

var _isInclusivelyAfterDay = _interopRequireDefault(__webpack_require__("rit9"));

var _constants = __webpack_require__("Fv1B");

var propTypes =  false ? undefined : {};
var defaultProps = {
  children: null,
  date: null,
  focused: false,
  placeholder: '',
  ariaLabel: undefined,
  screenReaderMessage: 'Date',
  showClearDate: false,
  showCaret: false,
  showDefaultInputIcon: false,
  inputIconPosition: _constants.ICON_BEFORE_POSITION,
  disabled: false,
  required: false,
  readOnly: false,
  openDirection: _constants.OPEN_DOWN,
  noBorder: false,
  block: false,
  small: false,
  regular: false,
  verticalSpacing: undefined,
  keepOpenOnDateSelect: false,
  reopenPickerOnClearDate: false,
  isOutsideRange: function isOutsideRange(day) {
    return !(0, _isInclusivelyAfterDay["default"])(day, (0, _moment["default"])());
  },
  displayFormat: function displayFormat() {
    return _moment["default"].localeData().longDateFormat('L');
  },
  onClose: function onClose() {},
  onKeyDownArrowDown: function onKeyDownArrowDown() {},
  onKeyDownQuestionMark: function onKeyDownQuestionMark() {},
  customInputIcon: null,
  customCloseIcon: null,
  // accessibility
  isFocused: false,
  // i18n
  phrases: _defaultPhrases.SingleDatePickerInputPhrases,
  isRTL: false
};

var SingleDatePickerInputController =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(SingleDatePickerInputController, _ref);
  var _proto = SingleDatePickerInputController.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function SingleDatePickerInputController(props) {
    var _this;

    _this = _ref.call(this, props) || this;
    _this.onChange = _this.onChange.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onFocus = _this.onFocus.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onClearFocus = _this.onClearFocus.bind((0, _assertThisInitialized2["default"])(_this));
    _this.clearDate = _this.clearDate.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.onChange = function onChange(dateString) {
    var _this$props = this.props,
        isOutsideRange = _this$props.isOutsideRange,
        keepOpenOnDateSelect = _this$props.keepOpenOnDateSelect,
        onDateChange = _this$props.onDateChange,
        onFocusChange = _this$props.onFocusChange,
        onClose = _this$props.onClose;
    var newDate = (0, _toMomentObject["default"])(dateString, this.getDisplayFormat());
    var isValid = newDate && !isOutsideRange(newDate);

    if (isValid) {
      onDateChange(newDate);

      if (!keepOpenOnDateSelect) {
        onFocusChange({
          focused: false
        });
        onClose({
          date: newDate
        });
      }
    } else {
      onDateChange(null);
    }
  };

  _proto.onFocus = function onFocus() {
    var _this$props2 = this.props,
        onFocusChange = _this$props2.onFocusChange,
        disabled = _this$props2.disabled;

    if (!disabled) {
      onFocusChange({
        focused: true
      });
    }
  };

  _proto.onClearFocus = function onClearFocus() {
    var _this$props3 = this.props,
        focused = _this$props3.focused,
        onFocusChange = _this$props3.onFocusChange,
        onClose = _this$props3.onClose,
        date = _this$props3.date;
    if (!focused) return;
    onFocusChange({
      focused: false
    });
    onClose({
      date: date
    });
  };

  _proto.getDisplayFormat = function getDisplayFormat() {
    var displayFormat = this.props.displayFormat;
    return typeof displayFormat === 'string' ? displayFormat : displayFormat();
  };

  _proto.getDateString = function getDateString(date) {
    var displayFormat = this.getDisplayFormat();

    if (date && displayFormat) {
      return date && date.format(displayFormat);
    }

    return (0, _toLocalizedDateString["default"])(date);
  };

  _proto.clearDate = function clearDate() {
    var _this$props4 = this.props,
        onDateChange = _this$props4.onDateChange,
        reopenPickerOnClearDate = _this$props4.reopenPickerOnClearDate,
        onFocusChange = _this$props4.onFocusChange;
    onDateChange(null);

    if (reopenPickerOnClearDate) {
      onFocusChange({
        focused: true
      });
    }
  };

  _proto.render = function render() {
    var _this$props5 = this.props,
        children = _this$props5.children,
        id = _this$props5.id,
        placeholder = _this$props5.placeholder,
        ariaLabel = _this$props5.ariaLabel,
        disabled = _this$props5.disabled,
        focused = _this$props5.focused,
        isFocused = _this$props5.isFocused,
        required = _this$props5.required,
        readOnly = _this$props5.readOnly,
        openDirection = _this$props5.openDirection,
        showClearDate = _this$props5.showClearDate,
        showCaret = _this$props5.showCaret,
        showDefaultInputIcon = _this$props5.showDefaultInputIcon,
        inputIconPosition = _this$props5.inputIconPosition,
        customCloseIcon = _this$props5.customCloseIcon,
        customInputIcon = _this$props5.customInputIcon,
        date = _this$props5.date,
        phrases = _this$props5.phrases,
        onKeyDownArrowDown = _this$props5.onKeyDownArrowDown,
        onKeyDownQuestionMark = _this$props5.onKeyDownQuestionMark,
        screenReaderMessage = _this$props5.screenReaderMessage,
        isRTL = _this$props5.isRTL,
        noBorder = _this$props5.noBorder,
        block = _this$props5.block,
        small = _this$props5.small,
        regular = _this$props5.regular,
        verticalSpacing = _this$props5.verticalSpacing;
    var displayValue = this.getDateString(date);
    return _react["default"].createElement(_SingleDatePickerInput["default"], {
      id: id,
      placeholder: placeholder,
      ariaLabel: ariaLabel,
      focused: focused,
      isFocused: isFocused,
      disabled: disabled,
      required: required,
      readOnly: readOnly,
      openDirection: openDirection,
      showCaret: showCaret,
      onClearDate: this.clearDate,
      showClearDate: showClearDate,
      showDefaultInputIcon: showDefaultInputIcon,
      inputIconPosition: inputIconPosition,
      customCloseIcon: customCloseIcon,
      customInputIcon: customInputIcon,
      displayValue: displayValue,
      onChange: this.onChange,
      onFocus: this.onFocus,
      onKeyDownShiftTab: this.onClearFocus,
      onKeyDownArrowDown: onKeyDownArrowDown,
      onKeyDownQuestionMark: onKeyDownQuestionMark,
      screenReaderMessage: screenReaderMessage,
      phrases: phrases,
      isRTL: isRTL,
      noBorder: noBorder,
      block: block,
      small: small,
      regular: regular,
      verticalSpacing: verticalSpacing
    }, children);
  };

  return SingleDatePickerInputController;
}(_react["default"].PureComponent || _react["default"].Component);

exports["default"] = SingleDatePickerInputController;
SingleDatePickerInputController.propTypes =  false ? undefined : {};
SingleDatePickerInputController.defaultProps = defaultProps;

/***/ }),

/***/ "Nloh":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__("284h");

var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = exports.PureDayPicker = exports.defaultProps = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _toConsumableArray2 = _interopRequireDefault(__webpack_require__("RIqP"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _throttle = _interopRequireDefault(__webpack_require__("DzJC"));

var _isTouchDevice = _interopRequireDefault(__webpack_require__("LTAC"));

var _reactOutsideClickHandler = _interopRequireDefault(__webpack_require__("3gBW"));

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _noflip = _interopRequireDefault(__webpack_require__("2WTt"));

var _CalendarMonthGrid = _interopRequireDefault(__webpack_require__("Thzv"));

var _DayPickerNavigation = _interopRequireDefault(__webpack_require__("zfJ5"));

var _DayPickerKeyboardShortcuts = _interopRequireWildcard(__webpack_require__("1+Kn"));

var _getNumberOfCalendarMonthWeeks = _interopRequireDefault(__webpack_require__("0Dl3"));

var _getCalendarMonthWidth = _interopRequireDefault(__webpack_require__("m2ax"));

var _calculateDimension = _interopRequireDefault(__webpack_require__("ixyq"));

var _getActiveElement = _interopRequireDefault(__webpack_require__("+51k"));

var _isDayVisible = _interopRequireDefault(__webpack_require__("IgE5"));

var _ModifiersShape = _interopRequireDefault(__webpack_require__("J7JS"));

var _ScrollableOrientationShape = _interopRequireDefault(__webpack_require__("aE6U"));

var _DayOfWeekShape = _interopRequireDefault(__webpack_require__("2S2E"));

var _CalendarInfoPositionShape = _interopRequireDefault(__webpack_require__("oR9Z"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var MONTH_PADDING = 23;
var PREV_TRANSITION = 'prev';
var NEXT_TRANSITION = 'next';
var MONTH_SELECTION_TRANSITION = 'month_selection';
var YEAR_SELECTION_TRANSITION = 'year_selection';
var propTypes =  false ? undefined : {};
var defaultProps = {
  // calendar presentation props
  enableOutsideDays: false,
  numberOfMonths: 2,
  orientation: _constants.HORIZONTAL_ORIENTATION,
  withPortal: false,
  onOutsideClick: function onOutsideClick() {},
  hidden: false,
  initialVisibleMonth: function initialVisibleMonth() {
    return (0, _moment["default"])();
  },
  firstDayOfWeek: null,
  renderCalendarInfo: null,
  calendarInfoPosition: _constants.INFO_POSITION_BOTTOM,
  hideKeyboardShortcutsPanel: false,
  daySize: _constants.DAY_SIZE,
  isRTL: false,
  verticalHeight: null,
  noBorder: false,
  transitionDuration: undefined,
  verticalBorderSpacing: undefined,
  horizontalMonthPadding: 13,
  renderKeyboardShortcutsButton: undefined,
  // navigation props
  disablePrev: false,
  disableNext: false,
  navPrev: null,
  navNext: null,
  noNavButtons: false,
  onPrevMonthClick: function onPrevMonthClick() {},
  onNextMonthClick: function onNextMonthClick() {},
  onMonthChange: function onMonthChange() {},
  onYearChange: function onYearChange() {},
  onMultiplyScrollableMonths: function onMultiplyScrollableMonths() {},
  // month props
  renderMonthText: null,
  renderMonthElement: null,
  // day props
  modifiers: {},
  renderCalendarDay: undefined,
  renderDayContents: null,
  onDayClick: function onDayClick() {},
  onDayMouseEnter: function onDayMouseEnter() {},
  onDayMouseLeave: function onDayMouseLeave() {},
  // accessibility props
  isFocused: false,
  getFirstFocusableDay: null,
  onBlur: function onBlur() {},
  showKeyboardShortcuts: false,
  onTab: function onTab() {},
  onShiftTab: function onShiftTab() {},
  // internationalization
  monthFormat: 'MMMM YYYY',
  weekDayFormat: 'dd',
  phrases: _defaultPhrases.DayPickerPhrases,
  dayAriaLabelFormat: undefined
};
exports.defaultProps = defaultProps;

var DayPicker =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(DayPicker, _ref);
  var _proto = DayPicker.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function DayPicker(props) {
    var _this;

    _this = _ref.call(this, props) || this;
    var currentMonth = props.hidden ? (0, _moment["default"])() : props.initialVisibleMonth();
    var focusedDate = currentMonth.clone().startOf('month');

    if (props.getFirstFocusableDay) {
      focusedDate = props.getFirstFocusableDay(currentMonth);
    }

    var horizontalMonthPadding = props.horizontalMonthPadding;
    var translationValue = props.isRTL && _this.isHorizontal() ? -(0, _getCalendarMonthWidth["default"])(props.daySize, horizontalMonthPadding) : 0;
    _this.hasSetInitialVisibleMonth = !props.hidden;
    _this.state = {
      currentMonth: currentMonth,
      monthTransition: null,
      translationValue: translationValue,
      scrollableMonthMultiple: 1,
      calendarMonthWidth: (0, _getCalendarMonthWidth["default"])(props.daySize, horizontalMonthPadding),
      focusedDate: !props.hidden || props.isFocused ? focusedDate : null,
      nextFocusedDate: null,
      showKeyboardShortcuts: props.showKeyboardShortcuts,
      onKeyboardShortcutsPanelClose: function onKeyboardShortcutsPanelClose() {},
      isTouchDevice: (0, _isTouchDevice["default"])(),
      withMouseInteractions: true,
      calendarInfoWidth: 0,
      monthTitleHeight: null,
      hasSetHeight: false
    };

    _this.setCalendarMonthWeeks(currentMonth);

    _this.calendarMonthGridHeight = 0;
    _this.setCalendarInfoWidthTimeout = null;
    _this.setCalendarMonthGridHeightTimeout = null;
    _this.onKeyDown = _this.onKeyDown.bind((0, _assertThisInitialized2["default"])(_this));
    _this.throttledKeyDown = (0, _throttle["default"])(_this.onFinalKeyDown, 200, {
      trailing: false
    });
    _this.onPrevMonthClick = _this.onPrevMonthClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onPrevMonthTransition = _this.onPrevMonthTransition.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onNextMonthClick = _this.onNextMonthClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onNextMonthTransition = _this.onNextMonthTransition.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onMonthChange = _this.onMonthChange.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onYearChange = _this.onYearChange.bind((0, _assertThisInitialized2["default"])(_this));
    _this.multiplyScrollableMonths = _this.multiplyScrollableMonths.bind((0, _assertThisInitialized2["default"])(_this));
    _this.updateStateAfterMonthTransition = _this.updateStateAfterMonthTransition.bind((0, _assertThisInitialized2["default"])(_this));
    _this.openKeyboardShortcutsPanel = _this.openKeyboardShortcutsPanel.bind((0, _assertThisInitialized2["default"])(_this));
    _this.closeKeyboardShortcutsPanel = _this.closeKeyboardShortcutsPanel.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setCalendarInfoRef = _this.setCalendarInfoRef.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setContainerRef = _this.setContainerRef.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setTransitionContainerRef = _this.setTransitionContainerRef.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setMonthTitleHeight = _this.setMonthTitleHeight.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.componentDidMount = function componentDidMount() {
    var currentMonth = this.state.currentMonth;

    if (this.calendarInfo) {
      this.setState({
        isTouchDevice: (0, _isTouchDevice["default"])(),
        calendarInfoWidth: (0, _calculateDimension["default"])(this.calendarInfo, 'width', true, true)
      });
    } else {
      this.setState({
        isTouchDevice: (0, _isTouchDevice["default"])()
      });
    }

    this.setCalendarMonthWeeks(currentMonth);
  };

  _proto.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
    var hidden = nextProps.hidden,
        isFocused = nextProps.isFocused,
        showKeyboardShortcuts = nextProps.showKeyboardShortcuts,
        onBlur = nextProps.onBlur,
        renderMonthText = nextProps.renderMonthText,
        horizontalMonthPadding = nextProps.horizontalMonthPadding;
    var currentMonth = this.state.currentMonth;

    if (!hidden) {
      if (!this.hasSetInitialVisibleMonth) {
        this.hasSetInitialVisibleMonth = true;
        this.setState({
          currentMonth: nextProps.initialVisibleMonth()
        });
      }
    }

    var _this$props = this.props,
        daySize = _this$props.daySize,
        prevIsFocused = _this$props.isFocused,
        prevRenderMonthText = _this$props.renderMonthText;

    if (nextProps.daySize !== daySize) {
      this.setState({
        calendarMonthWidth: (0, _getCalendarMonthWidth["default"])(nextProps.daySize, horizontalMonthPadding)
      });
    }

    if (isFocused !== prevIsFocused) {
      if (isFocused) {
        var focusedDate = this.getFocusedDay(currentMonth);
        var onKeyboardShortcutsPanelClose = this.state.onKeyboardShortcutsPanelClose;

        if (nextProps.showKeyboardShortcuts) {
          // the ? shortcut came from the input and we should return input there once it is close
          onKeyboardShortcutsPanelClose = onBlur;
        }

        this.setState({
          showKeyboardShortcuts: showKeyboardShortcuts,
          onKeyboardShortcutsPanelClose: onKeyboardShortcutsPanelClose,
          focusedDate: focusedDate,
          withMouseInteractions: false
        });
      } else {
        this.setState({
          focusedDate: null
        });
      }
    }

    if (renderMonthText !== prevRenderMonthText) {
      this.setState({
        monthTitleHeight: null
      });
    }
  };

  _proto.componentWillUpdate = function componentWillUpdate() {
    var _this2 = this;

    var transitionDuration = this.props.transitionDuration; // Calculating the dimensions trigger a DOM repaint which
    // breaks the CSS transition.
    // The setTimeout will wait until the transition ends.

    if (this.calendarInfo) {
      this.setCalendarInfoWidthTimeout = setTimeout(function () {
        var calendarInfoWidth = _this2.state.calendarInfoWidth;
        var calendarInfoPanelWidth = (0, _calculateDimension["default"])(_this2.calendarInfo, 'width', true, true);

        if (calendarInfoWidth !== calendarInfoPanelWidth) {
          _this2.setState({
            calendarInfoWidth: calendarInfoPanelWidth
          });
        }
      }, transitionDuration);
    }
  };

  _proto.componentDidUpdate = function componentDidUpdate(prevProps) {
    var _this$props2 = this.props,
        orientation = _this$props2.orientation,
        daySize = _this$props2.daySize,
        isFocused = _this$props2.isFocused,
        numberOfMonths = _this$props2.numberOfMonths;
    var _this$state = this.state,
        focusedDate = _this$state.focusedDate,
        monthTitleHeight = _this$state.monthTitleHeight;

    if (this.isHorizontal() && (orientation !== prevProps.orientation || daySize !== prevProps.daySize)) {
      var visibleCalendarWeeks = this.calendarMonthWeeks.slice(1, numberOfMonths + 1);
      var calendarMonthWeeksHeight = Math.max.apply(Math, [0].concat((0, _toConsumableArray2["default"])(visibleCalendarWeeks))) * (daySize - 1);
      var newMonthHeight = monthTitleHeight + calendarMonthWeeksHeight + 1;
      this.adjustDayPickerHeight(newMonthHeight);
    }

    if (!prevProps.isFocused && isFocused && !focusedDate) {
      this.container.focus();
    }
  };

  _proto.componentWillUnmount = function componentWillUnmount() {
    clearTimeout(this.setCalendarInfoWidthTimeout);
    clearTimeout(this.setCalendarMonthGridHeightTimeout);
  };

  _proto.onKeyDown = function onKeyDown(e) {
    e.stopPropagation();

    if (!_constants.MODIFIER_KEY_NAMES.has(e.key)) {
      this.throttledKeyDown(e);
    }
  };

  _proto.onFinalKeyDown = function onFinalKeyDown(e) {
    this.setState({
      withMouseInteractions: false
    });
    var _this$props3 = this.props,
        onBlur = _this$props3.onBlur,
        onTab = _this$props3.onTab,
        onShiftTab = _this$props3.onShiftTab,
        isRTL = _this$props3.isRTL;
    var _this$state2 = this.state,
        focusedDate = _this$state2.focusedDate,
        showKeyboardShortcuts = _this$state2.showKeyboardShortcuts;
    if (!focusedDate) return;
    var newFocusedDate = focusedDate.clone();
    var didTransitionMonth = false; // focus might be anywhere when the keyboard shortcuts panel is opened so we want to
    // return it to wherever it was before when the panel was opened

    var activeElement = (0, _getActiveElement["default"])();

    var onKeyboardShortcutsPanelClose = function onKeyboardShortcutsPanelClose() {
      if (activeElement) activeElement.focus();
    };

    switch (e.key) {
      case 'ArrowUp':
        e.preventDefault();
        newFocusedDate.subtract(1, 'week');
        didTransitionMonth = this.maybeTransitionPrevMonth(newFocusedDate);
        break;

      case 'ArrowLeft':
        e.preventDefault();

        if (isRTL) {
          newFocusedDate.add(1, 'day');
        } else {
          newFocusedDate.subtract(1, 'day');
        }

        didTransitionMonth = this.maybeTransitionPrevMonth(newFocusedDate);
        break;

      case 'Home':
        e.preventDefault();
        newFocusedDate.startOf('week');
        didTransitionMonth = this.maybeTransitionPrevMonth(newFocusedDate);
        break;

      case 'PageUp':
        e.preventDefault();
        newFocusedDate.subtract(1, 'month');
        didTransitionMonth = this.maybeTransitionPrevMonth(newFocusedDate);
        break;

      case 'ArrowDown':
        e.preventDefault();
        newFocusedDate.add(1, 'week');
        didTransitionMonth = this.maybeTransitionNextMonth(newFocusedDate);
        break;

      case 'ArrowRight':
        e.preventDefault();

        if (isRTL) {
          newFocusedDate.subtract(1, 'day');
        } else {
          newFocusedDate.add(1, 'day');
        }

        didTransitionMonth = this.maybeTransitionNextMonth(newFocusedDate);
        break;

      case 'End':
        e.preventDefault();
        newFocusedDate.endOf('week');
        didTransitionMonth = this.maybeTransitionNextMonth(newFocusedDate);
        break;

      case 'PageDown':
        e.preventDefault();
        newFocusedDate.add(1, 'month');
        didTransitionMonth = this.maybeTransitionNextMonth(newFocusedDate);
        break;

      case '?':
        this.openKeyboardShortcutsPanel(onKeyboardShortcutsPanelClose);
        break;

      case 'Escape':
        if (showKeyboardShortcuts) {
          this.closeKeyboardShortcutsPanel();
        } else {
          onBlur(e);
        }

        break;

      case 'Tab':
        if (e.shiftKey) {
          onShiftTab();
        } else {
          onTab(e);
        }

        break;

      default:
        break;
    } // If there was a month transition, do not update the focused date until the transition has
    // completed. Otherwise, attempting to focus on a DOM node may interrupt the CSS animation. If
    // didTransitionMonth is true, the focusedDate gets updated in #updateStateAfterMonthTransition


    if (!didTransitionMonth) {
      this.setState({
        focusedDate: newFocusedDate
      });
    }
  };

  _proto.onPrevMonthClick = function onPrevMonthClick(e) {
    if (e) e.preventDefault();
    this.onPrevMonthTransition();
  };

  _proto.onPrevMonthTransition = function onPrevMonthTransition(nextFocusedDate) {
    var _this$props4 = this.props,
        daySize = _this$props4.daySize,
        isRTL = _this$props4.isRTL,
        numberOfMonths = _this$props4.numberOfMonths;
    var _this$state3 = this.state,
        calendarMonthWidth = _this$state3.calendarMonthWidth,
        monthTitleHeight = _this$state3.monthTitleHeight;
    var translationValue;

    if (this.isVertical()) {
      var calendarMonthWeeksHeight = this.calendarMonthWeeks[0] * (daySize - 1);
      translationValue = monthTitleHeight + calendarMonthWeeksHeight + 1;
    } else if (this.isHorizontal()) {
      translationValue = calendarMonthWidth;

      if (isRTL) {
        translationValue = -2 * calendarMonthWidth;
      }

      var visibleCalendarWeeks = this.calendarMonthWeeks.slice(0, numberOfMonths);

      var _calendarMonthWeeksHeight = Math.max.apply(Math, [0].concat((0, _toConsumableArray2["default"])(visibleCalendarWeeks))) * (daySize - 1);

      var newMonthHeight = monthTitleHeight + _calendarMonthWeeksHeight + 1;
      this.adjustDayPickerHeight(newMonthHeight);
    }

    this.setState({
      monthTransition: PREV_TRANSITION,
      translationValue: translationValue,
      focusedDate: null,
      nextFocusedDate: nextFocusedDate
    });
  };

  _proto.onMonthChange = function onMonthChange(currentMonth) {
    this.setCalendarMonthWeeks(currentMonth);
    this.calculateAndSetDayPickerHeight(); // Translation value is a hack to force an invisible transition that
    // properly rerenders the CalendarMonthGrid

    this.setState({
      monthTransition: MONTH_SELECTION_TRANSITION,
      translationValue: 0.00001,
      focusedDate: null,
      nextFocusedDate: currentMonth,
      currentMonth: currentMonth
    });
  };

  _proto.onYearChange = function onYearChange(currentMonth) {
    this.setCalendarMonthWeeks(currentMonth);
    this.calculateAndSetDayPickerHeight(); // Translation value is a hack to force an invisible transition that
    // properly rerenders the CalendarMonthGrid

    this.setState({
      monthTransition: YEAR_SELECTION_TRANSITION,
      translationValue: 0.0001,
      focusedDate: null,
      nextFocusedDate: currentMonth,
      currentMonth: currentMonth
    });
  };

  _proto.onNextMonthClick = function onNextMonthClick(e) {
    if (e) e.preventDefault();
    this.onNextMonthTransition();
  };

  _proto.onNextMonthTransition = function onNextMonthTransition(nextFocusedDate) {
    var _this$props5 = this.props,
        isRTL = _this$props5.isRTL,
        numberOfMonths = _this$props5.numberOfMonths,
        daySize = _this$props5.daySize;
    var _this$state4 = this.state,
        calendarMonthWidth = _this$state4.calendarMonthWidth,
        monthTitleHeight = _this$state4.monthTitleHeight;
    var translationValue;

    if (this.isVertical()) {
      var firstVisibleMonthWeeks = this.calendarMonthWeeks[1];
      var calendarMonthWeeksHeight = firstVisibleMonthWeeks * (daySize - 1);
      translationValue = -(monthTitleHeight + calendarMonthWeeksHeight + 1);
    }

    if (this.isHorizontal()) {
      translationValue = -calendarMonthWidth;

      if (isRTL) {
        translationValue = 0;
      }

      var visibleCalendarWeeks = this.calendarMonthWeeks.slice(2, numberOfMonths + 2);

      var _calendarMonthWeeksHeight2 = Math.max.apply(Math, [0].concat((0, _toConsumableArray2["default"])(visibleCalendarWeeks))) * (daySize - 1);

      var newMonthHeight = monthTitleHeight + _calendarMonthWeeksHeight2 + 1;
      this.adjustDayPickerHeight(newMonthHeight);
    }

    this.setState({
      monthTransition: NEXT_TRANSITION,
      translationValue: translationValue,
      focusedDate: null,
      nextFocusedDate: nextFocusedDate
    });
  };

  _proto.getFirstDayOfWeek = function getFirstDayOfWeek() {
    var firstDayOfWeek = this.props.firstDayOfWeek;

    if (firstDayOfWeek == null) {
      return _moment["default"].localeData().firstDayOfWeek();
    }

    return firstDayOfWeek;
  };

  _proto.getFirstVisibleIndex = function getFirstVisibleIndex() {
    var orientation = this.props.orientation;
    var monthTransition = this.state.monthTransition;
    if (orientation === _constants.VERTICAL_SCROLLABLE) return 0;
    var firstVisibleMonthIndex = 1;

    if (monthTransition === PREV_TRANSITION) {
      firstVisibleMonthIndex -= 1;
    } else if (monthTransition === NEXT_TRANSITION) {
      firstVisibleMonthIndex += 1;
    }

    return firstVisibleMonthIndex;
  };

  _proto.getFocusedDay = function getFocusedDay(newMonth) {
    var _this$props6 = this.props,
        getFirstFocusableDay = _this$props6.getFirstFocusableDay,
        numberOfMonths = _this$props6.numberOfMonths;
    var focusedDate;

    if (getFirstFocusableDay) {
      focusedDate = getFirstFocusableDay(newMonth);
    }

    if (newMonth && (!focusedDate || !(0, _isDayVisible["default"])(focusedDate, newMonth, numberOfMonths))) {
      focusedDate = newMonth.clone().startOf('month');
    }

    return focusedDate;
  };

  _proto.setMonthTitleHeight = function setMonthTitleHeight(monthTitleHeight) {
    var _this3 = this;

    this.setState({
      monthTitleHeight: monthTitleHeight
    }, function () {
      _this3.calculateAndSetDayPickerHeight();
    });
  };

  _proto.setCalendarMonthWeeks = function setCalendarMonthWeeks(currentMonth) {
    var numberOfMonths = this.props.numberOfMonths;
    this.calendarMonthWeeks = [];
    var month = currentMonth.clone().subtract(1, 'months');
    var firstDayOfWeek = this.getFirstDayOfWeek();

    for (var i = 0; i < numberOfMonths + 2; i += 1) {
      var numberOfWeeks = (0, _getNumberOfCalendarMonthWeeks["default"])(month, firstDayOfWeek);
      this.calendarMonthWeeks.push(numberOfWeeks);
      month = month.add(1, 'months');
    }
  };

  _proto.setContainerRef = function setContainerRef(ref) {
    this.container = ref;
  };

  _proto.setCalendarInfoRef = function setCalendarInfoRef(ref) {
    this.calendarInfo = ref;
  };

  _proto.setTransitionContainerRef = function setTransitionContainerRef(ref) {
    this.transitionContainer = ref;
  };

  _proto.maybeTransitionNextMonth = function maybeTransitionNextMonth(newFocusedDate) {
    var numberOfMonths = this.props.numberOfMonths;
    var _this$state5 = this.state,
        currentMonth = _this$state5.currentMonth,
        focusedDate = _this$state5.focusedDate;
    var newFocusedDateMonth = newFocusedDate.month();
    var focusedDateMonth = focusedDate.month();
    var isNewFocusedDateVisible = (0, _isDayVisible["default"])(newFocusedDate, currentMonth, numberOfMonths);

    if (newFocusedDateMonth !== focusedDateMonth && !isNewFocusedDateVisible) {
      this.onNextMonthTransition(newFocusedDate);
      return true;
    }

    return false;
  };

  _proto.maybeTransitionPrevMonth = function maybeTransitionPrevMonth(newFocusedDate) {
    var numberOfMonths = this.props.numberOfMonths;
    var _this$state6 = this.state,
        currentMonth = _this$state6.currentMonth,
        focusedDate = _this$state6.focusedDate;
    var newFocusedDateMonth = newFocusedDate.month();
    var focusedDateMonth = focusedDate.month();
    var isNewFocusedDateVisible = (0, _isDayVisible["default"])(newFocusedDate, currentMonth, numberOfMonths);

    if (newFocusedDateMonth !== focusedDateMonth && !isNewFocusedDateVisible) {
      this.onPrevMonthTransition(newFocusedDate);
      return true;
    }

    return false;
  };

  _proto.multiplyScrollableMonths = function multiplyScrollableMonths(e) {
    var onMultiplyScrollableMonths = this.props.onMultiplyScrollableMonths;
    if (e) e.preventDefault();
    if (onMultiplyScrollableMonths) onMultiplyScrollableMonths(e);
    this.setState(function (_ref2) {
      var scrollableMonthMultiple = _ref2.scrollableMonthMultiple;
      return {
        scrollableMonthMultiple: scrollableMonthMultiple + 1
      };
    });
  };

  _proto.isHorizontal = function isHorizontal() {
    var orientation = this.props.orientation;
    return orientation === _constants.HORIZONTAL_ORIENTATION;
  };

  _proto.isVertical = function isVertical() {
    var orientation = this.props.orientation;
    return orientation === _constants.VERTICAL_ORIENTATION || orientation === _constants.VERTICAL_SCROLLABLE;
  };

  _proto.updateStateAfterMonthTransition = function updateStateAfterMonthTransition() {
    var _this4 = this;

    var _this$props7 = this.props,
        onPrevMonthClick = _this$props7.onPrevMonthClick,
        onNextMonthClick = _this$props7.onNextMonthClick,
        numberOfMonths = _this$props7.numberOfMonths,
        onMonthChange = _this$props7.onMonthChange,
        onYearChange = _this$props7.onYearChange,
        isRTL = _this$props7.isRTL;
    var _this$state7 = this.state,
        currentMonth = _this$state7.currentMonth,
        monthTransition = _this$state7.monthTransition,
        focusedDate = _this$state7.focusedDate,
        nextFocusedDate = _this$state7.nextFocusedDate,
        withMouseInteractions = _this$state7.withMouseInteractions,
        calendarMonthWidth = _this$state7.calendarMonthWidth;
    if (!monthTransition) return;
    var newMonth = currentMonth.clone();
    var firstDayOfWeek = this.getFirstDayOfWeek();

    if (monthTransition === PREV_TRANSITION) {
      newMonth.subtract(1, 'month');
      if (onPrevMonthClick) onPrevMonthClick(newMonth);
      var newInvisibleMonth = newMonth.clone().subtract(1, 'month');
      var numberOfWeeks = (0, _getNumberOfCalendarMonthWeeks["default"])(newInvisibleMonth, firstDayOfWeek);
      this.calendarMonthWeeks = [numberOfWeeks].concat((0, _toConsumableArray2["default"])(this.calendarMonthWeeks.slice(0, -1)));
    } else if (monthTransition === NEXT_TRANSITION) {
      newMonth.add(1, 'month');
      if (onNextMonthClick) onNextMonthClick(newMonth);

      var _newInvisibleMonth = newMonth.clone().add(numberOfMonths, 'month');

      var _numberOfWeeks = (0, _getNumberOfCalendarMonthWeeks["default"])(_newInvisibleMonth, firstDayOfWeek);

      this.calendarMonthWeeks = [].concat((0, _toConsumableArray2["default"])(this.calendarMonthWeeks.slice(1)), [_numberOfWeeks]);
    } else if (monthTransition === MONTH_SELECTION_TRANSITION) {
      if (onMonthChange) onMonthChange(newMonth);
    } else if (monthTransition === YEAR_SELECTION_TRANSITION) {
      if (onYearChange) onYearChange(newMonth);
    }

    var newFocusedDate = null;

    if (nextFocusedDate) {
      newFocusedDate = nextFocusedDate;
    } else if (!focusedDate && !withMouseInteractions) {
      newFocusedDate = this.getFocusedDay(newMonth);
    }

    this.setState({
      currentMonth: newMonth,
      monthTransition: null,
      translationValue: isRTL && this.isHorizontal() ? -calendarMonthWidth : 0,
      nextFocusedDate: null,
      focusedDate: newFocusedDate
    }, function () {
      // we don't want to focus on the relevant calendar day after a month transition
      // if the user is navigating around using a mouse
      if (withMouseInteractions) {
        var activeElement = (0, _getActiveElement["default"])();

        if (activeElement && activeElement !== document.body && _this4.container.contains(activeElement) && activeElement.blur) {
          activeElement.blur();
        }
      }
    });
  };

  _proto.adjustDayPickerHeight = function adjustDayPickerHeight(newMonthHeight) {
    var _this5 = this;

    var monthHeight = newMonthHeight + MONTH_PADDING;

    if (monthHeight !== this.calendarMonthGridHeight) {
      this.transitionContainer.style.height = "".concat(monthHeight, "px");

      if (!this.calendarMonthGridHeight) {
        this.setCalendarMonthGridHeightTimeout = setTimeout(function () {
          _this5.setState({
            hasSetHeight: true
          });
        }, 0);
      }

      this.calendarMonthGridHeight = monthHeight;
    }
  };

  _proto.calculateAndSetDayPickerHeight = function calculateAndSetDayPickerHeight() {
    var _this$props8 = this.props,
        daySize = _this$props8.daySize,
        numberOfMonths = _this$props8.numberOfMonths;
    var monthTitleHeight = this.state.monthTitleHeight;
    var visibleCalendarWeeks = this.calendarMonthWeeks.slice(1, numberOfMonths + 1);
    var calendarMonthWeeksHeight = Math.max.apply(Math, [0].concat((0, _toConsumableArray2["default"])(visibleCalendarWeeks))) * (daySize - 1);
    var newMonthHeight = monthTitleHeight + calendarMonthWeeksHeight + 1;

    if (this.isHorizontal()) {
      this.adjustDayPickerHeight(newMonthHeight);
    }
  };

  _proto.openKeyboardShortcutsPanel = function openKeyboardShortcutsPanel(onCloseCallBack) {
    this.setState({
      showKeyboardShortcuts: true,
      onKeyboardShortcutsPanelClose: onCloseCallBack
    });
  };

  _proto.closeKeyboardShortcutsPanel = function closeKeyboardShortcutsPanel() {
    var onKeyboardShortcutsPanelClose = this.state.onKeyboardShortcutsPanelClose;

    if (onKeyboardShortcutsPanelClose) {
      onKeyboardShortcutsPanelClose();
    }

    this.setState({
      onKeyboardShortcutsPanelClose: null,
      showKeyboardShortcuts: false
    });
  };

  _proto.renderNavigation = function renderNavigation() {
    var _this$props9 = this.props,
        disablePrev = _this$props9.disablePrev,
        disableNext = _this$props9.disableNext,
        navPrev = _this$props9.navPrev,
        navNext = _this$props9.navNext,
        noNavButtons = _this$props9.noNavButtons,
        orientation = _this$props9.orientation,
        phrases = _this$props9.phrases,
        isRTL = _this$props9.isRTL;

    if (noNavButtons) {
      return null;
    }

    var onNextMonthClick = orientation === _constants.VERTICAL_SCROLLABLE ? this.multiplyScrollableMonths : this.onNextMonthClick;
    return _react["default"].createElement(_DayPickerNavigation["default"], {
      disablePrev: disablePrev,
      disableNext: disableNext,
      onPrevMonthClick: this.onPrevMonthClick,
      onNextMonthClick: onNextMonthClick,
      navPrev: navPrev,
      navNext: navNext,
      orientation: orientation,
      phrases: phrases,
      isRTL: isRTL
    });
  };

  _proto.renderWeekHeader = function renderWeekHeader(index) {
    var _this$props10 = this.props,
        daySize = _this$props10.daySize,
        horizontalMonthPadding = _this$props10.horizontalMonthPadding,
        orientation = _this$props10.orientation,
        weekDayFormat = _this$props10.weekDayFormat,
        styles = _this$props10.styles,
        css = _this$props10.css;
    var calendarMonthWidth = this.state.calendarMonthWidth;
    var verticalScrollable = orientation === _constants.VERTICAL_SCROLLABLE;
    var horizontalStyle = {
      left: index * calendarMonthWidth
    };
    var verticalStyle = {
      marginLeft: -calendarMonthWidth / 2
    };
    var weekHeaderStyle = {}; // no styles applied to the vertical-scrollable orientation

    if (this.isHorizontal()) {
      weekHeaderStyle = horizontalStyle;
    } else if (this.isVertical() && !verticalScrollable) {
      weekHeaderStyle = verticalStyle;
    }

    var firstDayOfWeek = this.getFirstDayOfWeek();
    var header = [];

    for (var i = 0; i < 7; i += 1) {
      header.push(_react["default"].createElement("li", (0, _extends2["default"])({
        key: i
      }, css(styles.DayPicker_weekHeader_li, {
        width: daySize
      })), _react["default"].createElement("small", null, (0, _moment["default"])().day((i + firstDayOfWeek) % 7).format(weekDayFormat))));
    }

    return _react["default"].createElement("div", (0, _extends2["default"])({}, css(styles.DayPicker_weekHeader, this.isVertical() && styles.DayPicker_weekHeader__vertical, verticalScrollable && styles.DayPicker_weekHeader__verticalScrollable, weekHeaderStyle, {
      padding: "0 ".concat(horizontalMonthPadding, "px")
    }), {
      key: "week-".concat(index)
    }), _react["default"].createElement("ul", css(styles.DayPicker_weekHeader_ul), header));
  };

  _proto.render = function render() {
    var _this6 = this;

    var _this$state8 = this.state,
        calendarMonthWidth = _this$state8.calendarMonthWidth,
        currentMonth = _this$state8.currentMonth,
        monthTransition = _this$state8.monthTransition,
        translationValue = _this$state8.translationValue,
        scrollableMonthMultiple = _this$state8.scrollableMonthMultiple,
        focusedDate = _this$state8.focusedDate,
        showKeyboardShortcuts = _this$state8.showKeyboardShortcuts,
        isTouch = _this$state8.isTouchDevice,
        hasSetHeight = _this$state8.hasSetHeight,
        calendarInfoWidth = _this$state8.calendarInfoWidth,
        monthTitleHeight = _this$state8.monthTitleHeight;
    var _this$props11 = this.props,
        css = _this$props11.css,
        enableOutsideDays = _this$props11.enableOutsideDays,
        numberOfMonths = _this$props11.numberOfMonths,
        orientation = _this$props11.orientation,
        modifiers = _this$props11.modifiers,
        withPortal = _this$props11.withPortal,
        onDayClick = _this$props11.onDayClick,
        onDayMouseEnter = _this$props11.onDayMouseEnter,
        onDayMouseLeave = _this$props11.onDayMouseLeave,
        firstDayOfWeek = _this$props11.firstDayOfWeek,
        renderMonthText = _this$props11.renderMonthText,
        renderCalendarDay = _this$props11.renderCalendarDay,
        renderDayContents = _this$props11.renderDayContents,
        renderCalendarInfo = _this$props11.renderCalendarInfo,
        renderMonthElement = _this$props11.renderMonthElement,
        renderKeyboardShortcutsButton = _this$props11.renderKeyboardShortcutsButton,
        calendarInfoPosition = _this$props11.calendarInfoPosition,
        hideKeyboardShortcutsPanel = _this$props11.hideKeyboardShortcutsPanel,
        onOutsideClick = _this$props11.onOutsideClick,
        monthFormat = _this$props11.monthFormat,
        daySize = _this$props11.daySize,
        isFocused = _this$props11.isFocused,
        isRTL = _this$props11.isRTL,
        styles = _this$props11.styles,
        theme = _this$props11.theme,
        phrases = _this$props11.phrases,
        verticalHeight = _this$props11.verticalHeight,
        dayAriaLabelFormat = _this$props11.dayAriaLabelFormat,
        noBorder = _this$props11.noBorder,
        transitionDuration = _this$props11.transitionDuration,
        verticalBorderSpacing = _this$props11.verticalBorderSpacing,
        horizontalMonthPadding = _this$props11.horizontalMonthPadding;
    var dayPickerHorizontalPadding = theme.reactDates.spacing.dayPickerHorizontalPadding;
    var isHorizontal = this.isHorizontal();
    var numOfWeekHeaders = this.isVertical() ? 1 : numberOfMonths;
    var weekHeaders = [];

    for (var i = 0; i < numOfWeekHeaders; i += 1) {
      weekHeaders.push(this.renderWeekHeader(i));
    }

    var verticalScrollable = orientation === _constants.VERTICAL_SCROLLABLE;
    var height;

    if (isHorizontal) {
      height = this.calendarMonthGridHeight;
    } else if (this.isVertical() && !verticalScrollable && !withPortal) {
      // If the user doesn't set a desired height,
      // we default back to this kind of made-up value that generally looks good
      height = verticalHeight || 1.75 * calendarMonthWidth;
    }

    var isCalendarMonthGridAnimating = monthTransition !== null;
    var shouldFocusDate = !isCalendarMonthGridAnimating && isFocused;
    var keyboardShortcutButtonLocation = _DayPickerKeyboardShortcuts.BOTTOM_RIGHT;

    if (this.isVertical()) {
      keyboardShortcutButtonLocation = withPortal ? _DayPickerKeyboardShortcuts.TOP_LEFT : _DayPickerKeyboardShortcuts.TOP_RIGHT;
    }

    var shouldAnimateHeight = isHorizontal && hasSetHeight;
    var calendarInfoPositionTop = calendarInfoPosition === _constants.INFO_POSITION_TOP;
    var calendarInfoPositionBottom = calendarInfoPosition === _constants.INFO_POSITION_BOTTOM;
    var calendarInfoPositionBefore = calendarInfoPosition === _constants.INFO_POSITION_BEFORE;
    var calendarInfoPositionAfter = calendarInfoPosition === _constants.INFO_POSITION_AFTER;
    var calendarInfoIsInline = calendarInfoPositionBefore || calendarInfoPositionAfter;

    var calendarInfo = renderCalendarInfo && _react["default"].createElement("div", (0, _extends2["default"])({
      ref: this.setCalendarInfoRef
    }, css(calendarInfoIsInline && styles.DayPicker_calendarInfo__horizontal)), renderCalendarInfo());

    var calendarInfoPanelWidth = renderCalendarInfo && calendarInfoIsInline ? calendarInfoWidth : 0;
    var firstVisibleMonthIndex = this.getFirstVisibleIndex();
    var wrapperHorizontalWidth = calendarMonthWidth * numberOfMonths + 2 * dayPickerHorizontalPadding; // Adding `1px` because of whitespace between 2 inline-block

    var fullHorizontalWidth = wrapperHorizontalWidth + calendarInfoPanelWidth + 1;
    var transitionContainerStyle = {
      width: isHorizontal && wrapperHorizontalWidth,
      height: height
    };
    var dayPickerWrapperStyle = {
      width: isHorizontal && wrapperHorizontalWidth
    };
    var dayPickerStyle = {
      width: isHorizontal && fullHorizontalWidth,
      // These values are to center the datepicker (approximately) on the page
      marginLeft: isHorizontal && withPortal ? -fullHorizontalWidth / 2 : null,
      marginTop: isHorizontal && withPortal ? -calendarMonthWidth / 2 : null
    };
    return _react["default"].createElement("div", (0, _extends2["default"])({
      role: "application",
      "aria-roledescription": phrases.roleDescription,
      "aria-label": phrases.calendarLabel
    }, css(styles.DayPicker, isHorizontal && styles.DayPicker__horizontal, verticalScrollable && styles.DayPicker__verticalScrollable, isHorizontal && withPortal && styles.DayPicker_portal__horizontal, this.isVertical() && withPortal && styles.DayPicker_portal__vertical, dayPickerStyle, !monthTitleHeight && styles.DayPicker__hidden, !noBorder && styles.DayPicker__withBorder)), _react["default"].createElement(_reactOutsideClickHandler["default"], {
      onOutsideClick: onOutsideClick
    }, (calendarInfoPositionTop || calendarInfoPositionBefore) && calendarInfo, _react["default"].createElement("div", css(dayPickerWrapperStyle, calendarInfoIsInline && isHorizontal && styles.DayPicker_wrapper__horizontal), _react["default"].createElement("div", (0, _extends2["default"])({}, css(styles.DayPicker_weekHeaders, isHorizontal && styles.DayPicker_weekHeaders__horizontal), {
      "aria-hidden": "true",
      role: "presentation"
    }), weekHeaders), _react["default"].createElement("div", (0, _extends2["default"])({}, css(styles.DayPicker_focusRegion), {
      ref: this.setContainerRef,
      onClick: function onClick(e) {
        e.stopPropagation();
      },
      onKeyDown: this.onKeyDown,
      onMouseUp: function onMouseUp() {
        _this6.setState({
          withMouseInteractions: true
        });
      },
      role: "region",
      tabIndex: -1
    }), !verticalScrollable && this.renderNavigation(), _react["default"].createElement("div", (0, _extends2["default"])({}, css(styles.DayPicker_transitionContainer, shouldAnimateHeight && styles.DayPicker_transitionContainer__horizontal, this.isVertical() && styles.DayPicker_transitionContainer__vertical, verticalScrollable && styles.DayPicker_transitionContainer__verticalScrollable, transitionContainerStyle), {
      ref: this.setTransitionContainerRef
    }), _react["default"].createElement(_CalendarMonthGrid["default"], {
      setMonthTitleHeight: !monthTitleHeight ? this.setMonthTitleHeight : undefined,
      translationValue: translationValue,
      enableOutsideDays: enableOutsideDays,
      firstVisibleMonthIndex: firstVisibleMonthIndex,
      initialMonth: currentMonth,
      isAnimating: isCalendarMonthGridAnimating,
      modifiers: modifiers,
      orientation: orientation,
      numberOfMonths: numberOfMonths * scrollableMonthMultiple,
      onDayClick: onDayClick,
      onDayMouseEnter: onDayMouseEnter,
      onDayMouseLeave: onDayMouseLeave,
      onMonthChange: this.onMonthChange,
      onYearChange: this.onYearChange,
      renderMonthText: renderMonthText,
      renderCalendarDay: renderCalendarDay,
      renderDayContents: renderDayContents,
      renderMonthElement: renderMonthElement,
      onMonthTransitionEnd: this.updateStateAfterMonthTransition,
      monthFormat: monthFormat,
      daySize: daySize,
      firstDayOfWeek: firstDayOfWeek,
      isFocused: shouldFocusDate,
      focusedDate: focusedDate,
      phrases: phrases,
      isRTL: isRTL,
      dayAriaLabelFormat: dayAriaLabelFormat,
      transitionDuration: transitionDuration,
      verticalBorderSpacing: verticalBorderSpacing,
      horizontalMonthPadding: horizontalMonthPadding
    }), verticalScrollable && this.renderNavigation()), !isTouch && !hideKeyboardShortcutsPanel && _react["default"].createElement(_DayPickerKeyboardShortcuts["default"], {
      block: this.isVertical() && !withPortal,
      buttonLocation: keyboardShortcutButtonLocation,
      showKeyboardShortcutsPanel: showKeyboardShortcuts,
      openKeyboardShortcutsPanel: this.openKeyboardShortcutsPanel,
      closeKeyboardShortcutsPanel: this.closeKeyboardShortcutsPanel,
      phrases: phrases,
      renderKeyboardShortcutsButton: renderKeyboardShortcutsButton
    }))), (calendarInfoPositionBottom || calendarInfoPositionAfter) && calendarInfo));
  };

  return DayPicker;
}(_react["default"].PureComponent || _react["default"].Component);

exports.PureDayPicker = DayPicker;
DayPicker.propTypes =  false ? undefined : {};
DayPicker.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref3) {
  var _ref3$reactDates = _ref3.reactDates,
      color = _ref3$reactDates.color,
      font = _ref3$reactDates.font,
      noScrollBarOnVerticalScrollable = _ref3$reactDates.noScrollBarOnVerticalScrollable,
      spacing = _ref3$reactDates.spacing,
      zIndex = _ref3$reactDates.zIndex;
  return {
    DayPicker: {
      background: color.background,
      position: 'relative',
      textAlign: (0, _noflip["default"])('left')
    },
    DayPicker__horizontal: {
      background: color.background
    },
    DayPicker__verticalScrollable: {
      height: '100%'
    },
    DayPicker__hidden: {
      visibility: 'hidden'
    },
    DayPicker__withBorder: {
      boxShadow: (0, _noflip["default"])('0 2px 6px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0, 0, 0, 0.07)'),
      borderRadius: 3
    },
    DayPicker_portal__horizontal: {
      boxShadow: 'none',
      position: 'absolute',
      left: (0, _noflip["default"])('50%'),
      top: '50%'
    },
    DayPicker_portal__vertical: {
      position: 'initial'
    },
    DayPicker_focusRegion: {
      outline: 'none'
    },
    DayPicker_calendarInfo__horizontal: {
      display: 'inline-block',
      verticalAlign: 'top'
    },
    DayPicker_wrapper__horizontal: {
      display: 'inline-block',
      verticalAlign: 'top'
    },
    DayPicker_weekHeaders: {
      position: 'relative'
    },
    DayPicker_weekHeaders__horizontal: {
      marginLeft: (0, _noflip["default"])(spacing.dayPickerHorizontalPadding)
    },
    DayPicker_weekHeader: {
      color: color.placeholderText,
      position: 'absolute',
      top: 62,
      zIndex: zIndex + 2,
      textAlign: (0, _noflip["default"])('left')
    },
    DayPicker_weekHeader__vertical: {
      left: (0, _noflip["default"])('50%')
    },
    DayPicker_weekHeader__verticalScrollable: {
      top: 0,
      display: 'table-row',
      borderBottom: "1px solid ".concat(color.core.border),
      background: color.background,
      marginLeft: (0, _noflip["default"])(0),
      left: (0, _noflip["default"])(0),
      width: '100%',
      textAlign: 'center'
    },
    DayPicker_weekHeader_ul: {
      listStyle: 'none',
      margin: '1px 0',
      paddingLeft: (0, _noflip["default"])(0),
      paddingRight: (0, _noflip["default"])(0),
      fontSize: font.size
    },
    DayPicker_weekHeader_li: {
      display: 'inline-block',
      textAlign: 'center'
    },
    DayPicker_transitionContainer: {
      position: 'relative',
      overflow: 'hidden',
      borderRadius: 3
    },
    DayPicker_transitionContainer__horizontal: {
      transition: 'height 0.2s ease-in-out'
    },
    DayPicker_transitionContainer__vertical: {
      width: '100%'
    },
    DayPicker_transitionContainer__verticalScrollable: _objectSpread({
      paddingTop: 20,
      height: '100%',
      position: 'absolute',
      top: 0,
      bottom: 0,
      right: (0, _noflip["default"])(0),
      left: (0, _noflip["default"])(0),
      overflowY: 'scroll'
    }, noScrollBarOnVerticalScrollable && {
      '-webkitOverflowScrolling': 'touch',
      '::-webkit-scrollbar': {
        '-webkit-appearance': 'none',
        display: 'none'
      }
    })
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(DayPicker);

exports["default"] = _default;

/***/ }),

/***/ "NykK":
/***/ (function(module, exports, __webpack_require__) {

var Symbol = __webpack_require__("nmnc"),
    getRawTag = __webpack_require__("AP2z"),
    objectToString = __webpack_require__("KfNM");

/** `Object#toString` result references. */
var nullTag = '[object Null]',
    undefinedTag = '[object Undefined]';

/** Built-in value references. */
var symToStringTag = Symbol ? Symbol.toStringTag : undefined;

/**
 * The base implementation of `getTag` without fallbacks for buggy environments.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the `toStringTag`.
 */
function baseGetTag(value) {
  if (value == null) {
    return value === undefined ? undefinedTag : nullTag;
  }
  return (symToStringTag && symToStringTag in Object(value))
    ? getRawTag(value)
    : objectToString(value);
}

module.exports = baseGetTag;


/***/ }),

/***/ "PJYZ":
/***/ (function(module, exports) {

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

module.exports = _assertThisInitialized;

/***/ }),

/***/ "Pq96":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isPrevMonth;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _isSameMonth = _interopRequireDefault(__webpack_require__("ulUS"));

function isPrevMonth(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false;
  return (0, _isSameMonth["default"])(a.clone().subtract(1, 'month'), b);
}

/***/ }),

/***/ "QEu6":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
var CHANNEL = exports.CHANNEL = '__direction__';

var DIRECTIONS = exports.DIRECTIONS = {
  LTR: 'ltr',
  RTL: 'rtl'
};

/***/ }),

/***/ "QIyF":
/***/ (function(module, exports, __webpack_require__) {

var root = __webpack_require__("Kz5y");

/**
 * Gets the timestamp of the number of milliseconds that have elapsed since
 * the Unix epoch (1 January 1970 00:00:00 UTC).
 *
 * @static
 * @memberOf _
 * @since 2.4.0
 * @category Date
 * @returns {number} Returns the timestamp.
 * @example
 *
 * _.defer(function(stamp) {
 *   console.log(_.now() - stamp);
 * }, _.now());
 * // => Logs the number of milliseconds it took for the deferred invocation.
 */
var now = function() {
  return root.Date.now();
};

module.exports = now;


/***/ }),

/***/ "RIqP":
/***/ (function(module, exports, __webpack_require__) {

var arrayWithoutHoles = __webpack_require__("Ijbi");

var iterableToArray = __webpack_require__("EbDI");

var nonIterableSpread = __webpack_require__("Bnag");

function _toConsumableArray(arr) {
  return arrayWithoutHoles(arr) || iterableToArray(arr) || nonIterableSpread();
}

module.exports = _toConsumableArray;

/***/ }),

/***/ "TG4+":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__("284h");

var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.withStyles = withStyles;
Object.defineProperty(exports, "withStylesPropTypes", {
  enumerable: true,
  get: function get() {
    return _withStylesPropTypes.withStylesPropTypes;
  }
});
exports.css = exports["default"] = void 0;

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _hoistNonReactStatics = _interopRequireDefault(__webpack_require__("2mql"));

var _getComponentName = _interopRequireDefault(__webpack_require__("e4Aj"));

var _emptyStylesFn = _interopRequireDefault(__webpack_require__("vg41"));

var _perf = _interopRequireDefault(__webpack_require__("Ddh5"));

var _WithStylesContext = _interopRequireWildcard(__webpack_require__("+D8X"));

var _ThemedStyleSheet = _interopRequireWildcard(__webpack_require__("030x"));

var _withStylesPropTypes = __webpack_require__("AoOu");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * A higher order function that returns a higher order class component that injects
 * CSS-in-JS props derived from the react-with-styles theme, interface, and
 * direction provided through the WithStylesContext provider.
 *
 * The function should be used as follows:
 * `withStyles((theme) => styles, options)(Component)`
 *
 * Options can be used to rename the injected props, memoize the component, and flush
 * the styles to the styles tag (or whatever the interface implements as flush) before
 * rendering.
 *
 * @export
 * @param {Function|null|undefined} [stylesFn=EMPTY_STYLES_FN]
 * @param {Object} [{
 *     stylesPropName = 'styles',
 *     themePropName = 'theme',
 *     cssPropName = 'css',
 *     flushBefore = false,
 *     pureComponent = false,
 *   }={}]
 * @returns a higher order component that wraps the provided component and injects
 * the react-with-styles css, styles, and theme props.
 */
function withStyles() {
  var stylesFn = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _emptyStylesFn["default"];

  var _ref = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {},
      _ref$stylesPropName = _ref.stylesPropName,
      stylesPropName = _ref$stylesPropName === void 0 ? 'styles' : _ref$stylesPropName,
      _ref$themePropName = _ref.themePropName,
      themePropName = _ref$themePropName === void 0 ? 'theme' : _ref$themePropName,
      _ref$cssPropName = _ref.cssPropName,
      cssPropName = _ref$cssPropName === void 0 ? 'css' : _ref$cssPropName,
      _ref$flushBefore = _ref.flushBefore,
      flushBefore = _ref$flushBefore === void 0 ? false : _ref$flushBefore,
      _ref$pureComponent = _ref.pureComponent,
      pureComponent = _ref$pureComponent === void 0 ? false : _ref$pureComponent;

  stylesFn = stylesFn || _emptyStylesFn["default"];
  var BaseClass = pureComponent ? _react["default"].PureComponent : _react["default"].Component;
  /** Cache for storing the result of stylesFn(theme) for all themes. */

  var stylesFnResultCacheMap = new Map(); // The function that wraps the provided component in a wrapper
  // component that injects the withStyles props

  return function withStylesHOC(WrappedComponent) {
    var wrappedComponentName = (0, _getComponentName["default"])(WrappedComponent); // The wrapper component that injects the withStyles props

    var WithStyles =
    /*#__PURE__*/
    function (_BaseClass) {
      (0, _inheritsLoose2["default"])(WithStyles, _BaseClass);

      function WithStyles(props, context) {
        var _this;

        _this = _BaseClass.call(this, props, context) || this;
        /** Cache for storing the current props and objects used to derive them */

        _this.cache = {
          rtl: {},
          ltr: {}
        };
        return _this;
      }

      var _proto = WithStyles.prototype;

      _proto.getCache = function getCache(direction) {
        return this.cache[direction];
      };

      _proto.updateCache = function updateCache(direction, results) {
        this.cache[direction] = results;
      };

      _proto.getCurrentInterface = function getCurrentInterface() {
        // Fallback to the singleton implementation
        return this.context && this.context.stylesInterface || (0, _ThemedStyleSheet._getInterface)();
      };

      _proto.getCurrentTheme = function getCurrentTheme() {
        // Fallback to the singleton implementation
        return this.context && this.context.stylesTheme || (0, _ThemedStyleSheet._getTheme)();
      };

      _proto.getCurrentDirection = function getCurrentDirection() {
        return this.context && this.context.direction || _WithStylesContext.DIRECTIONS.LTR;
      };

      _proto.makeCreate = function makeCreate(direction, stylesInterface) {
        var directionSelector = direction === _WithStylesContext.DIRECTIONS.RTL ? 'RTL' : 'LTR';
        var create = stylesInterface["create".concat(directionSelector)] || stylesInterface.create;

        if (false) {}

        return create;
      };

      _proto.makeResolve = function makeResolve(direction, stylesInterface) {
        var directionSelector = direction === _WithStylesContext.DIRECTIONS.RTL ? 'RTL' : 'LTR';
        var resolve = stylesInterface["resolve".concat(directionSelector)] || stylesInterface.resolve;

        if (false) {}

        return resolve;
      };

      _proto.stylesFnResult = function stylesFnResult(theme) {
        // Get and store the result in the stylesFnResultsCache for the component
        // -- not the instance -- so we only apply the theme to the stylesFn
        // once per theme for this component.
        var cachedResultForTheme = stylesFnResultCacheMap.get(theme);
        var stylesFnResult = cachedResultForTheme || stylesFn(theme) || {};
        stylesFnResultCacheMap.set(theme, stylesFnResult); // cache the result of stylesFn(theme)

        return stylesFnResult;
      };

      _proto.getProps = function getProps() {
        // Get the styles interface, theme, and direction from context
        var stylesInterface = this.getCurrentInterface();
        var theme = this.getCurrentTheme();
        var direction = this.getCurrentDirection(); // Use a cache to store the interface methods and created styles by direction.
        // This way, if the theme and the interface don't change, we do not recalculate
        // styles or any other interface derivations. They are effectively only calculated
        // once per direction, until the theme or interface change.
        // Assume: always an object.

        var cache = this.getCache(direction); // Determine what's changed

        var interfaceChanged = !cache.stylesInterface || stylesInterface && cache.stylesInterface !== stylesInterface;
        var themeChanged = cache.theme !== theme; // If the interface and theme haven't changed for this direction,
        // we return the cached props immediately.

        if (!interfaceChanged && !themeChanged) {
          return cache.props;
        } // If the theme or the interface changed, then there are some values
        // we need to recalculate. We avoid recalculating the ones we already
        // calculated in the past if the objects they're derived from have not
        // changed.


        var create = interfaceChanged && this.makeCreate(direction, stylesInterface) || cache.create;
        var resolve = interfaceChanged && this.makeResolve(direction, stylesInterface) || cache.resolve; // Derive the css function prop

        var css = interfaceChanged && function () {
          for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
            args[_key] = arguments[_key];
          }

          return resolve(args);
        } || cache.props.css; // Get or calculate the themed styles from the stylesFn:
        // Uses a separate cache at the component level, not at the instance level,
        // to only apply the theme to the stylesFn once per component class per theme.


        var stylesFnResult = this.stylesFnResult(theme); // Derive the styles prop: recalculate it if create changed, or stylesFnResult changed

        var styles = (interfaceChanged || stylesFnResult !== cache.stylesFnResult) && create(stylesFnResult) || cache.props.styles; // Put the new props together

        var props = {
          css: css,
          styles: styles,
          theme: theme
        }; // Update the cache with all the new values

        this.updateCache(direction, {
          stylesInterface: stylesInterface,
          theme: theme,
          create: create,
          resolve: resolve,
          stylesFnResult: stylesFnResult,
          props: props
        });
        return props;
      };

      _proto.flush = function flush() {
        var stylesInterface = this.getCurrentInterface();

        if (stylesInterface && stylesInterface.flush) {
          stylesInterface.flush();
        }
      };

      _proto.render = function render() {
        var _ref2;

        // We only want to re-render if the theme, stylesInterface, or direction change.
        // These values are in context so we're listening for their updates.
        // this.getProps() derives the props from the theme, stylesInterface, and direction in
        // context, and memoizes them on the instance per direction.
        var _this$getProps = this.getProps(),
            theme = _this$getProps.theme,
            styles = _this$getProps.styles,
            css = _this$getProps.css; // Flush if specified


        if (flushBefore) {
          this.flush();
        }

        return _react["default"].createElement(WrappedComponent, (0, _extends2["default"])({}, this.props, (_ref2 = {}, (0, _defineProperty2["default"])(_ref2, themePropName, theme), (0, _defineProperty2["default"])(_ref2, stylesPropName, styles), (0, _defineProperty2["default"])(_ref2, cssPropName, css), _ref2)));
      };

      return WithStyles;
    }(BaseClass); // Copy the wrapped component's prop types and default props on WithStyles


    if (WrappedComponent.propTypes) {
      WithStyles.propTypes = _objectSpread({}, WrappedComponent.propTypes);
      delete WithStyles.propTypes[stylesPropName];
      delete WithStyles.propTypes[themePropName];
      delete WithStyles.propTypes[cssPropName];
    }

    if (WrappedComponent.defaultProps) {
      WithStyles.defaultProps = _objectSpread({}, WrappedComponent.defaultProps);
    }

    WithStyles.contextType = _WithStylesContext["default"];
    WithStyles.WrappedComponent = WrappedComponent;
    WithStyles.displayName = "withStyles(".concat(wrappedComponentName, ")");
    return (0, _hoistNonReactStatics["default"])(WithStyles, WrappedComponent);
  };
}

var _default = withStyles;
/**
* Deprecated: Do not use directly. Please wrap your component in `withStyles` and use the `css`
* prop injected via props instead.
*/

exports["default"] = _default;
var css = _ThemedStyleSheet["default"].resolveLTR;
exports.css = css;

/***/ }),

/***/ "TOwV":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


if (true) {
  module.exports = __webpack_require__("qT12");
} else {}


/***/ }),

/***/ "TUgy":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _reactMomentProptypes = _interopRequireDefault(__webpack_require__("XGBb"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _IconPositionShape = _interopRequireDefault(__webpack_require__("Bh6R"));

var _OrientationShape = _interopRequireDefault(__webpack_require__("qOSK"));

var _AnchorDirectionShape = _interopRequireDefault(__webpack_require__("0+bg"));

var _OpenDirectionShape = _interopRequireDefault(__webpack_require__("iuLr"));

var _DayOfWeekShape = _interopRequireDefault(__webpack_require__("2S2E"));

var _CalendarInfoPositionShape = _interopRequireDefault(__webpack_require__("oR9Z"));

var _default = {
  // required props for a functional interactive SingleDatePicker
  date: _reactMomentProptypes["default"].momentObj,
  onDateChange: _propTypes["default"].func.isRequired,
  focused: _propTypes["default"].bool,
  onFocusChange: _propTypes["default"].func.isRequired,
  // input related props
  id: _propTypes["default"].string.isRequired,
  placeholder: _propTypes["default"].string,
  ariaLabel: _propTypes["default"].string,
  disabled: _propTypes["default"].bool,
  required: _propTypes["default"].bool,
  readOnly: _propTypes["default"].bool,
  screenReaderInputMessage: _propTypes["default"].string,
  showClearDate: _propTypes["default"].bool,
  customCloseIcon: _propTypes["default"].node,
  showDefaultInputIcon: _propTypes["default"].bool,
  inputIconPosition: _IconPositionShape["default"],
  customInputIcon: _propTypes["default"].node,
  noBorder: _propTypes["default"].bool,
  block: _propTypes["default"].bool,
  small: _propTypes["default"].bool,
  regular: _propTypes["default"].bool,
  verticalSpacing: _airbnbPropTypes.nonNegativeInteger,
  keepFocusOnInput: _propTypes["default"].bool,
  // calendar presentation and interaction related props
  renderMonthText: (0, _airbnbPropTypes.mutuallyExclusiveProps)(_propTypes["default"].func, 'renderMonthText', 'renderMonthElement'),
  renderMonthElement: (0, _airbnbPropTypes.mutuallyExclusiveProps)(_propTypes["default"].func, 'renderMonthText', 'renderMonthElement'),
  orientation: _OrientationShape["default"],
  anchorDirection: _AnchorDirectionShape["default"],
  openDirection: _OpenDirectionShape["default"],
  horizontalMargin: _propTypes["default"].number,
  withPortal: _propTypes["default"].bool,
  withFullScreenPortal: _propTypes["default"].bool,
  appendToBody: _propTypes["default"].bool,
  disableScroll: _propTypes["default"].bool,
  initialVisibleMonth: _propTypes["default"].func,
  firstDayOfWeek: _DayOfWeekShape["default"],
  numberOfMonths: _propTypes["default"].number,
  keepOpenOnDateSelect: _propTypes["default"].bool,
  reopenPickerOnClearDate: _propTypes["default"].bool,
  renderCalendarInfo: _propTypes["default"].func,
  calendarInfoPosition: _CalendarInfoPositionShape["default"],
  hideKeyboardShortcutsPanel: _propTypes["default"].bool,
  daySize: _airbnbPropTypes.nonNegativeInteger,
  isRTL: _propTypes["default"].bool,
  verticalHeight: _airbnbPropTypes.nonNegativeInteger,
  transitionDuration: _airbnbPropTypes.nonNegativeInteger,
  horizontalMonthPadding: _airbnbPropTypes.nonNegativeInteger,
  // navigation related props
  navPrev: _propTypes["default"].node,
  navNext: _propTypes["default"].node,
  onPrevMonthClick: _propTypes["default"].func,
  onNextMonthClick: _propTypes["default"].func,
  onClose: _propTypes["default"].func,
  // day presentation and interaction related props
  renderCalendarDay: _propTypes["default"].func,
  renderDayContents: _propTypes["default"].func,
  enableOutsideDays: _propTypes["default"].bool,
  isDayBlocked: _propTypes["default"].func,
  isOutsideRange: _propTypes["default"].func,
  isDayHighlighted: _propTypes["default"].func,
  // internationalization props
  displayFormat: _propTypes["default"].oneOfType([_propTypes["default"].string, _propTypes["default"].func]),
  monthFormat: _propTypes["default"].string,
  weekDayFormat: _propTypes["default"].string,
  phrases: _propTypes["default"].shape((0, _getPhrasePropTypes["default"])(_defaultPhrases.SingleDatePickerPhrases)),
  dayAriaLabelFormat: _propTypes["default"].string
};
exports["default"] = _default;

/***/ }),

/***/ "TUyu":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = registerCSSInterfaceWithDefaultTheme;

var _reactWithStylesInterfaceCss = _interopRequireDefault(__webpack_require__("lzPt"));

var _registerInterfaceWithDefaultTheme = _interopRequireDefault(__webpack_require__("WI5Z"));

function registerCSSInterfaceWithDefaultTheme() {
  (0, _registerInterfaceWithDefaultTheme["default"])(_reactWithStylesInterfaceCss["default"]);
}

/***/ }),

/***/ "Teho":
/***/ (function(module, exports) {

module.exports = function isPrimitive(value) {
	return value === null || (typeof value !== 'function' && typeof value !== 'object');
};


/***/ }),

/***/ "Thzv":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _reactMomentProptypes = _interopRequireDefault(__webpack_require__("XGBb"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _consolidatedEvents = __webpack_require__("1TsT");

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _noflip = _interopRequireDefault(__webpack_require__("2WTt"));

var _CalendarMonth = _interopRequireDefault(__webpack_require__("mMiH"));

var _isTransitionEndSupported = _interopRequireDefault(__webpack_require__("dRQD"));

var _getTransformStyles = _interopRequireDefault(__webpack_require__("q86A"));

var _getCalendarMonthWidth = _interopRequireDefault(__webpack_require__("m2ax"));

var _toISOMonthString = _interopRequireDefault(__webpack_require__("jenk"));

var _isPrevMonth = _interopRequireDefault(__webpack_require__("Pq96"));

var _isNextMonth = _interopRequireDefault(__webpack_require__("6HWY"));

var _ModifiersShape = _interopRequireDefault(__webpack_require__("J7JS"));

var _ScrollableOrientationShape = _interopRequireDefault(__webpack_require__("aE6U"));

var _DayOfWeekShape = _interopRequireDefault(__webpack_require__("2S2E"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  enableOutsideDays: false,
  firstVisibleMonthIndex: 0,
  horizontalMonthPadding: 13,
  initialMonth: (0, _moment["default"])(),
  isAnimating: false,
  numberOfMonths: 1,
  modifiers: {},
  orientation: _constants.HORIZONTAL_ORIENTATION,
  onDayClick: function onDayClick() {},
  onDayMouseEnter: function onDayMouseEnter() {},
  onDayMouseLeave: function onDayMouseLeave() {},
  onMonthChange: function onMonthChange() {},
  onYearChange: function onYearChange() {},
  onMonthTransitionEnd: function onMonthTransitionEnd() {},
  renderMonthText: null,
  renderCalendarDay: undefined,
  renderDayContents: null,
  translationValue: null,
  renderMonthElement: null,
  daySize: _constants.DAY_SIZE,
  focusedDate: null,
  isFocused: false,
  firstDayOfWeek: null,
  setMonthTitleHeight: null,
  isRTL: false,
  transitionDuration: 200,
  verticalBorderSpacing: undefined,
  // i18n
  monthFormat: 'MMMM YYYY',
  // english locale
  phrases: _defaultPhrases.CalendarDayPhrases,
  dayAriaLabelFormat: undefined
};

function getMonths(initialMonth, numberOfMonths, withoutTransitionMonths) {
  var month = initialMonth.clone();
  if (!withoutTransitionMonths) month = month.subtract(1, 'month');
  var months = [];

  for (var i = 0; i < (withoutTransitionMonths ? numberOfMonths : numberOfMonths + 2); i += 1) {
    months.push(month);
    month = month.clone().add(1, 'month');
  }

  return months;
}

var CalendarMonthGrid =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(CalendarMonthGrid, _ref);
  var _proto = CalendarMonthGrid.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function CalendarMonthGrid(props) {
    var _this;

    _this = _ref.call(this, props) || this;
    var withoutTransitionMonths = props.orientation === _constants.VERTICAL_SCROLLABLE;
    _this.state = {
      months: getMonths(props.initialMonth, props.numberOfMonths, withoutTransitionMonths)
    };
    _this.isTransitionEndSupported = (0, _isTransitionEndSupported["default"])();
    _this.onTransitionEnd = _this.onTransitionEnd.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setContainerRef = _this.setContainerRef.bind((0, _assertThisInitialized2["default"])(_this));
    _this.locale = _moment["default"].locale();
    _this.onMonthSelect = _this.onMonthSelect.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onYearSelect = _this.onYearSelect.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.componentDidMount = function componentDidMount() {
    this.removeEventListener = (0, _consolidatedEvents.addEventListener)(this.container, 'transitionend', this.onTransitionEnd);
  };

  _proto.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
    var _this2 = this;

    var initialMonth = nextProps.initialMonth,
        numberOfMonths = nextProps.numberOfMonths,
        orientation = nextProps.orientation;
    var months = this.state.months;
    var _this$props = this.props,
        prevInitialMonth = _this$props.initialMonth,
        prevNumberOfMonths = _this$props.numberOfMonths;
    var hasMonthChanged = !prevInitialMonth.isSame(initialMonth, 'month');
    var hasNumberOfMonthsChanged = prevNumberOfMonths !== numberOfMonths;
    var newMonths = months;

    if (hasMonthChanged && !hasNumberOfMonthsChanged) {
      if ((0, _isNextMonth["default"])(prevInitialMonth, initialMonth)) {
        newMonths = months.slice(1);
        newMonths.push(months[months.length - 1].clone().add(1, 'month'));
      } else if ((0, _isPrevMonth["default"])(prevInitialMonth, initialMonth)) {
        newMonths = months.slice(0, months.length - 1);
        newMonths.unshift(months[0].clone().subtract(1, 'month'));
      } else {
        var withoutTransitionMonths = orientation === _constants.VERTICAL_SCROLLABLE;
        newMonths = getMonths(initialMonth, numberOfMonths, withoutTransitionMonths);
      }
    }

    if (hasNumberOfMonthsChanged) {
      var _withoutTransitionMonths = orientation === _constants.VERTICAL_SCROLLABLE;

      newMonths = getMonths(initialMonth, numberOfMonths, _withoutTransitionMonths);
    }

    var momentLocale = _moment["default"].locale();

    if (this.locale !== momentLocale) {
      this.locale = momentLocale;
      newMonths = newMonths.map(function (m) {
        return m.locale(_this2.locale);
      });
    }

    this.setState({
      months: newMonths
    });
  };

  _proto.componentDidUpdate = function componentDidUpdate() {
    var _this$props2 = this.props,
        isAnimating = _this$props2.isAnimating,
        transitionDuration = _this$props2.transitionDuration,
        onMonthTransitionEnd = _this$props2.onMonthTransitionEnd; // For IE9, immediately call onMonthTransitionEnd instead of
    // waiting for the animation to complete. Similarly, if transitionDuration
    // is set to 0, also immediately invoke the onMonthTransitionEnd callback

    if ((!this.isTransitionEndSupported || !transitionDuration) && isAnimating) {
      onMonthTransitionEnd();
    }
  };

  _proto.componentWillUnmount = function componentWillUnmount() {
    if (this.removeEventListener) this.removeEventListener();
  };

  _proto.onTransitionEnd = function onTransitionEnd() {
    var onMonthTransitionEnd = this.props.onMonthTransitionEnd;
    onMonthTransitionEnd();
  };

  _proto.onMonthSelect = function onMonthSelect(currentMonth, newMonthVal) {
    var newMonth = currentMonth.clone();
    var _this$props3 = this.props,
        onMonthChange = _this$props3.onMonthChange,
        orientation = _this$props3.orientation;
    var months = this.state.months;
    var withoutTransitionMonths = orientation === _constants.VERTICAL_SCROLLABLE;
    var initialMonthSubtraction = months.indexOf(currentMonth);

    if (!withoutTransitionMonths) {
      initialMonthSubtraction -= 1;
    }

    newMonth.set('month', newMonthVal).subtract(initialMonthSubtraction, 'months');
    onMonthChange(newMonth);
  };

  _proto.onYearSelect = function onYearSelect(currentMonth, newYearVal) {
    var newMonth = currentMonth.clone();
    var _this$props4 = this.props,
        onYearChange = _this$props4.onYearChange,
        orientation = _this$props4.orientation;
    var months = this.state.months;
    var withoutTransitionMonths = orientation === _constants.VERTICAL_SCROLLABLE;
    var initialMonthSubtraction = months.indexOf(currentMonth);

    if (!withoutTransitionMonths) {
      initialMonthSubtraction -= 1;
    }

    newMonth.set('year', newYearVal).subtract(initialMonthSubtraction, 'months');
    onYearChange(newMonth);
  };

  _proto.setContainerRef = function setContainerRef(ref) {
    this.container = ref;
  };

  _proto.render = function render() {
    var _this3 = this;

    var _this$props5 = this.props,
        enableOutsideDays = _this$props5.enableOutsideDays,
        firstVisibleMonthIndex = _this$props5.firstVisibleMonthIndex,
        horizontalMonthPadding = _this$props5.horizontalMonthPadding,
        isAnimating = _this$props5.isAnimating,
        modifiers = _this$props5.modifiers,
        numberOfMonths = _this$props5.numberOfMonths,
        monthFormat = _this$props5.monthFormat,
        orientation = _this$props5.orientation,
        translationValue = _this$props5.translationValue,
        daySize = _this$props5.daySize,
        onDayMouseEnter = _this$props5.onDayMouseEnter,
        onDayMouseLeave = _this$props5.onDayMouseLeave,
        onDayClick = _this$props5.onDayClick,
        renderMonthText = _this$props5.renderMonthText,
        renderCalendarDay = _this$props5.renderCalendarDay,
        renderDayContents = _this$props5.renderDayContents,
        renderMonthElement = _this$props5.renderMonthElement,
        onMonthTransitionEnd = _this$props5.onMonthTransitionEnd,
        firstDayOfWeek = _this$props5.firstDayOfWeek,
        focusedDate = _this$props5.focusedDate,
        isFocused = _this$props5.isFocused,
        isRTL = _this$props5.isRTL,
        styles = _this$props5.styles,
        phrases = _this$props5.phrases,
        dayAriaLabelFormat = _this$props5.dayAriaLabelFormat,
        transitionDuration = _this$props5.transitionDuration,
        verticalBorderSpacing = _this$props5.verticalBorderSpacing,
        setMonthTitleHeight = _this$props5.setMonthTitleHeight,
        css = _this$props5.css;
    var months = this.state.months;
    var isVertical = orientation === _constants.VERTICAL_ORIENTATION;
    var isVerticalScrollable = orientation === _constants.VERTICAL_SCROLLABLE;
    var isHorizontal = orientation === _constants.HORIZONTAL_ORIENTATION;
    var calendarMonthWidth = (0, _getCalendarMonthWidth["default"])(daySize, horizontalMonthPadding);
    var width = isVertical || isVerticalScrollable ? calendarMonthWidth : (numberOfMonths + 2) * calendarMonthWidth;
    var transformType = isVertical || isVerticalScrollable ? 'translateY' : 'translateX';
    var transformValue = "".concat(transformType, "(").concat(translationValue, "px)");
    return _react["default"].createElement("div", (0, _extends2["default"])({}, css(styles.CalendarMonthGrid, isHorizontal && styles.CalendarMonthGrid__horizontal, isVertical && styles.CalendarMonthGrid__vertical, isVerticalScrollable && styles.CalendarMonthGrid__vertical_scrollable, isAnimating && styles.CalendarMonthGrid__animating, isAnimating && transitionDuration && {
      transition: "transform ".concat(transitionDuration, "ms ease-in-out")
    }, _objectSpread({}, (0, _getTransformStyles["default"])(transformValue), {
      width: width
    })), {
      ref: this.setContainerRef,
      onTransitionEnd: onMonthTransitionEnd
    }), months.map(function (month, i) {
      var isVisible = i >= firstVisibleMonthIndex && i < firstVisibleMonthIndex + numberOfMonths;
      var hideForAnimation = i === 0 && !isVisible;
      var showForAnimation = i === 0 && isAnimating && isVisible;
      var monthString = (0, _toISOMonthString["default"])(month);
      return _react["default"].createElement("div", (0, _extends2["default"])({
        key: monthString
      }, css(isHorizontal && styles.CalendarMonthGrid_month__horizontal, hideForAnimation && styles.CalendarMonthGrid_month__hideForAnimation, showForAnimation && !isVertical && !isRTL && {
        position: 'absolute',
        left: -calendarMonthWidth
      }, showForAnimation && !isVertical && isRTL && {
        position: 'absolute',
        right: 0
      }, showForAnimation && isVertical && {
        position: 'absolute',
        top: -translationValue
      }, !isVisible && !isAnimating && styles.CalendarMonthGrid_month__hidden)), _react["default"].createElement(_CalendarMonth["default"], {
        month: month,
        isVisible: isVisible,
        enableOutsideDays: enableOutsideDays,
        modifiers: modifiers[monthString],
        monthFormat: monthFormat,
        orientation: orientation,
        onDayMouseEnter: onDayMouseEnter,
        onDayMouseLeave: onDayMouseLeave,
        onDayClick: onDayClick,
        onMonthSelect: _this3.onMonthSelect,
        onYearSelect: _this3.onYearSelect,
        renderMonthText: renderMonthText,
        renderCalendarDay: renderCalendarDay,
        renderDayContents: renderDayContents,
        renderMonthElement: renderMonthElement,
        firstDayOfWeek: firstDayOfWeek,
        daySize: daySize,
        focusedDate: isVisible ? focusedDate : null,
        isFocused: isFocused,
        phrases: phrases,
        setMonthTitleHeight: setMonthTitleHeight,
        dayAriaLabelFormat: dayAriaLabelFormat,
        verticalBorderSpacing: verticalBorderSpacing,
        horizontalMonthPadding: horizontalMonthPadding
      }));
    }));
  };

  return CalendarMonthGrid;
}(_react["default"].PureComponent || _react["default"].Component);

CalendarMonthGrid.propTypes =  false ? undefined : {};
CalendarMonthGrid.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref2) {
  var _ref2$reactDates = _ref2.reactDates,
      color = _ref2$reactDates.color,
      noScrollBarOnVerticalScrollable = _ref2$reactDates.noScrollBarOnVerticalScrollable,
      spacing = _ref2$reactDates.spacing,
      zIndex = _ref2$reactDates.zIndex;
  return {
    CalendarMonthGrid: {
      background: color.background,
      textAlign: (0, _noflip["default"])('left'),
      zIndex: zIndex
    },
    CalendarMonthGrid__animating: {
      zIndex: zIndex + 1
    },
    CalendarMonthGrid__horizontal: {
      position: 'absolute',
      left: (0, _noflip["default"])(spacing.dayPickerHorizontalPadding)
    },
    CalendarMonthGrid__vertical: {
      margin: '0 auto'
    },
    CalendarMonthGrid__vertical_scrollable: _objectSpread({
      margin: '0 auto',
      overflowY: 'scroll'
    }, noScrollBarOnVerticalScrollable && {
      '-webkitOverflowScrolling': 'touch',
      '::-webkit-scrollbar': {
        '-webkit-appearance': 'none',
        display: 'none'
      }
    }),
    CalendarMonthGrid_month__horizontal: {
      display: 'inline-block',
      verticalAlign: 'top',
      minHeight: '100%'
    },
    CalendarMonthGrid_month__hideForAnimation: {
      position: 'absolute',
      zIndex: zIndex - 1,
      opacity: 0,
      pointerEvents: 'none'
    },
    CalendarMonthGrid_month__hidden: {
      visibility: 'hidden'
    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(CalendarMonthGrid);

exports["default"] = _default;

/***/ }),

/***/ "TqRt":
/***/ (function(module, exports) {

function _interopRequireDefault(obj) {
  return obj && obj.__esModule ? obj : {
    "default": obj
  };
}

module.exports = _interopRequireDefault;

/***/ }),

/***/ "UVaH":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

var origSymbol = global.Symbol;
var hasSymbolSham = __webpack_require__("FpZJ");

module.exports = function hasNativeSymbols() {
	if (typeof origSymbol !== 'function') { return false; }
	if (typeof Symbol !== 'function') { return false; }
	if (typeof origSymbol('foo') !== 'symbol') { return false; }
	if (typeof Symbol('bar') !== 'symbol') { return false; }

	return hasSymbolSham();
};

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("yLpj")))

/***/ }),

/***/ "UyxP":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = toLocalizedDateString;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _toMomentObject = _interopRequireDefault(__webpack_require__("WmS1"));

var _constants = __webpack_require__("Fv1B");

function toLocalizedDateString(date, currentFormat) {
  var dateObj = _moment["default"].isMoment(date) ? date : (0, _toMomentObject["default"])(date, currentFormat);
  if (!dateObj) return null;
  return dateObj.format(_constants.DISPLAY_FORMAT);
}

/***/ }),

/***/ "VDVV":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _arrayPrototype = _interopRequireDefault(__webpack_require__("/ZKw"));

var _globalCache = _interopRequireDefault(__webpack_require__("9pTB"));

var _constants = __webpack_require__("kFtd");

var _getClassName = _interopRequireDefault(__webpack_require__("nLTY"));

var _separateStyles2 = _interopRequireDefault(__webpack_require__("3HjQ"));

/**
 * Function required as part of the react-with-styles interface. Parses the styles provided by
 * react-with-styles to produce class names based on the style name and optionally the namespace if
 * available.
 *
 * stylesObject {Object} The styles object passed to withStyles.
 *
 * Return an object mapping style names to class names.
 */
function create(stylesObject) {
  var stylesToClasses = {};
  var styleNames = Object.keys(stylesObject);
  var sharedState = _globalCache["default"].get(_constants.GLOBAL_CACHE_KEY) || {};
  var _sharedState$namespac = sharedState.namespace,
      namespace = _sharedState$namespac === void 0 ? '' : _sharedState$namespac;
  styleNames.forEach(function (styleName) {
    var className = (0, _getClassName["default"])(namespace, styleName);
    stylesToClasses[styleName] = className;
  });
  return stylesToClasses;
}
/**
 * Process styles to be consumed by a component.
 *
 * stylesArray {Array} Array of the following: values returned by create, plain JavaScript objects
 * representing inline styles, or arrays thereof.
 *
 * Return an object with optional className and style properties to be spread on a component.
 */


function resolve(stylesArray) {
  var flattenedStyles = (0, _arrayPrototype["default"])(stylesArray, Infinity);

  var _separateStyles = (0, _separateStyles2["default"])(flattenedStyles),
      classNames = _separateStyles.classNames,
      hasInlineStyles = _separateStyles.hasInlineStyles,
      inlineStyles = _separateStyles.inlineStyles;

  var specificClassNames = classNames.map(function (name, index) {
    return "".concat(name, " ").concat(name, "_").concat(index + 1);
  });
  var className = specificClassNames.join(' ');
  var result = {
    className: className
  };
  if (hasInlineStyles) result.style = inlineStyles;
  return result;
}

var _default = {
  create: create,
  resolve: resolve
};
exports["default"] = _default;

/***/ }),

/***/ "VbXa":
/***/ (function(module, exports) {

function _inheritsLoose(subClass, superClass) {
  subClass.prototype = Object.create(superClass.prototype);
  subClass.prototype.constructor = subClass;
  subClass.__proto__ = superClass;
}

module.exports = _inheritsLoose;

/***/ }),

/***/ "WFqU":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {/** Detect free variable `global` from Node.js. */
var freeGlobal = typeof global == 'object' && global && global.Object === Object && global;

module.exports = freeGlobal;

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("yLpj")))

/***/ }),

/***/ "WI5Z":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = registerInterfaceWithDefaultTheme;

var _ThemedStyleSheet = _interopRequireDefault(__webpack_require__("030x"));

var _DefaultTheme = _interopRequireDefault(__webpack_require__("xOhs"));

function registerInterfaceWithDefaultTheme(reactWithStylesInterface) {
  _ThemedStyleSheet["default"].registerInterface(reactWithStylesInterface);

  _ThemedStyleSheet["default"].registerTheme(_DefaultTheme["default"]);
}

/***/ }),

/***/ "WXWk":
/***/ (function(module, exports) {

module.exports = function sign(number) {
	return number >= 0 ? 1 : -1;
};


/***/ }),

/***/ "WZeS":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var hasSymbols = typeof Symbol === 'function' && typeof Symbol.iterator === 'symbol';

var isPrimitive = __webpack_require__("Teho");
var isCallable = __webpack_require__("IdCN");
var isDate = __webpack_require__("DmXP");
var isSymbol = __webpack_require__("/sVA");

var ordinaryToPrimitive = function OrdinaryToPrimitive(O, hint) {
	if (typeof O === 'undefined' || O === null) {
		throw new TypeError('Cannot call method on ' + O);
	}
	if (typeof hint !== 'string' || (hint !== 'number' && hint !== 'string')) {
		throw new TypeError('hint must be "string" or "number"');
	}
	var methodNames = hint === 'string' ? ['toString', 'valueOf'] : ['valueOf', 'toString'];
	var method, result, i;
	for (i = 0; i < methodNames.length; ++i) {
		method = O[methodNames[i]];
		if (isCallable(method)) {
			result = method.call(O);
			if (isPrimitive(result)) {
				return result;
			}
		}
	}
	throw new TypeError('No default value');
};

var GetMethod = function GetMethod(O, P) {
	var func = O[P];
	if (func !== null && typeof func !== 'undefined') {
		if (!isCallable(func)) {
			throw new TypeError(func + ' returned for property ' + P + ' of object ' + O + ' is not a function');
		}
		return func;
	}
	return void 0;
};

// http://www.ecma-international.org/ecma-262/6.0/#sec-toprimitive
module.exports = function ToPrimitive(input) {
	if (isPrimitive(input)) {
		return input;
	}
	var hint = 'default';
	if (arguments.length > 1) {
		if (arguments[1] === String) {
			hint = 'string';
		} else if (arguments[1] === Number) {
			hint = 'number';
		}
	}

	var exoticToPrim;
	if (hasSymbols) {
		if (Symbol.toPrimitive) {
			exoticToPrim = GetMethod(input, Symbol.toPrimitive);
		} else if (isSymbol(input)) {
			exoticToPrim = Symbol.prototype.valueOf;
		}
	}
	if (typeof exoticToPrim !== 'undefined') {
		var result = exoticToPrim.call(input, hint);
		if (isPrimitive(result)) {
			return result;
		}
		throw new TypeError('unable to convert exotic object to primitive');
	}
	if (hint === 'default' && (isDate(input) || isSymbol(input))) {
		hint = 'string';
	}
	return ordinaryToPrimitive(input, hint === 'default' ? 'number' : hint);
};


/***/ }),

/***/ "WbBG":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;


/***/ }),

/***/ "WmS1":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = toMomentObject;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _constants = __webpack_require__("Fv1B");

function toMomentObject(dateString, customFormat) {
  var dateFormats = customFormat ? [customFormat, _constants.DISPLAY_FORMAT, _constants.ISO_FORMAT] : [_constants.DISPLAY_FORMAT, _constants.ISO_FORMAT];
  var date = (0, _moment["default"])(dateString, dateFormats, true);
  return date.isValid() ? date.hour(12) : null;
}

/***/ }),

/***/ "XGBb":
/***/ (function(module, exports, __webpack_require__) {

var moment = __webpack_require__("wy2R");
var momentValidationWrapper = __webpack_require__("c6aN");
var core = __webpack_require__("iNdV");

module.exports = {

  momentObj : core.createMomentChecker(
    'object',
    function(obj) {
      return typeof obj === 'object';
    },
    function isValid(value) {
      return momentValidationWrapper.isValidMoment(value);
    },
    'Moment'
  ),

  momentString : core.createMomentChecker(
    'string',
    function(str) {
      return typeof str === 'string';
    },
    function isValid(value) {
      return momentValidationWrapper.isValidMoment(moment(value));
    },
    'Moment'
  ),

  momentDurationObj : core.createMomentChecker(
    'object',
    function(obj) {
      return typeof obj === 'object';
    },
    function isValid(value) {
      return moment.isDuration(value);
    },
    'Duration'
  ),

};


/***/ }),

/***/ "Xtko":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _slicedToArray2 = _interopRequireDefault(__webpack_require__("J4zp"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _reactMomentProptypes = _interopRequireDefault(__webpack_require__("XGBb"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _object = _interopRequireDefault(__webpack_require__("4cSd"));

var _isTouchDevice = _interopRequireDefault(__webpack_require__("LTAC"));

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _isSameDay = _interopRequireDefault(__webpack_require__("pRvc"));

var _isAfterDay = _interopRequireDefault(__webpack_require__("Nho6"));

var _getVisibleDays = _interopRequireDefault(__webpack_require__("u5Fq"));

var _toISODateString = _interopRequireDefault(__webpack_require__("pYxT"));

var _modifiers = __webpack_require__("kpKt");

var _ScrollableOrientationShape = _interopRequireDefault(__webpack_require__("aE6U"));

var _DayOfWeekShape = _interopRequireDefault(__webpack_require__("2S2E"));

var _CalendarInfoPositionShape = _interopRequireDefault(__webpack_require__("oR9Z"));

var _constants = __webpack_require__("Fv1B");

var _DayPicker = _interopRequireDefault(__webpack_require__("Nloh"));

var _getPooledMoment = _interopRequireDefault(__webpack_require__("gtaZ"));

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  date: undefined,
  // TODO: use null
  onDateChange: function onDateChange() {},
  focused: false,
  onFocusChange: function onFocusChange() {},
  onClose: function onClose() {},
  keepOpenOnDateSelect: false,
  isOutsideRange: function isOutsideRange() {},
  isDayBlocked: function isDayBlocked() {},
  isDayHighlighted: function isDayHighlighted() {},
  // DayPicker props
  renderMonthText: null,
  enableOutsideDays: false,
  numberOfMonths: 1,
  orientation: _constants.HORIZONTAL_ORIENTATION,
  withPortal: false,
  hideKeyboardShortcutsPanel: false,
  initialVisibleMonth: null,
  firstDayOfWeek: null,
  daySize: _constants.DAY_SIZE,
  verticalHeight: null,
  noBorder: false,
  verticalBorderSpacing: undefined,
  transitionDuration: undefined,
  horizontalMonthPadding: 13,
  navPrev: null,
  navNext: null,
  onPrevMonthClick: function onPrevMonthClick() {},
  onNextMonthClick: function onNextMonthClick() {},
  onOutsideClick: function onOutsideClick() {},
  renderCalendarDay: undefined,
  renderDayContents: null,
  renderCalendarInfo: null,
  renderMonthElement: null,
  calendarInfoPosition: _constants.INFO_POSITION_BOTTOM,
  // accessibility
  onBlur: function onBlur() {},
  isFocused: false,
  showKeyboardShortcuts: false,
  onTab: function onTab() {},
  onShiftTab: function onShiftTab() {},
  // i18n
  monthFormat: 'MMMM YYYY',
  weekDayFormat: 'dd',
  phrases: _defaultPhrases.DayPickerPhrases,
  dayAriaLabelFormat: undefined,
  isRTL: false
};

var DayPickerSingleDateController =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(DayPickerSingleDateController, _ref);
  var _proto = DayPickerSingleDateController.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function DayPickerSingleDateController(props) {
    var _this;

    _this = _ref.call(this, props) || this;
    _this.isTouchDevice = false;
    _this.today = (0, _moment["default"])();
    _this.modifiers = {
      today: function today(day) {
        return _this.isToday(day);
      },
      blocked: function blocked(day) {
        return _this.isBlocked(day);
      },
      'blocked-calendar': function blockedCalendar(day) {
        return props.isDayBlocked(day);
      },
      'blocked-out-of-range': function blockedOutOfRange(day) {
        return props.isOutsideRange(day);
      },
      'highlighted-calendar': function highlightedCalendar(day) {
        return props.isDayHighlighted(day);
      },
      valid: function valid(day) {
        return !_this.isBlocked(day);
      },
      hovered: function hovered(day) {
        return _this.isHovered(day);
      },
      selected: function selected(day) {
        return _this.isSelected(day);
      },
      'first-day-of-week': function firstDayOfWeek(day) {
        return _this.isFirstDayOfWeek(day);
      },
      'last-day-of-week': function lastDayOfWeek(day) {
        return _this.isLastDayOfWeek(day);
      }
    };

    var _this$getStateForNewM = _this.getStateForNewMonth(props),
        currentMonth = _this$getStateForNewM.currentMonth,
        visibleDays = _this$getStateForNewM.visibleDays;

    _this.state = {
      hoverDate: null,
      currentMonth: currentMonth,
      visibleDays: visibleDays
    };
    _this.onDayMouseEnter = _this.onDayMouseEnter.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onDayMouseLeave = _this.onDayMouseLeave.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onDayClick = _this.onDayClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onPrevMonthClick = _this.onPrevMonthClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onNextMonthClick = _this.onNextMonthClick.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onMonthChange = _this.onMonthChange.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onYearChange = _this.onYearChange.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onMultiplyScrollableMonths = _this.onMultiplyScrollableMonths.bind((0, _assertThisInitialized2["default"])(_this));
    _this.getFirstFocusableDay = _this.getFirstFocusableDay.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.componentDidMount = function componentDidMount() {
    this.isTouchDevice = (0, _isTouchDevice["default"])();
  };

  _proto.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
    var _this2 = this;

    var date = nextProps.date,
        focused = nextProps.focused,
        isOutsideRange = nextProps.isOutsideRange,
        isDayBlocked = nextProps.isDayBlocked,
        isDayHighlighted = nextProps.isDayHighlighted,
        initialVisibleMonth = nextProps.initialVisibleMonth,
        numberOfMonths = nextProps.numberOfMonths,
        enableOutsideDays = nextProps.enableOutsideDays;
    var _this$props = this.props,
        prevIsOutsideRange = _this$props.isOutsideRange,
        prevIsDayBlocked = _this$props.isDayBlocked,
        prevIsDayHighlighted = _this$props.isDayHighlighted,
        prevNumberOfMonths = _this$props.numberOfMonths,
        prevEnableOutsideDays = _this$props.enableOutsideDays,
        prevInitialVisibleMonth = _this$props.initialVisibleMonth,
        prevFocused = _this$props.focused,
        prevDate = _this$props.date;
    var visibleDays = this.state.visibleDays;
    var recomputeOutsideRange = false;
    var recomputeDayBlocked = false;
    var recomputeDayHighlighted = false;

    if (isOutsideRange !== prevIsOutsideRange) {
      this.modifiers['blocked-out-of-range'] = function (day) {
        return isOutsideRange(day);
      };

      recomputeOutsideRange = true;
    }

    if (isDayBlocked !== prevIsDayBlocked) {
      this.modifiers['blocked-calendar'] = function (day) {
        return isDayBlocked(day);
      };

      recomputeDayBlocked = true;
    }

    if (isDayHighlighted !== prevIsDayHighlighted) {
      this.modifiers['highlighted-calendar'] = function (day) {
        return isDayHighlighted(day);
      };

      recomputeDayHighlighted = true;
    }

    var recomputePropModifiers = recomputeOutsideRange || recomputeDayBlocked || recomputeDayHighlighted;

    if (numberOfMonths !== prevNumberOfMonths || enableOutsideDays !== prevEnableOutsideDays || initialVisibleMonth !== prevInitialVisibleMonth && !prevFocused && focused) {
      var newMonthState = this.getStateForNewMonth(nextProps);
      var currentMonth = newMonthState.currentMonth;
      visibleDays = newMonthState.visibleDays;
      this.setState({
        currentMonth: currentMonth,
        visibleDays: visibleDays
      });
    }

    var didDateChange = date !== prevDate;
    var didFocusChange = focused !== prevFocused;
    var modifiers = {};

    if (didDateChange) {
      modifiers = this.deleteModifier(modifiers, prevDate, 'selected');
      modifiers = this.addModifier(modifiers, date, 'selected');
    }

    if (didFocusChange || recomputePropModifiers) {
      (0, _object["default"])(visibleDays).forEach(function (days) {
        Object.keys(days).forEach(function (day) {
          var momentObj = (0, _getPooledMoment["default"])(day);

          if (_this2.isBlocked(momentObj)) {
            modifiers = _this2.addModifier(modifiers, momentObj, 'blocked');
          } else {
            modifiers = _this2.deleteModifier(modifiers, momentObj, 'blocked');
          }

          if (didFocusChange || recomputeOutsideRange) {
            if (isOutsideRange(momentObj)) {
              modifiers = _this2.addModifier(modifiers, momentObj, 'blocked-out-of-range');
            } else {
              modifiers = _this2.deleteModifier(modifiers, momentObj, 'blocked-out-of-range');
            }
          }

          if (didFocusChange || recomputeDayBlocked) {
            if (isDayBlocked(momentObj)) {
              modifiers = _this2.addModifier(modifiers, momentObj, 'blocked-calendar');
            } else {
              modifiers = _this2.deleteModifier(modifiers, momentObj, 'blocked-calendar');
            }
          }

          if (didFocusChange || recomputeDayHighlighted) {
            if (isDayHighlighted(momentObj)) {
              modifiers = _this2.addModifier(modifiers, momentObj, 'highlighted-calendar');
            } else {
              modifiers = _this2.deleteModifier(modifiers, momentObj, 'highlighted-calendar');
            }
          }
        });
      });
    }

    var today = (0, _moment["default"])();

    if (!(0, _isSameDay["default"])(this.today, today)) {
      modifiers = this.deleteModifier(modifiers, this.today, 'today');
      modifiers = this.addModifier(modifiers, today, 'today');
      this.today = today;
    }

    if (Object.keys(modifiers).length > 0) {
      this.setState({
        visibleDays: _objectSpread({}, visibleDays, {}, modifiers)
      });
    }
  };

  _proto.componentWillUpdate = function componentWillUpdate() {
    this.today = (0, _moment["default"])();
  };

  _proto.onDayClick = function onDayClick(day, e) {
    if (e) e.preventDefault();
    if (this.isBlocked(day)) return;
    var _this$props2 = this.props,
        onDateChange = _this$props2.onDateChange,
        keepOpenOnDateSelect = _this$props2.keepOpenOnDateSelect,
        onFocusChange = _this$props2.onFocusChange,
        onClose = _this$props2.onClose;
    onDateChange(day);

    if (!keepOpenOnDateSelect) {
      onFocusChange({
        focused: false
      });
      onClose({
        date: day
      });
    }
  };

  _proto.onDayMouseEnter = function onDayMouseEnter(day) {
    if (this.isTouchDevice) return;
    var _this$state = this.state,
        hoverDate = _this$state.hoverDate,
        visibleDays = _this$state.visibleDays;
    var modifiers = this.deleteModifier({}, hoverDate, 'hovered');
    modifiers = this.addModifier(modifiers, day, 'hovered');
    this.setState({
      hoverDate: day,
      visibleDays: _objectSpread({}, visibleDays, {}, modifiers)
    });
  };

  _proto.onDayMouseLeave = function onDayMouseLeave() {
    var _this$state2 = this.state,
        hoverDate = _this$state2.hoverDate,
        visibleDays = _this$state2.visibleDays;
    if (this.isTouchDevice || !hoverDate) return;
    var modifiers = this.deleteModifier({}, hoverDate, 'hovered');
    this.setState({
      hoverDate: null,
      visibleDays: _objectSpread({}, visibleDays, {}, modifiers)
    });
  };

  _proto.onPrevMonthClick = function onPrevMonthClick() {
    var _this$props3 = this.props,
        onPrevMonthClick = _this$props3.onPrevMonthClick,
        numberOfMonths = _this$props3.numberOfMonths,
        enableOutsideDays = _this$props3.enableOutsideDays;
    var _this$state3 = this.state,
        currentMonth = _this$state3.currentMonth,
        visibleDays = _this$state3.visibleDays;
    var newVisibleDays = {};
    Object.keys(visibleDays).sort().slice(0, numberOfMonths + 1).forEach(function (month) {
      newVisibleDays[month] = visibleDays[month];
    });
    var prevMonth = currentMonth.clone().subtract(1, 'month');
    var prevMonthVisibleDays = (0, _getVisibleDays["default"])(prevMonth, 1, enableOutsideDays);
    this.setState({
      currentMonth: prevMonth,
      visibleDays: _objectSpread({}, newVisibleDays, {}, this.getModifiers(prevMonthVisibleDays))
    }, function () {
      onPrevMonthClick(prevMonth.clone());
    });
  };

  _proto.onNextMonthClick = function onNextMonthClick() {
    var _this$props4 = this.props,
        onNextMonthClick = _this$props4.onNextMonthClick,
        numberOfMonths = _this$props4.numberOfMonths,
        enableOutsideDays = _this$props4.enableOutsideDays;
    var _this$state4 = this.state,
        currentMonth = _this$state4.currentMonth,
        visibleDays = _this$state4.visibleDays;
    var newVisibleDays = {};
    Object.keys(visibleDays).sort().slice(1).forEach(function (month) {
      newVisibleDays[month] = visibleDays[month];
    });
    var nextMonth = currentMonth.clone().add(numberOfMonths, 'month');
    var nextMonthVisibleDays = (0, _getVisibleDays["default"])(nextMonth, 1, enableOutsideDays);
    var newCurrentMonth = currentMonth.clone().add(1, 'month');
    this.setState({
      currentMonth: newCurrentMonth,
      visibleDays: _objectSpread({}, newVisibleDays, {}, this.getModifiers(nextMonthVisibleDays))
    }, function () {
      onNextMonthClick(newCurrentMonth.clone());
    });
  };

  _proto.onMonthChange = function onMonthChange(newMonth) {
    var _this$props5 = this.props,
        numberOfMonths = _this$props5.numberOfMonths,
        enableOutsideDays = _this$props5.enableOutsideDays,
        orientation = _this$props5.orientation;
    var withoutTransitionMonths = orientation === _constants.VERTICAL_SCROLLABLE;
    var newVisibleDays = (0, _getVisibleDays["default"])(newMonth, numberOfMonths, enableOutsideDays, withoutTransitionMonths);
    this.setState({
      currentMonth: newMonth.clone(),
      visibleDays: this.getModifiers(newVisibleDays)
    });
  };

  _proto.onYearChange = function onYearChange(newMonth) {
    var _this$props6 = this.props,
        numberOfMonths = _this$props6.numberOfMonths,
        enableOutsideDays = _this$props6.enableOutsideDays,
        orientation = _this$props6.orientation;
    var withoutTransitionMonths = orientation === _constants.VERTICAL_SCROLLABLE;
    var newVisibleDays = (0, _getVisibleDays["default"])(newMonth, numberOfMonths, enableOutsideDays, withoutTransitionMonths);
    this.setState({
      currentMonth: newMonth.clone(),
      visibleDays: this.getModifiers(newVisibleDays)
    });
  };

  _proto.onMultiplyScrollableMonths = function onMultiplyScrollableMonths() {
    var _this$props7 = this.props,
        numberOfMonths = _this$props7.numberOfMonths,
        enableOutsideDays = _this$props7.enableOutsideDays;
    var _this$state5 = this.state,
        currentMonth = _this$state5.currentMonth,
        visibleDays = _this$state5.visibleDays;
    var numberOfVisibleMonths = Object.keys(visibleDays).length;
    var nextMonth = currentMonth.clone().add(numberOfVisibleMonths, 'month');
    var newVisibleDays = (0, _getVisibleDays["default"])(nextMonth, numberOfMonths, enableOutsideDays, true);
    this.setState({
      visibleDays: _objectSpread({}, visibleDays, {}, this.getModifiers(newVisibleDays))
    });
  };

  _proto.getFirstFocusableDay = function getFirstFocusableDay(newMonth) {
    var _this3 = this;

    var _this$props8 = this.props,
        date = _this$props8.date,
        numberOfMonths = _this$props8.numberOfMonths;
    var focusedDate = newMonth.clone().startOf('month');

    if (date) {
      focusedDate = date.clone();
    }

    if (this.isBlocked(focusedDate)) {
      var days = [];
      var lastVisibleDay = newMonth.clone().add(numberOfMonths - 1, 'months').endOf('month');
      var currentDay = focusedDate.clone();

      while (!(0, _isAfterDay["default"])(currentDay, lastVisibleDay)) {
        currentDay = currentDay.clone().add(1, 'day');
        days.push(currentDay);
      }

      var viableDays = days.filter(function (day) {
        return !_this3.isBlocked(day) && (0, _isAfterDay["default"])(day, focusedDate);
      });

      if (viableDays.length > 0) {
        var _viableDays = (0, _slicedToArray2["default"])(viableDays, 1);

        focusedDate = _viableDays[0];
      }
    }

    return focusedDate;
  };

  _proto.getModifiers = function getModifiers(visibleDays) {
    var _this4 = this;

    var modifiers = {};
    Object.keys(visibleDays).forEach(function (month) {
      modifiers[month] = {};
      visibleDays[month].forEach(function (day) {
        modifiers[month][(0, _toISODateString["default"])(day)] = _this4.getModifiersForDay(day);
      });
    });
    return modifiers;
  };

  _proto.getModifiersForDay = function getModifiersForDay(day) {
    var _this5 = this;

    return new Set(Object.keys(this.modifiers).filter(function (modifier) {
      return _this5.modifiers[modifier](day);
    }));
  };

  _proto.getStateForNewMonth = function getStateForNewMonth(nextProps) {
    var _this6 = this;

    var initialVisibleMonth = nextProps.initialVisibleMonth,
        date = nextProps.date,
        numberOfMonths = nextProps.numberOfMonths,
        orientation = nextProps.orientation,
        enableOutsideDays = nextProps.enableOutsideDays;
    var initialVisibleMonthThunk = initialVisibleMonth || (date ? function () {
      return date;
    } : function () {
      return _this6.today;
    });
    var currentMonth = initialVisibleMonthThunk();
    var withoutTransitionMonths = orientation === _constants.VERTICAL_SCROLLABLE;
    var visibleDays = this.getModifiers((0, _getVisibleDays["default"])(currentMonth, numberOfMonths, enableOutsideDays, withoutTransitionMonths));
    return {
      currentMonth: currentMonth,
      visibleDays: visibleDays
    };
  };

  _proto.addModifier = function addModifier(updatedDays, day, modifier) {
    return (0, _modifiers.addModifier)(updatedDays, day, modifier, this.props, this.state);
  };

  _proto.deleteModifier = function deleteModifier(updatedDays, day, modifier) {
    return (0, _modifiers.deleteModifier)(updatedDays, day, modifier, this.props, this.state);
  };

  _proto.isBlocked = function isBlocked(day) {
    var _this$props9 = this.props,
        isDayBlocked = _this$props9.isDayBlocked,
        isOutsideRange = _this$props9.isOutsideRange;
    return isDayBlocked(day) || isOutsideRange(day);
  };

  _proto.isHovered = function isHovered(day) {
    var _ref2 = this.state || {},
        hoverDate = _ref2.hoverDate;

    return (0, _isSameDay["default"])(day, hoverDate);
  };

  _proto.isSelected = function isSelected(day) {
    var date = this.props.date;
    return (0, _isSameDay["default"])(day, date);
  };

  _proto.isToday = function isToday(day) {
    return (0, _isSameDay["default"])(day, this.today);
  };

  _proto.isFirstDayOfWeek = function isFirstDayOfWeek(day) {
    var firstDayOfWeek = this.props.firstDayOfWeek;
    return day.day() === (firstDayOfWeek || _moment["default"].localeData().firstDayOfWeek());
  };

  _proto.isLastDayOfWeek = function isLastDayOfWeek(day) {
    var firstDayOfWeek = this.props.firstDayOfWeek;
    return day.day() === ((firstDayOfWeek || _moment["default"].localeData().firstDayOfWeek()) + 6) % 7;
  };

  _proto.render = function render() {
    var _this$props10 = this.props,
        numberOfMonths = _this$props10.numberOfMonths,
        orientation = _this$props10.orientation,
        monthFormat = _this$props10.monthFormat,
        renderMonthText = _this$props10.renderMonthText,
        navPrev = _this$props10.navPrev,
        navNext = _this$props10.navNext,
        onOutsideClick = _this$props10.onOutsideClick,
        onShiftTab = _this$props10.onShiftTab,
        onTab = _this$props10.onTab,
        withPortal = _this$props10.withPortal,
        focused = _this$props10.focused,
        enableOutsideDays = _this$props10.enableOutsideDays,
        hideKeyboardShortcutsPanel = _this$props10.hideKeyboardShortcutsPanel,
        daySize = _this$props10.daySize,
        firstDayOfWeek = _this$props10.firstDayOfWeek,
        renderCalendarDay = _this$props10.renderCalendarDay,
        renderDayContents = _this$props10.renderDayContents,
        renderCalendarInfo = _this$props10.renderCalendarInfo,
        renderMonthElement = _this$props10.renderMonthElement,
        calendarInfoPosition = _this$props10.calendarInfoPosition,
        isFocused = _this$props10.isFocused,
        isRTL = _this$props10.isRTL,
        phrases = _this$props10.phrases,
        dayAriaLabelFormat = _this$props10.dayAriaLabelFormat,
        onBlur = _this$props10.onBlur,
        showKeyboardShortcuts = _this$props10.showKeyboardShortcuts,
        weekDayFormat = _this$props10.weekDayFormat,
        verticalHeight = _this$props10.verticalHeight,
        noBorder = _this$props10.noBorder,
        transitionDuration = _this$props10.transitionDuration,
        verticalBorderSpacing = _this$props10.verticalBorderSpacing,
        horizontalMonthPadding = _this$props10.horizontalMonthPadding;
    var _this$state6 = this.state,
        currentMonth = _this$state6.currentMonth,
        visibleDays = _this$state6.visibleDays;
    return _react["default"].createElement(_DayPicker["default"], {
      orientation: orientation,
      enableOutsideDays: enableOutsideDays,
      modifiers: visibleDays,
      numberOfMonths: numberOfMonths,
      onDayClick: this.onDayClick,
      onDayMouseEnter: this.onDayMouseEnter,
      onDayMouseLeave: this.onDayMouseLeave,
      onPrevMonthClick: this.onPrevMonthClick,
      onNextMonthClick: this.onNextMonthClick,
      onMonthChange: this.onMonthChange,
      onYearChange: this.onYearChange,
      onMultiplyScrollableMonths: this.onMultiplyScrollableMonths,
      monthFormat: monthFormat,
      withPortal: withPortal,
      hidden: !focused,
      hideKeyboardShortcutsPanel: hideKeyboardShortcutsPanel,
      initialVisibleMonth: function initialVisibleMonth() {
        return currentMonth;
      },
      firstDayOfWeek: firstDayOfWeek,
      onOutsideClick: onOutsideClick,
      navPrev: navPrev,
      navNext: navNext,
      renderMonthText: renderMonthText,
      renderCalendarDay: renderCalendarDay,
      renderDayContents: renderDayContents,
      renderCalendarInfo: renderCalendarInfo,
      renderMonthElement: renderMonthElement,
      calendarInfoPosition: calendarInfoPosition,
      isFocused: isFocused,
      getFirstFocusableDay: this.getFirstFocusableDay,
      onBlur: onBlur,
      onTab: onTab,
      onShiftTab: onShiftTab,
      phrases: phrases,
      daySize: daySize,
      isRTL: isRTL,
      showKeyboardShortcuts: showKeyboardShortcuts,
      weekDayFormat: weekDayFormat,
      dayAriaLabelFormat: dayAriaLabelFormat,
      verticalHeight: verticalHeight,
      noBorder: noBorder,
      transitionDuration: transitionDuration,
      verticalBorderSpacing: verticalBorderSpacing,
      horizontalMonthPadding: horizontalMonthPadding
    });
  };

  return DayPickerSingleDateController;
}(_react["default"].PureComponent || _react["default"].Component);

exports["default"] = DayPickerSingleDateController;
DayPickerSingleDateController.propTypes =  false ? undefined : {};
DayPickerSingleDateController.defaultProps = defaultProps;

/***/ }),

/***/ "YAW8":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isNextDay;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _isSameDay = _interopRequireDefault(__webpack_require__("pRvc"));

function isNextDay(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false;
  var nextDay = (0, _moment["default"])(a).add(1, 'day');
  return (0, _isSameDay["default"])(nextDay, b);
}

/***/ }),

/***/ "YCEU":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isInclusivelyBeforeDay;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _isAfterDay = _interopRequireDefault(__webpack_require__("Nho6"));

function isInclusivelyBeforeDay(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false;
  return !(0, _isAfterDay["default"])(a, b);
}

/***/ }),

/***/ "aE6U":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _constants = __webpack_require__("Fv1B");

var _default = _propTypes["default"].oneOf([_constants.HORIZONTAL_ORIENTATION, _constants.VERTICAL_ORIENTATION, _constants.VERTICAL_SCROLLABLE]);

exports["default"] = _default;

/***/ }),

/***/ "aI7X":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/* eslint no-invalid-this: 1 */

var ERROR_MESSAGE = 'Function.prototype.bind called on incompatible ';
var slice = Array.prototype.slice;
var toStr = Object.prototype.toString;
var funcType = '[object Function]';

module.exports = function bind(that) {
    var target = this;
    if (typeof target !== 'function' || toStr.call(target) !== funcType) {
        throw new TypeError(ERROR_MESSAGE + target);
    }
    var args = slice.call(arguments, 1);

    var bound;
    var binder = function () {
        if (this instanceof bound) {
            var result = target.apply(
                this,
                args.concat(slice.call(arguments))
            );
            if (Object(result) === result) {
                return result;
            }
            return this;
        } else {
            return target.apply(
                that,
                args.concat(slice.call(arguments))
            );
        }
    };

    var boundLength = Math.max(0, target.length - args.length);
    var boundArgs = [];
    for (var i = 0; i < boundLength; i++) {
        boundArgs.push('$' + i);
    }

    bound = Function('binder', 'return function (' + boundArgs.join(',') + '){ return binder.apply(this,arguments); }')(binder);

    if (target.prototype) {
        var Empty = function Empty() {};
        Empty.prototype = target.prototype;
        bound.prototype = new Empty();
        Empty.prototype = null;
    }

    return bound;
};


/***/ }),

/***/ "acCH":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _react = __webpack_require__("cDcd");

var _react2 = _interopRequireDefault(_react);

var _propTypes = __webpack_require__("17x9");

var _propTypes2 = _interopRequireDefault(_propTypes);

var _gud = __webpack_require__("fZtv");

var _gud2 = _interopRequireDefault(_gud);

var _warning = __webpack_require__("2mcs");

var _warning2 = _interopRequireDefault(_warning);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var MAX_SIGNED_31_BIT_INT = 1073741823;

// Inlined Object.is polyfill.
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is
function objectIs(x, y) {
  if (x === y) {
    return x !== 0 || 1 / x === 1 / y;
  } else {
    return x !== x && y !== y;
  }
}

function createEventEmitter(value) {
  var handlers = [];
  return {
    on: function on(handler) {
      handlers.push(handler);
    },
    off: function off(handler) {
      handlers = handlers.filter(function (h) {
        return h !== handler;
      });
    },
    get: function get() {
      return value;
    },
    set: function set(newValue, changedBits) {
      value = newValue;
      handlers.forEach(function (handler) {
        return handler(value, changedBits);
      });
    }
  };
}

function onlyChild(children) {
  return Array.isArray(children) ? children[0] : children;
}

function createReactContext(defaultValue, calculateChangedBits) {
  var _Provider$childContex, _Consumer$contextType;

  var contextProp = '__create-react-context-' + (0, _gud2.default)() + '__';

  var Provider = function (_Component) {
    _inherits(Provider, _Component);

    function Provider() {
      var _temp, _this, _ret;

      _classCallCheck(this, Provider);

      for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      return _ret = (_temp = (_this = _possibleConstructorReturn(this, _Component.call.apply(_Component, [this].concat(args))), _this), _this.emitter = createEventEmitter(_this.props.value), _temp), _possibleConstructorReturn(_this, _ret);
    }

    Provider.prototype.getChildContext = function getChildContext() {
      var _ref;

      return _ref = {}, _ref[contextProp] = this.emitter, _ref;
    };

    Provider.prototype.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
      if (this.props.value !== nextProps.value) {
        var oldValue = this.props.value;
        var newValue = nextProps.value;
        var changedBits = void 0;

        if (objectIs(oldValue, newValue)) {
          changedBits = 0; // No change
        } else {
          changedBits = typeof calculateChangedBits === 'function' ? calculateChangedBits(oldValue, newValue) : MAX_SIGNED_31_BIT_INT;
          if (false) {}

          changedBits |= 0;

          if (changedBits !== 0) {
            this.emitter.set(nextProps.value, changedBits);
          }
        }
      }
    };

    Provider.prototype.render = function render() {
      return this.props.children;
    };

    return Provider;
  }(_react.Component);

  Provider.childContextTypes = (_Provider$childContex = {}, _Provider$childContex[contextProp] = _propTypes2.default.object.isRequired, _Provider$childContex);

  var Consumer = function (_Component2) {
    _inherits(Consumer, _Component2);

    function Consumer() {
      var _temp2, _this2, _ret2;

      _classCallCheck(this, Consumer);

      for (var _len2 = arguments.length, args = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        args[_key2] = arguments[_key2];
      }

      return _ret2 = (_temp2 = (_this2 = _possibleConstructorReturn(this, _Component2.call.apply(_Component2, [this].concat(args))), _this2), _this2.state = {
        value: _this2.getValue()
      }, _this2.onUpdate = function (newValue, changedBits) {
        var observedBits = _this2.observedBits | 0;
        if ((observedBits & changedBits) !== 0) {
          _this2.setState({ value: _this2.getValue() });
        }
      }, _temp2), _possibleConstructorReturn(_this2, _ret2);
    }

    Consumer.prototype.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
      var observedBits = nextProps.observedBits;

      this.observedBits = observedBits === undefined || observedBits === null ? MAX_SIGNED_31_BIT_INT // Subscribe to all changes by default
      : observedBits;
    };

    Consumer.prototype.componentDidMount = function componentDidMount() {
      if (this.context[contextProp]) {
        this.context[contextProp].on(this.onUpdate);
      }
      var observedBits = this.props.observedBits;

      this.observedBits = observedBits === undefined || observedBits === null ? MAX_SIGNED_31_BIT_INT // Subscribe to all changes by default
      : observedBits;
    };

    Consumer.prototype.componentWillUnmount = function componentWillUnmount() {
      if (this.context[contextProp]) {
        this.context[contextProp].off(this.onUpdate);
      }
    };

    Consumer.prototype.getValue = function getValue() {
      if (this.context[contextProp]) {
        return this.context[contextProp].get();
      } else {
        return defaultValue;
      }
    };

    Consumer.prototype.render = function render() {
      return onlyChild(this.props.children)(this.state.value);
    };

    return Consumer;
  }(_react.Component);

  Consumer.contextTypes = (_Consumer$contextType = {}, _Consumer$contextType[contextProp] = _propTypes2.default.object, _Consumer$contextType);


  return {
    Provider: Provider,
    Consumer: Consumer
  };
}

exports.default = createReactContext;
module.exports = exports['default'];

/***/ }),

/***/ "bQgK":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(process) {// Generated by CoffeeScript 1.12.2
(function() {
  var getNanoSeconds, hrtime, loadTime, moduleLoadTime, nodeLoadTime, upTime;

  if ((typeof performance !== "undefined" && performance !== null) && performance.now) {
    module.exports = function() {
      return performance.now();
    };
  } else if ((typeof process !== "undefined" && process !== null) && process.hrtime) {
    module.exports = function() {
      return (getNanoSeconds() - nodeLoadTime) / 1e6;
    };
    hrtime = process.hrtime;
    getNanoSeconds = function() {
      var hr;
      hr = hrtime();
      return hr[0] * 1e9 + hr[1];
    };
    moduleLoadTime = getNanoSeconds();
    upTime = process.uptime() * 1e9;
    nodeLoadTime = moduleLoadTime - upTime;
  } else if (Date.now) {
    module.exports = function() {
      return Date.now() - loadTime;
    };
    loadTime = Date.now();
  } else {
    module.exports = function() {
      return new Date().getTime() - loadTime;
    };
    loadTime = new Date().getTime();
  }

}).call(this);

//# sourceMappingURL=performance-now.js.map

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("8oxB")))

/***/ }),

/***/ "bbcx":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/* https://people.mozilla.org/~jorendorff/es6-draft.html#sec-object.is */

var NumberIsNaN = function (value) {
	return value !== value;
};

module.exports = function is(a, b) {
	if (a === 0 && b === 0) {
		return 1 / a === 1 / b;
	} else if (a === b) {
		return true;
	} else if (NumberIsNaN(a) && NumberIsNaN(b)) {
		return true;
	}
	return false;
};



/***/ }),

/***/ "c6aN":
/***/ (function(module, exports, __webpack_require__) {

var moment = __webpack_require__("wy2R");

function isValidMoment(testMoment) {
  if (typeof moment.isMoment === 'function' && !moment.isMoment(testMoment)) {
    return false;
  }

  /* istanbul ignore else  */
  if (typeof testMoment.isValid === 'function') {
    // moment 1.7.0+
    return testMoment.isValid();
  }

  /* istanbul ignore next */
  return !isNaN(testMoment);
}

module.exports = {
  isValidMoment : isValidMoment,
};


/***/ }),

/***/ "cDcd":
/***/ (function(module, exports) {

(function() { module.exports = this["React"]; }());

/***/ }),

/***/ "dRQD":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isTransitionEndSupported;

function isTransitionEndSupported() {
  return !!(typeof window !== 'undefined' && 'TransitionEvent' in window);
}

/***/ }),

/***/ "e4Aj":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getComponentName;

var _functionPrototype = _interopRequireDefault(__webpack_require__("Hi9J"));

var _reactIs = __webpack_require__("jpOL");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function getComponentName(Component) {
  if (typeof Component === 'string') {
    return Component;
  }

  if (typeof Component === 'function') {
    return Component.displayName || (0, _functionPrototype["default"])(Component);
  }

  if ((0, _reactIs.isForwardRef)({
    type: Component,
    $$typeof: _reactIs.Element
  })) {
    return Component.displayName;
  }

  if ((0, _reactIs.isMemo)(Component)) {
    return getComponentName(Component.type);
  }

  return null;
}
//# sourceMappingURL=getComponentName.js.map

/***/ }),

/***/ "fHbK":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.withDirectionPropTypes = exports.DIRECTIONS = undefined;

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

exports['default'] = withDirection;

var _react = __webpack_require__("cDcd");

var _react2 = _interopRequireDefault(_react);

var _hoistNonReactStatics = __webpack_require__("2mql");

var _hoistNonReactStatics2 = _interopRequireDefault(_hoistNonReactStatics);

var _deepmerge = __webpack_require__("ocq3");

var _deepmerge2 = _interopRequireDefault(_deepmerge);

var _getComponentName = __webpack_require__("e4Aj");

var _getComponentName2 = _interopRequireDefault(_getComponentName);

var _constants = __webpack_require__("QEu6");

var _brcast = __webpack_require__("sDMB");

var _brcast2 = _interopRequireDefault(_brcast);

var _direction = __webpack_require__("HWoq");

var _direction2 = _interopRequireDefault(_direction);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; } /* eslint-disable react/forbid-foreign-prop-types */
// This higher-order component consumes a string from React context that is
// provided by the DirectionProvider component.
// We can use this to conditionally switch layout/direction for right-to-left layouts.

var contextTypes = _defineProperty({}, _constants.CHANNEL, _brcast2['default']);

exports.DIRECTIONS = _constants.DIRECTIONS;

// set a default direction so that a component wrapped with this HOC can be
// used even without a DirectionProvider ancestor in its react tree.

var defaultDirection = _constants.DIRECTIONS.LTR;

// export for convenience, in order for components to spread these onto their propTypes
var withDirectionPropTypes = exports.withDirectionPropTypes = {
  direction: _direction2['default'].isRequired
};

function withDirection(WrappedComponent) {
  var WithDirection = function (_React$Component) {
    _inherits(WithDirection, _React$Component);

    function WithDirection(props, context) {
      _classCallCheck(this, WithDirection);

      var _this = _possibleConstructorReturn(this, (WithDirection.__proto__ || Object.getPrototypeOf(WithDirection)).call(this, props, context));

      _this.state = {
        direction: context[_constants.CHANNEL] ? context[_constants.CHANNEL].getState() : defaultDirection
      };
      return _this;
    }

    _createClass(WithDirection, [{
      key: 'componentDidMount',
      value: function () {
        function componentDidMount() {
          var _this2 = this;

          if (this.context[_constants.CHANNEL]) {
            // subscribe to future direction changes
            this.channelUnsubscribe = this.context[_constants.CHANNEL].subscribe(function (direction) {
              _this2.setState({ direction: direction });
            });
          }
        }

        return componentDidMount;
      }()
    }, {
      key: 'componentWillUnmount',
      value: function () {
        function componentWillUnmount() {
          if (this.channelUnsubscribe) {
            this.channelUnsubscribe();
          }
        }

        return componentWillUnmount;
      }()
    }, {
      key: 'render',
      value: function () {
        function render() {
          var direction = this.state.direction;


          return _react2['default'].createElement(WrappedComponent, _extends({}, this.props, {
            direction: direction
          }));
        }

        return render;
      }()
    }]);

    return WithDirection;
  }(_react2['default'].Component);

  var wrappedComponentName = (0, _getComponentName2['default'])(WrappedComponent) || 'Component';

  WithDirection.WrappedComponent = WrappedComponent;
  WithDirection.contextTypes = contextTypes;
  WithDirection.displayName = 'withDirection(' + String(wrappedComponentName) + ')';
  if (WrappedComponent.propTypes) {
    WithDirection.propTypes = (0, _deepmerge2['default'])({}, WrappedComponent.propTypes);
    delete WithDirection.propTypes.direction;
  }
  if (WrappedComponent.defaultProps) {
    WithDirection.defaultProps = (0, _deepmerge2['default'])({}, WrappedComponent.defaultProps);
  }

  return (0, _hoistNonReactStatics2['default'])(WithDirection, WrappedComponent);
}

/***/ }),

/***/ "fZtv":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {// @flow


var key = '__global_unique_id__';

module.exports = function() {
  return global[key] = (global[key] || 0) + 1;
};

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("yLpj")))

/***/ }),

/***/ "faye":
/***/ (function(module, exports) {

(function() { module.exports = this["ReactDOM"]; }());

/***/ }),

/***/ "gZI3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var RightArrow = function RightArrow(props) {
  return _react["default"].createElement("svg", props, _react["default"].createElement("path", {
    d: "M694 242l249 250c12 11 12 21 1 32L694 773c-5 5-10 7-16 7s-11-2-16-7c-11-11-11-21 0-32l210-210H68c-13 0-23-10-23-23s10-23 23-23h806L662 275c-21-22 11-54 32-33z"
  }));
};

RightArrow.defaultProps = {
  focusable: "false",
  viewBox: "0 0 1000 1000"
};
var _default = RightArrow;
exports["default"] = _default;

/***/ }),

/***/ "gaAG":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var implementation = __webpack_require__("l4Qr");

module.exports = function getPolyfill() {
	return implementation;
};


/***/ }),

/***/ "gtaZ":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getPooledMoment;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var momentPool = new Map();

function getPooledMoment(dayString) {
  if (!momentPool.has(dayString)) {
    momentPool.set(dayString, (0, _moment["default"])(dayString));
  }

  return momentPool.get(dayString);
}

/***/ }),

/***/ "h6xH":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isBeforeDay;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

function isBeforeDay(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false;
  var aYear = a.year();
  var aMonth = a.month();
  var bYear = b.year();
  var bMonth = b.month();
  var isSameYear = aYear === bYear;
  var isSameMonth = aMonth === bMonth;
  if (isSameYear && isSameMonth) return a.date() < b.date();
  if (isSameYear) return aMonth < bMonth;
  return aYear < bYear;
}

/***/ }),

/***/ "hgME":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getInputHeight;

/* eslint-disable camelcase */
function getPadding(vertical, top, bottom) {
  var isTopDefined = typeof top === 'number';
  var isBottomDefined = typeof bottom === 'number';
  var isVerticalDefined = typeof vertical === 'number';

  if (isTopDefined && isBottomDefined) {
    return top + bottom;
  }

  if (isTopDefined && isVerticalDefined) {
    return top + vertical;
  }

  if (isTopDefined) {
    return top;
  }

  if (isBottomDefined && isVerticalDefined) {
    return bottom + vertical;
  }

  if (isBottomDefined) {
    return bottom;
  }

  if (isVerticalDefined) {
    return 2 * vertical;
  }

  return 0;
}

function getInputHeight(_ref, small) {
  var _ref$font$input = _ref.font.input,
      lineHeight = _ref$font$input.lineHeight,
      lineHeight_small = _ref$font$input.lineHeight_small,
      _ref$spacing = _ref.spacing,
      inputPadding = _ref$spacing.inputPadding,
      displayTextPaddingVertical = _ref$spacing.displayTextPaddingVertical,
      displayTextPaddingTop = _ref$spacing.displayTextPaddingTop,
      displayTextPaddingBottom = _ref$spacing.displayTextPaddingBottom,
      displayTextPaddingVertical_small = _ref$spacing.displayTextPaddingVertical_small,
      displayTextPaddingTop_small = _ref$spacing.displayTextPaddingTop_small,
      displayTextPaddingBottom_small = _ref$spacing.displayTextPaddingBottom_small;
  var calcLineHeight = small ? lineHeight_small : lineHeight;
  var padding = small ? getPadding(displayTextPaddingVertical_small, displayTextPaddingTop_small, displayTextPaddingBottom_small) : getPadding(displayTextPaddingVertical, displayTextPaddingTop, displayTextPaddingBottom);
  return parseInt(calcLineHeight, 10) + 2 * inputPadding + padding;
}

/***/ }),

/***/ "hwBm":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _noflip = _interopRequireDefault(__webpack_require__("2WTt"));

var _OpenDirectionShape = _interopRequireDefault(__webpack_require__("iuLr"));

var _DateInput = _interopRequireDefault(__webpack_require__("Dv0f"));

var _IconPositionShape = _interopRequireDefault(__webpack_require__("Bh6R"));

var _DisabledShape = _interopRequireDefault(__webpack_require__("5Fvu"));

var _RightArrow = _interopRequireDefault(__webpack_require__("gZI3"));

var _LeftArrow = _interopRequireDefault(__webpack_require__("0XP8"));

var _CloseButton = _interopRequireDefault(__webpack_require__("xEte"));

var _CalendarIcon = _interopRequireDefault(__webpack_require__("LfrC"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  children: null,
  startDateId: _constants.START_DATE,
  endDateId: _constants.END_DATE,
  startDatePlaceholderText: 'Start Date',
  endDatePlaceholderText: 'End Date',
  startDateAriaLabel: undefined,
  endDateAriaLabel: undefined,
  screenReaderMessage: '',
  onStartDateFocus: function onStartDateFocus() {},
  onEndDateFocus: function onEndDateFocus() {},
  onStartDateChange: function onStartDateChange() {},
  onEndDateChange: function onEndDateChange() {},
  onStartDateShiftTab: function onStartDateShiftTab() {},
  onEndDateTab: function onEndDateTab() {},
  onClearDates: function onClearDates() {},
  onKeyDownArrowDown: function onKeyDownArrowDown() {},
  onKeyDownQuestionMark: function onKeyDownQuestionMark() {},
  startDate: '',
  endDate: '',
  isStartDateFocused: false,
  isEndDateFocused: false,
  showClearDates: false,
  disabled: false,
  required: false,
  readOnly: false,
  openDirection: _constants.OPEN_DOWN,
  showCaret: false,
  showDefaultInputIcon: false,
  inputIconPosition: _constants.ICON_BEFORE_POSITION,
  customInputIcon: null,
  customArrowIcon: null,
  customCloseIcon: null,
  noBorder: false,
  block: false,
  small: false,
  regular: false,
  verticalSpacing: undefined,
  // accessibility
  isFocused: false,
  // i18n
  phrases: _defaultPhrases.DateRangePickerInputPhrases,
  isRTL: false
};

function DateRangePickerInput(_ref) {
  var children = _ref.children,
      css = _ref.css,
      startDate = _ref.startDate,
      startDateId = _ref.startDateId,
      startDatePlaceholderText = _ref.startDatePlaceholderText,
      screenReaderMessage = _ref.screenReaderMessage,
      isStartDateFocused = _ref.isStartDateFocused,
      onStartDateChange = _ref.onStartDateChange,
      onStartDateFocus = _ref.onStartDateFocus,
      onStartDateShiftTab = _ref.onStartDateShiftTab,
      startDateAriaLabel = _ref.startDateAriaLabel,
      endDate = _ref.endDate,
      endDateId = _ref.endDateId,
      endDatePlaceholderText = _ref.endDatePlaceholderText,
      isEndDateFocused = _ref.isEndDateFocused,
      onEndDateChange = _ref.onEndDateChange,
      onEndDateFocus = _ref.onEndDateFocus,
      onEndDateTab = _ref.onEndDateTab,
      endDateAriaLabel = _ref.endDateAriaLabel,
      onKeyDownArrowDown = _ref.onKeyDownArrowDown,
      onKeyDownQuestionMark = _ref.onKeyDownQuestionMark,
      onClearDates = _ref.onClearDates,
      showClearDates = _ref.showClearDates,
      disabled = _ref.disabled,
      required = _ref.required,
      readOnly = _ref.readOnly,
      showCaret = _ref.showCaret,
      openDirection = _ref.openDirection,
      showDefaultInputIcon = _ref.showDefaultInputIcon,
      inputIconPosition = _ref.inputIconPosition,
      customInputIcon = _ref.customInputIcon,
      customArrowIcon = _ref.customArrowIcon,
      customCloseIcon = _ref.customCloseIcon,
      isFocused = _ref.isFocused,
      phrases = _ref.phrases,
      isRTL = _ref.isRTL,
      noBorder = _ref.noBorder,
      block = _ref.block,
      verticalSpacing = _ref.verticalSpacing,
      small = _ref.small,
      regular = _ref.regular,
      styles = _ref.styles;

  var calendarIcon = customInputIcon || _react["default"].createElement(_CalendarIcon["default"], css(styles.DateRangePickerInput_calendarIcon_svg));

  var arrowIcon = customArrowIcon || _react["default"].createElement(_RightArrow["default"], css(styles.DateRangePickerInput_arrow_svg));

  if (isRTL) arrowIcon = _react["default"].createElement(_LeftArrow["default"], css(styles.DateRangePickerInput_arrow_svg));
  if (small) arrowIcon = '-';

  var closeIcon = customCloseIcon || _react["default"].createElement(_CloseButton["default"], css(styles.DateRangePickerInput_clearDates_svg, small && styles.DateRangePickerInput_clearDates_svg__small));

  var screenReaderStartDateText = screenReaderMessage || phrases.keyboardForwardNavigationInstructions;
  var screenReaderEndDateText = screenReaderMessage || phrases.keyboardBackwardNavigationInstructions;

  var inputIcon = (showDefaultInputIcon || customInputIcon !== null) && _react["default"].createElement("button", (0, _extends2["default"])({}, css(styles.DateRangePickerInput_calendarIcon), {
    type: "button",
    disabled: disabled,
    "aria-label": phrases.focusStartDate,
    onClick: onKeyDownArrowDown
  }), calendarIcon);

  var startDateDisabled = disabled === _constants.START_DATE || disabled === true;
  var endDateDisabled = disabled === _constants.END_DATE || disabled === true;
  return _react["default"].createElement("div", css(styles.DateRangePickerInput, disabled && styles.DateRangePickerInput__disabled, isRTL && styles.DateRangePickerInput__rtl, !noBorder && styles.DateRangePickerInput__withBorder, block && styles.DateRangePickerInput__block, showClearDates && styles.DateRangePickerInput__showClearDates), inputIconPosition === _constants.ICON_BEFORE_POSITION && inputIcon, _react["default"].createElement(_DateInput["default"], {
    id: startDateId,
    placeholder: startDatePlaceholderText,
    ariaLabel: startDateAriaLabel,
    displayValue: startDate,
    screenReaderMessage: screenReaderStartDateText,
    focused: isStartDateFocused,
    isFocused: isFocused,
    disabled: startDateDisabled,
    required: required,
    readOnly: readOnly,
    showCaret: showCaret,
    openDirection: openDirection,
    onChange: onStartDateChange,
    onFocus: onStartDateFocus,
    onKeyDownShiftTab: onStartDateShiftTab,
    onKeyDownArrowDown: onKeyDownArrowDown,
    onKeyDownQuestionMark: onKeyDownQuestionMark,
    verticalSpacing: verticalSpacing,
    small: small,
    regular: regular
  }), children, _react["default"].createElement("div", (0, _extends2["default"])({}, css(styles.DateRangePickerInput_arrow), {
    "aria-hidden": "true",
    role: "presentation"
  }), arrowIcon), _react["default"].createElement(_DateInput["default"], {
    id: endDateId,
    placeholder: endDatePlaceholderText,
    ariaLabel: endDateAriaLabel,
    displayValue: endDate,
    screenReaderMessage: screenReaderEndDateText,
    focused: isEndDateFocused,
    isFocused: isFocused,
    disabled: endDateDisabled,
    required: required,
    readOnly: readOnly,
    showCaret: showCaret,
    openDirection: openDirection,
    onChange: onEndDateChange,
    onFocus: onEndDateFocus,
    onKeyDownArrowDown: onKeyDownArrowDown,
    onKeyDownQuestionMark: onKeyDownQuestionMark,
    onKeyDownTab: onEndDateTab,
    verticalSpacing: verticalSpacing,
    small: small,
    regular: regular
  }), showClearDates && _react["default"].createElement("button", (0, _extends2["default"])({
    type: "button",
    "aria-label": phrases.clearDates
  }, css(styles.DateRangePickerInput_clearDates, small && styles.DateRangePickerInput_clearDates__small, !customCloseIcon && styles.DateRangePickerInput_clearDates_default, !(startDate || endDate) && styles.DateRangePickerInput_clearDates__hide), {
    onClick: onClearDates,
    disabled: disabled
  }), closeIcon), inputIconPosition === _constants.ICON_AFTER_POSITION && inputIcon);
}

DateRangePickerInput.propTypes =  false ? undefined : {};
DateRangePickerInput.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref2) {
  var _ref2$reactDates = _ref2.reactDates,
      border = _ref2$reactDates.border,
      color = _ref2$reactDates.color,
      sizing = _ref2$reactDates.sizing;
  return {
    DateRangePickerInput: {
      backgroundColor: color.background,
      display: 'inline-block'
    },
    DateRangePickerInput__disabled: {
      background: color.disabled
    },
    DateRangePickerInput__withBorder: {
      borderColor: color.border,
      borderWidth: border.pickerInput.borderWidth,
      borderStyle: border.pickerInput.borderStyle,
      borderRadius: border.pickerInput.borderRadius
    },
    DateRangePickerInput__rtl: {
      direction: (0, _noflip["default"])('rtl')
    },
    DateRangePickerInput__block: {
      display: 'block'
    },
    DateRangePickerInput__showClearDates: {
      paddingRight: 30 // TODO: should be noflip wrapped and handled by an isRTL prop

    },
    DateRangePickerInput_arrow: {
      display: 'inline-block',
      verticalAlign: 'middle',
      color: color.text
    },
    DateRangePickerInput_arrow_svg: {
      verticalAlign: 'middle',
      fill: color.text,
      height: sizing.arrowWidth,
      width: sizing.arrowWidth
    },
    DateRangePickerInput_clearDates: {
      background: 'none',
      border: 0,
      color: 'inherit',
      font: 'inherit',
      lineHeight: 'normal',
      overflow: 'visible',
      cursor: 'pointer',
      padding: 10,
      margin: '0 10px 0 5px',
      // TODO: should be noflip wrapped and handled by an isRTL prop
      position: 'absolute',
      right: 0,
      // TODO: should be noflip wrapped and handled by an isRTL prop
      top: '50%',
      transform: 'translateY(-50%)'
    },
    DateRangePickerInput_clearDates__small: {
      padding: 6
    },
    DateRangePickerInput_clearDates_default: {
      ':focus': {
        background: color.core.border,
        borderRadius: '50%'
      },
      ':hover': {
        background: color.core.border,
        borderRadius: '50%'
      }
    },
    DateRangePickerInput_clearDates__hide: {
      visibility: 'hidden'
    },
    DateRangePickerInput_clearDates_svg: {
      fill: color.core.grayLight,
      height: 12,
      width: 15,
      verticalAlign: 'middle'
    },
    DateRangePickerInput_clearDates_svg__small: {
      height: 9
    },
    DateRangePickerInput_calendarIcon: {
      background: 'none',
      border: 0,
      color: 'inherit',
      font: 'inherit',
      lineHeight: 'normal',
      overflow: 'visible',
      cursor: 'pointer',
      display: 'inline-block',
      verticalAlign: 'middle',
      padding: 10,
      margin: '0 5px 0 10px' // TODO: should be noflip wrapped and handled by an isRTL prop

    },
    DateRangePickerInput_calendarIcon_svg: {
      fill: color.core.grayLight,
      height: 15,
      width: 14,
      verticalAlign: 'middle'
    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(DateRangePickerInput);

exports["default"] = _default;

/***/ }),

/***/ "i1el":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = __webpack_require__("WZeS");


/***/ }),

/***/ "iNdV":
/***/ (function(module, exports) {

var messages = {
  invalidPredicate: '`predicate` must be a function',
  invalidPropValidator: '`propValidator` must be a function',
  requiredCore: 'is marked as required',
  invalidTypeCore: 'Invalid input type',
  predicateFailureCore: 'Failed to succeed with predicate',
  anonymousMessage: '<<anonymous>>',
  baseInvalidMessage: 'Invalid ',
};

function constructPropValidatorVariations(propValidator) {
  if (typeof propValidator !== 'function') {
    throw new Error(messages.invalidPropValidator);
  }

  var requiredPropValidator = propValidator.bind(null, false, null);
  requiredPropValidator.isRequired = propValidator.bind(null, true, null);

  requiredPropValidator.withPredicate = function predicateApplication(predicate) {
    if (typeof predicate !== 'function') {
      throw new Error(messages.invalidPredicate);
    }
    var basePropValidator = propValidator.bind(null, false, predicate);
    basePropValidator.isRequired = propValidator.bind(null, true, predicate);
    return basePropValidator;
  };

  return requiredPropValidator;
}

function createInvalidRequiredErrorMessage(propName, componentName, value) {
  return new Error(
    'The prop `' + propName + '` ' + messages.requiredCore +
    ' in `' + componentName + '`, but its value is `' + value + '`.'
  );
}

var independentGuardianValue = -1;

function preValidationRequireCheck(isRequired, componentName, propFullName, propValue) {
  var isPropValueUndefined = typeof propValue === 'undefined';
  var isPropValueNull = propValue === null;

  if (isRequired) {
    if (isPropValueUndefined) {
      return createInvalidRequiredErrorMessage(propFullName, componentName, 'undefined');
    } else if (isPropValueNull) {
      return createInvalidRequiredErrorMessage(propFullName, componentName, 'null');
    }
  }

  if (isPropValueUndefined || isPropValueNull) {
    return null;
  }

  return independentGuardianValue;
}

function createMomentChecker(type, typeValidator, validator, momentType) {

  function propValidator(
    isRequired, // Bound parameter to indicate with the propType is required
    predicate, // Bound parameter to allow user to add dynamic validation
    props,
    propName,
    componentName,
    location,
    propFullName
  ) {
    var propValue = props[ propName ];
    var propType = typeof propValue;

    componentName = componentName || messages.anonymousMessage;
    propFullName = propFullName || propName;

    var preValidationRequireCheckValue = preValidationRequireCheck(
      isRequired, componentName, propFullName, propValue
    );

    if (preValidationRequireCheckValue !== independentGuardianValue) {
      return preValidationRequireCheckValue;
    }

    if (typeValidator && !typeValidator(propValue)) {
      return new Error(
        messages.invalidTypeCore + ': `' + propName + '` of type `' + propType + '` ' +
        'supplied to `' + componentName + '`, expected `' + type + '`.'
      );
    }

    if (!validator(propValue)) {
      return new Error(
        messages.baseInvalidMessage + location + ' `' + propName + '` of type `' + propType + '` ' +
        'supplied to `' + componentName + '`, expected `' + momentType + '`.'
      );
    }

    if (predicate && !predicate(propValue)) {
      var predicateName = predicate.name || messages.anonymousMessage;
      return new Error(
        messages.baseInvalidMessage + location + ' `' + propName + '` of type `' + propType + '` ' +
        'supplied to `' + componentName + '`. ' + messages.predicateFailureCore + ' `' +
        predicateName + '`.'
      );
    }

    return null;

  }

  return constructPropValidatorVariations(propValidator);

}

module.exports = {
  constructPropValidatorVariations: constructPropValidatorVariations,
  createMomentChecker: createMomentChecker,
  messages: messages,
};


/***/ }),

/***/ "ib7Q":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var getPolyfill = __webpack_require__("xoj2");
var define = __webpack_require__("82c2");

module.exports = function shimValues() {
	var polyfill = getPolyfill();
	define(Object, { values: polyfill }, {
		values: function testValues() {
			return Object.values !== polyfill;
		}
	});
	return polyfill;
};


/***/ }),

/***/ "iuLr":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _constants = __webpack_require__("Fv1B");

var _default = _propTypes["default"].oneOf([_constants.OPEN_DOWN, _constants.OPEN_UP]);

exports["default"] = _default;

/***/ }),

/***/ "ixyq":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = calculateDimension;

function calculateDimension(el, axis) {
  var borderBox = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var withMargin = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

  if (!el) {
    return 0;
  }

  var axisStart = axis === 'width' ? 'Left' : 'Top';
  var axisEnd = axis === 'width' ? 'Right' : 'Bottom'; // Only read styles if we need to

  var style = !borderBox || withMargin ? window.getComputedStyle(el) : null; // Offset includes border and padding

  var offsetWidth = el.offsetWidth,
      offsetHeight = el.offsetHeight;
  var size = axis === 'width' ? offsetWidth : offsetHeight; // Get the inner size

  if (!borderBox) {
    size -= parseFloat(style["padding".concat(axisStart)]) + parseFloat(style["padding".concat(axisEnd)]) + parseFloat(style["border".concat(axisStart, "Width")]) + parseFloat(style["border".concat(axisEnd, "Width")]);
  } // Apply margin


  if (withMargin) {
    size += parseFloat(style["margin".concat(axisStart)]) + parseFloat(style["margin".concat(axisEnd)]);
  }

  return size;
}

/***/ }),

/***/ "jenk":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = toISOMonthString;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _toMomentObject = _interopRequireDefault(__webpack_require__("WmS1"));

function toISOMonthString(date, currentFormat) {
  var dateObj = _moment["default"].isMoment(date) ? date : (0, _toMomentObject["default"])(date, currentFormat);
  if (!dateObj) return null; // Template strings compiled in strict mode uses concat, which is slow. Since
  // this code is in a hot path and we want it to be as fast as possible, we
  // want to use old-fashioned +.
  // eslint-disable-next-line prefer-template

  return dateObj.year() + '-' + String(dateObj.month() + 1).padStart(2, '0');
}

/***/ }),

/***/ "jpOL":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


if (true) {
  module.exports = __webpack_require__("lPsR");
} else {}


/***/ }),

/***/ "kFtd":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.MAX_SPECIFICITY = exports.GLOBAL_CACHE_KEY = void 0;
var GLOBAL_CACHE_KEY = 'reactWithStylesInterfaceCSS';
exports.GLOBAL_CACHE_KEY = GLOBAL_CACHE_KEY;
var MAX_SPECIFICITY = 20;
exports.MAX_SPECIFICITY = MAX_SPECIFICITY;

/***/ }),

/***/ "kfJL":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
Object.defineProperty(exports, "CalendarDay", {
  enumerable: true,
  get: function get() {
    return _CalendarDay["default"];
  }
});
Object.defineProperty(exports, "CalendarMonth", {
  enumerable: true,
  get: function get() {
    return _CalendarMonth["default"];
  }
});
Object.defineProperty(exports, "CalendarMonthGrid", {
  enumerable: true,
  get: function get() {
    return _CalendarMonthGrid["default"];
  }
});
Object.defineProperty(exports, "DateRangePicker", {
  enumerable: true,
  get: function get() {
    return _DateRangePicker["default"];
  }
});
Object.defineProperty(exports, "DateRangePickerInput", {
  enumerable: true,
  get: function get() {
    return _DateRangePickerInput["default"];
  }
});
Object.defineProperty(exports, "DateRangePickerInputController", {
  enumerable: true,
  get: function get() {
    return _DateRangePickerInputController["default"];
  }
});
Object.defineProperty(exports, "DateRangePickerShape", {
  enumerable: true,
  get: function get() {
    return _DateRangePickerShape["default"];
  }
});
Object.defineProperty(exports, "DayPicker", {
  enumerable: true,
  get: function get() {
    return _DayPicker["default"];
  }
});
Object.defineProperty(exports, "DayPickerRangeController", {
  enumerable: true,
  get: function get() {
    return _DayPickerRangeController["default"];
  }
});
Object.defineProperty(exports, "DayPickerSingleDateController", {
  enumerable: true,
  get: function get() {
    return _DayPickerSingleDateController["default"];
  }
});
Object.defineProperty(exports, "SingleDatePicker", {
  enumerable: true,
  get: function get() {
    return _SingleDatePicker["default"];
  }
});
Object.defineProperty(exports, "SingleDatePickerInput", {
  enumerable: true,
  get: function get() {
    return _SingleDatePickerInput["default"];
  }
});
Object.defineProperty(exports, "SingleDatePickerShape", {
  enumerable: true,
  get: function get() {
    return _SingleDatePickerShape["default"];
  }
});
Object.defineProperty(exports, "isInclusivelyAfterDay", {
  enumerable: true,
  get: function get() {
    return _isInclusivelyAfterDay["default"];
  }
});
Object.defineProperty(exports, "isInclusivelyBeforeDay", {
  enumerable: true,
  get: function get() {
    return _isInclusivelyBeforeDay["default"];
  }
});
Object.defineProperty(exports, "isNextDay", {
  enumerable: true,
  get: function get() {
    return _isNextDay["default"];
  }
});
Object.defineProperty(exports, "isSameDay", {
  enumerable: true,
  get: function get() {
    return _isSameDay["default"];
  }
});
Object.defineProperty(exports, "toISODateString", {
  enumerable: true,
  get: function get() {
    return _toISODateString["default"];
  }
});
Object.defineProperty(exports, "toLocalizedDateString", {
  enumerable: true,
  get: function get() {
    return _toLocalizedDateString["default"];
  }
});
Object.defineProperty(exports, "toMomentObject", {
  enumerable: true,
  get: function get() {
    return _toMomentObject["default"];
  }
});

var _CalendarDay = _interopRequireDefault(__webpack_require__("N3k4"));

var _CalendarMonth = _interopRequireDefault(__webpack_require__("mMiH"));

var _CalendarMonthGrid = _interopRequireDefault(__webpack_require__("Thzv"));

var _DateRangePicker = _interopRequireDefault(__webpack_require__("9VuS"));

var _DateRangePickerInput = _interopRequireDefault(__webpack_require__("hwBm"));

var _DateRangePickerInputController = _interopRequireDefault(__webpack_require__("nmTR"));

var _DateRangePickerShape = _interopRequireDefault(__webpack_require__("yLoW"));

var _DayPicker = _interopRequireDefault(__webpack_require__("Nloh"));

var _DayPickerRangeController = _interopRequireDefault(__webpack_require__("CDAp"));

var _DayPickerSingleDateController = _interopRequireDefault(__webpack_require__("Xtko"));

var _SingleDatePicker = _interopRequireDefault(__webpack_require__("4CzF"));

var _SingleDatePickerInput = _interopRequireDefault(__webpack_require__("HUEL"));

var _SingleDatePickerShape = _interopRequireDefault(__webpack_require__("TUgy"));

var _isInclusivelyAfterDay = _interopRequireDefault(__webpack_require__("rit9"));

var _isInclusivelyBeforeDay = _interopRequireDefault(__webpack_require__("YCEU"));

var _isNextDay = _interopRequireDefault(__webpack_require__("YAW8"));

var _isSameDay = _interopRequireDefault(__webpack_require__("pRvc"));

var _toISODateString = _interopRequireDefault(__webpack_require__("pYxT"));

var _toLocalizedDateString = _interopRequireDefault(__webpack_require__("UyxP"));

var _toMomentObject = _interopRequireDefault(__webpack_require__("WmS1"));

/***/ }),

/***/ "kpKt":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.addModifier = addModifier;
exports.deleteModifier = deleteModifier;

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _isDayVisible = _interopRequireDefault(__webpack_require__("IgE5"));

var _toISODateString = _interopRequireDefault(__webpack_require__("pYxT"));

var _toISOMonthString = _interopRequireDefault(__webpack_require__("jenk"));

var _getPreviousMonthMemoLast = _interopRequireDefault(__webpack_require__("qkIn"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function addModifier(updatedDays, day, modifier, props, state) {
  var numberOfVisibleMonths = props.numberOfMonths,
      enableOutsideDays = props.enableOutsideDays,
      orientation = props.orientation;
  var firstVisibleMonth = state.currentMonth,
      visibleDays = state.visibleDays;
  var currentMonth = firstVisibleMonth;
  var numberOfMonths = numberOfVisibleMonths;

  if (orientation === _constants.VERTICAL_SCROLLABLE) {
    numberOfMonths = Object.keys(visibleDays).length;
  } else {
    currentMonth = (0, _getPreviousMonthMemoLast["default"])(currentMonth);
    numberOfMonths += 2;
  }

  if (!day || !(0, _isDayVisible["default"])(day, currentMonth, numberOfMonths, enableOutsideDays)) {
    return updatedDays;
  }

  var iso = (0, _toISODateString["default"])(day);

  var updatedDaysAfterAddition = _objectSpread({}, updatedDays);

  if (enableOutsideDays) {
    var monthsToUpdate = Object.keys(visibleDays).filter(function (monthKey) {
      return Object.keys(visibleDays[monthKey]).indexOf(iso) > -1;
    });
    updatedDaysAfterAddition = monthsToUpdate.reduce(function (acc, monthIso) {
      var month = updatedDays[monthIso] || visibleDays[monthIso];

      if (!month[iso] || !month[iso].has(modifier)) {
        var modifiers = new Set(month[iso]);
        modifiers.add(modifier);
        acc[monthIso] = _objectSpread({}, month, (0, _defineProperty2["default"])({}, iso, modifiers));
      }

      return acc;
    }, updatedDaysAfterAddition);
  } else {
    var monthIso = (0, _toISOMonthString["default"])(day);
    var month = updatedDays[monthIso] || visibleDays[monthIso] || {};

    if (!month[iso] || !month[iso].has(modifier)) {
      var modifiers = new Set(month[iso]);
      modifiers.add(modifier);
      updatedDaysAfterAddition[monthIso] = _objectSpread({}, month, (0, _defineProperty2["default"])({}, iso, modifiers));
    }
  }

  return updatedDaysAfterAddition;
}

function deleteModifier(updatedDays, day, modifier, props, state) {
  var numberOfVisibleMonths = props.numberOfMonths,
      enableOutsideDays = props.enableOutsideDays,
      orientation = props.orientation;
  var firstVisibleMonth = state.currentMonth,
      visibleDays = state.visibleDays;
  var currentMonth = firstVisibleMonth;
  var numberOfMonths = numberOfVisibleMonths;

  if (orientation === _constants.VERTICAL_SCROLLABLE) {
    numberOfMonths = Object.keys(visibleDays).length;
  } else {
    currentMonth = (0, _getPreviousMonthMemoLast["default"])(currentMonth);
    numberOfMonths += 2;
  }

  if (!day || !(0, _isDayVisible["default"])(day, currentMonth, numberOfMonths, enableOutsideDays)) {
    return updatedDays;
  }

  var iso = (0, _toISODateString["default"])(day);

  var updatedDaysAfterDeletion = _objectSpread({}, updatedDays);

  if (enableOutsideDays) {
    var monthsToUpdate = Object.keys(visibleDays).filter(function (monthKey) {
      return Object.keys(visibleDays[monthKey]).indexOf(iso) > -1;
    });
    updatedDaysAfterDeletion = monthsToUpdate.reduce(function (acc, monthIso) {
      var month = updatedDays[monthIso] || visibleDays[monthIso];

      if (month[iso] && month[iso].has(modifier)) {
        var modifiers = new Set(month[iso]);
        modifiers["delete"](modifier);
        acc[monthIso] = _objectSpread({}, month, (0, _defineProperty2["default"])({}, iso, modifiers));
      }

      return acc;
    }, updatedDaysAfterDeletion);
  } else {
    var monthIso = (0, _toISOMonthString["default"])(day);
    var month = updatedDays[monthIso] || visibleDays[monthIso] || {};

    if (month[iso] && month[iso].has(modifier)) {
      var modifiers = new Set(month[iso]);
      modifiers["delete"](modifier);
      updatedDaysAfterDeletion[monthIso] = _objectSpread({}, month, (0, _defineProperty2["default"])({}, iso, modifiers));
    }
  }

  return updatedDaysAfterDeletion;
}

/***/ }),

/***/ "l4Qr":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var isCallable = __webpack_require__("IdCN");
var functionsHaveNames = __webpack_require__("JQAW")();
var bind = __webpack_require__("D3zA");
var functionToString = bind.call(Function.call, Function.prototype.toString);
var stringMatch = bind.call(Function.call, String.prototype.match);

var classRegex = /^class /;

var isClass = function isClassConstructor(fn) {
	if (isCallable(fn)) {
		return false;
	}
	if (typeof fn !== 'function') {
		return false;
	}
	try {
		var match = stringMatch(functionToString(fn), classRegex);
		return !!match;
	} catch (e) {}
	return false;
};

var regex = /\s*function\s+([^(\s]*)\s*/;

var functionProto = Function.prototype;

module.exports = function getName() {
	if (!isClass(this) && !isCallable(this)) {
		throw new TypeError('Function.prototype.name sham getter called on non-function');
	}
	if (functionsHaveNames) {
		return this.name;
	}
	if (this === functionProto) {
		return '';
	}
	var str = functionToString(this);
	var match = stringMatch(str, regex);
	var name = match && match[1];
	return name;
};


/***/ }),

/***/ "lPsR":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/** @license React v16.9.0
 * react-is.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

Object.defineProperty(exports,"__esModule",{value:!0});
var b="function"===typeof Symbol&&Symbol.for,c=b?Symbol.for("react.element"):60103,d=b?Symbol.for("react.portal"):60106,e=b?Symbol.for("react.fragment"):60107,f=b?Symbol.for("react.strict_mode"):60108,g=b?Symbol.for("react.profiler"):60114,h=b?Symbol.for("react.provider"):60109,k=b?Symbol.for("react.context"):60110,l=b?Symbol.for("react.async_mode"):60111,m=b?Symbol.for("react.concurrent_mode"):60111,n=b?Symbol.for("react.forward_ref"):60112,p=b?Symbol.for("react.suspense"):60113,q=b?Symbol.for("react.suspense_list"):
60120,r=b?Symbol.for("react.memo"):60115,t=b?Symbol.for("react.lazy"):60116,v=b?Symbol.for("react.fundamental"):60117,w=b?Symbol.for("react.responder"):60118;function x(a){if("object"===typeof a&&null!==a){var u=a.$$typeof;switch(u){case c:switch(a=a.type,a){case l:case m:case e:case g:case f:case p:return a;default:switch(a=a&&a.$$typeof,a){case k:case n:case h:return a;default:return u}}case t:case r:case d:return u}}}function y(a){return x(a)===m}exports.typeOf=x;exports.AsyncMode=l;
exports.ConcurrentMode=m;exports.ContextConsumer=k;exports.ContextProvider=h;exports.Element=c;exports.ForwardRef=n;exports.Fragment=e;exports.Lazy=t;exports.Memo=r;exports.Portal=d;exports.Profiler=g;exports.StrictMode=f;exports.Suspense=p;
exports.isValidElementType=function(a){return"string"===typeof a||"function"===typeof a||a===e||a===m||a===g||a===f||a===p||a===q||"object"===typeof a&&null!==a&&(a.$$typeof===t||a.$$typeof===r||a.$$typeof===h||a.$$typeof===k||a.$$typeof===n||a.$$typeof===v||a.$$typeof===w)};exports.isAsyncMode=function(a){return y(a)||x(a)===l};exports.isConcurrentMode=y;exports.isContextConsumer=function(a){return x(a)===k};exports.isContextProvider=function(a){return x(a)===h};
exports.isElement=function(a){return"object"===typeof a&&null!==a&&a.$$typeof===c};exports.isForwardRef=function(a){return x(a)===n};exports.isFragment=function(a){return x(a)===e};exports.isLazy=function(a){return x(a)===t};exports.isMemo=function(a){return x(a)===r};exports.isPortal=function(a){return x(a)===d};exports.isProfiler=function(a){return x(a)===g};exports.isStrictMode=function(a){return x(a)===f};exports.isSuspense=function(a){return x(a)===p};


/***/ }),

/***/ "lSNA":
/***/ (function(module, exports) {

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

module.exports = _defineProperty;

/***/ }),

/***/ "lzPt":
/***/ (function(module, exports, __webpack_require__) {

// eslint-disable-next-line import/no-unresolved
module.exports = __webpack_require__("VDVV").default;


/***/ }),

/***/ "m0LI":
/***/ (function(module, exports) {

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

module.exports = _iterableToArrayLimit;

/***/ }),

/***/ "m2ax":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getCalendarMonthWidth;

function getCalendarMonthWidth(daySize) {
  var calendarMonthPadding = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
  return 7 * daySize + 2 * calendarMonthPadding + 1;
}

/***/ }),

/***/ "mMiH":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _reactMomentProptypes = _interopRequireDefault(__webpack_require__("XGBb"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _CalendarWeek = _interopRequireDefault(__webpack_require__("2Q00"));

var _CalendarDay = _interopRequireDefault(__webpack_require__("N3k4"));

var _calculateDimension = _interopRequireDefault(__webpack_require__("ixyq"));

var _getCalendarMonthWeeks = _interopRequireDefault(__webpack_require__("F7ZS"));

var _isSameDay = _interopRequireDefault(__webpack_require__("pRvc"));

var _toISODateString = _interopRequireDefault(__webpack_require__("pYxT"));

var _ModifiersShape = _interopRequireDefault(__webpack_require__("J7JS"));

var _ScrollableOrientationShape = _interopRequireDefault(__webpack_require__("aE6U"));

var _DayOfWeekShape = _interopRequireDefault(__webpack_require__("2S2E"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  month: (0, _moment["default"])(),
  horizontalMonthPadding: 13,
  isVisible: true,
  enableOutsideDays: false,
  modifiers: {},
  orientation: _constants.HORIZONTAL_ORIENTATION,
  daySize: _constants.DAY_SIZE,
  onDayClick: function onDayClick() {},
  onDayMouseEnter: function onDayMouseEnter() {},
  onDayMouseLeave: function onDayMouseLeave() {},
  onMonthSelect: function onMonthSelect() {},
  onYearSelect: function onYearSelect() {},
  renderMonthText: null,
  renderCalendarDay: function renderCalendarDay(props) {
    return _react["default"].createElement(_CalendarDay["default"], props);
  },
  renderDayContents: null,
  renderMonthElement: null,
  firstDayOfWeek: null,
  setMonthTitleHeight: null,
  focusedDate: null,
  isFocused: false,
  // i18n
  monthFormat: 'MMMM YYYY',
  // english locale
  phrases: _defaultPhrases.CalendarDayPhrases,
  dayAriaLabelFormat: undefined,
  verticalBorderSpacing: undefined
};

var CalendarMonth =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(CalendarMonth, _ref);
  var _proto = CalendarMonth.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function CalendarMonth(props) {
    var _this;

    _this = _ref.call(this, props) || this;
    _this.state = {
      weeks: (0, _getCalendarMonthWeeks["default"])(props.month, props.enableOutsideDays, props.firstDayOfWeek == null ? _moment["default"].localeData().firstDayOfWeek() : props.firstDayOfWeek)
    };
    _this.setCaptionRef = _this.setCaptionRef.bind((0, _assertThisInitialized2["default"])(_this));
    _this.setMonthTitleHeight = _this.setMonthTitleHeight.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.componentDidMount = function componentDidMount() {
    this.setMonthTitleHeightTimeout = setTimeout(this.setMonthTitleHeight, 0);
  };

  _proto.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
    var month = nextProps.month,
        enableOutsideDays = nextProps.enableOutsideDays,
        firstDayOfWeek = nextProps.firstDayOfWeek;
    var _this$props = this.props,
        prevMonth = _this$props.month,
        prevEnableOutsideDays = _this$props.enableOutsideDays,
        prevFirstDayOfWeek = _this$props.firstDayOfWeek;

    if (!month.isSame(prevMonth) || enableOutsideDays !== prevEnableOutsideDays || firstDayOfWeek !== prevFirstDayOfWeek) {
      this.setState({
        weeks: (0, _getCalendarMonthWeeks["default"])(month, enableOutsideDays, firstDayOfWeek == null ? _moment["default"].localeData().firstDayOfWeek() : firstDayOfWeek)
      });
    }
  };

  _proto.componentWillUnmount = function componentWillUnmount() {
    if (this.setMonthTitleHeightTimeout) {
      clearTimeout(this.setMonthTitleHeightTimeout);
    }
  };

  _proto.setMonthTitleHeight = function setMonthTitleHeight() {
    var setMonthTitleHeight = this.props.setMonthTitleHeight;

    if (setMonthTitleHeight) {
      var captionHeight = (0, _calculateDimension["default"])(this.captionRef, 'height', true, true);
      setMonthTitleHeight(captionHeight);
    }
  };

  _proto.setCaptionRef = function setCaptionRef(ref) {
    this.captionRef = ref;
  };

  _proto.render = function render() {
    var _this$props2 = this.props,
        css = _this$props2.css,
        dayAriaLabelFormat = _this$props2.dayAriaLabelFormat,
        daySize = _this$props2.daySize,
        focusedDate = _this$props2.focusedDate,
        horizontalMonthPadding = _this$props2.horizontalMonthPadding,
        isFocused = _this$props2.isFocused,
        isVisible = _this$props2.isVisible,
        modifiers = _this$props2.modifiers,
        month = _this$props2.month,
        monthFormat = _this$props2.monthFormat,
        onDayClick = _this$props2.onDayClick,
        onDayMouseEnter = _this$props2.onDayMouseEnter,
        onDayMouseLeave = _this$props2.onDayMouseLeave,
        onMonthSelect = _this$props2.onMonthSelect,
        onYearSelect = _this$props2.onYearSelect,
        orientation = _this$props2.orientation,
        phrases = _this$props2.phrases,
        renderCalendarDay = _this$props2.renderCalendarDay,
        renderDayContents = _this$props2.renderDayContents,
        renderMonthElement = _this$props2.renderMonthElement,
        renderMonthText = _this$props2.renderMonthText,
        styles = _this$props2.styles,
        verticalBorderSpacing = _this$props2.verticalBorderSpacing;
    var weeks = this.state.weeks;
    var monthTitle = renderMonthText ? renderMonthText(month) : month.format(monthFormat);
    var verticalScrollable = orientation === _constants.VERTICAL_SCROLLABLE;
    return _react["default"].createElement("div", (0, _extends2["default"])({}, css(styles.CalendarMonth, {
      padding: "0 ".concat(horizontalMonthPadding, "px")
    }), {
      "data-visible": isVisible
    }), _react["default"].createElement("div", (0, _extends2["default"])({
      ref: this.setCaptionRef
    }, css(styles.CalendarMonth_caption, verticalScrollable && styles.CalendarMonth_caption__verticalScrollable)), renderMonthElement ? renderMonthElement({
      month: month,
      onMonthSelect: onMonthSelect,
      onYearSelect: onYearSelect,
      isVisible: isVisible
    }) : _react["default"].createElement("strong", null, monthTitle)), _react["default"].createElement("table", (0, _extends2["default"])({}, css(!verticalBorderSpacing && styles.CalendarMonth_table, verticalBorderSpacing && styles.CalendarMonth_verticalSpacing, verticalBorderSpacing && {
      borderSpacing: "0px ".concat(verticalBorderSpacing, "px")
    }), {
      role: "presentation"
    }), _react["default"].createElement("tbody", null, weeks.map(function (week, i) {
      return _react["default"].createElement(_CalendarWeek["default"], {
        key: i
      }, week.map(function (day, dayOfWeek) {
        return renderCalendarDay({
          key: dayOfWeek,
          day: day,
          daySize: daySize,
          isOutsideDay: !day || day.month() !== month.month(),
          tabIndex: isVisible && (0, _isSameDay["default"])(day, focusedDate) ? 0 : -1,
          isFocused: isFocused,
          onDayMouseEnter: onDayMouseEnter,
          onDayMouseLeave: onDayMouseLeave,
          onDayClick: onDayClick,
          renderDayContents: renderDayContents,
          phrases: phrases,
          modifiers: modifiers[(0, _toISODateString["default"])(day)],
          ariaLabelFormat: dayAriaLabelFormat
        });
      }));
    }))));
  };

  return CalendarMonth;
}(_react["default"].PureComponent || _react["default"].Component);

CalendarMonth.propTypes =  false ? undefined : {};
CalendarMonth.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref2) {
  var _ref2$reactDates = _ref2.reactDates,
      color = _ref2$reactDates.color,
      font = _ref2$reactDates.font,
      spacing = _ref2$reactDates.spacing;
  return {
    CalendarMonth: {
      background: color.background,
      textAlign: 'center',
      verticalAlign: 'top',
      userSelect: 'none'
    },
    CalendarMonth_table: {
      borderCollapse: 'collapse',
      borderSpacing: 0
    },
    CalendarMonth_verticalSpacing: {
      borderCollapse: 'separate'
    },
    CalendarMonth_caption: {
      color: color.text,
      fontSize: font.captionSize,
      textAlign: 'center',
      paddingTop: spacing.captionPaddingTop,
      paddingBottom: spacing.captionPaddingBottom,
      captionSide: 'initial'
    },
    CalendarMonth_caption__verticalScrollable: {
      paddingTop: 12,
      paddingBottom: 7
    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(CalendarMonth);

exports["default"] = _default;

/***/ }),

/***/ "n1Y7":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var define = __webpack_require__("82c2");

var implementation = __webpack_require__("nRDI");
var getPolyfill = __webpack_require__("5yQQ");
var polyfill = getPolyfill();
var shim = __webpack_require__("Gn0q");

var boundContains = function contains(node, other) {
	return polyfill.apply(node, [other]);
};

define(boundContains, {
	getPolyfill: getPolyfill,
	implementation: implementation,
	shim: shim
});

module.exports = boundContains;


/***/ }),

/***/ "nLTY":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getClassName;

/**
 * Construct a class name.
 *
 * namespace {String} Used to construct unique class names.
 * styleName {String} Name identifying the specific style.
 *
 * Return the class name.
 */
function getClassName(namespace, styleName) {
  var namespaceSegment = namespace.length > 0 ? "".concat(namespace, "__") : '';
  return "".concat(namespaceSegment).concat(styleName);
}

/***/ }),

/***/ "nRDI":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function contains(other) {
	if (arguments.length < 1) {
		throw new TypeError('1 argument is required');
	}
	if (typeof other !== 'object') {
		throw new TypeError('Argument 1 (”other“) to Node.contains must be an instance of Node');
	}

	var node = other;
	do {
		if (this === node) {
			return true;
		}
		if (node) {
			node = node.parentNode;
		}
	} while (node);

	return false;
};


/***/ }),

/***/ "nmTR":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _enzymeShallowEqual = _interopRequireDefault(__webpack_require__("NE9H"));

var _assertThisInitialized2 = _interopRequireDefault(__webpack_require__("PJYZ"));

var _inheritsLoose2 = _interopRequireDefault(__webpack_require__("VbXa"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _reactMomentProptypes = _interopRequireDefault(__webpack_require__("XGBb"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _OpenDirectionShape = _interopRequireDefault(__webpack_require__("iuLr"));

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _DateRangePickerInput = _interopRequireDefault(__webpack_require__("hwBm"));

var _IconPositionShape = _interopRequireDefault(__webpack_require__("Bh6R"));

var _DisabledShape = _interopRequireDefault(__webpack_require__("5Fvu"));

var _toMomentObject = _interopRequireDefault(__webpack_require__("WmS1"));

var _toLocalizedDateString = _interopRequireDefault(__webpack_require__("UyxP"));

var _isInclusivelyAfterDay = _interopRequireDefault(__webpack_require__("rit9"));

var _isBeforeDay = _interopRequireDefault(__webpack_require__("h6xH"));

var _constants = __webpack_require__("Fv1B");

var propTypes =  false ? undefined : {};
var defaultProps = {
  children: null,
  startDate: null,
  startDateId: _constants.START_DATE,
  startDatePlaceholderText: 'Start Date',
  isStartDateFocused: false,
  startDateAriaLabel: undefined,
  endDate: null,
  endDateId: _constants.END_DATE,
  endDatePlaceholderText: 'End Date',
  isEndDateFocused: false,
  endDateAriaLabel: undefined,
  screenReaderMessage: '',
  showClearDates: false,
  showCaret: false,
  showDefaultInputIcon: false,
  inputIconPosition: _constants.ICON_BEFORE_POSITION,
  disabled: false,
  required: false,
  readOnly: false,
  openDirection: _constants.OPEN_DOWN,
  noBorder: false,
  block: false,
  small: false,
  regular: false,
  verticalSpacing: undefined,
  keepOpenOnDateSelect: false,
  reopenPickerOnClearDates: false,
  withFullScreenPortal: false,
  minimumNights: 1,
  isOutsideRange: function isOutsideRange(day) {
    return !(0, _isInclusivelyAfterDay["default"])(day, (0, _moment["default"])());
  },
  displayFormat: function displayFormat() {
    return _moment["default"].localeData().longDateFormat('L');
  },
  onFocusChange: function onFocusChange() {},
  onClose: function onClose() {},
  onDatesChange: function onDatesChange() {},
  onKeyDownArrowDown: function onKeyDownArrowDown() {},
  onKeyDownQuestionMark: function onKeyDownQuestionMark() {},
  customInputIcon: null,
  customArrowIcon: null,
  customCloseIcon: null,
  // accessibility
  isFocused: false,
  // i18n
  phrases: _defaultPhrases.DateRangePickerInputPhrases,
  isRTL: false
};

var DateRangePickerInputController =
/*#__PURE__*/
function (_ref) {
  (0, _inheritsLoose2["default"])(DateRangePickerInputController, _ref);
  var _proto = DateRangePickerInputController.prototype;

  _proto[!_react["default"].PureComponent && "shouldComponentUpdate"] = function (nextProps, nextState) {
    return !(0, _enzymeShallowEqual["default"])(this.props, nextProps) || !(0, _enzymeShallowEqual["default"])(this.state, nextState);
  };

  function DateRangePickerInputController(props) {
    var _this;

    _this = _ref.call(this, props) || this;
    _this.onClearFocus = _this.onClearFocus.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onStartDateChange = _this.onStartDateChange.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onStartDateFocus = _this.onStartDateFocus.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onEndDateChange = _this.onEndDateChange.bind((0, _assertThisInitialized2["default"])(_this));
    _this.onEndDateFocus = _this.onEndDateFocus.bind((0, _assertThisInitialized2["default"])(_this));
    _this.clearDates = _this.clearDates.bind((0, _assertThisInitialized2["default"])(_this));
    return _this;
  }

  _proto.onClearFocus = function onClearFocus() {
    var _this$props = this.props,
        onFocusChange = _this$props.onFocusChange,
        onClose = _this$props.onClose,
        startDate = _this$props.startDate,
        endDate = _this$props.endDate;
    onFocusChange(null);
    onClose({
      startDate: startDate,
      endDate: endDate
    });
  };

  _proto.onEndDateChange = function onEndDateChange(endDateString) {
    var _this$props2 = this.props,
        startDate = _this$props2.startDate,
        isOutsideRange = _this$props2.isOutsideRange,
        minimumNights = _this$props2.minimumNights,
        keepOpenOnDateSelect = _this$props2.keepOpenOnDateSelect,
        onDatesChange = _this$props2.onDatesChange;
    var endDate = (0, _toMomentObject["default"])(endDateString, this.getDisplayFormat());
    var isEndDateValid = endDate && !isOutsideRange(endDate) && !(startDate && (0, _isBeforeDay["default"])(endDate, startDate.clone().add(minimumNights, 'days')));

    if (isEndDateValid) {
      onDatesChange({
        startDate: startDate,
        endDate: endDate
      });
      if (!keepOpenOnDateSelect) this.onClearFocus();
    } else {
      onDatesChange({
        startDate: startDate,
        endDate: null
      });
    }
  };

  _proto.onEndDateFocus = function onEndDateFocus() {
    var _this$props3 = this.props,
        startDate = _this$props3.startDate,
        onFocusChange = _this$props3.onFocusChange,
        withFullScreenPortal = _this$props3.withFullScreenPortal,
        disabled = _this$props3.disabled;

    if (!startDate && withFullScreenPortal && (!disabled || disabled === _constants.END_DATE)) {
      // When the datepicker is full screen, we never want to focus the end date first
      // because there's no indication that that is the case once the datepicker is open and it
      // might confuse the user
      onFocusChange(_constants.START_DATE);
    } else if (!disabled || disabled === _constants.START_DATE) {
      onFocusChange(_constants.END_DATE);
    }
  };

  _proto.onStartDateChange = function onStartDateChange(startDateString) {
    var endDate = this.props.endDate;
    var _this$props4 = this.props,
        isOutsideRange = _this$props4.isOutsideRange,
        minimumNights = _this$props4.minimumNights,
        onDatesChange = _this$props4.onDatesChange,
        onFocusChange = _this$props4.onFocusChange,
        disabled = _this$props4.disabled;
    var startDate = (0, _toMomentObject["default"])(startDateString, this.getDisplayFormat());
    var isEndDateBeforeStartDate = startDate && (0, _isBeforeDay["default"])(endDate, startDate.clone().add(minimumNights, 'days'));
    var isStartDateValid = startDate && !isOutsideRange(startDate) && !(disabled === _constants.END_DATE && isEndDateBeforeStartDate);

    if (isStartDateValid) {
      if (isEndDateBeforeStartDate) {
        endDate = null;
      }

      onDatesChange({
        startDate: startDate,
        endDate: endDate
      });
      onFocusChange(_constants.END_DATE);
    } else {
      onDatesChange({
        startDate: null,
        endDate: endDate
      });
    }
  };

  _proto.onStartDateFocus = function onStartDateFocus() {
    var _this$props5 = this.props,
        disabled = _this$props5.disabled,
        onFocusChange = _this$props5.onFocusChange;

    if (!disabled || disabled === _constants.END_DATE) {
      onFocusChange(_constants.START_DATE);
    }
  };

  _proto.getDisplayFormat = function getDisplayFormat() {
    var displayFormat = this.props.displayFormat;
    return typeof displayFormat === 'string' ? displayFormat : displayFormat();
  };

  _proto.getDateString = function getDateString(date) {
    var displayFormat = this.getDisplayFormat();

    if (date && displayFormat) {
      return date && date.format(displayFormat);
    }

    return (0, _toLocalizedDateString["default"])(date);
  };

  _proto.clearDates = function clearDates() {
    var _this$props6 = this.props,
        onDatesChange = _this$props6.onDatesChange,
        reopenPickerOnClearDates = _this$props6.reopenPickerOnClearDates,
        onFocusChange = _this$props6.onFocusChange;
    onDatesChange({
      startDate: null,
      endDate: null
    });

    if (reopenPickerOnClearDates) {
      onFocusChange(_constants.START_DATE);
    }
  };

  _proto.render = function render() {
    var _this$props7 = this.props,
        children = _this$props7.children,
        startDate = _this$props7.startDate,
        startDateId = _this$props7.startDateId,
        startDatePlaceholderText = _this$props7.startDatePlaceholderText,
        isStartDateFocused = _this$props7.isStartDateFocused,
        startDateAriaLabel = _this$props7.startDateAriaLabel,
        endDate = _this$props7.endDate,
        endDateId = _this$props7.endDateId,
        endDatePlaceholderText = _this$props7.endDatePlaceholderText,
        endDateAriaLabel = _this$props7.endDateAriaLabel,
        isEndDateFocused = _this$props7.isEndDateFocused,
        screenReaderMessage = _this$props7.screenReaderMessage,
        showClearDates = _this$props7.showClearDates,
        showCaret = _this$props7.showCaret,
        showDefaultInputIcon = _this$props7.showDefaultInputIcon,
        inputIconPosition = _this$props7.inputIconPosition,
        customInputIcon = _this$props7.customInputIcon,
        customArrowIcon = _this$props7.customArrowIcon,
        customCloseIcon = _this$props7.customCloseIcon,
        disabled = _this$props7.disabled,
        required = _this$props7.required,
        readOnly = _this$props7.readOnly,
        openDirection = _this$props7.openDirection,
        isFocused = _this$props7.isFocused,
        phrases = _this$props7.phrases,
        onKeyDownArrowDown = _this$props7.onKeyDownArrowDown,
        onKeyDownQuestionMark = _this$props7.onKeyDownQuestionMark,
        isRTL = _this$props7.isRTL,
        noBorder = _this$props7.noBorder,
        block = _this$props7.block,
        small = _this$props7.small,
        regular = _this$props7.regular,
        verticalSpacing = _this$props7.verticalSpacing;
    var startDateString = this.getDateString(startDate);
    var endDateString = this.getDateString(endDate);
    return _react["default"].createElement(_DateRangePickerInput["default"], {
      startDate: startDateString,
      startDateId: startDateId,
      startDatePlaceholderText: startDatePlaceholderText,
      isStartDateFocused: isStartDateFocused,
      startDateAriaLabel: startDateAriaLabel,
      endDate: endDateString,
      endDateId: endDateId,
      endDatePlaceholderText: endDatePlaceholderText,
      isEndDateFocused: isEndDateFocused,
      endDateAriaLabel: endDateAriaLabel,
      isFocused: isFocused,
      disabled: disabled,
      required: required,
      readOnly: readOnly,
      openDirection: openDirection,
      showCaret: showCaret,
      showDefaultInputIcon: showDefaultInputIcon,
      inputIconPosition: inputIconPosition,
      customInputIcon: customInputIcon,
      customArrowIcon: customArrowIcon,
      customCloseIcon: customCloseIcon,
      phrases: phrases,
      onStartDateChange: this.onStartDateChange,
      onStartDateFocus: this.onStartDateFocus,
      onStartDateShiftTab: this.onClearFocus,
      onEndDateChange: this.onEndDateChange,
      onEndDateFocus: this.onEndDateFocus,
      showClearDates: showClearDates,
      onClearDates: this.clearDates,
      screenReaderMessage: screenReaderMessage,
      onKeyDownArrowDown: onKeyDownArrowDown,
      onKeyDownQuestionMark: onKeyDownQuestionMark,
      isRTL: isRTL,
      noBorder: noBorder,
      block: block,
      small: small,
      regular: regular,
      verticalSpacing: verticalSpacing
    }, children);
  };

  return DateRangePickerInputController;
}(_react["default"].PureComponent || _react["default"].Component);

exports["default"] = DateRangePickerInputController;
DateRangePickerInputController.propTypes =  false ? undefined : {};
DateRangePickerInputController.defaultProps = defaultProps;

/***/ }),

/***/ "nmnc":
/***/ (function(module, exports, __webpack_require__) {

var root = __webpack_require__("Kz5y");

/** Built-in value references. */
var Symbol = root.Symbol;

module.exports = Symbol;


/***/ }),

/***/ "oMlu":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = __webpack_require__("2gq3");


/***/ }),

/***/ "oNNP":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var bind = __webpack_require__("D3zA");

module.exports = bind.call(Function.call, Object.prototype.hasOwnProperty);


/***/ }),

/***/ "oOcr":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getPhrase;

function getPhrase(phrase, args) {
  if (typeof phrase === 'string') return phrase;

  if (typeof phrase === 'function') {
    return phrase(args);
  }

  return '';
}

/***/ }),

/***/ "oR9Z":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _constants = __webpack_require__("Fv1B");

var _default = _propTypes["default"].oneOf([_constants.INFO_POSITION_TOP, _constants.INFO_POSITION_BOTTOM, _constants.INFO_POSITION_BEFORE, _constants.INFO_POSITION_AFTER]);

exports["default"] = _default;

/***/ }),

/***/ "ocq3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var isMergeableObject = function isMergeableObject(value) {
	return isNonNullObject(value)
		&& !isSpecial(value)
};

function isNonNullObject(value) {
	return !!value && typeof value === 'object'
}

function isSpecial(value) {
	var stringValue = Object.prototype.toString.call(value);

	return stringValue === '[object RegExp]'
		|| stringValue === '[object Date]'
		|| isReactElement(value)
}

// see https://github.com/facebook/react/blob/b5ac963fb791d1298e7f396236383bc955f916c1/src/isomorphic/classic/element/ReactElement.js#L21-L25
var canUseSymbol = typeof Symbol === 'function' && Symbol.for;
var REACT_ELEMENT_TYPE = canUseSymbol ? Symbol.for('react.element') : 0xeac7;

function isReactElement(value) {
	return value.$$typeof === REACT_ELEMENT_TYPE
}

function emptyTarget(val) {
    return Array.isArray(val) ? [] : {}
}

function cloneIfNecessary(value, optionsArgument) {
    var clone = optionsArgument && optionsArgument.clone === true;
    return (clone && isMergeableObject(value)) ? deepmerge(emptyTarget(value), value, optionsArgument) : value
}

function defaultArrayMerge(target, source, optionsArgument) {
    var destination = target.slice();
    source.forEach(function(e, i) {
        if (typeof destination[i] === 'undefined') {
            destination[i] = cloneIfNecessary(e, optionsArgument);
        } else if (isMergeableObject(e)) {
            destination[i] = deepmerge(target[i], e, optionsArgument);
        } else if (target.indexOf(e) === -1) {
            destination.push(cloneIfNecessary(e, optionsArgument));
        }
    });
    return destination
}

function mergeObject(target, source, optionsArgument) {
    var destination = {};
    if (isMergeableObject(target)) {
        Object.keys(target).forEach(function(key) {
            destination[key] = cloneIfNecessary(target[key], optionsArgument);
        });
    }
    Object.keys(source).forEach(function(key) {
        if (!isMergeableObject(source[key]) || !target[key]) {
            destination[key] = cloneIfNecessary(source[key], optionsArgument);
        } else {
            destination[key] = deepmerge(target[key], source[key], optionsArgument);
        }
    });
    return destination
}

function deepmerge(target, source, optionsArgument) {
    var sourceIsArray = Array.isArray(source);
    var targetIsArray = Array.isArray(target);
    var options = optionsArgument || { arrayMerge: defaultArrayMerge };
    var sourceAndTargetTypesMatch = sourceIsArray === targetIsArray;

    if (!sourceAndTargetTypesMatch) {
        return cloneIfNecessary(source, optionsArgument)
    } else if (sourceIsArray) {
        var arrayMerge = options.arrayMerge || defaultArrayMerge;
        return arrayMerge(target, source, optionsArgument)
    } else {
        return mergeObject(target, source, optionsArgument)
    }
}

deepmerge.all = function deepmergeAll(array, optionsArgument) {
    if (!Array.isArray(array) || array.length < 2) {
        throw new Error('first argument should be an array with at least two elements')
    }

    // we are sure there are at least 2 values, so it is safe to have no initial value
    return array.reduce(function(prev, next) {
        return deepmerge(prev, next, optionsArgument)
    })
};

var deepmerge_1 = deepmerge;

module.exports = deepmerge_1;


/***/ }),

/***/ "ohE5":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * 
 */

function makeEmptyFunction(arg) {
  return function () {
    return arg;
  };
}

/**
 * This function accepts and discards inputs; it has no side effects. This is
 * primarily useful idiomatically for overridable function endpoints which
 * always need to be callable, since JS lacks a null-call idiom ala Cocoa.
 */
var emptyFunction = function emptyFunction() {};

emptyFunction.thatReturns = makeEmptyFunction;
emptyFunction.thatReturnsFalse = makeEmptyFunction(false);
emptyFunction.thatReturnsTrue = makeEmptyFunction(true);
emptyFunction.thatReturnsNull = makeEmptyFunction(null);
emptyFunction.thatReturnsThis = function () {
  return this;
};
emptyFunction.thatReturnsArgument = function (arg) {
  return arg;
};

module.exports = emptyFunction;

/***/ }),

/***/ "pRvc":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isSameDay;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

function isSameDay(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false; // Compare least significant, most likely to change units first
  // Moment's isSame clones moment inputs and is a tad slow

  return a.date() === b.date() && a.month() === b.month() && a.year() === b.year();
}

/***/ }),

/***/ "pVnL":
/***/ (function(module, exports) {

function _extends() {
  module.exports = _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}

module.exports = _extends;

/***/ }),

/***/ "pYxT":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = toISODateString;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _toMomentObject = _interopRequireDefault(__webpack_require__("WmS1"));

function toISODateString(date, currentFormat) {
  var dateObj = _moment["default"].isMoment(date) ? date : (0, _toMomentObject["default"])(date, currentFormat);
  if (!dateObj) return null; // Template strings compiled in strict mode uses concat, which is slow. Since
  // this code is in a hot path and we want it to be as fast as possible, we
  // want to use old-fashioned +.
  // eslint-disable-next-line prefer-template

  return dateObj.year() + '-' + String(dateObj.month() + 1).padStart(2, '0') + '-' + String(dateObj.date()).padStart(2, '0');
}

/***/ }),

/***/ "q86A":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getTransformStyles;

function getTransformStyles(transformValue) {
  return {
    transform: transformValue,
    msTransform: transformValue,
    MozTransform: transformValue,
    WebkitTransform: transformValue
  };
}

/***/ }),

/***/ "qNGI":
/***/ (function(module, exports, __webpack_require__) {

// eslint-disable-next-line import/no-unresolved
module.exports = __webpack_require__("kfJL");


/***/ }),

/***/ "qOSK":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _constants = __webpack_require__("Fv1B");

var _default = _propTypes["default"].oneOf([_constants.HORIZONTAL_ORIENTATION, _constants.VERTICAL_ORIENTATION]);

exports["default"] = _default;

/***/ }),

/***/ "qT12":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/** @license React v16.8.6
 * react-is.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

Object.defineProperty(exports,"__esModule",{value:!0});
var b="function"===typeof Symbol&&Symbol.for,c=b?Symbol.for("react.element"):60103,d=b?Symbol.for("react.portal"):60106,e=b?Symbol.for("react.fragment"):60107,f=b?Symbol.for("react.strict_mode"):60108,g=b?Symbol.for("react.profiler"):60114,h=b?Symbol.for("react.provider"):60109,k=b?Symbol.for("react.context"):60110,l=b?Symbol.for("react.async_mode"):60111,m=b?Symbol.for("react.concurrent_mode"):60111,n=b?Symbol.for("react.forward_ref"):60112,p=b?Symbol.for("react.suspense"):60113,q=b?Symbol.for("react.memo"):
60115,r=b?Symbol.for("react.lazy"):60116;function t(a){if("object"===typeof a&&null!==a){var u=a.$$typeof;switch(u){case c:switch(a=a.type,a){case l:case m:case e:case g:case f:case p:return a;default:switch(a=a&&a.$$typeof,a){case k:case n:case h:return a;default:return u}}case r:case q:case d:return u}}}function v(a){return t(a)===m}exports.typeOf=t;exports.AsyncMode=l;exports.ConcurrentMode=m;exports.ContextConsumer=k;exports.ContextProvider=h;exports.Element=c;exports.ForwardRef=n;
exports.Fragment=e;exports.Lazy=r;exports.Memo=q;exports.Portal=d;exports.Profiler=g;exports.StrictMode=f;exports.Suspense=p;exports.isValidElementType=function(a){return"string"===typeof a||"function"===typeof a||a===e||a===m||a===g||a===f||a===p||"object"===typeof a&&null!==a&&(a.$$typeof===r||a.$$typeof===q||a.$$typeof===h||a.$$typeof===k||a.$$typeof===n)};exports.isAsyncMode=function(a){return v(a)||t(a)===l};exports.isConcurrentMode=v;exports.isContextConsumer=function(a){return t(a)===k};
exports.isContextProvider=function(a){return t(a)===h};exports.isElement=function(a){return"object"===typeof a&&null!==a&&a.$$typeof===c};exports.isForwardRef=function(a){return t(a)===n};exports.isFragment=function(a){return t(a)===e};exports.isLazy=function(a){return t(a)===r};exports.isMemo=function(a){return t(a)===q};exports.isPortal=function(a){return t(a)===d};exports.isProfiler=function(a){return t(a)===g};exports.isStrictMode=function(a){return t(a)===f};
exports.isSuspense=function(a){return t(a)===p};


/***/ }),

/***/ "qkIn":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getPreviousMonthMemoLast;
var getPreviousMonthMemoKey;
var getPreviousMonthMemoValue;

function getPreviousMonthMemoLast(month) {
  if (month !== getPreviousMonthMemoKey) {
    getPreviousMonthMemoKey = month;
    getPreviousMonthMemoValue = month.clone().subtract(1, 'month');
  }

  return getPreviousMonthMemoValue;
}

/***/ }),

/***/ "rQy3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var ES = __webpack_require__("oMlu");
var has = __webpack_require__("oNNP");
var bind = __webpack_require__("D3zA");
var isEnumerable = bind.call(Function.call, Object.prototype.propertyIsEnumerable);

module.exports = function values(O) {
	var obj = ES.RequireObjectCoercible(O);
	var vals = [];
	for (var key in obj) {
		if (has(obj, key) && isEnumerable(obj, key)) {
			vals.push(obj[key]);
		}
	}
	return vals;
};


/***/ }),

/***/ "rit9":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isInclusivelyAfterDay;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _isBeforeDay = _interopRequireDefault(__webpack_require__("h6xH"));

function isInclusivelyAfterDay(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false;
  return !(0, _isBeforeDay["default"])(a, b);
}

/***/ }),

/***/ "sDMB":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _propTypes = __webpack_require__("17x9");

var _propTypes2 = _interopRequireDefault(_propTypes);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

exports['default'] = _propTypes2['default'].shape({
  getState: _propTypes2['default'].func,
  setState: _propTypes2['default'].func,
  subscribe: _propTypes2['default'].func
});

/***/ }),

/***/ "sEE1":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var bind = __webpack_require__("D3zA");

var ES2016 = __webpack_require__("2gq3");
var assign = __webpack_require__("DGeY");
var forEach = __webpack_require__("yjl6");

var GetIntrinsic = __webpack_require__("6ayh");

var $TypeError = GetIntrinsic('%TypeError%');
var $isEnumerable = bind.call(Function.call, GetIntrinsic('%ObjectPrototype%').propertyIsEnumerable);
var $pushApply = bind.call(Function.apply, GetIntrinsic('%ArrayPrototype%').push);

var ES2017 = assign(assign({}, ES2016), {
	ToIndex: function ToIndex(value) {
		if (typeof value === 'undefined') {
			return 0;
		}
		var integerIndex = this.ToInteger(value);
		if (integerIndex < 0) {
			throw new RangeError('index must be >= 0');
		}
		var index = this.ToLength(integerIndex);
		if (!this.SameValueZero(integerIndex, index)) {
			throw new RangeError('index must be >= 0 and < 2 ** 53 - 1');
		}
		return index;
	},

	// https://www.ecma-international.org/ecma-262/8.0/#sec-enumerableownproperties
	EnumerableOwnProperties: function EnumerableOwnProperties(O, kind) {
		var keys = ES2016.EnumerableOwnNames(O);
		if (kind === 'key') {
			return keys;
		}
		if (kind === 'value' || kind === 'key+value') {
			var results = [];
			forEach(keys, function (key) {
				if ($isEnumerable(O, key)) {
					$pushApply(results, [
						kind === 'value' ? O[key] : [key, O[key]]
					]);
				}
			});
			return results;
		}
		throw new $TypeError('Assertion failed: "kind" is not "key", "value", or "key+value": ' + kind);
	}
});

delete ES2017.EnumerableOwnNames; // replaced with EnumerableOwnProperties

module.exports = ES2017;


/***/ }),

/***/ "sEfC":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("GoyQ"),
    now = __webpack_require__("QIyF"),
    toNumber = __webpack_require__("tLB3");

/** Error message constants. */
var FUNC_ERROR_TEXT = 'Expected a function';

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeMax = Math.max,
    nativeMin = Math.min;

/**
 * Creates a debounced function that delays invoking `func` until after `wait`
 * milliseconds have elapsed since the last time the debounced function was
 * invoked. The debounced function comes with a `cancel` method to cancel
 * delayed `func` invocations and a `flush` method to immediately invoke them.
 * Provide `options` to indicate whether `func` should be invoked on the
 * leading and/or trailing edge of the `wait` timeout. The `func` is invoked
 * with the last arguments provided to the debounced function. Subsequent
 * calls to the debounced function return the result of the last `func`
 * invocation.
 *
 * **Note:** If `leading` and `trailing` options are `true`, `func` is
 * invoked on the trailing edge of the timeout only if the debounced function
 * is invoked more than once during the `wait` timeout.
 *
 * If `wait` is `0` and `leading` is `false`, `func` invocation is deferred
 * until to the next tick, similar to `setTimeout` with a timeout of `0`.
 *
 * See [David Corbacho's article](https://css-tricks.com/debouncing-throttling-explained-examples/)
 * for details over the differences between `_.debounce` and `_.throttle`.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Function
 * @param {Function} func The function to debounce.
 * @param {number} [wait=0] The number of milliseconds to delay.
 * @param {Object} [options={}] The options object.
 * @param {boolean} [options.leading=false]
 *  Specify invoking on the leading edge of the timeout.
 * @param {number} [options.maxWait]
 *  The maximum time `func` is allowed to be delayed before it's invoked.
 * @param {boolean} [options.trailing=true]
 *  Specify invoking on the trailing edge of the timeout.
 * @returns {Function} Returns the new debounced function.
 * @example
 *
 * // Avoid costly calculations while the window size is in flux.
 * jQuery(window).on('resize', _.debounce(calculateLayout, 150));
 *
 * // Invoke `sendMail` when clicked, debouncing subsequent calls.
 * jQuery(element).on('click', _.debounce(sendMail, 300, {
 *   'leading': true,
 *   'trailing': false
 * }));
 *
 * // Ensure `batchLog` is invoked once after 1 second of debounced calls.
 * var debounced = _.debounce(batchLog, 250, { 'maxWait': 1000 });
 * var source = new EventSource('/stream');
 * jQuery(source).on('message', debounced);
 *
 * // Cancel the trailing debounced invocation.
 * jQuery(window).on('popstate', debounced.cancel);
 */
function debounce(func, wait, options) {
  var lastArgs,
      lastThis,
      maxWait,
      result,
      timerId,
      lastCallTime,
      lastInvokeTime = 0,
      leading = false,
      maxing = false,
      trailing = true;

  if (typeof func != 'function') {
    throw new TypeError(FUNC_ERROR_TEXT);
  }
  wait = toNumber(wait) || 0;
  if (isObject(options)) {
    leading = !!options.leading;
    maxing = 'maxWait' in options;
    maxWait = maxing ? nativeMax(toNumber(options.maxWait) || 0, wait) : maxWait;
    trailing = 'trailing' in options ? !!options.trailing : trailing;
  }

  function invokeFunc(time) {
    var args = lastArgs,
        thisArg = lastThis;

    lastArgs = lastThis = undefined;
    lastInvokeTime = time;
    result = func.apply(thisArg, args);
    return result;
  }

  function leadingEdge(time) {
    // Reset any `maxWait` timer.
    lastInvokeTime = time;
    // Start the timer for the trailing edge.
    timerId = setTimeout(timerExpired, wait);
    // Invoke the leading edge.
    return leading ? invokeFunc(time) : result;
  }

  function remainingWait(time) {
    var timeSinceLastCall = time - lastCallTime,
        timeSinceLastInvoke = time - lastInvokeTime,
        timeWaiting = wait - timeSinceLastCall;

    return maxing
      ? nativeMin(timeWaiting, maxWait - timeSinceLastInvoke)
      : timeWaiting;
  }

  function shouldInvoke(time) {
    var timeSinceLastCall = time - lastCallTime,
        timeSinceLastInvoke = time - lastInvokeTime;

    // Either this is the first call, activity has stopped and we're at the
    // trailing edge, the system time has gone backwards and we're treating
    // it as the trailing edge, or we've hit the `maxWait` limit.
    return (lastCallTime === undefined || (timeSinceLastCall >= wait) ||
      (timeSinceLastCall < 0) || (maxing && timeSinceLastInvoke >= maxWait));
  }

  function timerExpired() {
    var time = now();
    if (shouldInvoke(time)) {
      return trailingEdge(time);
    }
    // Restart the timer.
    timerId = setTimeout(timerExpired, remainingWait(time));
  }

  function trailingEdge(time) {
    timerId = undefined;

    // Only invoke if we have `lastArgs` which means `func` has been
    // debounced at least once.
    if (trailing && lastArgs) {
      return invokeFunc(time);
    }
    lastArgs = lastThis = undefined;
    return result;
  }

  function cancel() {
    if (timerId !== undefined) {
      clearTimeout(timerId);
    }
    lastInvokeTime = 0;
    lastArgs = lastCallTime = lastThis = timerId = undefined;
  }

  function flush() {
    return timerId === undefined ? result : trailingEdge(now());
  }

  function debounced() {
    var time = now(),
        isInvoking = shouldInvoke(time);

    lastArgs = arguments;
    lastThis = this;
    lastCallTime = time;

    if (isInvoking) {
      if (timerId === undefined) {
        return leadingEdge(lastCallTime);
      }
      if (maxing) {
        // Handle invocations in a tight loop.
        timerId = setTimeout(timerExpired, wait);
        return invokeFunc(lastCallTime);
      }
    }
    if (timerId === undefined) {
      timerId = setTimeout(timerExpired, wait);
    }
    return result;
  }
  debounced.cancel = cancel;
  debounced.flush = flush;
  return debounced;
}

module.exports = debounce;


/***/ }),

/***/ "tLB3":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("GoyQ"),
    isSymbol = __webpack_require__("/9aa");

/** Used as references for various `Number` constants. */
var NAN = 0 / 0;

/** Used to match leading and trailing whitespace. */
var reTrim = /^\s+|\s+$/g;

/** Used to detect bad signed hexadecimal string values. */
var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;

/** Used to detect binary string values. */
var reIsBinary = /^0b[01]+$/i;

/** Used to detect octal string values. */
var reIsOctal = /^0o[0-7]+$/i;

/** Built-in method references without a dependency on `root`. */
var freeParseInt = parseInt;

/**
 * Converts `value` to a number.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to process.
 * @returns {number} Returns the number.
 * @example
 *
 * _.toNumber(3.2);
 * // => 3.2
 *
 * _.toNumber(Number.MIN_VALUE);
 * // => 5e-324
 *
 * _.toNumber(Infinity);
 * // => Infinity
 *
 * _.toNumber('3.2');
 * // => 3.2
 */
function toNumber(value) {
  if (typeof value == 'number') {
    return value;
  }
  if (isSymbol(value)) {
    return NAN;
  }
  if (isObject(value)) {
    var other = typeof value.valueOf == 'function' ? value.valueOf() : value;
    value = isObject(other) ? (other + '') : other;
  }
  if (typeof value != 'string') {
    return value === 0 ? value : +value;
  }
  value = value.replace(reTrim, '');
  var isBinary = reIsBinary.test(value);
  return (isBinary || reIsOctal.test(value))
    ? freeParseInt(value.slice(2), isBinary ? 2 : 8)
    : (reIsBadHex.test(value) ? NAN : +value);
}

module.exports = toNumber;


/***/ }),

/***/ "u1Mj":
/***/ (function(module, exports) {

module.exports = function mod(number, modulo) {
	var remain = number % modulo;
	return Math.floor(remain >= 0 ? remain : remain + modulo);
};


/***/ }),

/***/ "u5Fq":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getVisibleDays;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

var _toISOMonthString = _interopRequireDefault(__webpack_require__("jenk"));

function getVisibleDays(month, numberOfMonths, enableOutsideDays, withoutTransitionMonths) {
  if (!_moment["default"].isMoment(month)) return {};
  var visibleDaysByMonth = {};
  var currentMonth = withoutTransitionMonths ? month.clone() : month.clone().subtract(1, 'month');

  for (var i = 0; i < (withoutTransitionMonths ? numberOfMonths : numberOfMonths + 2); i += 1) {
    var visibleDays = []; // set utc offset to get correct dates in future (when timezone changes)

    var baseDate = currentMonth.clone();
    var firstOfMonth = baseDate.clone().startOf('month').hour(12);
    var lastOfMonth = baseDate.clone().endOf('month').hour(12);
    var currentDay = firstOfMonth.clone(); // days belonging to the previous month

    if (enableOutsideDays) {
      for (var j = 0; j < currentDay.weekday(); j += 1) {
        var prevDay = currentDay.clone().subtract(j + 1, 'day');
        visibleDays.unshift(prevDay);
      }
    }

    while (currentDay < lastOfMonth) {
      visibleDays.push(currentDay.clone());
      currentDay.add(1, 'day');
    }

    if (enableOutsideDays) {
      // weekday() returns the index of the day of the week according to the locale
      // this means if the week starts on Monday, weekday() will return 0 for a Monday date, not 1
      if (currentDay.weekday() !== 0) {
        // days belonging to the next month
        for (var k = currentDay.weekday(), count = 0; k < 7; k += 1, count += 1) {
          var nextDay = currentDay.clone().add(count, 'day');
          visibleDays.push(nextDay);
        }
      }
    }

    visibleDaysByMonth[(0, _toISOMonthString["default"])(currentMonth)] = visibleDays;
    currentMonth = currentMonth.clone().add(1, 'month');
  }

  return visibleDaysByMonth;
}

/***/ }),

/***/ "ulUS":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = isSameMonth;

var _moment = _interopRequireDefault(__webpack_require__("wy2R"));

function isSameMonth(a, b) {
  if (!_moment["default"].isMoment(a) || !_moment["default"].isMoment(b)) return false; // Compare least significant, most likely to change units first
  // Moment's isSame clones moment inputs and is a tad slow

  return a.month() === b.month() && a.year() === b.year();
}

/***/ }),

/***/ "v3P4":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var define = __webpack_require__("82c2");
var getPolyfill = __webpack_require__("22yB");

module.exports = function shimFlat() {
	var polyfill = getPolyfill();
	define(
		Array.prototype,
		{ flat: polyfill },
		{ flat: function () { return Array.prototype.flat !== polyfill; } }
	);
	return polyfill;
};


/***/ }),

/***/ "vV+G":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.CalendarDayPhrases = exports.DayPickerNavigationPhrases = exports.DayPickerKeyboardShortcutsPhrases = exports.DayPickerPhrases = exports.SingleDatePickerInputPhrases = exports.SingleDatePickerPhrases = exports.DateRangePickerInputPhrases = exports.DateRangePickerPhrases = exports["default"] = void 0;
var calendarLabel = 'Calendar';
var roleDescription = 'datepicker';
var closeDatePicker = 'Close';
var focusStartDate = 'Interact with the calendar and add the check-in date for your trip.';
var clearDate = 'Clear Date';
var clearDates = 'Clear Dates';
var jumpToPrevMonth = 'Move backward to switch to the previous month.';
var jumpToNextMonth = 'Move forward to switch to the next month.';
var keyboardShortcuts = 'Keyboard Shortcuts';
var showKeyboardShortcutsPanel = 'Open the keyboard shortcuts panel.';
var hideKeyboardShortcutsPanel = 'Close the shortcuts panel.';
var openThisPanel = 'Open this panel.';
var enterKey = 'Enter key';
var leftArrowRightArrow = 'Right and left arrow keys';
var upArrowDownArrow = 'up and down arrow keys';
var pageUpPageDown = 'page up and page down keys';
var homeEnd = 'Home and end keys';
var escape = 'Escape key';
var questionMark = 'Question mark';
var selectFocusedDate = 'Select the date in focus.';
var moveFocusByOneDay = 'Move backward (left) and forward (right) by one day.';
var moveFocusByOneWeek = 'Move backward (up) and forward (down) by one week.';
var moveFocusByOneMonth = 'Switch months.';
var moveFocustoStartAndEndOfWeek = 'Go to the first or last day of a week.';
var returnFocusToInput = 'Return to the date input field.';
var keyboardForwardNavigationInstructions = 'Navigate forward to interact with the calendar and select a date. Press the question mark key to get the keyboard shortcuts for changing dates.';
var keyboardBackwardNavigationInstructions = 'Navigate backward to interact with the calendar and select a date. Press the question mark key to get the keyboard shortcuts for changing dates.';

var chooseAvailableStartDate = function chooseAvailableStartDate(_ref) {
  var date = _ref.date;
  return "Choose ".concat(date, " as your check-in date. It\u2019s available.");
};

var chooseAvailableEndDate = function chooseAvailableEndDate(_ref2) {
  var date = _ref2.date;
  return "Choose ".concat(date, " as your check-out date. It\u2019s available.");
};

var chooseAvailableDate = function chooseAvailableDate(_ref3) {
  var date = _ref3.date;
  return date;
};

var dateIsUnavailable = function dateIsUnavailable(_ref4) {
  var date = _ref4.date;
  return "Not available. ".concat(date);
};

var dateIsSelected = function dateIsSelected(_ref5) {
  var date = _ref5.date;
  return "Selected. ".concat(date);
};

var dateIsSelectedAsStartDate = function dateIsSelectedAsStartDate(_ref6) {
  var date = _ref6.date;
  return "Selected as start date. ".concat(date);
};

var dateIsSelectedAsEndDate = function dateIsSelectedAsEndDate(_ref7) {
  var date = _ref7.date;
  return "Selected as end date. ".concat(date);
};

var _default = {
  calendarLabel: calendarLabel,
  roleDescription: roleDescription,
  closeDatePicker: closeDatePicker,
  focusStartDate: focusStartDate,
  clearDate: clearDate,
  clearDates: clearDates,
  jumpToPrevMonth: jumpToPrevMonth,
  jumpToNextMonth: jumpToNextMonth,
  keyboardShortcuts: keyboardShortcuts,
  showKeyboardShortcutsPanel: showKeyboardShortcutsPanel,
  hideKeyboardShortcutsPanel: hideKeyboardShortcutsPanel,
  openThisPanel: openThisPanel,
  enterKey: enterKey,
  leftArrowRightArrow: leftArrowRightArrow,
  upArrowDownArrow: upArrowDownArrow,
  pageUpPageDown: pageUpPageDown,
  homeEnd: homeEnd,
  escape: escape,
  questionMark: questionMark,
  selectFocusedDate: selectFocusedDate,
  moveFocusByOneDay: moveFocusByOneDay,
  moveFocusByOneWeek: moveFocusByOneWeek,
  moveFocusByOneMonth: moveFocusByOneMonth,
  moveFocustoStartAndEndOfWeek: moveFocustoStartAndEndOfWeek,
  returnFocusToInput: returnFocusToInput,
  keyboardForwardNavigationInstructions: keyboardForwardNavigationInstructions,
  keyboardBackwardNavigationInstructions: keyboardBackwardNavigationInstructions,
  chooseAvailableStartDate: chooseAvailableStartDate,
  chooseAvailableEndDate: chooseAvailableEndDate,
  dateIsUnavailable: dateIsUnavailable,
  dateIsSelected: dateIsSelected,
  dateIsSelectedAsStartDate: dateIsSelectedAsStartDate,
  dateIsSelectedAsEndDate: dateIsSelectedAsEndDate
};
exports["default"] = _default;
var DateRangePickerPhrases = {
  calendarLabel: calendarLabel,
  roleDescription: roleDescription,
  closeDatePicker: closeDatePicker,
  clearDates: clearDates,
  focusStartDate: focusStartDate,
  jumpToPrevMonth: jumpToPrevMonth,
  jumpToNextMonth: jumpToNextMonth,
  keyboardShortcuts: keyboardShortcuts,
  showKeyboardShortcutsPanel: showKeyboardShortcutsPanel,
  hideKeyboardShortcutsPanel: hideKeyboardShortcutsPanel,
  openThisPanel: openThisPanel,
  enterKey: enterKey,
  leftArrowRightArrow: leftArrowRightArrow,
  upArrowDownArrow: upArrowDownArrow,
  pageUpPageDown: pageUpPageDown,
  homeEnd: homeEnd,
  escape: escape,
  questionMark: questionMark,
  selectFocusedDate: selectFocusedDate,
  moveFocusByOneDay: moveFocusByOneDay,
  moveFocusByOneWeek: moveFocusByOneWeek,
  moveFocusByOneMonth: moveFocusByOneMonth,
  moveFocustoStartAndEndOfWeek: moveFocustoStartAndEndOfWeek,
  returnFocusToInput: returnFocusToInput,
  keyboardForwardNavigationInstructions: keyboardForwardNavigationInstructions,
  keyboardBackwardNavigationInstructions: keyboardBackwardNavigationInstructions,
  chooseAvailableStartDate: chooseAvailableStartDate,
  chooseAvailableEndDate: chooseAvailableEndDate,
  dateIsUnavailable: dateIsUnavailable,
  dateIsSelected: dateIsSelected,
  dateIsSelectedAsStartDate: dateIsSelectedAsStartDate,
  dateIsSelectedAsEndDate: dateIsSelectedAsEndDate
};
exports.DateRangePickerPhrases = DateRangePickerPhrases;
var DateRangePickerInputPhrases = {
  focusStartDate: focusStartDate,
  clearDates: clearDates,
  keyboardForwardNavigationInstructions: keyboardForwardNavigationInstructions,
  keyboardBackwardNavigationInstructions: keyboardBackwardNavigationInstructions
};
exports.DateRangePickerInputPhrases = DateRangePickerInputPhrases;
var SingleDatePickerPhrases = {
  calendarLabel: calendarLabel,
  roleDescription: roleDescription,
  closeDatePicker: closeDatePicker,
  clearDate: clearDate,
  jumpToPrevMonth: jumpToPrevMonth,
  jumpToNextMonth: jumpToNextMonth,
  keyboardShortcuts: keyboardShortcuts,
  showKeyboardShortcutsPanel: showKeyboardShortcutsPanel,
  hideKeyboardShortcutsPanel: hideKeyboardShortcutsPanel,
  openThisPanel: openThisPanel,
  enterKey: enterKey,
  leftArrowRightArrow: leftArrowRightArrow,
  upArrowDownArrow: upArrowDownArrow,
  pageUpPageDown: pageUpPageDown,
  homeEnd: homeEnd,
  escape: escape,
  questionMark: questionMark,
  selectFocusedDate: selectFocusedDate,
  moveFocusByOneDay: moveFocusByOneDay,
  moveFocusByOneWeek: moveFocusByOneWeek,
  moveFocusByOneMonth: moveFocusByOneMonth,
  moveFocustoStartAndEndOfWeek: moveFocustoStartAndEndOfWeek,
  returnFocusToInput: returnFocusToInput,
  keyboardForwardNavigationInstructions: keyboardForwardNavigationInstructions,
  keyboardBackwardNavigationInstructions: keyboardBackwardNavigationInstructions,
  chooseAvailableDate: chooseAvailableDate,
  dateIsUnavailable: dateIsUnavailable,
  dateIsSelected: dateIsSelected
};
exports.SingleDatePickerPhrases = SingleDatePickerPhrases;
var SingleDatePickerInputPhrases = {
  clearDate: clearDate,
  keyboardForwardNavigationInstructions: keyboardForwardNavigationInstructions,
  keyboardBackwardNavigationInstructions: keyboardBackwardNavigationInstructions
};
exports.SingleDatePickerInputPhrases = SingleDatePickerInputPhrases;
var DayPickerPhrases = {
  calendarLabel: calendarLabel,
  roleDescription: roleDescription,
  jumpToPrevMonth: jumpToPrevMonth,
  jumpToNextMonth: jumpToNextMonth,
  keyboardShortcuts: keyboardShortcuts,
  showKeyboardShortcutsPanel: showKeyboardShortcutsPanel,
  hideKeyboardShortcutsPanel: hideKeyboardShortcutsPanel,
  openThisPanel: openThisPanel,
  enterKey: enterKey,
  leftArrowRightArrow: leftArrowRightArrow,
  upArrowDownArrow: upArrowDownArrow,
  pageUpPageDown: pageUpPageDown,
  homeEnd: homeEnd,
  escape: escape,
  questionMark: questionMark,
  selectFocusedDate: selectFocusedDate,
  moveFocusByOneDay: moveFocusByOneDay,
  moveFocusByOneWeek: moveFocusByOneWeek,
  moveFocusByOneMonth: moveFocusByOneMonth,
  moveFocustoStartAndEndOfWeek: moveFocustoStartAndEndOfWeek,
  returnFocusToInput: returnFocusToInput,
  chooseAvailableStartDate: chooseAvailableStartDate,
  chooseAvailableEndDate: chooseAvailableEndDate,
  chooseAvailableDate: chooseAvailableDate,
  dateIsUnavailable: dateIsUnavailable,
  dateIsSelected: dateIsSelected,
  dateIsSelectedAsStartDate: dateIsSelectedAsStartDate,
  dateIsSelectedAsEndDate: dateIsSelectedAsEndDate
};
exports.DayPickerPhrases = DayPickerPhrases;
var DayPickerKeyboardShortcutsPhrases = {
  keyboardShortcuts: keyboardShortcuts,
  showKeyboardShortcutsPanel: showKeyboardShortcutsPanel,
  hideKeyboardShortcutsPanel: hideKeyboardShortcutsPanel,
  openThisPanel: openThisPanel,
  enterKey: enterKey,
  leftArrowRightArrow: leftArrowRightArrow,
  upArrowDownArrow: upArrowDownArrow,
  pageUpPageDown: pageUpPageDown,
  homeEnd: homeEnd,
  escape: escape,
  questionMark: questionMark,
  selectFocusedDate: selectFocusedDate,
  moveFocusByOneDay: moveFocusByOneDay,
  moveFocusByOneWeek: moveFocusByOneWeek,
  moveFocusByOneMonth: moveFocusByOneMonth,
  moveFocustoStartAndEndOfWeek: moveFocustoStartAndEndOfWeek,
  returnFocusToInput: returnFocusToInput
};
exports.DayPickerKeyboardShortcutsPhrases = DayPickerKeyboardShortcutsPhrases;
var DayPickerNavigationPhrases = {
  jumpToPrevMonth: jumpToPrevMonth,
  jumpToNextMonth: jumpToNextMonth
};
exports.DayPickerNavigationPhrases = DayPickerNavigationPhrases;
var CalendarDayPhrases = {
  chooseAvailableDate: chooseAvailableDate,
  dateIsUnavailable: dateIsUnavailable,
  dateIsSelected: dateIsSelected,
  dateIsSelectedAsStartDate: dateIsSelectedAsStartDate,
  dateIsSelectedAsEndDate: dateIsSelectedAsEndDate
};
exports.CalendarDayPhrases = CalendarDayPhrases;

/***/ }),

/***/ "vg41":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;
var EMPTY_STYLES = {};

var EMPTY_STYLES_FN = function EMPTY_STYLES_FN() {
  return EMPTY_STYLES;
};

var _default = EMPTY_STYLES_FN;
exports["default"] = _default;

/***/ }),

/***/ "w0iJ":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getResponsiveContainerStyles;

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _constants = __webpack_require__("Fv1B");

function getResponsiveContainerStyles(anchorDirection, currentOffset, containerEdge, margin) {
  var windowWidth = typeof window !== 'undefined' ? window.innerWidth : 0;
  var calculatedOffset = anchorDirection === _constants.ANCHOR_LEFT ? windowWidth - containerEdge : containerEdge;
  var calculatedMargin = margin || 0;
  return (0, _defineProperty2["default"])({}, anchorDirection, Math.min(currentOffset + calculatedOffset - calculatedMargin, 0));
}

/***/ }),

/***/ "wTVA":
/***/ (function(module, exports) {

function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

module.exports = _arrayWithHoles;

/***/ }),

/***/ "wkBT":
/***/ (function(module, exports) {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

module.exports = _nonIterableRest;

/***/ }),

/***/ "wy2R":
/***/ (function(module, exports) {

(function() { module.exports = this["moment"]; }());

/***/ }),

/***/ "xEkU":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var now = __webpack_require__("bQgK")
  , root = typeof window === 'undefined' ? global : window
  , vendors = ['moz', 'webkit']
  , suffix = 'AnimationFrame'
  , raf = root['request' + suffix]
  , caf = root['cancel' + suffix] || root['cancelRequest' + suffix]

for(var i = 0; !raf && i < vendors.length; i++) {
  raf = root[vendors[i] + 'Request' + suffix]
  caf = root[vendors[i] + 'Cancel' + suffix]
      || root[vendors[i] + 'CancelRequest' + suffix]
}

// Some versions of FF have rAF but not cAF
if(!raf || !caf) {
  var last = 0
    , id = 0
    , queue = []
    , frameDuration = 1000 / 60

  raf = function(callback) {
    if(queue.length === 0) {
      var _now = now()
        , next = Math.max(0, frameDuration - (_now - last))
      last = next + _now
      setTimeout(function() {
        var cp = queue.slice(0)
        // Clear queue here to prevent
        // callbacks from appending listeners
        // to the current frame's queue
        queue.length = 0
        for(var i = 0; i < cp.length; i++) {
          if(!cp[i].cancelled) {
            try{
              cp[i].callback(last)
            } catch(e) {
              setTimeout(function() { throw e }, 0)
            }
          }
        }
      }, Math.round(next))
    }
    queue.push({
      handle: ++id,
      callback: callback,
      cancelled: false
    })
    return id
  }

  caf = function(handle) {
    for(var i = 0; i < queue.length; i++) {
      if(queue[i].handle === handle) {
        queue[i].cancelled = true
      }
    }
  }
}

module.exports = function(fn) {
  // Wrap in a new function to prevent
  // `cancel` potentially being assigned
  // to the native rAF function
  return raf.call(root, fn)
}
module.exports.cancel = function() {
  caf.apply(root, arguments)
}
module.exports.polyfill = function(object) {
  if (!object) {
    object = root;
  }
  object.requestAnimationFrame = raf
  object.cancelAnimationFrame = caf
}

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("yLpj")))

/***/ }),

/***/ "xEte":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var CloseButton = function CloseButton(props) {
  return _react["default"].createElement("svg", props, _react["default"].createElement("path", {
    fillRule: "evenodd",
    d: "M11.53.47a.75.75 0 0 0-1.061 0l-4.47 4.47L1.529.47A.75.75 0 1 0 .468 1.531l4.47 4.47-4.47 4.47a.75.75 0 1 0 1.061 1.061l4.47-4.47 4.47 4.47a.75.75 0 1 0 1.061-1.061l-4.47-4.47 4.47-4.47a.75.75 0 0 0 0-1.061z"
  }));
};

CloseButton.defaultProps = {
  focusable: "false",
  viewBox: "0 0 12 12"
};
var _default = CloseButton;
exports["default"] = _default;

/***/ }),

/***/ "xG2L":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var GetIntrinsic = __webpack_require__("6ayh");

var $TypeError = GetIntrinsic('%TypeError%');
var $SyntaxError = GetIntrinsic('%SyntaxError%');

var has = __webpack_require__("oNNP");

var predicates = {
  // https://ecma-international.org/ecma-262/6.0/#sec-property-descriptor-specification-type
  'Property Descriptor': function isPropertyDescriptor(ES, Desc) {
    if (ES.Type(Desc) !== 'Object') {
      return false;
    }
    var allowed = {
      '[[Configurable]]': true,
      '[[Enumerable]]': true,
      '[[Get]]': true,
      '[[Set]]': true,
      '[[Value]]': true,
      '[[Writable]]': true
    };

    for (var key in Desc) { // eslint-disable-line
      if (has(Desc, key) && !allowed[key]) {
        return false;
      }
    }

    var isData = has(Desc, '[[Value]]');
    var IsAccessor = has(Desc, '[[Get]]') || has(Desc, '[[Set]]');
    if (isData && IsAccessor) {
      throw new $TypeError('Property Descriptors may not be both accessor and data descriptors');
    }
    return true;
  }
};

module.exports = function assertRecord(ES, recordType, argumentName, value) {
  var predicate = predicates[recordType];
  if (typeof predicate !== 'function') {
    throw new $SyntaxError('unknown record type: ' + recordType);
  }
  if (!predicate(ES, value)) {
    throw new $TypeError(argumentName + ' must be a ' + recordType);
  }
  console.log(predicate(ES, value), value);
};


/***/ }),

/***/ "xOhs":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;
var core = {
  white: '#fff',
  gray: '#484848',
  grayLight: '#82888a',
  grayLighter: '#cacccd',
  grayLightest: '#f2f2f2',
  borderMedium: '#c4c4c4',
  border: '#dbdbdb',
  borderLight: '#e4e7e7',
  borderLighter: '#eceeee',
  borderBright: '#f4f5f5',
  primary: '#00a699',
  primaryShade_1: '#33dacd',
  primaryShade_2: '#66e2da',
  primaryShade_3: '#80e8e0',
  primaryShade_4: '#b2f1ec',
  primary_dark: '#008489',
  secondary: '#007a87',
  yellow: '#ffe8bc',
  yellow_dark: '#ffce71'
};
var _default = {
  reactDates: {
    zIndex: 0,
    border: {
      input: {
        border: 0,
        borderTop: 0,
        borderRight: 0,
        borderBottom: '2px solid transparent',
        borderLeft: 0,
        outlineFocused: 0,
        borderFocused: 0,
        borderTopFocused: 0,
        borderLeftFocused: 0,
        borderBottomFocused: "2px solid ".concat(core.primary_dark),
        borderRightFocused: 0,
        borderRadius: 0
      },
      pickerInput: {
        borderWidth: 1,
        borderStyle: 'solid',
        borderRadius: 2
      }
    },
    color: {
      core: core,
      disabled: core.grayLightest,
      background: core.white,
      backgroundDark: '#f2f2f2',
      backgroundFocused: core.white,
      border: 'rgb(219, 219, 219)',
      text: core.gray,
      textDisabled: core.border,
      textFocused: '#007a87',
      placeholderText: '#757575',
      outside: {
        backgroundColor: core.white,
        backgroundColor_active: core.white,
        backgroundColor_hover: core.white,
        color: core.gray,
        color_active: core.gray,
        color_hover: core.gray
      },
      highlighted: {
        backgroundColor: core.yellow,
        backgroundColor_active: core.yellow_dark,
        backgroundColor_hover: core.yellow_dark,
        color: core.gray,
        color_active: core.gray,
        color_hover: core.gray
      },
      minimumNights: {
        backgroundColor: core.white,
        backgroundColor_active: core.white,
        backgroundColor_hover: core.white,
        borderColor: core.borderLighter,
        color: core.grayLighter,
        color_active: core.grayLighter,
        color_hover: core.grayLighter
      },
      hoveredSpan: {
        backgroundColor: core.primaryShade_4,
        backgroundColor_active: core.primaryShade_3,
        backgroundColor_hover: core.primaryShade_4,
        borderColor: core.primaryShade_3,
        borderColor_active: core.primaryShade_3,
        borderColor_hover: core.primaryShade_3,
        color: core.secondary,
        color_active: core.secondary,
        color_hover: core.secondary
      },
      selectedSpan: {
        backgroundColor: core.primaryShade_2,
        backgroundColor_active: core.primaryShade_1,
        backgroundColor_hover: core.primaryShade_1,
        borderColor: core.primaryShade_1,
        borderColor_active: core.primary,
        borderColor_hover: core.primary,
        color: core.white,
        color_active: core.white,
        color_hover: core.white
      },
      selected: {
        backgroundColor: core.primary,
        backgroundColor_active: core.primary,
        backgroundColor_hover: core.primary,
        borderColor: core.primary,
        borderColor_active: core.primary,
        borderColor_hover: core.primary,
        color: core.white,
        color_active: core.white,
        color_hover: core.white
      },
      blocked_calendar: {
        backgroundColor: core.grayLighter,
        backgroundColor_active: core.grayLighter,
        backgroundColor_hover: core.grayLighter,
        borderColor: core.grayLighter,
        borderColor_active: core.grayLighter,
        borderColor_hover: core.grayLighter,
        color: core.grayLight,
        color_active: core.grayLight,
        color_hover: core.grayLight
      },
      blocked_out_of_range: {
        backgroundColor: core.white,
        backgroundColor_active: core.white,
        backgroundColor_hover: core.white,
        borderColor: core.borderLight,
        borderColor_active: core.borderLight,
        borderColor_hover: core.borderLight,
        color: core.grayLighter,
        color_active: core.grayLighter,
        color_hover: core.grayLighter
      }
    },
    spacing: {
      dayPickerHorizontalPadding: 9,
      captionPaddingTop: 22,
      captionPaddingBottom: 37,
      inputPadding: 0,
      displayTextPaddingVertical: undefined,
      displayTextPaddingTop: 11,
      displayTextPaddingBottom: 9,
      displayTextPaddingHorizontal: undefined,
      displayTextPaddingLeft: 11,
      displayTextPaddingRight: 11,
      displayTextPaddingVertical_small: undefined,
      displayTextPaddingTop_small: 7,
      displayTextPaddingBottom_small: 5,
      displayTextPaddingHorizontal_small: undefined,
      displayTextPaddingLeft_small: 7,
      displayTextPaddingRight_small: 7
    },
    sizing: {
      inputWidth: 130,
      inputWidth_small: 97,
      arrowWidth: 24
    },
    noScrollBarOnVerticalScrollable: false,
    font: {
      size: 14,
      captionSize: 18,
      input: {
        size: 19,
        lineHeight: '24px',
        size_small: 15,
        lineHeight_small: '18px',
        letterSpacing_small: '0.2px',
        styleDisabled: 'italic'
      }
    }
  }
};
exports["default"] = _default;

/***/ }),

/***/ "xhJ2":
/***/ (function(module, exports) {

var $isNaN = Number.isNaN || function (a) { return a !== a; };

module.exports = Number.isFinite || function (x) { return typeof x === 'number' && !$isNaN(x) && x !== Infinity && x !== -Infinity; };


/***/ }),

/***/ "xoj2":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var implementation = __webpack_require__("rQy3");

module.exports = function getPolyfill() {
	return typeof Object.values === 'function' ? Object.values : implementation;
};


/***/ }),

/***/ "yLoW":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _reactMomentProptypes = _interopRequireDefault(__webpack_require__("XGBb"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _FocusedInputShape = _interopRequireDefault(__webpack_require__("5bN9"));

var _IconPositionShape = _interopRequireDefault(__webpack_require__("Bh6R"));

var _OrientationShape = _interopRequireDefault(__webpack_require__("qOSK"));

var _DisabledShape = _interopRequireDefault(__webpack_require__("5Fvu"));

var _AnchorDirectionShape = _interopRequireDefault(__webpack_require__("0+bg"));

var _OpenDirectionShape = _interopRequireDefault(__webpack_require__("iuLr"));

var _DayOfWeekShape = _interopRequireDefault(__webpack_require__("2S2E"));

var _CalendarInfoPositionShape = _interopRequireDefault(__webpack_require__("oR9Z"));

var _default = {
  // required props for a functional interactive DateRangePicker
  startDate: _reactMomentProptypes["default"].momentObj,
  endDate: _reactMomentProptypes["default"].momentObj,
  onDatesChange: _propTypes["default"].func.isRequired,
  focusedInput: _FocusedInputShape["default"],
  onFocusChange: _propTypes["default"].func.isRequired,
  onClose: _propTypes["default"].func,
  // input related props
  startDateId: _propTypes["default"].string.isRequired,
  startDatePlaceholderText: _propTypes["default"].string,
  startDateOffset: _propTypes["default"].func,
  endDateOffset: _propTypes["default"].func,
  endDateId: _propTypes["default"].string.isRequired,
  endDatePlaceholderText: _propTypes["default"].string,
  startDateAriaLabel: _propTypes["default"].string,
  endDateAriaLabel: _propTypes["default"].string,
  disabled: _DisabledShape["default"],
  required: _propTypes["default"].bool,
  readOnly: _propTypes["default"].bool,
  screenReaderInputMessage: _propTypes["default"].string,
  showClearDates: _propTypes["default"].bool,
  showDefaultInputIcon: _propTypes["default"].bool,
  inputIconPosition: _IconPositionShape["default"],
  customInputIcon: _propTypes["default"].node,
  customArrowIcon: _propTypes["default"].node,
  customCloseIcon: _propTypes["default"].node,
  noBorder: _propTypes["default"].bool,
  block: _propTypes["default"].bool,
  small: _propTypes["default"].bool,
  regular: _propTypes["default"].bool,
  keepFocusOnInput: _propTypes["default"].bool,
  // calendar presentation and interaction related props
  renderMonthText: (0, _airbnbPropTypes.mutuallyExclusiveProps)(_propTypes["default"].func, 'renderMonthText', 'renderMonthElement'),
  renderMonthElement: (0, _airbnbPropTypes.mutuallyExclusiveProps)(_propTypes["default"].func, 'renderMonthText', 'renderMonthElement'),
  orientation: _OrientationShape["default"],
  anchorDirection: _AnchorDirectionShape["default"],
  openDirection: _OpenDirectionShape["default"],
  horizontalMargin: _propTypes["default"].number,
  withPortal: _propTypes["default"].bool,
  withFullScreenPortal: _propTypes["default"].bool,
  appendToBody: _propTypes["default"].bool,
  disableScroll: _propTypes["default"].bool,
  daySize: _airbnbPropTypes.nonNegativeInteger,
  isRTL: _propTypes["default"].bool,
  firstDayOfWeek: _DayOfWeekShape["default"],
  initialVisibleMonth: _propTypes["default"].func,
  numberOfMonths: _propTypes["default"].number,
  keepOpenOnDateSelect: _propTypes["default"].bool,
  reopenPickerOnClearDates: _propTypes["default"].bool,
  renderCalendarInfo: _propTypes["default"].func,
  calendarInfoPosition: _CalendarInfoPositionShape["default"],
  hideKeyboardShortcutsPanel: _propTypes["default"].bool,
  verticalHeight: _airbnbPropTypes.nonNegativeInteger,
  transitionDuration: _airbnbPropTypes.nonNegativeInteger,
  verticalSpacing: _airbnbPropTypes.nonNegativeInteger,
  horizontalMonthPadding: _airbnbPropTypes.nonNegativeInteger,
  // navigation related props
  navPrev: _propTypes["default"].node,
  navNext: _propTypes["default"].node,
  onPrevMonthClick: _propTypes["default"].func,
  onNextMonthClick: _propTypes["default"].func,
  // day presentation and interaction related props
  renderCalendarDay: _propTypes["default"].func,
  renderDayContents: _propTypes["default"].func,
  minimumNights: _propTypes["default"].number,
  enableOutsideDays: _propTypes["default"].bool,
  isDayBlocked: _propTypes["default"].func,
  isOutsideRange: _propTypes["default"].func,
  isDayHighlighted: _propTypes["default"].func,
  // internationalization props
  displayFormat: _propTypes["default"].oneOfType([_propTypes["default"].string, _propTypes["default"].func]),
  monthFormat: _propTypes["default"].string,
  weekDayFormat: _propTypes["default"].string,
  phrases: _propTypes["default"].shape((0, _getPhrasePropTypes["default"])(_defaultPhrases.DateRangePickerPhrases)),
  dayAriaLabelFormat: _propTypes["default"].string
};
exports["default"] = _default;

/***/ }),

/***/ "yLpj":
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ "yN6O":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var ES = __webpack_require__("sEE1");

var MAX_SAFE_INTEGER = Number.MAX_SAFE_INTEGER || (Math.pow(2, 53) - 1);

// eslint-disable-next-line max-params, max-statements
var FlattenIntoArray = function FlattenIntoArray(target, source, sourceLen, start, depth) {
	var targetIndex = start;
	var sourceIndex = 0;

	/*
	var mapperFunction;
	if (arguments.length > 5) {
		mapperFunction = arguments[5];
	}
	*/

	while (sourceIndex < sourceLen) {
		var P = ES.ToString(sourceIndex);
		var exists = ES.HasProperty(source, P);
		if (exists) {
			var element = ES.Get(source, P);
			/*
			if (typeof mapperFunction !== 'undefined') {
				if (arguments.length <= 6) {
					throw new TypeError('Assertion failed: thisArg is required when mapperFunction is provided');
				}
				element = ES.Call(mapperFunction, arguments[6], [element, sourceIndex, source]);
			}
			*/
			var shouldFlatten = false;
			if (depth > 0) {
				shouldFlatten = ES.IsArray(element);
			}
			if (shouldFlatten) {
				var elementLen = ES.ToLength(ES.Get(element, 'length'));
				targetIndex = FlattenIntoArray(target, element, elementLen, targetIndex, depth - 1);
			} else {
				if (targetIndex >= MAX_SAFE_INTEGER) {
					throw new TypeError('index too large');
				}
				ES.CreateDataPropertyOrThrow(target, ES.ToString(targetIndex), element);
				targetIndex += 1;
			}
		}
		sourceIndex += 1;
	}

	return targetIndex;
};

module.exports = function flat() {
	var O = ES.ToObject(this);
	var sourceLen = ES.ToLength(ES.Get(O, 'length'));

	var depthNum = 1;
	if (arguments.length > 0 && typeof arguments[0] !== 'undefined') {
		depthNum = ES.ToInteger(arguments[0]);
	}

	var A = ES.ArraySpeciesCreate(O, 0);
	FlattenIntoArray(A, O, sourceLen, 0, depthNum);
	return A;
};


/***/ }),

/***/ "yR3C":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getDetachedContainerStyles;

var _constants = __webpack_require__("Fv1B");

/**
 * Calculate and return a CSS transform style to position a detached element
 * next to a reference element. The open and anchor direction indicate wether
 * it should be positioned above/below and/or to the left/right of the
 * reference element.
 *
 * Assuming r(0,0), r(1,1), d(0,0), d(1,1) for the bottom-left and top-right
 * corners of the reference and detached elements, respectively:
 *  - openDirection = DOWN, anchorDirection = LEFT => d(0,1) == r(0,1)
 *  - openDirection = UP, anchorDirection = LEFT => d(0,0) == r(0,0)
 *  - openDirection = DOWN, anchorDirection = RIGHT => d(1,1) == r(1,1)
 *  - openDirection = UP, anchorDirection = RIGHT => d(1,0) == r(1,0)
 *
 * By using a CSS transform, we allow to further position it using
 * top/bottom CSS properties for the anchor gutter.
 *
 * @param {string} openDirection The vertical positioning of the popup
 * @param {string} anchorDirection The horizontal position of the popup
 * @param {HTMLElement} referenceEl The reference element
 */
function getDetachedContainerStyles(openDirection, anchorDirection, referenceEl) {
  var referenceRect = referenceEl.getBoundingClientRect();
  var offsetX = referenceRect.left;
  var offsetY = referenceRect.top;

  if (openDirection === _constants.OPEN_UP) {
    offsetY = -(window.innerHeight - referenceRect.bottom);
  }

  if (anchorDirection === _constants.ANCHOR_RIGHT) {
    offsetX = -(window.innerWidth - referenceRect.right);
  }

  return {
    transform: "translate3d(".concat(Math.round(offsetX), "px, ").concat(Math.round(offsetY), "px, 0)")
  };
}

/***/ }),

/***/ "yc2e":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = getPhrasePropTypes;

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function getPhrasePropTypes(defaultPhrases) {
  return Object.keys(defaultPhrases).reduce(function (phrases, key) {
    return _objectSpread({}, phrases, (0, _defineProperty2["default"])({}, key, _propTypes["default"].oneOfType([_propTypes["default"].string, _propTypes["default"].func, _propTypes["default"].node])));
  }, {});
}

/***/ }),

/***/ "yjl6":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function forEach(array, callback) {
	for (var i = 0; i < array.length; i += 1) {
		callback(array[i], i, array);
	}
};


/***/ }),

/***/ "zN8g":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  block: false
};

function KeyboardShortcutRow(_ref) {
  var css = _ref.css,
      unicode = _ref.unicode,
      label = _ref.label,
      action = _ref.action,
      block = _ref.block,
      styles = _ref.styles;
  return _react["default"].createElement("li", css(styles.KeyboardShortcutRow, block && styles.KeyboardShortcutRow__block), _react["default"].createElement("div", css(styles.KeyboardShortcutRow_keyContainer, block && styles.KeyboardShortcutRow_keyContainer__block), _react["default"].createElement("span", (0, _extends2["default"])({}, css(styles.KeyboardShortcutRow_key), {
    role: "img",
    "aria-label": "".concat(label, ",") // add comma so screen readers will pause before reading action

  }), unicode)), _react["default"].createElement("div", css(styles.KeyboardShortcutRow_action), action));
}

KeyboardShortcutRow.propTypes =  false ? undefined : {};
KeyboardShortcutRow.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref2) {
  var color = _ref2.reactDates.color;
  return {
    KeyboardShortcutRow: {
      listStyle: 'none',
      margin: '6px 0'
    },
    KeyboardShortcutRow__block: {
      marginBottom: 16
    },
    KeyboardShortcutRow_keyContainer: {
      display: 'inline-block',
      whiteSpace: 'nowrap',
      textAlign: 'right',
      // is not handled by isRTL
      marginRight: 6 // is not handled by isRTL

    },
    KeyboardShortcutRow_keyContainer__block: {
      textAlign: 'left',
      // is not handled by isRTL
      display: 'inline'
    },
    KeyboardShortcutRow_key: {
      fontFamily: 'monospace',
      fontSize: 12,
      textTransform: 'uppercase',
      background: color.core.grayLightest,
      padding: '2px 6px'
    },
    KeyboardShortcutRow_action: {
      display: 'inline',
      wordBreak: 'break-word',
      marginLeft: 8 // is not handled by isRTL

    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(KeyboardShortcutRow);

exports["default"] = _default;

/***/ }),

/***/ "zec9":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.getScrollParent = getScrollParent;
exports.getScrollAncestorsOverflowY = getScrollAncestorsOverflowY;
exports["default"] = disableScroll;

var getScrollingRoot = function getScrollingRoot() {
  return document.scrollingElement || document.documentElement;
};
/**
 * Recursively finds the scroll parent of a node. The scroll parrent of a node
 * is the closest node that is scrollable. A node is scrollable if:
 *  - it is allowed to scroll via CSS ('overflow-y' not visible or hidden);
 *  - and its children/content are "bigger" than the node's box height.
 *
 * The root of the document always scrolls by default.
 *
 * @param {HTMLElement} node Any DOM element.
 * @return {HTMLElement} The scroll parent element.
 */


function getScrollParent(node) {
  var parent = node.parentElement;
  if (parent == null) return getScrollingRoot();

  var _window$getComputedSt = window.getComputedStyle(parent),
      overflowY = _window$getComputedSt.overflowY;

  var canScroll = overflowY !== 'visible' && overflowY !== 'hidden';

  if (canScroll && parent.scrollHeight > parent.clientHeight) {
    return parent;
  }

  return getScrollParent(parent);
}
/**
 * Recursively traverses the tree upwards from the given node, capturing all
 * ancestor nodes that scroll along with their current 'overflow-y' CSS
 * property.
 *
 * @param {HTMLElement} node Any DOM element.
 * @param {Map<HTMLElement,string>} [acc] Accumulator map.
 * @return {Map<HTMLElement,string>} Map of ancestors with their 'overflow-y' value.
 */


function getScrollAncestorsOverflowY(node) {
  var acc = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Map();
  var scrollingRoot = getScrollingRoot();
  var scrollParent = getScrollParent(node);
  acc.set(scrollParent, scrollParent.style.overflowY);
  if (scrollParent === scrollingRoot) return acc;
  return getScrollAncestorsOverflowY(scrollParent, acc);
}
/**
 * Disabling the scroll on a node involves finding all the scrollable ancestors
 * and set their 'overflow-y' CSS property to 'hidden'. When all ancestors have
 * 'overflow-y: hidden' (up to the document element) there is no scroll
 * container, thus all the scroll outside of the node is disabled. In order to
 * enable scroll again, we store the previous value of the 'overflow-y' for
 * every ancestor in a closure and reset it back.
 *
 * @param {HTMLElement} node Any DOM element.
 */


function disableScroll(node) {
  var scrollAncestorsOverflowY = getScrollAncestorsOverflowY(node);

  var toggle = function toggle(on) {
    return scrollAncestorsOverflowY.forEach(function (overflowY, ancestor) {
      ancestor.style.setProperty('overflow-y', on ? 'hidden' : overflowY);
    });
  };

  toggle(true);
  return function () {
    return toggle(false);
  };
}

/***/ }),

/***/ "zfJ5":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__("TqRt");

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _extends2 = _interopRequireDefault(__webpack_require__("pVnL"));

var _toConsumableArray2 = _interopRequireDefault(__webpack_require__("RIqP"));

var _defineProperty2 = _interopRequireDefault(__webpack_require__("lSNA"));

var _react = _interopRequireDefault(__webpack_require__("cDcd"));

var _propTypes = _interopRequireDefault(__webpack_require__("17x9"));

var _airbnbPropTypes = __webpack_require__("Hsqg");

var _reactWithStyles = __webpack_require__("TG4+");

var _defaultPhrases = __webpack_require__("vV+G");

var _getPhrasePropTypes = _interopRequireDefault(__webpack_require__("yc2e"));

var _noflip = _interopRequireDefault(__webpack_require__("2WTt"));

var _LeftArrow = _interopRequireDefault(__webpack_require__("0XP8"));

var _RightArrow = _interopRequireDefault(__webpack_require__("gZI3"));

var _ChevronUp = _interopRequireDefault(__webpack_require__("9gmn"));

var _ChevronDown = _interopRequireDefault(__webpack_require__("DHWS"));

var _ScrollableOrientationShape = _interopRequireDefault(__webpack_require__("aE6U"));

var _constants = __webpack_require__("Fv1B");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0, _defineProperty2["default"])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var propTypes =  false ? undefined : {};
var defaultProps = {
  disablePrev: false,
  disableNext: false,
  navPrev: null,
  navNext: null,
  orientation: _constants.HORIZONTAL_ORIENTATION,
  onPrevMonthClick: function onPrevMonthClick() {},
  onNextMonthClick: function onNextMonthClick() {},
  // internationalization
  phrases: _defaultPhrases.DayPickerNavigationPhrases,
  isRTL: false
};

function DayPickerNavigation(_ref) {
  var css = _ref.css,
      disablePrev = _ref.disablePrev,
      disableNext = _ref.disableNext,
      navPrev = _ref.navPrev,
      navNext = _ref.navNext,
      onPrevMonthClick = _ref.onPrevMonthClick,
      onNextMonthClick = _ref.onNextMonthClick,
      orientation = _ref.orientation,
      phrases = _ref.phrases,
      isRTL = _ref.isRTL,
      styles = _ref.styles;
  var isHorizontal = orientation === _constants.HORIZONTAL_ORIENTATION;
  var isVertical = orientation !== _constants.HORIZONTAL_ORIENTATION;
  var isVerticalScrollable = orientation === _constants.VERTICAL_SCROLLABLE;
  var navPrevIcon = navPrev;
  var navNextIcon = navNext;
  var isDefaultNavPrev = false;
  var isDefaultNavNext = false;
  var navPrevTabIndex = {};
  var navNextTabIndex = {};

  if (!navPrevIcon) {
    navPrevTabIndex = {
      tabIndex: '0'
    };
    isDefaultNavPrev = true;
    var Icon = isVertical ? _ChevronUp["default"] : _LeftArrow["default"];

    if (isRTL && !isVertical) {
      Icon = _RightArrow["default"];
    }

    navPrevIcon = _react["default"].createElement(Icon, css(isHorizontal && styles.DayPickerNavigation_svg__horizontal, isVertical && styles.DayPickerNavigation_svg__vertical, disablePrev && styles.DayPickerNavigation_svg__disabled));
  }

  if (!navNextIcon) {
    navNextTabIndex = {
      tabIndex: '0'
    };
    isDefaultNavNext = true;

    var _Icon = isVertical ? _ChevronDown["default"] : _RightArrow["default"];

    if (isRTL && !isVertical) {
      _Icon = _LeftArrow["default"];
    }

    navNextIcon = _react["default"].createElement(_Icon, css(isHorizontal && styles.DayPickerNavigation_svg__horizontal, isVertical && styles.DayPickerNavigation_svg__vertical, disableNext && styles.DayPickerNavigation_svg__disabled));
  }

  var isDefaultNav = isVerticalScrollable ? isDefaultNavNext : isDefaultNavNext || isDefaultNavPrev;
  return _react["default"].createElement("div", css.apply(void 0, [styles.DayPickerNavigation, isHorizontal && styles.DayPickerNavigation__horizontal].concat((0, _toConsumableArray2["default"])(isVertical ? [styles.DayPickerNavigation__vertical, isDefaultNav && styles.DayPickerNavigation__verticalDefault] : []), (0, _toConsumableArray2["default"])(isVerticalScrollable ? [styles.DayPickerNavigation__verticalScrollable, isDefaultNav && styles.DayPickerNavigation__verticalScrollableDefault] : []))), !isVerticalScrollable && _react["default"].createElement("div", (0, _extends2["default"])({
    // eslint-disable-line jsx-a11y/interactive-supports-focus
    role: "button"
  }, navPrevTabIndex, css.apply(void 0, [styles.DayPickerNavigation_button, isDefaultNavPrev && styles.DayPickerNavigation_button__default, disablePrev && styles.DayPickerNavigation_button__disabled].concat((0, _toConsumableArray2["default"])(isHorizontal ? [styles.DayPickerNavigation_button__horizontal].concat((0, _toConsumableArray2["default"])(isDefaultNavPrev ? [styles.DayPickerNavigation_button__horizontalDefault, !isRTL && styles.DayPickerNavigation_leftButton__horizontalDefault, isRTL && styles.DayPickerNavigation_rightButton__horizontalDefault] : [])) : []), (0, _toConsumableArray2["default"])(isVertical ? [styles.DayPickerNavigation_button__vertical].concat((0, _toConsumableArray2["default"])(isDefaultNavPrev ? [styles.DayPickerNavigation_button__verticalDefault, styles.DayPickerNavigation_prevButton__verticalDefault] : [])) : []))), {
    "aria-disabled": disablePrev ? true : undefined,
    "aria-label": phrases.jumpToPrevMonth,
    onClick: disablePrev ? undefined : onPrevMonthClick,
    onKeyUp: disablePrev ? undefined : function (e) {
      var key = e.key;
      if (key === 'Enter' || key === ' ') onPrevMonthClick(e);
    },
    onMouseUp: disablePrev ? undefined : function (e) {
      e.currentTarget.blur();
    }
  }), navPrevIcon), _react["default"].createElement("div", (0, _extends2["default"])({
    // eslint-disable-line jsx-a11y/interactive-supports-focus
    role: "button"
  }, navNextTabIndex, css.apply(void 0, [styles.DayPickerNavigation_button, isDefaultNavNext && styles.DayPickerNavigation_button__default, disableNext && styles.DayPickerNavigation_button__disabled].concat((0, _toConsumableArray2["default"])(isHorizontal ? [styles.DayPickerNavigation_button__horizontal].concat((0, _toConsumableArray2["default"])(isDefaultNavNext ? [styles.DayPickerNavigation_button__horizontalDefault, isRTL && styles.DayPickerNavigation_leftButton__horizontalDefault, !isRTL && styles.DayPickerNavigation_rightButton__horizontalDefault] : [])) : []), (0, _toConsumableArray2["default"])(isVertical ? [styles.DayPickerNavigation_button__vertical, styles.DayPickerNavigation_nextButton__vertical].concat((0, _toConsumableArray2["default"])(isDefaultNavNext ? [styles.DayPickerNavigation_button__verticalDefault, styles.DayPickerNavigation_nextButton__verticalDefault, isVerticalScrollable && styles.DayPickerNavigation_nextButton__verticalScrollableDefault] : [])) : []))), {
    "aria-disabled": disableNext ? true : undefined,
    "aria-label": phrases.jumpToNextMonth,
    onClick: disableNext ? undefined : onNextMonthClick,
    onKeyUp: disableNext ? undefined : function (e) {
      var key = e.key;
      if (key === 'Enter' || key === ' ') onNextMonthClick(e);
    },
    onMouseUp: disableNext ? undefined : function (e) {
      e.currentTarget.blur();
    }
  }), navNextIcon));
}

DayPickerNavigation.propTypes =  false ? undefined : {};
DayPickerNavigation.defaultProps = defaultProps;

var _default = (0, _reactWithStyles.withStyles)(function (_ref2) {
  var _ref2$reactDates = _ref2.reactDates,
      color = _ref2$reactDates.color,
      zIndex = _ref2$reactDates.zIndex;
  return {
    DayPickerNavigation: {
      position: 'relative',
      zIndex: zIndex + 2
    },
    DayPickerNavigation__horizontal: {
      height: 0
    },
    DayPickerNavigation__vertical: {},
    DayPickerNavigation__verticalScrollable: {},
    DayPickerNavigation__verticalDefault: {
      position: 'absolute',
      width: '100%',
      height: 52,
      bottom: 0,
      left: (0, _noflip["default"])(0)
    },
    DayPickerNavigation__verticalScrollableDefault: {
      position: 'relative'
    },
    DayPickerNavigation_button: {
      cursor: 'pointer',
      userSelect: 'none',
      border: 0,
      padding: 0,
      margin: 0
    },
    DayPickerNavigation_button__default: {
      border: "1px solid ".concat(color.core.borderLight),
      backgroundColor: color.background,
      color: color.placeholderText,
      ':focus': {
        border: "1px solid ".concat(color.core.borderMedium)
      },
      ':hover': {
        border: "1px solid ".concat(color.core.borderMedium)
      },
      ':active': {
        background: color.backgroundDark
      }
    },
    DayPickerNavigation_button__disabled: {
      cursor: 'default',
      border: "1px solid ".concat(color.disabled),
      ':focus': {
        border: "1px solid ".concat(color.disabled)
      },
      ':hover': {
        border: "1px solid ".concat(color.disabled)
      },
      ':active': {
        background: 'none'
      }
    },
    DayPickerNavigation_button__horizontal: {},
    DayPickerNavigation_button__horizontalDefault: {
      position: 'absolute',
      top: 18,
      lineHeight: 0.78,
      borderRadius: 3,
      padding: '6px 9px'
    },
    DayPickerNavigation_leftButton__horizontalDefault: {
      left: (0, _noflip["default"])(22)
    },
    DayPickerNavigation_rightButton__horizontalDefault: {
      right: (0, _noflip["default"])(22)
    },
    DayPickerNavigation_button__vertical: {},
    DayPickerNavigation_button__verticalDefault: {
      padding: 5,
      background: color.background,
      boxShadow: (0, _noflip["default"])('0 0 5px 2px rgba(0, 0, 0, 0.1)'),
      position: 'relative',
      display: 'inline-block',
      textAlign: 'center',
      height: '100%',
      width: '50%'
    },
    DayPickerNavigation_prevButton__verticalDefault: {},
    DayPickerNavigation_nextButton__verticalDefault: {
      borderLeft: (0, _noflip["default"])(0)
    },
    DayPickerNavigation_nextButton__verticalScrollableDefault: {
      width: '100%'
    },
    DayPickerNavigation_svg__horizontal: {
      height: 19,
      width: 19,
      fill: color.core.grayLight,
      display: 'block'
    },
    DayPickerNavigation_svg__vertical: {
      height: 42,
      width: 42,
      fill: color.text
    },
    DayPickerNavigation_svg__disabled: {
      fill: color.disabled
    }
  };
}, {
  pureComponent: typeof _react["default"].PureComponent !== 'undefined'
})(DayPickerNavigation);

exports["default"] = _default;

/***/ })

/******/ })));