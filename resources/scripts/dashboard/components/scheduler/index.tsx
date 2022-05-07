import React, { useMemo, useRef } from 'react';
import moment, { Moment, MomentInput } from 'moment';
import { format, dateI18n } from '@wordpress/date';

import './styles.scss';
import { Column, ColumnGroup } from 'react-table';
import { useLogger, useMeasure, useObservable, useSize } from 'react-use';
import HeaderDayCell from '~/dashboard/components/scheduler/HeaderDayCell';
import HeaderMonthCell from '~/dashboard/components/scheduler/HeaderMonthCell';
import HeaderRoomCell from '~/dashboard/components/scheduler/HeaderRoomCell';
import { ColumnType, DateColumn } from './types';
import Table from './Table';

const resources = [
	{
		id: 1,
		name: 'Room 1'
	}
];

function enumerateDaysBetweenDates(start: MomentInput, end: MomentInput, duration = 'P1D'): Array<Moment> {
	let current = moment(start);
	const dates = [];

	while (current.isSameOrBefore(end)) {
		dates.push(current);

		current = current.clone().add(duration);
	}

	return dates;
}

function Scheduler({
	startDate = moment()
}): JSX.Element {
	const [rootRef, { width, height }] = useMeasure<HTMLDivElement>();

	console.log([width, height]);

	const columns = useMemo<ColumnType[]>(() => {
		const dates = enumerateDaysBetweenDates(startDate, startDate.clone().add(35, 'days'));

		const dateColumns: Record<string, Omit<ColumnGroup, 'columns'> & { columns: DateColumn[] }> = {};

		for (const date of dates) {
			const dateString = date.format('MMMM-YYYY');

			if (!dateColumns[dateString]) {
				dateColumns[dateString] = {
					id: dateString,
					Header: HeaderMonthCell,
					columns: []
				};
			}

			dateColumns[dateString].columns.push({
				id: date.format('YYYY-MM-DD'),
				Header: HeaderDayCell,
				date: date
			});
		}

		const roomColumn: Column = {
			id: 'room',
			Header: HeaderRoomCell,
			width: 350
		};

		return [
			roomColumn,
			...Object.values(dateColumns)
		] as ColumnType[];
	}, [startDate]);

	const data = useMemo(() => {
		const rows = [];

		for (let i = 0; i < 25; i++) {
			rows.push({ id: i, name: `Room ${i}` });
		}

		return rows;
	}, []);

	return (
		<div className="w-full" ref={rootRef}>
			{width && (
				<Table
					columns={columns}
					data={data}
				/>
			)}
		</div>
	);
}

export default Scheduler;
