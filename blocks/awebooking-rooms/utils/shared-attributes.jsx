export default {
  postsToShow: {
    type: 'number',
    default: 5,
  },
  orderBy: {
    type: 'string',
    default: 'date',
  },
  order: {
    type: 'string',
    default: 'desc',
  },
  offset: {
    type: 'number',
    default: 0,
  },

  /**
   * Room attributes, used to display only rooms with the given attributes.
   */
  attributes: {
    type: 'array',
    default: [],
  },
};
