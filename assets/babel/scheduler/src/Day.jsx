import React from 'react'
import PropTypes from 'prop-types'
import MomentPropTypes from 'react-moment-proptypes'

const propTypes = {
  day: MomentPropTypes.momentObj,
  onDayClick: PropTypes.func,
  onDayMouseEnter: PropTypes.func,
  onDayMouseLeave: PropTypes.func,
  renderDayContents: PropTypes.func,
  ariaLabelFormat: PropTypes.string
}

const defaultProps = {
  day: moment(),
  onDayClick() {},
  onDayMouseEnter() {},
  onDayMouseLeave() {},
  renderDayContents: null,
  ariaLabelFormat: 'dddd, LL',
}

class Day extends React.PureComponent {
  onDayClick(day, e) {
    const { onDayClick } = this.props
    onDayClick(day, e)
  }

  onDayMouseEnter(day, e) {
    const { onDayMouseEnter } = this.props
    onDayMouseEnter(day, e)
  }

  onDayMouseLeave(day, e) {
    const { onDayMouseLeave } = this.props
    onDayMouseLeave(day, e)
  }

  onKeyDown(day, e) {
    const { key } = e
    const { onDayClick } = this.props

    if (key === 'Enter' || key === ' ') {
      onDayClick(day, e)
    }
  }

  render() {
    const {
      day,
      ariaLabelFormat,
      modifiers,
      renderDayContents,
    } = this.props

    return (
      <li
        onMouseEnter={(e) => { this.onDayMouseEnter(day, e) }}
        onMouseLeave={(e) => { this.onDayMouseLeave(day, e) }}
        onKeyDown={(e) => { this.onKeyDown(day, e) }}
        onClick={(e) => { this.onDayClick(day, e) }}
      >
        {renderDayContents ? renderDayContents(day, modifiers) : day.format('D')}
      </li>
    )
  }
}

Day.propTypes = propTypes
Day.defaultProps = defaultProps

export default Day
