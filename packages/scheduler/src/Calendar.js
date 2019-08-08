import React from 'react';
import {findDOMNode} from 'react-dom';
import PropTypes from 'prop-types';
import clsx from 'clsx';
import {Grid} from 'react-virtualized';
import Day from './Day';
import DayHeaderWrapper from './DayHeaderWrapper';
import DayWrapper from './DayWrapper';

import './style.scss';

export default class Calendar extends React.PureComponent {
  static propTypes = {
    /** This is just set on the grid top element. */
    'aria-label': PropTypes.string,

    /** This is just set on the grid top element. */
    'aria-labelledby': PropTypes.string,

    /**
     * Removes fixed height from the scrollingContainer so that the total height
     * of rows can stretch the window. Intended for use with WindowScroller
     */
    autoHeight: PropTypes.bool,

    /** One or more Columns describing the data displayed in this row */
    children: props => {
      const children = React.Children.toArray(props.children);
      for (let i = 0; i < children.length; i++) {
        const childType = children[i].type;
        if (childType !== Day && !(childType.prototype instanceof Day)) {
          return new Error('Table only accepts children of type Day');
        }
      }
    },

    /** Optional CSS class name */
    className: PropTypes.string,

    /** Disable rendering the header at all */
    disableHeader: PropTypes.bool,

    /**
     * Used to estimate the total height of a Table before all of its rows have actually been measured.
     * The estimated total height is adjusted as rows are rendered.
     */
    estimatedRowSize: PropTypes.number.isRequired,

    /** Optional custom CSS class name to attach to inner Grid element. */
    gridClassName: PropTypes.string,

    /** Optional inline style to attach to inner Grid element. */
    gridStyle: PropTypes.object,

    /** Optional CSS class to apply to all column headers */
    headerClassName: PropTypes.string,

    /** Fixed height of header row */
    headerHeight: PropTypes.number.isRequired,

    /** Optional custom inline style to attach to table header columns. */
    headerStyle: PropTypes.object,

    /** Fixed/available height for out DOM element */
    height: PropTypes.number.isRequired,

    /** Optional id */
    id: PropTypes.string,

    /** Optional renderer to be used in place of table body rows when rowCount is 0 */
    noRowsRenderer: PropTypes.func,

    /**
     * Optional callback when a column is clicked.
     * ({ columnData: any, dataKey: string }): void
     */
    onColumnClick: PropTypes.func,

    /**
     * Optional callback when a column's header is clicked.
     * ({ columnData: any, dataKey: string }): void
     */
    onHeaderClick: PropTypes.func,

    /**
     * Callback invoked when a user clicks on a table row.
     * ({ index: number }): void
     */
    onRowClick: PropTypes.func,

    /**
     * Callback invoked when a user double-clicks on a table row.
     * ({ index: number }): void
     */
    onRowDoubleClick: PropTypes.func,

    /**
     * Callback invoked when the mouse leaves a table row.
     * ({ index: number }): void
     */
    onRowMouseOut: PropTypes.func,

    /**
     * Callback invoked when a user moves the mouse over a table row.
     * ({ index: number }): void
     */
    onRowMouseOver: PropTypes.func,

    /**
     * Callback invoked when a user right-clicks on a table row.
     * ({ index: number }): void
     */
    onRowRightClick: PropTypes.func,

    /**
     * Callback invoked with information about the slice of rows that were just rendered.
     * ({ startIndex, stopIndex }): void
     */
    onRowsRendered: PropTypes.func,

    /**
     * Callback invoked whenever the scroll offset changes within the inner scrollable region.
     * This callback can be used to sync scrolling between lists, tables, or grids.
     * ({ clientHeight, scrollHeight, scrollTop }): void
     */
    onScroll: PropTypes.func.isRequired,

    /** See Grid#overscanIndicesGetter */
    // overscanIndicesGetter: PropTypes.func.isRequired,

    /**
     * Number of rows to render above/below the visible bounds of the list.
     * These rows can help for smoother scrolling on touch devices.
     */
    overscanRowCount: PropTypes.number.isRequired,

    /**
     * Optional CSS class to apply to all table rows (including the header row).
     * This property can be a CSS class name (string) or a function that returns a class name.
     * If a function is provided its signature should be: ({ index: number }): string
     */
    rowClassName: PropTypes.oneOfType([PropTypes.string, PropTypes.func]),

    /**
     * Callback responsible for returning a data row given an index.
     * ({ index: number }): any
     */
    // rowGetter: PropTypes.func.isRequired,

    /**
     * Either a fixed row height (number) or a function that returns the height of a row given its index.
     * ({ index: number }): number
     */
    rowHeight: PropTypes.oneOfType(
      [PropTypes.number, PropTypes.func]).isRequired,

    /** Number of rows in table. */
    rowCount: PropTypes.number.isRequired,

    /** Optional custom inline style to attach to table rows. */
    rowStyle: PropTypes.oneOfType(
      [PropTypes.object, PropTypes.func]).isRequired,

    /** See Grid#scrollToAlignment */
    scrollToAlignment: PropTypes.oneOf(
      ['auto', 'end', 'start', 'center']).isRequired,

    /** Row index to ensure visible (by forcefully scrolling if necessary) */
    scrollToIndex: PropTypes.number.isRequired,

    /** Vertical offset. */
    scrollTop: PropTypes.number,

    /** Optional inline style */
    style: PropTypes.object,

    /** Tab index for focus */
    tabIndex: PropTypes.number,

    /** Width of list */
    width: PropTypes.number.isRequired,
  };

