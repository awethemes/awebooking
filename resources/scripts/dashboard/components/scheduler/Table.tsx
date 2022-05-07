import React, { useMemo } from 'react';
import {
	useTable,
	useFlexLayout
} from 'react-table';
import { useMeasure, useScroll, useScrolling } from 'react-use';

function Table({ columns, data }: any): JSX.Element {
	const {
		rows,
		headerGroups,
		prepareRow,
		getTableProps
	} = useTable({
			data,
			columns,
			defaultColumn: {
				width: 60
			}
		},
		useFlexLayout
	);

	const wrapRef = React.useRef<HTMLDivElement>(null);
	const ref = React.useRef<HTMLDivElement>(null);

	const scrolling = useScrolling(wrapRef);

	return (
		<div className="table-wrap" ref={wrapRef}>
			<div {...getTableProps()} className="atable">
				<div className="thead">
					{headerGroups.map(headerGroup => (
						<div
							{...headerGroup.getHeaderGroupProps({
								// style: { paddingRight: '15px' },
							})}
							className="tr"
							ref={ref}
						>
							{headerGroup.headers.map(column => (
								<div {...column.getHeaderProps()} className="th">
									{column.render('Header', { scrolling, parentRef: wrapRef })}
								</div>
							))}
						</div>
					))}
				</div>

				<div className="tbody">
					{rows.map(row => {
						prepareRow(row);
						return (
							<div {...row.getRowProps()} className="tr">
								{row.cells.map(cell => {
									return (
										<div {...cell.getCellProps()} className="td">
											{cell.render('Cell')}
										</div>
									);
								})}
							</div>
						);
					})}
				</div>
			</div>
		</div>
	);
}

export default Table;
