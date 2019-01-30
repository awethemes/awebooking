/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/editor';
import { Component, Fragment } from '@wordpress/element';
import { debounce } from 'lodash';

import {
  PanelBody,
  Placeholder,
  Spinner,
  SelectControl,
  TextControl,
  ToggleControl
} from '@wordpress/components';
import { withState } from '@wordpress/compose';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import SearchFormPreview from './components/search-form-preview';

/**
 * Component to handle edit mode of "Form".
 */
class SearchFormBlock extends Component {
  constructor() {
    super( ...arguments );
    this.state = {
      form: [],
    };

    this.debouncedGetForm = debounce( this.getForm.bind( this ), 200 );
  }

  componentDidMount() {
    this.getForm();
  }

  componentDidUpdate( prevProps ) {
    const hasChange = [ 'layout', 'alignment', 'hotel_location', 'occupancy', 'container_class' ].reduce(
      ( acc, key ) => {
        return acc || prevProps.attributes[ key ] !== this.props.attributes[ key ];
      },
      false
    );
    if ( hasChange ) {
      this.debouncedGetForm();
    }
  }

  getForm() {
    const form = this.props.attributes;
    this.setState( { form, loaded: true } );
  }

  getInspectorControls() {
    const { attributes, setAttributes } = this.props;
    const { layout, alignment, hotel_location, occupancy, container_class } = attributes;

    return (
      <InspectorControls key="inspector">
        <PanelBody title={ __( 'Search Form Settings', 'awebooking' ) }>
          <SelectControl
            label={ __( 'Layout', 'awebooking' ) }
            value={ layout }
            options={ [
              { value: 'horizontal', label: __( 'Horizontal', 'awebooking' ) },
              { value: 'vertical', label: __( 'Vertical', 'awebooking' ) },
            ] }
            onChange={ ( value ) => setAttributes( { layout: value } ) }
          />

          <SelectControl
            label={ __( 'Alignment', 'awebooking' ) }
            value={ alignment }
            options={ [
              { value: 'left', label: __( 'Left', 'awebooking' ) },
              { value: 'center', label: __( 'Center', 'awebooking' ) },
              { value: 'right', label: __( 'Right', 'awebooking' ) },
            ] }
            onChange={ ( value ) => setAttributes( { alignment: value } ) }
          />

          <ToggleControl
            label={ __( 'Hotel location', 'awebooking' ) }
            help={ __( 'Display hotel location input?', 'awebooking' ) }
            checked={ hotel_location }
            onChange={ ( value ) => setAttributes( { hotel_location: value } ) }
          />

          <ToggleControl
            label={ __( 'Occupancy', 'awebooking' ) }
            help={ __( 'Display occupancy input?', 'awebooking' ) }
            checked={ occupancy }
            onChange={ ( value ) => setAttributes( { occupancy: value } ) }
          />

          <TextControl
            label={ __( 'Container classes', 'awebooking' ) }
            value={ container_class }
            onChange={ ( value ) => setAttributes( { container_class: value } ) }
          />
        </PanelBody>
      </InspectorControls>
    );
  }

  render() {
    const { form } = this.state;
    const classes = [ 'awebooking-block-search-form' ];

    if ( form.layout ) {
      classes.push( 'awebooking-block-search-form--' + form.layout );
    }

    if ( form.container_class ) {
      classes.push( form.container_class );
    }

    return (
      <Fragment>
        { this.getInspectorControls() }
        <div className={ classes.join( ' ' ) }>
          { form &&
            <SearchFormPreview form={ form } />
          }
        </div>
      </Fragment>
    );
  }
}

SearchFormBlock.propTypes = {
  /**
   * The attributes for this block
   */
  attributes: PropTypes.object.isRequired,
  /**
   * The register block name.
   */
  name: PropTypes.string.isRequired,
  /**
   * A callback to update attributes
   */
  setAttributes: PropTypes.func.isRequired,
};

export default SearchFormBlock;
