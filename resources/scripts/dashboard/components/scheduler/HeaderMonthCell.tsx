import React, { useEffect } from 'react';
import { HeaderProps } from 'react-table';
import { useIntersection, useMeasure, useScroll } from 'react-use';

export default function HeaderMonthCell({
	column,
	scrolling,
	parentRef,
	...props
}: HeaderProps<any> | any): JSX.Element {
	const { totalColumnsWidth } = props;

	const [offset, setOffset] = React.useState(0);
	const ref = React.useRef<HTMLDivElement>(null);

	useEffect(() => {
		const parentRect = parentRef.current.getBoundingClientRect();
		const rect = ref.current?.getBoundingClientRect();

		setOffset(Math.round(rect.x - parentRect.x));
	}, [scrolling]);

	return (
		<div
			ref={ref}
			style={{
				position: 'relative',
				left: offset
			}}>
			Header
		</div>
	);
}
