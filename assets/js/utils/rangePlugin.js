(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

(function () {
  'use strict';

  function rangePlugin(config) {
    if (config === void 0) {
      config = {};
    }

    return function (fp) {
      var dateFormat = "",
          secondInput,
          _secondInputFocused,
          _prevDates;

      var createSecondInput = function createSecondInput() {
        if (config.input) {
          secondInput = config.input instanceof Element ? config.input : window.document.querySelector(config.input);
        } else {
          secondInput = fp._input.cloneNode();
          secondInput.removeAttribute("id");
          secondInput._flatpickr = undefined;
        }

        if (secondInput.value) {
          var parsedDate = fp.parseDate(secondInput.value);
          if (parsedDate) fp.selectedDates.push(parsedDate);
        }

        secondInput.setAttribute("data-fp-omit", "");

        fp._bind(secondInput, ["focus", "click"], function () {
          if (fp.selectedDates[1]) {
            fp.latestSelectedDateObj = fp.selectedDates[1];

            fp._setHoursFromDate(fp.selectedDates[1]);

            fp.jumpToDate(fp.selectedDates[1]);
          }
          _secondInputFocused = true;
          fp.isOpen = false;
          fp.open(undefined, secondInput);
        });

        fp._bind(fp._input, ["focus", "click"], function (e) {
          e.preventDefault();
          fp.isOpen = false;
          fp.open();
        });

        if (fp.config.allowInput) fp._bind(secondInput, "keydown", function (e) {
          if (e.key === "Enter") {
            fp.setDate([fp.selectedDates[0], secondInput.value], true, dateFormat);
            secondInput.click();
          }
        });
        if (!config.input) fp._input.parentNode && fp._input.parentNode.insertBefore(secondInput, fp._input.nextSibling);
      };

      var plugin = {
        onParseConfig: function onParseConfig() {
          fp.config.mode = "range";
          dateFormat = fp.config.altInput ? fp.config.altFormat : fp.config.dateFormat;
        },
        onReady: function onReady() {
          createSecondInput();
          fp.config.ignoredFocusElements.push(secondInput);

          if (fp.config.allowInput) {
            fp._input.removeAttribute("readonly");

            secondInput.removeAttribute("readonly");
          } else {
            secondInput.setAttribute("readonly", "readonly");
          }

          fp._bind(fp._input, "focus", function () {
            fp.latestSelectedDateObj = fp.selectedDates[0];

            fp._setHoursFromDate(fp.selectedDates[0]);
            _secondInputFocused = false;
            fp.jumpToDate(fp.selectedDates[0]);
          });

          if (fp.config.allowInput) fp._bind(fp._input, "keydown", function (e) {
            if (e.key === "Enter") fp.setDate([fp._input.value, fp.selectedDates[1]], true, dateFormat);
          });
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
        onChange: function onChange() {
          if (!fp.selectedDates.length) {
            setTimeout(function () {
              if (fp.selectedDates.length) return;
              secondInput.value = "";
              _prevDates = [];
            }, 10);
          }

          if (_secondInputFocused) {
            setTimeout(function () {
              secondInput.focus();
            }, 0);
          }
        },
        onDestroy: function onDestroy() {
          if (!config.input) secondInput.parentNode && secondInput.parentNode.removeChild(secondInput);
        },
        onValueUpdate: function onValueUpdate(selDates) {
          if (!secondInput) return;
          _prevDates = !_prevDates || selDates.length >= _prevDates.length ? selDates.concat() : _prevDates;

          if (_prevDates.length > selDates.length) {
            var newSelectedDate = selDates[0];
            var newDates = _secondInputFocused ? [_prevDates[0], newSelectedDate] : [newSelectedDate, _prevDates[1]];
            fp.setDate(newDates, false);
            _prevDates = newDates.concat();
          }

          var _selectedDates = fp.selectedDates.map(function (d) {
            return fp.formatDate(d, dateFormat);
          });

          var _selectedDates2 = _selectedDates[0];
          fp._input.value = _selectedDates2 === void 0 ? "" : _selectedDates2;

          var _selectedDates3 = _selectedDates[1];
          secondInput.value = _selectedDates3 === void 0 ? "" : _selectedDates3;
        }
      };

      return plugin;
    };
  }

  window.rangePlugin = rangePlugin;
})();

},{}]},{},[1]);

//# sourceMappingURL=rangePlugin.js.map
