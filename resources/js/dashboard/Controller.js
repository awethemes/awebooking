import React, { createElement } from 'react';
import { isEqual, last, omit } from 'lodash';
import { getPersistedQuery } from '@awebooking/navigation';
import { parse } from 'qs';

const { document } = window;

export class Controller extends React.Component {
  componentDidMount() {
    document.documentElement.scrollTop = 0;
  }

  componentDidUpdate(prevProps) {
    const prevQuery = this.getQuery(prevProps.location.search);
    const prevBaseQuery = omit(this.getQuery(prevProps.location.search), 'paged');
    const baseQuery = omit(this.getQuery(this.props.location.search), 'paged');

    if (prevQuery.paged > 1 && !isEqual(prevBaseQuery, baseQuery)) {
      getHistory().replace(getNewPath({ paged: 1 }));
    }

    if (prevProps.match.url !== this.props.match.url) {
      document.documentElement.scrollTop = 0;
    }
  }

  render() {
    const { page, match, location } = this.props;

    const { url, params } = match;
    const query = this.getQuery(location.search);

    window._wpNavMenuUrlUpdate(page, query);
    window._wpNavMenuClassChange(page, url);

    return createElement(page.container, { params, path: url, pathMatch: page.path, query });
  }

  getQuery(searchString) {
    if (!searchString) {
      return {};
    }

    const search = searchString.substring(1);
    return parse(search);
  }
}

/**
 * Update an anchor's link in sidebar to include persisted queries. Leave excluded screens
 * as is.
 *
 * @param {HTMLElement} item - Sidebar anchor link.
 * @param {object} nextQuery - A query object to be added to updated hrefs.
 * @param {Array} excludedScreens - The admin screens to avoid updating.
 */
export function updateLinkHref(item, nextQuery, excludedScreens) {
  const isDashboardScreen = /admin.php\?awebooking=\/dashboard/.test(item.href);

  if (isDashboardScreen) {
    const search = last(item.href.split('?'));
    const query = parse(search);
    const path = query.path || 'dashboard';
    const screen = path.replace('/analytics', '').replace('/', '');

    const isExcludedScreen = excludedScreens.includes(screen);
    const href = 'admin.php?' + stringify(Object.assign(query, isExcludedScreen ? {} : nextQuery));

    // Replace the href so you can see the url on hover.
    item.href = href;

    item.onclick = e => {
      e.preventDefault();
      getHistory().push(href);
    };
  }
}

// Update's admin links in wp-admin menu
window._wpNavMenuUrlUpdate = function(page, query) {
  const excludedScreens = [];

  const nextQuery = getPersistedQuery(query);

  Array.from(document.querySelectorAll('#adminmenu a')).forEach(item =>
    updateLinkHref(item, nextQuery, excludedScreens),
  );
};

// When the route changes, we need to update wp-admin's menu with the correct section & current link
window._wpNavMenuClassChange = function(page, url) {
  Array.from(document.getElementsByClassName('current')).forEach(function(item) {
    item.classList.remove('current');
  });

  const submenu = Array.from(document.querySelectorAll('.wp-has-current-submenu'));

  submenu.forEach(function(element) {
    element.classList.remove('wp-has-current-submenu');
    element.classList.remove('wp-menu-open');
    element.classList.remove('selected');
    element.classList.add('wp-not-current-submenu');
    element.classList.add('menu-top');
  });

  const pageUrl = '/' === url
    ? 'admin.php?awebooking=/dashboard'
    : 'admin.php?awebooking=/dashboard/' + encodeURIComponent(url);

  const currentItemsSelector = url === '/'
    ? `li > a[href$="${pageUrl}"], li > a[href*="${pageUrl}?"]`
    : `li > a[href*="${pageUrl}"]`;

  const currentItems = document.querySelectorAll(currentItemsSelector);

  Array.from(currentItems).forEach(function(item) {
    item.parentElement.classList.add('current');
  });

  if (page.wpOpenMenu) {
    const currentMenu = document.querySelector('#' + page.wpOpenMenu);

    currentMenu.classList.remove('wp-not-current-submenu');
    currentMenu.classList.add('wp-has-current-submenu');
    currentMenu.classList.add('wp-menu-open');
    currentMenu.classList.add('current');
  }

  const wpWrap = document.querySelector('#wpwrap');
  wpWrap.classList.remove('wp-responsive-open');
};
