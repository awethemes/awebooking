import React from 'react';
import { render } from 'react-dom';
import { createInertiaApp } from '@inertiajs/inertia-react';

// Import the stylesheet.
import './styles.scss';

createInertiaApp({
	id: 'awebooking-root',
	resolve: name => require(`./dashboard/views/${name}`),
	setup({ el, App, props }) {
		render(<App {...props} />, el);
	}
});
