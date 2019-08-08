import React from 'react';
import { storiesOf } from '@storybook/react';
import { withState } from '@wordpress/compose';
import { Button, ToggleControl } from '@wordpress/components';

import EllipsisMenu from './index';
import MenuItem from './menu-item';
import MenuTitle from './menu-title';

const MyEllipsisMenu = withState({
  showCustomers: true,
  showOrders: true,
  showActions: true,
})(({ setState, showCustomers, showOrders, showActions }) => (
  <EllipsisMenu
    label="Choose which analytics to display"
    renderContent={({ onToggle }) => {
      return (
        <div>
          <MenuTitle>Display Stats</MenuTitle>

          <MenuItem isCheckbox isClickable checked={showActions} onInvoke={() => setState({ showActions: !showActions })}>
            <span>Show Actions</span>
          </MenuItem>

          <MenuItem onInvoke={() => setState({ showCustomers: !showCustomers })}>
            <ToggleControl
              label="Show Customers"
              checked={showCustomers}
              onChange={() => setState({ showCustomers: !showCustomers })}
            />
          </MenuItem>

          <MenuItem onInvoke={() => setState({ showOrders: !showOrders })}>
            <ToggleControl
              label="Show Orders"
              checked={showOrders}
              onChange={() => setState({ showOrders: !showOrders })}
            />
          </MenuItem>

          <MenuItem onInvoke={onToggle}>
            <Button
              label="Close menu"
              onClick={onToggle}
            >
              Close Menu
            </Button>
          </MenuItem>
        </div>
      );
    }}
  />
));

storiesOf('EllipsisMenu', module).add('Default', () => (
  <MyEllipsisMenu />
));
