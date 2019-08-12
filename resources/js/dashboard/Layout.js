import React from 'react';
import PropTypes from 'prop-types';
import { get } from 'lodash';
import { getHistory } from '@awebooking/navigation';
import { Router, Route, Switch } from 'react-router-dom';

import { getPages } from './pages';
import { Controller } from './Controller';

class PageLayout extends React.Component {
  static propTypes = {
    isEmbedded: PropTypes.bool,
    page: PropTypes.shape({
      container: PropTypes.func,
      path: PropTypes.string,
      wpOpenMenu: PropTypes.string,
    }).isRequired,
  };

  componentDidMount() {
    // this.recordPageViewTrack();
    document.body.classList.remove('awebooking-admin-is-loading');
  }

  componentDidUpdate(prevProps) {
    const previousPath = get(prevProps, 'location.pathname');
    const currentPath = get(this.props, 'location.pathname');

    if (!previousPath || !currentPath) {
      return;
    }

    if (previousPath !== currentPath) {
      // this.recordPageViewTrack();
    }
  }

  recordPageViewTrack() {
    const pathname = get(this.props, 'location.pathname');

    if (!pathname) {
      return;
    }

    // Remove leading slash, and camel case remaining pathname
    let path = pathname.substring(1).replace(/\//g, '_');

    // When pathname is `/` we are on the dashboard
    if (path.length === 0) {
      path = 'dashboard';
    }

    recordPageView(path);
  }

  render() {
    const { isEmbedded, ...restProps } = this.props;

    return (
      <div className="awebooking-layout">
        {!isEmbedded && (
          <div className="awebooking-layout__primary" id="awebooking-layout__primary">
            <div className="awebooking-layout__main">
              <Controller {...restProps} />
            </div>
          </div>
        )}
      </div>
    );
  }
}

class Layout extends React.Component {
  render() {
    return (
      <Router history={getHistory()}>
        <Switch>
          {getPages().map(page => {
            return (
              <Route
                key={page.path}
                path={page.path}
                exact
                render={props => <PageLayout page={page} {...props} />}
              />
            );
          })}
        </Switch>
      </Router>
    );
  }
}

export default Layout;
