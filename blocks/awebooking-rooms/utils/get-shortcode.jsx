export default function getShortcode( { attributes } ) {
  const { postsToShow, orderBy, order, offset } = attributes;

  const shortcodeAtts = new Map();
  shortcodeAtts.set( 'posts_per_page', postsToShow );

  if ( orderBy ) {
    shortcodeAtts.set( 'orderby', orderBy );
  }

  if ( order ) {
    shortcodeAtts.set( 'order', order );
  }

  if ( offset ) {
    shortcodeAtts.set( 'offset', offset );
  }

  // Build the shortcode string out of the set shortcode attributes.
  let shortcode = '[awebooking_rooms';
  for ( const [ key, value ] of shortcodeAtts ) {
    shortcode += ' ' + key + '="' + value + '"';
  }
  shortcode += ']';

  return shortcode;
}
