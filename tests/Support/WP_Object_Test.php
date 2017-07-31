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

	public function testMapping() {
		$a = new AweBooking_Post_WP_Object( $this->got );

		$this->assertArrayHasKey('release', $a->get_mapping());
		$this->assertArrayHasKey('episodes', $a->get_mapping());

		$this->assertTrue($a->has_mapping());
		$this->assertTrue($a->has_mapping('release'));
		$this->assertTrue($a->has_mapping('episodes'));
		$this->assertTrue($a->has_mapping('episodes', 'release'));
		$this->assertTrue($a->has_mapping(['title', 'episodes']));
		$this->assertFalse($a->has_mapping('title'));

		$this->assertNull($a->get_mapping_metakey('title'));
		$this->assertEquals($a->get_mapping_metakey('episodes'), '_number_of_episodes');
		$this->assertEquals($a->get_mapping_metakey('release'), '_number_release');
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

		$this->assertTrue($object->is_dirty( 'title' ));
		$this->assertFalse($object->is_clean( 'title' ));
		$this->assertTrue($object->is_dirty( 'description' ));
		$this->assertFalse($object->is_clean( 'description' ));
		$this->assertTrue($object->is_dirty(['title', 'description']));
		$this->assertFalse($object->is_clean('title', 'description'));

		$this->assertFalse($object->is_dirty('release'));
		$this->assertTrue($object->is_clean('release'));
		$this->assertFalse($object->is_dirty('episodes'));
		$this->assertTrue($object->is_clean('episodes'));
		$this->assertFalse($object->is_dirty(['release', 'episodes']));
		$this->assertTrue($object->is_clean('release', 'episodes'));

		$this->assertInternalType('array', $object->get_dirty());
		$this->assertArrayHasKey('title', $object->get_dirty());
		$this->assertArrayHasKey('description', $object->get_dirty());

		$this->assertContains('title', $object->test_get_changes_only($object->get_dirty(), 'title'));
		$this->assertNotContains('description', $object->test_get_changes_only($object->get_dirty(), 'title'));
		$this->assertContains('title', $object->test_get_changes_only($object->get_dirty(), ['title', 'description']));
		$this->assertContains('description', $object->test_get_changes_only($object->get_dirty(), ['title', 'description']));
		$this->assertEmpty($object->test_get_changes_only($object->get_dirty(), ['release', 'episodes']));

		$object->save();

		$this->assertTrue($object->was_changed('title'));
		$this->assertTrue($object->was_changed('description'));
		$this->assertFalse($object->was_changed('release'));
		$this->assertFalse($object->was_changed('episodes'));

		$this->assertInternalType('array', $object->get_changes());
		$this->assertArrayHasKey('title', $object->get_changes());
		$this->assertArrayHasKey('description', $object->get_changes());
		$this->assertArrayNotHasKey('release', $object->get_changes());
		$this->assertArrayNotHasKey('episodes', $object->get_changes());
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

	public function testMixed() {
		$a = new AweBooking_Post_WP_Object;
		$a->fill([
			'title'        => 'Game of Thrones',
			'description'  => 'Season 7',
			'episodes'     => 7,
			'release'      => '7-2017',
		])->save();

		$this->assertEquals($a['episodes'], get_post_meta($a->get_id(), '_number_of_episodes', true));
		$this->assertEquals($a['release'], get_post_meta($a->get_id(), '_number_release', true));

		$a['episodes'] = 8;
		$a['description'] = 'Season 8';
		$a->update_meta('_number_release', '08-2018');
		$a->save();

		$this->assertEquals($a['release'], '08-2018');
		$this->assertEquals($a['description'], 'Season 8');
		$this->assertEquals(get_post_meta($a->get_id(), '_number_release', true), '08-2018');
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
		'episodes' => '_number_of_episodes',
		'release'  => '_number_release',
	];

	protected $casts = [
		'episodes' => 'int',
	];

	protected function perform_insert() {
		$post_id = wp_insert_post([
			'post_title' => $this['title'],
			'post_excerpt' => $this['description'],
		]);

		return $post_id;
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
	}

	public function test_get_changes_only($a, $b) {
		return $this->get_changes_only($a, $b);
	}
}
