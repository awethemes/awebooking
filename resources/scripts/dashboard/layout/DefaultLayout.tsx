import React from 'react';
import { SlotFillProvider } from '@wordpress/components';

import Toolbar from './Toolbar';

interface Props {
	title?: string;
	children: React.ReactNode;
}

const DefaultLayout = ({ children, title }: Props): JSX.Element => {
	return (
		<SlotFillProvider>
			<div className="awebooking-page bg-white rounded shadow">
				<div className="awebooking-wrap p-4">
					<header className="flex items-center justify-between gap-4">
						<h1>{title || 'AweBooking'}</h1>

						<Toolbar />
					</header>

					<main className="flex flex-column">
						{children}
					</main>

					<footer>
					</footer>
				</div>
			</div>
		</SlotFillProvider>
	);
};

DefaultLayout.Toolbar = Toolbar.Content;

export default DefaultLayout;
