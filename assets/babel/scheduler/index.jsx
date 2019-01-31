'use strict'

import React from 'react'
import ReactDOM from 'react-dom'

import _ from 'lodash'
import moment from 'moment'

import { List, Grid, MultiGrid, AutoSizer } from 'react-virtualized'
import Timebar from './src/Timebar'

if (module.hot) {
  module.hot.accept()
}

const STYLE = {
  border: '1px solid #ddd',
}
const STYLE_BOTTOM_LEFT_GRID = {
  borderRight: '2px solid #aaa',
  backgroundColor: '#f7f7f7',
}
const STYLE_TOP_LEFT_GRID = {
  borderBottom: '2px solid #aaa',
  borderRight: '2px solid #aaa',
  fontWeight: 'bold',
}
const STYLE_TOP_RIGHT_GRID = {
  borderBottom: '2px solid #aaa',
  fontWeight: 'bold',
}

const rooms = window.awebookingRoomTypes[0].rooms

const DefaultProps = {}

const PropTypes = {}

class Scheduler extends React.PureComponent {
  constructor(props) {
    super(props)

    this.state = {}

    this._cellRenderer = this._cellRenderer.bind(this)
    this._renderHeaderCell = this._renderHeaderCell.bind(this)
  }

  _renderHeaderCell({ columnIndex, key, rowIndex, style }) {
    if (columnIndex < 1) {
      return
    }

    return this._renderLeftHeaderCell({ columnIndex, key, rowIndex, style })
  }

  _cellRenderer({ columnIndex, key, rowIndex, style }) {
    return (
      <div key={key} style={style}>
        {columnIndex}, {rowIndex}
      </div>
    )
  }

  rowRenderer({
                key,         // Unique key within array of rows
                index,       // Index of row within collection
                isScrolling, // The List is currently being scrolled
                isVisible,   // This row is visible within the List (eg it is not an overscanned row)
                style        // Style object to be applied to row (to position it)
              }) {
    return (
      <div
        key={key}
        style={style}
      >
        {rooms[index].name}
      </div>
    )
  }

  render() {
    return (
      <div>
        <AutoSizer>
          {({ height, width }) => (
            <div>

              <Timebar
                start={this.props.startDate}
                end={this.props.endDate}
                width={width}
                leftOffset={groupOffset}
                selectedRanges={this.state.selection}
              />

              <MultiGrid
                fixedColumnCount={2}
                fixedRowCount={1}
                cellRenderer={this._cellRenderer}
                columnWidth={75}
                columnCount={100}
                enableFixedColumnScroll
                enableFixedRowScroll
                style={STYLE}
                styleBottomLeftGrid={STYLE_BOTTOM_LEFT_GRID}
                styleTopLeftGrid={STYLE_TOP_LEFT_GRID}
                styleTopRightGrid={STYLE_TOP_RIGHT_GRID}
                height={300}
                rowHeight={40}
                rowCount={100}
                width={width}
                hideTopRightGridScrollbar
                hideBottomLeftGridScrollbar
              />
            </div>
          )}
        </AutoSizer>
      </div>
    )
  }
}

// Scheduler.propTypes = PropTypes
// Scheduler.defaultProps = DefaultProps

(function () {
  ReactDOM.render(<Scheduler/>, document.getElementById('awebooking-calendar-root'))
})()
