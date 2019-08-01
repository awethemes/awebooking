import React from 'react'
import Moment from 'moment'
import { extendMoment } from 'moment-range'
import { render } from 'react-dom'
import PropTypes from 'prop-types'
import { momentString } from 'react-moment-proptypes'
import AutoSizer from 'react-virtualized-auto-sizer'
import {
  FixedSizeGrid as Grid,
  FixedSizeList as ListA
} from 'react-window'
import DatePicker from './src/DatePicker'
import { Button, Dropdown } from '@wordpress/components'

const moment = extendMoment(Moment)

class Scheduler extends React.Component {
  static propTypes = {
    startDate: momentString.isRequired,
    durations: PropTypes.number,
  }

  static defaultProps = {
    durations: 90
  }

  constructor(props) {
    super(props)

    const {
      startDate,
      durations
    } = this.props

    const rooms = window.awebookingRoomTypes[0].rooms
    console.log(rooms)

    this.state = {
      rooms,
      startDate: moment(startDate),
      endDate: moment(startDate).add(durations, 'days')
    }
  }

  render() {
    const {
      startDate,
      endDate,
    } = this.state

    const range = moment.range(startDate, endDate)
    const dates = Array.from(range.by('days', { excludeEnd: true }))

    const Row = ({ index, style }) => (
      <div style={style}>Row {index}</div>
    )

    const Cell = ({ columnIndex, rowIndex, style }) => {
      const date = dates[columnIndex]

      return (
        <div style={style}>
          {date.format('MMM Do YY')}
        </div>
      )
    }

    const Main = () => (
      <AutoSizer>
        {({ height, width }) => (
          <Grid
            width={width}
            height={height}
            columnCount={dates.length}
            columnWidth={60}
            rowCount={100}
            rowHeight={60}
          >
            {Cell}
          </Grid>
        )}
      </AutoSizer>
    )

    const Aside = () => (
      <ListA
        height={500}
        itemCount={250}
        itemSize={60}
        width={320}
      >
        {Row}
      </ListA>
    )

    const MyDropdown = () => (
      <Dropdown
        className="my-container-class-name"
        contentClassName="my-popover-content-classname"
        position="bottom right"
        renderToggle={({ isOpen, onToggle }) => (
          <Button isPrimary onClick={onToggle} aria-expanded={isOpen}>
            Toggle Popover!
          </Button>
        )}
        renderContent={() => (
          <div>

            <DatePicker/>
          </div>
        )}
      />
    )

    return (
      <div className={'scheduler'}>
        <div className="scheduler__header">
          <MyDropdown/>
        </div>

        <div className="scheduler__container">
          <div className="scheduler__aside">
            <Aside/>
          </div>

          <div className="scheduler__main">
            <Main/>
          </div>
        </div>
      </div>
    )
  }
}

(function () {
  render(
    <Scheduler startDate={'2019-07-23'}/>,
    document.getElementById('awebooking-calendar-root')
  )
})()
