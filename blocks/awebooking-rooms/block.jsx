/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/editor';
import { Component, Fragment } from '@wordpress/element';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { debounce } from 'lodash';

import {
  PanelBody,
  Placeholder,
  RangeControl,
  Spinner,
  SelectControl
} from '@wordpress/components';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import getQuery from './utils/get-query';
import RoomPreview from './components/room-preview';

/**
 * Component to handle edit mode of "Rooms".
 */
class RoomsBlock extends Component {
  constructor() {
    super( ...arguments );
    this.state = {
      rooms: [],
      loaded: false,
    };

    this.debouncedGetRooms = debounce( this.getRooms.bind( this ), 200 );
  }

  componentDidMount() {
    this.getRooms();
  }

  componentDidUpdate( prevProps ) {
    const hasChange = [ 'postsToShow', 'orderBy', 'order', 'offset' ].reduce(
      ( acc, key ) => {
        return acc || prevProps.attributes[ key ] !== this.props.attributes[ key ];
      },
      false
    );
    if ( hasChange ) {
      this.debouncedGetRooms();
    }
  }

  getRooms() {
    apiFetch( {
      path: addQueryArgs(
        '/wp/v2/room_type',
        getQuery( this.props.attributes )
      ),
    } )
    .then( ( rooms ) => {
      console.log(rooms)
      this.setState( { rooms, loaded: true } );
    } )
    .catch( () => {
      this.setState( { rooms: [], loaded: true } );
    } );
  }

  getInspectorControls() {
    const { attributes, setAttributes } = this.props;
    const { postsToShow, order, orderBy, offset } = attributes;

    return (
      <InspectorControls key="inspector">
        <PanelBody title={ __( 'Rooms Settings', 'awebooking' ) }>
          <RangeControl
            label={__('Number of items', 'awebooking')}
            value={ postsToShow }
            onChange={ ( value ) => setAttributes( { postsToShow: value } ) }
            min={1}
            max={36}
            help={__( 'How much items per page to show', 'awebooking' )}
          />

          <SelectControl
            label={ __( 'Order by', 'awebooking' ) }
            value={ orderBy }
            options={ [
              { value: 'date', label: __( 'Date', 'awebooking' ) },
              { value: 'title', label: __( 'Title', 'awebooking' ) },
              { value: 'id', label: __( 'ID', 'awebooking' ) },
              { value: 'modified', label: __( 'Modified', 'awebooking' ) },
              { value: 'author', label: __( 'Author', 'awebooking' ) },
            ] }
            onChange={ ( value ) => setAttributes( { orderBy: value } ) }
          />

          <SelectControl
            label={ __( 'Sort order', 'awebooking' ) }
            value={ order }
            options={ [
              { value: 'desc', label: __( 'Descending', 'awebooking' ) },
              { value: 'asc', label: __( 'Ascending', 'awebooking' ) },
            ] }
            onChange={ ( value ) => setAttributes( { order: value } ) }
          />

          <RangeControl
            label={__('Offset', 'awebooking')}
            value={ offset }
            onChange={ ( value ) => setAttributes( { offset: value } ) }
            min={0}
            max={36}
            help={__( 'Number of post to displace or pass over.', 'awebooking' )}
          />
        </PanelBody>
      </InspectorControls>
    );
  }

  render() {
    const { loaded, rooms } = this.state;
    const classes = [ 'awebooking-block-rooms' ];

    if ( rooms && ! rooms.length ) {
      if ( ! loaded ) {
        classes.push( 'is-loading' );
      } else {
        classes.push( 'is-not-found' );
      }
    }

    return (
      <Fragment>
        <p>sasdasd</p>
        { this.getInspectorControls() }
        <div className={ classes.join( ' ' ) }>
          { rooms.length ? (
            rooms.map( ( room ) => (
              <RoomPreview room={ room } key={ room.id } />
            ) )
          ) : (
            <Placeholder
              label={ __( 'Rooms', 'awebooking' ) }
            >
              { ! loaded ? (
                <Spinner />
              ) : (
                __( 'No rooms found.', 'awebooking' )
              ) }
            </Placeholder>
          ) }
        </div>
      </Fragment>
    );
  }
}

RoomsBlock.propTypes = {
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

export default RoomsBlock;
