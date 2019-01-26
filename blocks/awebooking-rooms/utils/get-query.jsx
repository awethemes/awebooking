export default function getQuery (attributes) {
  const { postsToShow, order, orderBy, offset } = attributes

  const query = {
    status: 'publish',
    per_page: postsToShow,
    orderby: orderBy,
    order: order,
    offset: offset,
  }

  return query
}
