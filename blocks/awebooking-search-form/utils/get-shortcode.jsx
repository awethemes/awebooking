export default function getShortcode( { attributes } ) {
  const { layout, alignment, hotel_location, occupancy, container_class } = attributes;

  const shortcodeAtts = new Map();
  if ( layout ) {
    shortcodeAtts.set( 'layout', layout );
  }

  if ( alignment ) {
    shortcodeAtts.set( 'alignment', alignment );
  }

  if ( hotel_location ) {
    shortcodeAtts.set( 'hotel_location', 'true' );
  }

  if ( occupancy ) {
    shortcodeAtts.set( 'occupancy', 'true' );
  }

  if ( container_class ) {
    shortcodeAtts.set( 'container_class', container_class );
  }

  // Build the shortcode string out of the set shortcode attributes.
  let shortcode = '[awebooking_search_form';
  for ( const [ key, value ] of shortcodeAtts ) {
    shortcode += ' ' + key + '="' + value + '"';
  }
  shortcode += ']';

  return shortcode;
}
