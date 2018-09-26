<?php
namespace Awethemes\Relationships\Side;

use Awethemes\Relationships\Utils;

class User_Side extends Side {
	/**
	 * {@inheritdoc}
	 */
	public function get_object_type() {
		return 'user';
	}

	/**
	 * {@inheritdoc}
	 */
	public function do_query( $query_vars ) {
		return new \WP_User_Query( $query_vars );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_indeterminate( Side $side ) {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function parse_object_id( $object ) {
		$object_id = Utils::parse_object_id( $object );

		if ( ! $object_id || ! get_userdata( $object_id ) ) {
			return null;
		}

		return $object_id;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function resolve_label() {
		return '';
	}
}
