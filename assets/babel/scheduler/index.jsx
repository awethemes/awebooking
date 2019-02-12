'use strict'

import React from 'react'
import ReactDOM from 'react-dom'

import _ from 'lodash'
import moment from 'moment'

import { List, Grid, ColumnSizer, AutoSizer } from 'react-virtualized'
import Timebar from './src/Timebar'

if (module.hot) {
  module.hot.accept()
}

const rooms = window.awebookingRoomTypes[0].rooms

class Scheduler extends React.PureComponent {
  constructor(props) {
    super(props)

    this.state = {}

    this.state.startDate = moment()
    this.state.endDate = moment().add(900, 'days')

    this._cellRenderer = this._cellRenderer.bind(this)
  }

  _cellRenderer({ columnIndex, key, rowIndex, style }) {
    return (
      <div key={key} style={style}>
        {columnIndex}, {rowIndex}
      </div>
    )
  }

  render() {
    return (
      <div>
        <AutoSizer disableHeight>
          {({ width }) => (
            <ColumnSizer
              columnMaxWidth={100}
              columnMinWidth={60}
              columnCount={100}
              width={width}>
              {({ adjustedWidth, columnWidth, registerChild }) => (
                <div
                  style={{
                    height: 500,
                    width: adjustedWidth,
                  }}>
                  <Grid
                    ref={registerChild}
                    columnWidth={columnWidth}
                    columnCount={30000}
                    height={500}
                    cellRenderer={this._cellRenderer}
                    rowHeight={50}
                    rowCount={rooms.length}
                    width={adjustedWidth}
                  />
                </div>
              )}
            </ColumnSizer>
          )}
        </AutoSizer>
      </div>
    )
  }
}

(function () {
  ReactDOM.render(<Scheduler/>, document.getElementById('awebooking-calendar-root'))
})()
