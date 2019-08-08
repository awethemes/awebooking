/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import clsx from 'clsx';

/**
 * Spinner - An indeterminate circular progress indicator.
 */
class Spinner extends React.Component {
	render() {
		const { className } = this.props;
		const classes = clsx( 'awebooking-spinner', className );

		return (
			<svg className={ classes } viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
				<circle
					className="awebooking-spinner__circle"
					fill="none"
					strokeWidth="5"
					strokeLinecap="round"
					cx="50"
					cy="50"
					r="30"
				/>
			</svg>
		);
	}
}

Spinner.propTypes = {
	/**
	 * Additional class name to style the component.
	 */
	className: PropTypes.string,
};

export default Spinner;
