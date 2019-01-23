import React from 'react'
import ReactDOM from 'react-dom'
import OutsideClickHandler from 'react-outside-click-handler'
import moment from 'moment'
import { Popper } from 'react-popper'

import 'react-dates/initialize'
import isBeforeDay from 'react-dates/lib/utils/isBeforeDay'
import toMomentObject from 'react-dates/lib/utils/toMomentObject'
import toLocalizedDateString from 'react-dates/lib/utils/toLocalizedDateString'
import { DayPickerRangeController, isInclusivelyAfterDay } from 'react-dates'
import { START_DATE, END_DATE, ISO_FORMAT } from 'react-dates/constants'

const defaultProps = {
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

  withFullScreenPortal: false,
  displayFormat: 'DD MM YY',

  // calendar presentation and interaction related props
	numberOfMonths: 2,

  disabled: false,
  keepOpenOnDateSelect: false,
  isDayBlocked: () => false,
  isDayHighlighted: () => false,
  isOutsideRange: day => !isInclusivelyAfterDay(day, moment()),

	// Internationalization
	monthFormat: 'MMMM YYYY',
	weekDayFormat: 'dd',

	onFocus() {},
	onChange() {},
	onClose() {},
}

export class DatePicker extends React.Component {
  constructor(props) {
    super(props)

    if (typeof window.Popper === 'undefined') {
      throw new TypeError('The Calendar require Popper.js (https://popper.js.org/)')
    }

    this.form = props.form
    const { elements } = this.form

    this.state = {
      focusedInput: null,
      startDate: toMomentObject(elements['check_in'].get()),
      endDate: toMomentObject(elements['check_out'].get()),
      isDayPickerFocused: false,
    }

    this.onDatesChange = this.onDatesChange.bind(this)
    this.onFocusChange = this.onFocusChange.bind(this)
    this.onOutsideClick = this.onOutsideClick.bind(this)
    this.onDayPickerBlur = this.onDayPickerBlur.bind(this)
    this.isOutsideRange = this.isOutsideRange.bind(this)
  }

  componentDidMount() {
    this.getFormElement('check_in_alt').on('change', this.onStartDateChange.bind(this))
    this.getFormElement('check_out_alt').on('change', this.onEndDateChange.bind(this))

    this.getFormElement('check_in_alt').on('focus', this.onStartDateFocus.bind(this))
    this.getFormElement('check_out_alt').on('focus', this.onEndDateFocus.bind(this))
  }

  componentWillUnmount() {
    this.getFormElement('check_in').off('change')
    this.getFormElement('check_out').off('change')

    this.getFormElement('check_in_alt').off('focus')
    this.getFormElement('check_out_alt').off('focus')
  }

  open(focusedInput) {
    this.onFocusChange(focusedInput ? focusedInput : START_DATE)
  }

  close() {
    this.onFocusChange(null)
  }

  onFocusChange(focusedInput) {
    this.setState({ focusedInput }, this._onInputFocus.bind(this, focusedInput))
  }

  _onInputFocus(focusedInput) {
    const { onFocus } = this.props

    if (focusedInput === START_DATE && !this.getFormElement('check_in_alt').is(':focus')) {
      this.getFormElement('check_in_alt')[0].focus()
    } else if (focusedInput === END_DATE && !this.getFormElement('check_out_alt').is(':focus')) {
      this.getFormElement('check_out_alt')[0].focus()
    }

    onFocus(focusedInput)
  }

	onDatesChange({ startDate, endDate }) {
		const { onChange } = this.props

    this.setState({ startDate, endDate }, () => {
      const elements = this.form.elements

      elements['check_in'].set(startDate ? startDate.format(ISO_FORMAT) : '')
      elements['check_out'].set(endDate ? endDate.format(ISO_FORMAT) : '')

      onChange({ startDate, endDate })
    })
	}

	clearDates() {
		const { reopenPickerOnClearDates } = this.props

		this.onDatesChange({ startDate: null, endDate: null })

		if (reopenPickerOnClearDates) {
			this.onFocusChange(START_DATE)
		}
	}

