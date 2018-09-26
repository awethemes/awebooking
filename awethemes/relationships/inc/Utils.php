<?php
namespace Awethemes\Relationships;

use Awethemes\WP_Object\WP_Object;

class Utils {
	/**
	 * Parse IDs for sql.
	 *
	 * @param mixed $ids The ids.
	 * @return string
	 */
	public static function parse_sql_ids( $ids ) {
		if ( is_numeric( $ids ) ) {
			return (string) $ids;
		}

		return implode( ',', wp_parse_id_list( $ids ) );
	}

	/**
	 * Parse the object_id.
	 *
	 * @param  mixed $object The WP object.
	 * @return int|null
	 */
	public static function parse_object_id( $object ) {
		if ( is_numeric( $object ) && $object > 0 ) {
			return (int) $object;
		}

		if ( ! empty( $object->ID ) ) {
			return (int) $object->ID;
		}

		if ( ! empty( $object->term_id ) ) {
			return (int) $object->term_id;
		}

		if ( $object instanceof WP_Object ) {
			return $object->get_id();
		}

		return 0;
	}
}
