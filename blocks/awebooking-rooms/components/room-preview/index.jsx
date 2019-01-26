/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import './style.scss';

/**
 * Display a preview for a given room.
 */
const RoomPreview = ( { room } ) => {
  let image = null;
  if ( room.thumbnail_url ) {
    image = <img src={ room.thumbnail_url } alt="" />;
  }

  return (
    <div className="awebooking-room-preview">
      { image }
      <div className="awebooking-room-preview__title">{ room.title.rendered }</div>
    </div>
  );
};

RoomPreview.propTypes = {
  /**
   * The room object as returned from the API.
   */
  room: PropTypes.shape( {
    id: PropTypes.number,
    thumbnail_url: PropTypes.string,
    title: PropTypes.array,
  } ).isRequired,
};

export default RoomPreview;