  static defaultProps = {
    disableHeader: false,
    estimatedRowSize: 30,
    headerHeight: 0,
    headerStyle: {},
    noRowsRenderer: () => null,
    onRowsRendered: () => null,
    onScroll: () => null,
    // overscanIndicesGetter: accessibilityOverscanIndicesGetter,
    overscanRowCount: 10,
    rowStyle: {},
    scrollToAlignment: 'auto',
    scrollToIndex: -1,
    style: {},
  };

  constructor(props) {
    super(props);

    this.state = {
      scrollbarWidth: 0,
    };

    this._createColumn = this._createColumn.bind(this);
    this._createRow = this._createRow.bind(this);
    this._onScroll = this._onScroll.bind(this);
    this._onSectionRendered = this._onSectionRendered.bind(this);
    this._setRef = this._setRef.bind(this);
  }

  forceUpdateGrid() {
    if (this.Grid) {
      this.Grid.forceUpdate();
    }
  }

  /** See Grid#getOffsetForCell */
  getOffsetForRow({alignment, index}) {
    if (this.Grid) {
      const {scrollTop} = this.Grid.getOffsetForCell({
        alignment,
        rowIndex: index,
      });

      return scrollTop;
    }
    return 0;
  }

  /** CellMeasurer compatibility */
  invalidateCellSizeAfterRender({columnIndex, rowIndex}) {
    if (this.Grid) {
      this.Grid.invalidateCellSizeAfterRender({
        rowIndex,
        columnIndex,
      });
    }
  }

  /** See Grid#measureAllCells */
  measureAllRows() {
    if (this.Grid) {
      this.Grid.measureAllCells();
    }
  }

  /** CellMeasurer compatibility */
  recomputeGridSize({columnIndex = 0, rowIndex = 0} = {}) {
    if (this.Grid) {
      this.Grid.recomputeGridSize({
        rowIndex,
        columnIndex,
      });
    }
  }

  /** See Grid#recomputeGridSize */
  recomputeRowHeights(index = 0) {
    if (this.Grid) {
      this.Grid.recomputeGridSize({
        rowIndex: index,
      });
    }
  }

  /** See Grid#scrollToPosition */
  scrollToPosition(scrollTop = 0) {
    if (this.Grid) {
      this.Grid.scrollToPosition({scrollTop});
    }
  }

  /** See Grid#scrollToCell */
  scrollToRow(index = 0) {
    if (this.Grid) {
      this.Grid.scrollToCell({
        columnIndex: 0,
        rowIndex: index,
      });
    }
  }

  componentDidMount() {
    this._setScrollbarWidth();
  }

  componentDidUpdate() {
    this._setScrollbarWidth();
  }

