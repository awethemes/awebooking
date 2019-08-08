import React from 'react';
import PropTypes from 'prop-types';
import MomentPropTypes from 'react-moment-proptypes';
import moment from 'moment';

export default class Day extends React.Component {
  static propTypes = {
    /** Optional aria-label value to set on the column header */
    ariaLabelFormat: PropTypes.string,

    /** Optional CSS class to apply to cell */
    className: PropTypes.string,

    /** Optional CSS class to apply to this column's header */
    headerClassName: PropTypes.string,

    day: MomentPropTypes.momentObj,
    onDayClick: PropTypes.func,
    onDayMouseEnter: PropTypes.func,
    onDayMouseLeave: PropTypes.func,

    headerRenderer: PropTypes.func.isRequired,
    renderDayContents: PropTypes.func,

    /** Maximum width of column; this property will only be used if :flexGrow is > 0. */
    maxWidth: PropTypes.number,

    /** Minimum width of column. */
    minWidth: PropTypes.number,

    /** Flex basis (width) for this column; This value can grow or shrink based on :flexGrow and :flexShrink properties. */
    width: PropTypes.number.isRequired,

    /** Flex grow style; defaults to 0 */
    flexGrow: PropTypes.number,

    /** Flex shrink style; defaults to 1 */
    flexShrink: PropTypes.number,
  };

  static defaultProps = {
    day: moment(),
    onDayClick() {},
    onDayMouseEnter() {},
    onDayMouseLeave() {},
    renderDayContents: null,
    ariaLabelFormat: 'dddd, LL',

    width: 60,
    flexGrow: 1,
    flexShrink: 0,
    minWidth: 60,
  };
}
