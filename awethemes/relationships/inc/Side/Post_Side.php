<?php
namespace Awethemes\Relationships\Side;

use Awethemes\Relationships\Utils;

class Post_Side extends Side {
	/**
	 * {@inheritdoc}
	 */
	public function get_object_type() {
		return 'post';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_query_vars() {
		return array_merge( $this->query_vars, [
			'post_type'           => $this->post_type,
			'ignore_sticky_posts' => true,
			'suppress_filters'    => false,
		] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function do_query( $query_vars ) {
		return new \WP_Query( $query_vars );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function resolve_label() {
		$object = get_post_type_object( $this->post_type );

		return $object ? $object->label : '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function parse_object_id( $object ) {
		$object_id = Utils::parse_object_id( $object );

		if ( ! $object_id || get_post_type( $object_id ) !== $this->post_type ) {
			return null;
		}

		return $object_id;
	}
}
