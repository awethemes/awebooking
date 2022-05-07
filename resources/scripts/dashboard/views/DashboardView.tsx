import React, { useState } from 'react';
import { Button } from '@wordpress/components';
import Scheduler from '~/dashboard/components/scheduler';
import Layout from '~/dashboard/layout';

const DashboardView = (props: any) => {
	const [count, setCount] = useState(0);

	return (
		<>
			<Layout.Toolbar>
				<Button variant="primary" onClick={() => setCount(count + 1)}>{props.hello}</Button>
			</Layout.Toolbar>

			<Scheduler />
		</>
	);
};

DashboardView.layout = (page: React.ReactNode) => <Layout children={page} />;

export default DashboardView;
