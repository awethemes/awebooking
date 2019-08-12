import { __ } from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';

import Reports from './reports';
import Dashboard from './dashboard';

export const getPages = () => {
  const pages = [];

  pages.push({
    container: Dashboard,
    path: '/',
    breadcrumbs: [__('Dashboard', 'awebooking')],
    wpOpenMenu: 'toplevel_page_awebooking',
  });

  pages.push({
    container: Reports,
    path: '/reports',
    breadcrumbs: [__('Dashboard', 'awebooking'), __('Reports', 'awebooking')],
    wpOpenMenu: 'toplevel_page_awebooking',
  });

  return applyFilters('awebooking_admin_pages', pages);
};
