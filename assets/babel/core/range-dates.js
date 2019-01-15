export default function rangePlugin(config = {}) {
  return function (fp) {
    let dateFormat = '',
      secondInput,
      _firstInputFocused,
      _secondInputFocused

    const createSecondInput = () => {
      if (config.input) {
        secondInput = config.input instanceof Element ?
          config.input :
          window.document.querySelector(config.input)
      } else {
        secondInput = fp._input.cloneNode()
        secondInput.removeAttribute('id')
        secondInput._flatpickr = undefined
      }

      if (secondInput.value) {
        const parsedDate = fp.parseDate(secondInput.value)

        if (parsedDate) {
          fp.selectedDates.push(parsedDate)
        }
      }

      secondInput.setAttribute('data-fp-omit', '')

      fp._bind(secondInput, ['focus', 'click'], () => {
        if (fp.selectedDates[1]) {
          fp.latestSelectedDateObj = fp.selectedDates[1]
          fp._setHoursFromDate(fp.selectedDates[1])
          fp.jumpToDate(fp.selectedDates[1])
        }

        [_firstInputFocused, _secondInputFocused] = [false, true]

        fp.isOpen = false
        fp.open(undefined, secondInput)
      })

      fp._bind(fp._input, ['focus', 'click'], (e) => {
        e.preventDefault()
        fp.isOpen = false
        fp.open()
      })

      if (fp.config.allowInput) {
        fp._bind(secondInput, 'keydown', (e) => {
          if (e.key === 'Enter') {
            fp.setDate([fp.selectedDates[0], secondInput.value], true, dateFormat)
            secondInput.click()
          }
        })
      }

      if (!config.input) {
        fp._input.parentNode && fp._input.parentNode.insertBefore(secondInput, fp._input.nextSibling)
      }
    }

    const plugin = {
      onParseConfig() {
        fp.config.mode = 'range'
        dateFormat = fp.config.altInput ? fp.config.altFormat : fp.config.dateFormat
      },

      onReady() {
        createSecondInput()
        fp.config.ignoredFocusElements.push(secondInput)

        if (fp.config.allowInput) {
          fp._input.removeAttribute('readonly')
          secondInput.removeAttribute('readonly')
        } else {
          secondInput.setAttribute('readonly', 'readonly')
        }

        fp._bind(fp._input, 'focus', () => {
          fp.latestSelectedDateObj = fp.selectedDates[0]
          fp._setHoursFromDate(fp.selectedDates[0]);

          [_firstInputFocused, _secondInputFocused] = [true, false]
          // fp.jumpToDate(fp.selectedDates[0]);
        })

        if (fp.config.allowInput) {
          fp._bind(fp._input, 'keydown', (e) => {
            if (e.key === 'Enter') {
              fp.setDate([fp._input.value, fp.selectedDates[1]], true, dateFormat)
            }
          })
        }

        fp.setDate(fp.selectedDates, false)
        plugin.onValueUpdate(fp.selectedDates)
      },

      onPreCalendarPosition() {
        if (_secondInputFocused) {
          fp._positionElement = secondInput
          setTimeout(() => { fp._positionElement = fp._input }, 0)
        }
      },

      onValueUpdate() {
        if (!secondInput) {
          return
        }

        [fp._input.value = '', secondInput.value = ''] = fp.selectedDates.map(
          d => fp.formatDate(d, dateFormat)
        )
      },

      onChange() {
        if (!fp.selectedDates.length) {
          setTimeout(() => {
            if (fp.selectedDates.length) {
              return
            }

            secondInput.value = ''
          }, 10)
        }

        if (_secondInputFocused) {
          setTimeout(() => { secondInput.focus() }, 0)
        }
      },

      onDestroy() {
        if (!config.input) {
          secondInput.parentNode && secondInput.parentNode.removeChild(secondInput)
        }
      },
    }

    return plugin
  }
}
