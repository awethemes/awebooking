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
    <div className="list-room-preview">
      <div className="list-room-preview__wrap">
        <div className="list-room-preview__media">
          { image }
        </div>

        <div className="list-room-preview__info">
          <div className="list-room-preview__header">
            <h2 className="list-room-preview__title">{ room.room_name }</h2>
            <div
              className="list-room-preview__price"
              dangerouslySetInnerHTML={ { __html: room.price_html } }
            />
          </div>

          <div className="list-room-preview__container">
            <div
              className="list-room-preview__desc"
              dangerouslySetInnerHTML={ { __html: room.short_description } }
            />
          </div>

          <div className="list-room-preview__footer">
            <span className="list-room-preview__button">{ __( 'View more infomation', 'awebooking' ) }</span>
          </div>
        </div>
      </div>
    </div>
  );
};

RoomPreview.propTypes = {
  /**
   * The room object as returned from the API.
   */
  room: PropTypes.shape( {
  id: PropTypes.number,
  room_name: PropTypes.string,
  thumbnail_url: PropTypes.string,
  short_description: PropTypes.string,
  } ).isRequired,
};

export default RoomPreview;
