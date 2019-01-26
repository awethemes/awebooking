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
import sharedAttributes from './utils/shared-attributes';

registerBlockType( 'awebooking/awebooking-rooms', {
  title: __('Rooms', 'awebooking'),
  category: 'awebooking',
  keywords: [ __( 'AweBooking', 'awebooking' ) ],
  description: __( 'Display a list of rooms.', 'awebooking' ),
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
      <RawHTML className={ align ? `align${ align }` : '' }>
        { getShortcode( props, 'woocommerce/product-best-sellers' ) }
      </RawHTML>
    );
  },
} );
