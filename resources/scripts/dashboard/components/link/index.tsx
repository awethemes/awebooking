import React from 'react';
import { InertiaLinkProps, Link as InertiaLink } from '@inertiajs/inertia-react';
import { addQueryArgs } from '@wordpress/url';

type LinkProps = Omit<InertiaLinkProps, 'href'> & {
	to?: string;
	href?: string;
	args?: Record<string, unknown>;
}

export default function Link({ to, href, args, ...props }: LinkProps): JSX.Element | null {
	if (typeof to !== 'undefined' && !href) {
		href = addQueryArgs(`/wp-admin/admin.php?page=awebooking-pms${to}`, args);
	}

	if (!href) {
		return null;
	}

	return <InertiaLink{...props} href={href} />;
}
