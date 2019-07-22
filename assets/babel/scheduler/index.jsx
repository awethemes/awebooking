import React from 'react'
import moment from 'moment'
import ReactDOM from 'react-dom'
import { FixedSizeList as ListA } from 'react-window'

if (module.hot) {
  module.hot.accept()
}

const Row = ({ index, style }) => (
  <div style={style}>Row {index}</div>
)

const Example = () => (
  <ListA
    height={150}
    itemCount={1000}
    itemSize={35}
    width={300}
  >
    {Row}
  </ListA>
)

const rooms = window.awebookingRoomTypes[0].rooms
console.log(rooms)

class Scheduler extends React.PureComponent {
  constructor(props) {
    super(props)

    this.state = {}

    this.state.startDate = moment()
    this.state.endDate = moment().add(900, 'days')
  }

  render() {
    return (
      <div>
        <Example/>
      </div>
    )
  }
}

(function () {
  ReactDOM.render(<Scheduler/>, document.getElementById('awebooking-calendar-root'))
})()