	isOpened() {
		const { focusedInput } = this.state

		return focusedInput === START_DATE || focusedInput === END_DATE
	}

  onStartDateChange(e) {
    let startDateString = e.target.value

    let { endDate } = this.state
    const { disabled, minimumNights, isOutsideRange } = this.props

    const startDate = toMomentObject(startDateString, this.getDisplayFormat())
    console.log(startDate)

    const isEndDateBeforeStartDate = startDate
      && isBeforeDay(endDate, startDate.clone().add(minimumNights, 'days'))

    const isStartDateValid = startDate
      && !isOutsideRange(startDate)
      && !(disabled === END_DATE && isEndDateBeforeStartDate)

    if (isStartDateValid) {
      if (isEndDateBeforeStartDate) {
        endDate = null
      }

      this.onDatesChange({ startDate, endDate })
      this.onFocusChange(END_DATE)
    } else {
      this.onDatesChange({
        startDate: null,
        endDate,
      })
    }
  }

  onEndDateChange(e) {
    const endDateString = e.target.value

    const { startDate } = this.state
    const { minimumNights, isOutsideRange, keepOpenOnDateSelect } = this.props

    const endDate = toMomentObject(endDateString, this.getDisplayFormat())

    const isEndDateValid = endDate
      && !isOutsideRange(endDate)
      && !(startDate && isBeforeDay(endDate, startDate.clone().add(minimumNights, 'days')))

    if (isEndDateValid) {
      this.onDatesChange({ startDate, endDate })
      if (!keepOpenOnDateSelect) this.onClearFocus()
    } else {
      this.onDatesChange({
        startDate,
        endDate: null,
      })
    }
  }

  onStartDateFocus() {
    const { disabled, focusedInput } = this.props

    if (this.isOpened() && focusedInput === START_DATE) {
      return
    }

    if (!disabled || disabled === END_DATE) {
      this.onFocusChange(START_DATE)
    }
  }

	onEndDateFocus() {
		const { startDate, focusedInput } = this.state
		const { disabled, withFullScreenPortal } = this.props

    if (this.isOpened() && focusedInput === END_DATE) {
      return
    }

		// When the datepicker is full screen, we never want to focus the end date first
		// because there's no indication that that is the case once the datepicker is open and it
		// might confuse the user
		if (!startDate && withFullScreenPortal && (!disabled || disabled === END_DATE)) {
			this.onFocusChange(START_DATE)
		} else if (!disabled || disabled === START_DATE) {
			this.onFocusChange(END_DATE)
		}
	}

  onClearFocus() {
    this.close()
  }

  getDateString(date) {
    const displayFormat = this.getDisplayFormat()

    if (date && displayFormat) {
      return date && date.format(displayFormat)
    }

    return toLocalizedDateString(date)
  }

  getDisplayFormat() {
    const { displayFormat } = this.props

    return typeof displayFormat === 'string' ? displayFormat : displayFormat()
  }

  /**
   * Gets a DOM element in the form.
   *
   * @param  {string} name
   * @return {jQuery}
   */
  getFormElement(name) {
    const elements = this.form.elements

    if (elements.hasOwnProperty(name)) {
      return elements[name].element
    }
  }

  onOutsideClick(e) {
    if (!this.isOpened()) {
      return
    }

    if (this._isClickOnInputs(e)) {
      return
    }

    this.setState({
      isDayPickerFocused: false,
    })

    this.close()
  }

  _isClickOnInputs(e) {
    const inputs = [
      this.getFormElement('check_in'),
      this.getFormElement('check_out'),
      this.getFormElement('check_in_alt'),
      this.getFormElement('check_out_alt')
    ]

    let clicked = false

    inputs.forEach((element) => {
      if (element[0] && element[0].contains(e.target)) {
        clicked = true
      }
    })

    return clicked
  }

