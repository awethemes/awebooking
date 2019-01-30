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
 * Display a preview for a given form.
 */
const SearchFormPreview = ( { form } ) => {
  return (
      <div className="search-form-preview">
        { form.hotel_location &&
        <div className="search-form-hotel">
          <div className="search-form-placeholder">
            <span className="dashicons dashicons-admin-multisite"></span>
          </div>
        </div>
        }

        <div className="search-form-dates">
          <div className="search-form-placeholder">
            <span className="dashicons dashicons-calendar-alt"></span>
          </div>
        </div>

        {form.occupancy &&
        <div className="search-form-occupancy">
          <div className="search-form-placeholder">
            <span className="dashicons dashicons-groups"></span>
          </div>
        </div>
        }

        <div className="search-form-button">
          <div className="search-form-placeholder">
            <span className="dashicons dashicons-search"></span>
          </div>
        </div>
      </div>
  );
};

SearchFormPreview.propTypes = {
  /**
   * The form object as returned from the API.
   */
  form: PropTypes.shape( {
    layout: PropTypes.string,
    alignment: PropTypes.string,
    hotel_location: PropTypes.string,
    occupancy: PropTypes.string,
    container_class: PropTypes.string,
  } ).isRequired,
};

export default SearchFormPreview;