  render() {
    const {
      children,
      className,
      gridClassName,
      gridStyle,
      headerHeight,
      height,
      id,
      noRowsRenderer,
      rowClassName,
      rowStyle,
      scrollToIndex,
      style,
      width,
    } = this.props;
    const {scrollbarWidth} = this.state;

    const availableRowsHeight = height - headerHeight;

    const rowClass = typeof rowClassName === 'function'
      ? rowClassName({index: -1})
      : rowClassName;

    const rowStyleObject = typeof rowStyle === 'function'
      ? rowStyle({index: -1})
      : rowStyle;

    // Precompute and cache column styles before rendering rows and columns to speed things up
    this._cachedColumnStyles = [];

    React.Children.toArray(children).forEach((column, index) => {
      const flexStyles = this._getFlexStyleForColumn(
        column,
        column.props.style,
      );

      this._cachedColumnStyles[index] = {
        ...flexStyles,
        overflow: 'hidden',
      };
    });

    // Note that we specify :rowCount, :scrollbarWidth as properties on Grid even though these have nothing to do with Grid.
    // This is done because Grid is a pure component and won't update unless its properties or state has changed.
    // Any property that should trigger a re-render of Grid then is specified here to avoid a stale display.
    return (
      <div
        aria-label={this.props['aria-label']}
        aria-labelledby={this.props['aria-labelledby']}
        aria-colcount={React.Children.toArray(children).length}
        aria-rowcount={this.props.rowCount}
        className={clsx('ReactVirtualized__Table', className)}
        id={id}
        role="grid"
        style={style}>
        <DayHeaderWrapper
          className={clsx('ReactVirtualized__Table__headerRow', rowClass)}
          columns={this._getHeaderColumns()}
          style={{
            height: headerHeight,
            overflow: 'hidden',
            paddingRight: scrollbarWidth,
            width: width,
            ...rowStyleObject,
          }}
        />

        <Grid
          {...this.props}
          aria-readonly={null}
          autoContainerWidth
          className={clsx('ReactVirtualized__Table__Grid', gridClassName)}
          cellRenderer={this._createRow}
          columnWidth={width}
          columnCount={1}
          height={availableRowsHeight}
          id={undefined}
          noContentRenderer={noRowsRenderer}
          onScroll={this._onScroll}
          onSectionRendered={this._onSectionRendered}
          ref={this._setRef}
          role="rowgroup"
          scrollbarWidth={scrollbarWidth}
          scrollToRow={scrollToIndex}
          style={{
            ...gridStyle,
            // overflowX: 'hidden',
          }}
        />
      </div>
    );
  }

  _createColumn({column, columnIndex, isScrolling, parent, rowData, rowIndex}) {
    const {onColumnClick} = this.props;

    const {
      className,
      columnData,
      dataKey,
      id,
      day,
    } = column.props;

    const cellData = 'A';

    const cellRenderer = () => {
      return day.format('MM DD');
    };

    const renderedCell = cellRenderer({
      cellData,
      columnData,
      columnIndex,
      dataKey,
      isScrolling,
      parent,
      rowData,
      rowIndex,
    });

    const onClick = event => {
      onColumnClick && onColumnClick({columnData, dataKey, event});
    };

    const style = this._cachedColumnStyles[columnIndex];
    const title = typeof renderedCell === 'string' ? renderedCell : null;

    // Avoid using object-spread syntax with multiple objects here,
    // Since it results in an extra method call to 'babel-runtime/helpers/extends'
    // See PR https://github.com/bvaughn/react-virtualized/pull/942
    return (
      <div
        aria-colindex={day.format('YYYY-MM-DD')}
        aria-describedby={id}
        className={clsx('ReactVirtualized__Table__rowColumn', className)}
        key={'Row' + rowIndex + '-' + 'Col' + columnIndex}
        onClick={onClick}
        role="gridcell"
        style={style}
        title={title}>
        {renderedCell}
      </div>
    );
  }

