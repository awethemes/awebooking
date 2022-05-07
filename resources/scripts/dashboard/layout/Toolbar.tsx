import React from 'react';
import { createSlotFill } from '@wordpress/components';

const { Fill, Slot } = createSlotFill('awebooking/toolbar');

function Toolbar(): JSX.Element {
	return (
		<div className="toolbars">
			<Slot />
		</div>
	);
}

Toolbar.Content = Fill;

export default Toolbar;
