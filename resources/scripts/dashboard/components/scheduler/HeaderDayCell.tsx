import React from 'react';
import type { DateHeaderProp } from './types';

export default function HeaderDayCell({ column, ...props }: DateHeaderProp<any>): JSX.Element {
	const { date } = column;

	return (
		<div>
			{date.format('DD')}
		</div>
	);
}
