export default {
  layout: {
    type: 'string',
    default: 'horizontal',
  },
  alignment: {
    type: 'string',
    default: 'left',
  },
  hotel_location: {
    type: 'string',
    default: 'true',
  },
  occupancy: {
    type: 'string',
    default: 'true',
  },
  container_class: {
    type: 'string',
    default: '',
  },

  /**
   * Room attributes, used to display only rooms with the given attributes.
   */
  attributes: {
    type: 'array',
    default: [],
  },
};
