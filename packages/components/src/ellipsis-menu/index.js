/** @format */
/**
 * External dependencies
 */
import { Component } from '@wordpress/element';
import React from 'react';
import classnames from 'classnames';
import { IconButton, Dropdown, NavigableMenu } from '@wordpress/components';
import PropTypes from 'prop-types';

/**
 * This is a dropdown menu hidden behind a vertical ellipsis icon. When clicked, the inner MenuItems are displayed.
 */
class EllipsisMenu extends React.Component {
  render() {
    const { label, renderContent } = this.props;
    if (!renderContent) {
      return null;
    }

    const renderEllipsis = ({ onToggle, isOpen }) => {
      const toggleClassname = classnames('awebooking-ellipsis-menu__toggle', {
        'is-opened': isOpen,
      });

      return (
        <IconButton
          className={toggleClassname}
          onClick={onToggle}
          icon="ellipsis"
          title={label}
          aria-expanded={isOpen}
        />
      );
    };

    const renderMenu = renderContentArgs => (
      <NavigableMenu className="awebooking-ellipsis-menu__content">
        {renderContent(renderContentArgs)}
      </NavigableMenu>
    );

    return (
      <div className="awebooking-ellipsis-menu">
        <Dropdown
          contentClassName="awebooking-ellipsis-menu__popover"
          position="bottom left"
          renderToggle={renderEllipsis}
          renderContent={renderMenu}
        />
      </div>
    );
  }
}

EllipsisMenu.propTypes = {
  /**
   * The label shown when hovering/focusing on the icon button.
   */
  label: PropTypes.string.isRequired,
  /**
   * A function returning `MenuTitle`/`MenuItem` components as a render prop. Arguments from Dropdown passed as function arguments.
   */
  renderContent: PropTypes.func,
};

export default EllipsisMenu;