  onDayPickerFocus() {
    /*const { focusedInput, onFocusChange } = this.props
    if (!focusedInput) onFocusChange(START_DATE)

    this.setState({
      isDayPickerFocused: true,
    })*/
  }

  onDayPickerBlur() {
    this.setState({
      isDayPickerFocused: false,
    })
  }

  getReferenceElement(focusedInput) {
    const { numberOfMonths } = this.props

    if (!focusedInput) {
      return
    }

    let referenceElement = focusedInput === START_DATE
      ? this.getFormElement('check_in_alt')
      : this.getFormElement('check_out_alt')

    if (numberOfMonths > 1 && referenceElement.closest('.abrs-searchbox__group-wrap').length > 0) {
      return referenceElement.closest('.abrs-searchbox__group')[0]
    }

    return referenceElement.closest('.abrs-searchbox__box')[0]
  }

  isOutsideRange(day) {
    const { startDate, focusedInput } = this.state

    const {
      disablePastDates,
      disableFutureDates,
      maximumNights,
      isOutsideRange,
      minimumDateRange,
      maximumDateRange
    } = this.props

    const today = moment()
    if (startDate && focusedInput === 'endDate' && day.isBefore(startDate, 'day')) {
      return true
    }

    if (disablePastDates && today.isAfter(day, 'day')) {
      return true
    }

    if (disableFutureDates && today.isBefore(day, 'day')) {
      return true
    }

    if (maximumNights && startDate) {
      const maxDate = startDate.clone().add(maximumNights, 'day')

      if (focusedInput === 'endDate' && maxDate.isBefore(day)) {
        return true
      }
    }

    if (minimumDateRange > 0 || maximumDateRange > 0) {
      return !isInclusivelyAfterDay(day, today.clone().add(minimumDateRange, 'days')) ||
        maximumDateRange && isInclusivelyAfterDay(day, today.clone().add(maximumDateRange, 'days'))
    }

    if (isOutsideRange) {
      return isOutsideRange(day)
    }

    return false
  }

	render() {
    const {
      focusedInput,
      startDate,
      endDate,
      isDayPickerFocused,
    } = this.state

    const {
      disabled,
      numberOfMonths,
      minimumNights,
      keepOpenOnDateSelect,
      isDayBlocked,
      isDayHighlighted,
    } = this.props

    const modifiers = {
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
    }

		return (
			focusedInput && (
				<OutsideClickHandler onOutsideClick={this.onOutsideClick}>
					<Popper
						placement="bottom-end"
						referenceElement={this.getReferenceElement(focusedInput)}
            modifiers={modifiers}
					>
            {({ ref, style, placement, arrowProps }) => (
              <div className="DayPickerPopper" ref={ref} style={style} data-placement={placement}>
                <div className="DayPickerPopper__Arrow" ref={arrowProps.ref} style={arrowProps.style}/>

								<DayPickerRangeController
									endDate={endDate}
									startDate={startDate}
									focusedInput={focusedInput}
									onDatesChange={this.onDatesChange}
									onFocusChange={this.onFocusChange}
                  disabled={disabled}
                  numberOfMonths={numberOfMonths}
                  minimumNights={minimumNights}
                  keepOpenOnDateSelect={keepOpenOnDateSelect}
                  isDayBlocked={isDayBlocked}
                  isDayHighlighted={isDayHighlighted}
                  isOutsideRange={this.isOutsideRange}
                  isFocused={isDayPickerFocused}
                  onBlur={this.onDayPickerBlur}
                  hideKeyboardShortcutsPanel={true}
                  // onClose={onClose}
                />
							</div>
						)}
					</Popper>
				</OutsideClickHandler>
			)
		)
	}
}

DatePicker.defaultProps = defaultProps

export function createDatePicker(searchform, props) {
  const root = searchform
    .getRootElement()
    .querySelector('.abrs-searchbox__dates')

	return ReactDOM.render(<DatePicker form={searchform} {...props}/>, root)
}

(function () {
  window.createReactDatePicker = createDatePicker
})()
