import { __ } from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';

import Dashboard from './dashboard';

export const getPages = () => {
  const pages = [];

  pages.push({
    container: Dashboard,
    path: '/',
    breadcrumbs: [__('Dashboard', 'awebooking')],
    wpOpenMenu: 'toplevel_page_awebooking',
  });

  return applyFilters('awebooking_admin_pages', pages);
};
