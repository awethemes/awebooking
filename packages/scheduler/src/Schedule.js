import React from 'react';
import PropTypes from 'prop-types';
import moment from 'moment';
import { momentString } from 'react-moment-proptypes';
import AutoSizer from 'react-virtualized/dist/commonjs/AutoSizer';
import { range } from './utils/dates';
import Calendar from './Calendar';
import Day from './Day';

export default class Schedule extends React.PureComponent {
  static propTypes = {
    startDate: momentString.isRequired,
    durations: PropTypes.number,
  };

  static defaultProps = {
    durations: 90,
  };

  constructor(props) {
    super(props);

    const {
      startDate,
      durations,
    } = this.props;

    this.state = {
      range: range(
        moment(startDate),
        moment(startDate).add(durations, 'days'),
      ),
    };
  }

  render() {
    const { range } = this.state;

    const columns = Array.from(range).map((m) => {
      return (
        <Day
          day={m}
          key={m.toString()}
          onDayClick={() => {}}
          onDayMouseEnter={() => {}}
          onDayMouseLeave={() => {}}
          headerRenderer={this._headerRenderer}
        />
      );
    });

    return (
      <div className="scheduler__container">
        <div className="scheduler__aside">
        </div>

        <div className="scheduler__mains" style={{ flex: '1 1 0%' }}>
          <AutoSizer disableHeight>
            {({ width }) => (
              <Calendar
                ref="SchedulerTable"
                width={width}
                height={500}
                rowCount={100}
                rowHeight={60}
                headerHeight={60}
                scrollToIndex={0}
              >
                {columns}
              </Calendar>
            )}
          </AutoSizer>
        </div>
      </div>
    );
  }

  _headerRenderer({ dataKey, sortBy, sortDirection }) {
    return (
      <div>A</div>
    );
  }
}
