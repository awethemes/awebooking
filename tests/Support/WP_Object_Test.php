<?php

use AweBooking\Support\WP_Object;

class WP_Object_Test extends WP_UnitTestCase {
	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		$this->got = $this->factory->post->create([
			'post_title'   => 'Game of Thrones',
			'post_excerpt' => 'Season 7',
		]);

		update_post_meta( $this->got, '_release_date', '2017-07-17' );
		update_post_meta( $this->got, '_number_of_episodes', '7' );
	}

	public function testGetDefault() {
		$object = new AweBooking_Post_WP_Object( 0 );

		$this->assertEmpty($object['title']);
		$this->assertEmpty($object['description']);
		$this->assertEmpty($object['release']);
		$this->assertNull($object['episodes']);
	}

	public function testSetAndSet() {
		$object = new AweBooking_Post_WP_Object( $this->got );

		$this->assertEquals($object['title'], 'Game of Thrones');
		$this->assertEquals($object->title, 'Game of Thrones');

		$this->assertEquals($object['description'], 'Season 7');
		$this->assertEquals($object->description, 'Season 7');

		$this->assertEquals($object['episodes'], '7');
		$this->assertEquals($object->episodes, '7');

		$getOnly = $object->only('title', 'description');
		$getOnlySame = $object->only(['title', 'description']);

		$this->assertEquals($getOnly, $getOnlySame);
		$this->assertArrayHasKey('title', $getOnly);
		$this->assertArrayHasKey('description', $getOnly);
	}

	public function testCastsAttr() {
		$object = new AweBooking_Post_WP_Object( $this->got );

		$this->assertSame($object['episodes'], 7);
		$this->assertSame($object->episodes, 7);
	}

	public function testGetChanges() {
		$object = new AweBooking_Post_WP_Object( $this->got );

		$object['title'] = 'Changed';
		$object['description'] = 'Desc has been changed';
		$changes_array = [ 'title' => 'Changed', 'description' => 'Desc has been changed' ];

		$this->assertTrue($object->has_change( 'title' ));
		$this->assertTrue($object->has_change( 'description' ));
		$this->assertTrue($object->has_change( ['title', 'description'] ));

		$this->assertInternalType('array', $object->get_changes());
		$this->assertSame($object->get_changes(), $changes_array);

		$this->assertFalse($object->has_change('release'));
		$this->assertFalse($object->has_change('episodes'));
		$this->assertFalse($object->has_change('unknown'));
	}

	public function testSoftDelete() {
		$post_id = $this->factory->post->create();
		$object = new AweBooking_Post_WP_Object( $post_id );

		$this->assertTrue($object->delete()); // Soft delete.
		$this->assertEquals('trash', get_post_status($post_id));
		$this->assertFalse($object->exists());
		$this->assertNull($object->delete());
	}

	public function testForceDelete() {
		$post_id = $this->factory->post->create();
		$object = new AweBooking_Post_WP_Object( $post_id );

		$this->assertTrue($object->delete( true ));
		$this->assertNull(get_post($post_id));
		$this->assertFalse($object->exists());
		$this->assertNull($object->delete());
	}

	public function testFailedDelete() {
		$object = new AweBooking_Post_WP_Object(0);
		$this->assertNull($object->delete());
	}

	public function testInsert() {
		$a = new AweBooking_Post_WP_Object;
		$a['title'] = 'HAHA';
		$a['description'] = 'HEHE';
		$a->save();

		$post = get_post( $a->get_id() );
		$this->assertEquals($a['title'], $post->post_title);
		$this->assertEquals($a['description'], $post->post_excerpt);
	}

	public function testUpdate() {
		$id = $this->factory->post->create([
			'post_title'   => 'Game of Thrones',
			'post_excerpt' => 'Season 7',
		]);

		$a = new AweBooking_Post_WP_Object( $id );
		$a['description'] = 'Season7';
		$a->save();

		$post = get_post( $a->get_id() );
		$this->assertEquals($a['title'], $post->post_title);
		$this->assertEquals($a['description'], $post->post_excerpt);
	}
}

class AweBooking_Post_WP_Object extends WP_Object {
	protected $attributes = [
		'title' => '',
		'description' => '',
		'release' => '',
		'episodes' => null,
	];

	protected $maps = [
		'_number_of_episodes' => 'episodes',
	];

	protected $casts = [
		'episodes' => 'int',
	];

	protected function perform_insert() {
		$post_id = wp_insert_post([
			'post_title' => $this['title'],
			'post_excerpt' => $this['description'],
		]);

		$this->set_id( $post_id );

		return true;
	}

	protected function perform_update( array $changes ) {
		wp_update_post([
			'ID' => $this->get_id(),
			'post_title' => $this['title'],
			'post_excerpt' => $this['description'],
		]);

		return true;
	}

	protected function setup() {
		$this['title'] = $this->instance->post_title;
		$this['description'] = $this->instance->post_excerpt;
		$this['release'] = get_post_meta( $this->id, '_release_date', true );
	}
}