  _createHeader({column, index}) {
    const {
      headerClassName,
      headerStyle,
      onHeaderClick,
    } = this.props;

    const {
      columnData,
      dataKey,
      id,
      label,
      day,
    } = column.props;

    const classNames = clsx(
      'ReactVirtualized__Table__headerColumn',
      headerClassName,
      column.props.headerClassName,
    );

    const style = this._getFlexStyleForColumn(column, {
      ...headerStyle,
      ...column.props.headerStyle,
    });

    const renderedHeader = day.format('MM DD');

    let headerOnClick,
      headerOnKeyDown,
      headerTabIndex,
      headerAriaLabel;

    if (onHeaderClick) {
      const onClick = event => {
        onHeaderClick({columnData, dataKey, event});
      };

      const onKeyDown = event => {
        if (event.key === 'Enter' || event.key === ' ') {
          onClick(event);
        }
      };

      headerAriaLabel = column.props['aria-label'] || label || dataKey;
      headerTabIndex = 0;
      headerOnClick = onClick;
      headerOnKeyDown = onKeyDown;
    }

    // Avoid using object-spread syntax with multiple objects here,
    // Since it results in an extra method call to 'babel-runtime/helpers/extends'
    // See PR https://github.com/bvaughn/react-virtualized/pull/942
    return (
      <div
        aria-label={headerAriaLabel}
        className={classNames}
        id={id}
        key={'Header-Col' + index}
        onClick={headerOnClick}
        onKeyDown={headerOnKeyDown}
        role="columnheader"
        style={style}
        tabIndex={headerTabIndex}>
        {renderedHeader}
      </div>
    );
  }

  _createRow({rowIndex: index, isScrolling, key, parent, style}) {
    const {
      children,
      onRowClick,
      onRowDoubleClick,
      onRowRightClick,
      onRowMouseOver,
      onRowMouseOut,
      rowClassName,
      rowStyle,
    } = this.props;

    const {scrollbarWidth} = this.state;

    const rowClass =
      typeof rowClassName === 'function' ? rowClassName({index}) : rowClassName;

    const rowStyleObject =
      typeof rowStyle === 'function' ? rowStyle({index}) : rowStyle;

    const rowData = 'A';

    const columns = React.Children.toArray(children).map(
      (column, columnIndex) =>
        this._createColumn({
          column,
          columnIndex,
          isScrolling,
          parent,
          rowData,
          rowIndex: index,
          scrollbarWidth,
        }),
    );

    const className = clsx('ReactVirtualized__Table__row', rowClass);
    const flattenedStyle = {
      ...style,
      height: this._getRowHeight(index),
      overflow: 'hidden',
      paddingRight: scrollbarWidth,
      ...rowStyleObject,
    };

    return DayWrapper({
      className,
      columns,
      index,
      isScrolling,
      key,
      onRowClick,
      onRowDoubleClick,
      onRowRightClick,
      onRowMouseOver,
      onRowMouseOut,
      rowData,
      style: flattenedStyle,
    });
  }

  /**
   * Determines the flex-shrink, flex-grow, and width values for a cell (header or column).
   */
  _getFlexStyleForColumn(column, customStyle = {}) {
    const flexValue = `${column.props.flexGrow} ${column.props.flexShrink} ${
      column.props.width || 60
    }px`;

    const style = {
      ...customStyle,
      flex: flexValue,
      msFlex: flexValue,
      WebkitFlex: flexValue,
    };

    if (column.props.maxWidth) {
      style.maxWidth = column.props.maxWidth;
    }

    if (column.props.minWidth) {
      style.minWidth = column.props.minWidth;
    }

    return style;
  }

  _getHeaderColumns() {
    const {children, disableHeader} = this.props;
    const items = disableHeader ? [] : React.Children.toArray(children);

    return items.map((column, index) => this._createHeader({column, index}));
  }

  _getRowHeight(rowIndex) {
    const {rowHeight} = this.props;

    return typeof rowHeight === 'function'
      ? rowHeight({index: rowIndex})
      : rowHeight;
  }

  _onScroll({clientHeight, scrollHeight, scrollTop}) {
    const {onScroll} = this.props;

    onScroll({clientHeight, scrollHeight, scrollTop});
  }

  _onSectionRendered({
    rowOverscanStartIndex,
    rowOverscanStopIndex,
    rowStartIndex,
    rowStopIndex,
  }) {
    const {onRowsRendered} = this.props;

    onRowsRendered({
      overscanStartIndex: rowOverscanStartIndex,
      overscanStopIndex: rowOverscanStopIndex,
      startIndex: rowStartIndex,
      stopIndex: rowStopIndex,
    });
  }

  _setRef(ref) {
    this.Grid = ref;
  }

  _setScrollbarWidth() {
    if (this.Grid) {
      const Grid = findDOMNode(this.Grid);

      const clientWidth = Grid.clientWidth || 0;
      const offsetWidth = Grid.offsetWidth || 0;
      const scrollbarWidth = offsetWidth - clientWidth;

      this.setState({scrollbarWidth});
    }
  }
}
