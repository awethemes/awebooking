import { Moment } from 'moment';
import { Column, ColumnInstance, TableInstance } from 'react-table';

export type DateColumn = Column & {
	date: Moment;
}

export type ColumnType = DateColumn | Column;

export type DateHeaderProp<D extends Object> = TableInstance<D> & {
	column: ColumnInstance<D> & {
		date: Moment;
	};
}
