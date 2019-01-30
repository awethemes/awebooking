/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { RawHTML } from '@wordpress/element';

/**
 * Internal dependencies
 */
import Block from './block';
import getShortcode from './utils/get-shortcode';
import sharedAttributes from './utils/shared-attributes';

registerBlockType( 'awebooking/awebooking-search-form', {
  title: __('Search Form', 'awebooking'),
  category: 'awebooking',
  keywords: [ __( 'AweBooking', 'awebooking' ) ],
  description: __( 'Display a check availability form.', 'awebooking' ),
  attributes: {
    ...sharedAttributes,
  },

  /**
   * Renders and manages the block.
   */
  edit( props ) {
    return <Block { ...props } />;
  },

  /**
   * Save the block content in the post content. Block content is saved as a rooms shortcode.
   *
   * @return string
   */
  save( props ) {
    return (
      <RawHTML>
        { getShortcode( props ) }
      </RawHTML>
    );
  },
} );
