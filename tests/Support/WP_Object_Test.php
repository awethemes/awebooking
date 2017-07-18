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

	public function testChanges() {
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

	public function test_save() {

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

	protected function setup() {
		$this['title'] = $this->instance->post_title;
		$this['description'] = $this->instance->post_excerpt;
		$this['release'] = get_post_meta( $this->id, '_release_date', true );
	}
}
