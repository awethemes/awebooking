<?php


use Awethemes\Relationships\Direction\Directed;

class Core_Test extends WP_UnitTestCase {
	/**
	 * @var \Awethemes\Relationships\Manager
	 */
	protected $relationships;

	/**
	 * @var \Awethemes\Relationships\Storage
	 */
	protected $storage;

	function setUp() {
		parent::setUp();

		$this->relationships = _get_rel_test();
		$this->storage = $this->relationships->get_storage();

		foreach ( [ 'actor', 'movie', 'director' ] as $p_type ) {
			register_post_type( $p_type, [ 'public' => true, 'label' => $p_type ] );
		}

		$this->relationships->register( 'posts_to_users', 'post', 'user' );

		$this->relationships->register( 'actor_to_movie', 'actor', 'movie' );

		$this->relationships->register( 'director_to_movie', 'director', 'movie', [
			'cardinality' => 'one-to-many',
		]);
	}

	public function test_manager() {
		$this->assertNull( $this->relationships->get( 'none' ) );
		$this->assertNotNull( $this->relationships->get( 'actor_to_movie' ) );
		$this->assertNotNull( $this->relationships->get( 'director_to_movie' ) );
	}

	public function testGetSide() {
		$rel = $this->relationships->get( 'actor_to_movie' );
		$rel2 = $this->relationships->get( 'director_to_movie' );

		$this->assertInstanceOf( \Awethemes\Relationships\Side\Post_Side::class, $rel->get_side( 'from' ));
		$this->assertInstanceOf( \Awethemes\Relationships\Side\Post_Side::class, $rel->get_side( 'to' ));
		$this->assertEquals( 'post', $rel->get_side( 'from' )->get_object_type() );
		$this->assertEquals( 'post', $rel->get_side( 'to' )->get_object_type() );
		$this->assertEquals( 'actor', $rel->get_side( 'from' )->get_post_type() );
		$this->assertEquals( 'movie', $rel->get_side( 'to' )->get_post_type() );
		$this->assertEquals( 'many', $rel->get_side( 'from' )->get_cardinality() );
		$this->assertEquals( 'many', $rel->get_side( 'to' )->get_cardinality() );

		$this->assertInstanceOf( \Awethemes\Relationships\Side\Post_Side::class, $rel2->get_side( 'from' ));
		$this->assertInstanceOf( \Awethemes\Relationships\Side\Post_Side::class, $rel2->get_side( 'to' ));
		$this->assertEquals( 'post', $rel2->get_side( 'from' )->get_object_type() );
		$this->assertEquals( 'post', $rel2->get_side( 'to' )->get_object_type() );
		$this->assertEquals( 'director', $rel2->get_side( 'from' )->get_post_type() );
		$this->assertEquals( 'movie', $rel2->get_side( 'to' )->get_post_type() );
		$this->assertEquals( 'one', $rel2->get_side( 'from' )->get_cardinality() );
		$this->assertEquals( 'many', $rel2->get_side( 'to' )->get_cardinality() );
	}

	public function testFindDirection() {
		$rel   = $this->relationships->get( 'actor_to_movie' );
		$actor = $this->generate_post( 'actor' );
		$movie = $this->generate_post( 'movie' );

		$this->assertEquals( 'from', $rel->find_direction( $actor ) );
		$this->assertEquals( 'to', $rel->find_direction( $movie ) );
		$this->assertNull( $rel->find_direction( 0 ) );
	}

	function testDirectionUser() {
		$ctype = $this->relationships->get( 'posts_to_users' );

		$post = $this->generate_post();
		$user = $this->generate_user();

		$this->assertEquals( 'from', $ctype->find_direction( $post ) );
		$this->assertEquals( 'to', $ctype->find_direction( $user ) );
	}

	public function testConnect() {
		$rel   = $this->relationships->get( 'actor_to_movie' );
		$actor = $this->generate_post( 'actor' );
		$movie = $this->generate_post( 'movie' );

		$this->assertInternalType( 'int', $rel->connect( $actor, $movie ) );
		$this->assertIsWPError( $rel->connect( $actor, $movie ), 'duplicate_connection' );

		// TODO...
	}

	public function testQuery() {
		$rel   = $this->relationships->get( 'actor_to_movie' );

		$actor = $this->generate_post( 'actor' );
		$movie = $this->generate_post( 'movie' );
		$movie2 = $this->generate_post( 'movie' );

		$connect_id = $rel->connect( $actor, $movie );
		// $connect_id2 = $rel->connect( $actor, $movie2 );

		$this->assertTrue( $connect_id > 0 );
		// $this->assertTrue( $connect_id2 > 0 );

		$query = get_posts([
			'relationship' => [
				'name' => 'actor_to_movie',
				'to' => $movie->ID,
			],
		]);

		global $wpdb;
		dump($wpdb->last_query);
		dump($query);

		dump($this->debug());
	}

	public function testDirected() {
		$rel = $this->relationships->get( 'director_to_movie' );

		$directed_from = new Directed( $rel, 'from' );
		$this->assertEquals( 'director', $directed_from->get_current()->get_post_type() );
		$this->assertEquals( 'one', $directed_from->get_current()->get_cardinality() );
		$this->assertEquals( 'movie', $directed_from->get_opposite()->get_post_type() );
		$this->assertEquals( 'many', $directed_from->get_opposite()->get_cardinality() );

		$flip_directed_from = $directed_from->flip_direction();
		$this->assertEquals( 'movie', $flip_directed_from->get_current()->get_post_type() );
		$this->assertEquals( 'many', $flip_directed_from->get_current()->get_cardinality() );
		$this->assertEquals( 'director', $flip_directed_from->get_opposite()->get_post_type() );
		$this->assertEquals( 'one', $flip_directed_from->get_opposite()->get_cardinality() );

		$directed_to   = new Directed( $rel, 'to' );
		$directed_any  = new Directed( $rel, 'any' );

		// TODO...
	}

	/**
	 * @expectedException \OutOfBoundsException
	 */
	public function testInvalidDirected() {
		$rel = $this->relationships->get( 'actor_to_movie' );
		$rel->get_direction('ola');
	}

	protected function assertIsWPError($actual, $error) {
		$this->assertWPError($actual);
		$this->assertEquals( $error, $actual->get_error_code(), 'The error code is not same');
	}

	protected function generate_posts( $type, $count ) {
		return $this->factory->post->create_many( $count, [
			'post_type' => $type,
		] );
	}

	protected function generate_post( $type = 'post' ) {
		return $this->factory->post->create_and_get( [
			'post_type' => $type,
		] );
	}

	protected function generate_user() {
		return $this->factory->user->create_and_get();
	}

	public function debug() {
		global $wpdb;

		return $wpdb->get_results("SELECT * FROM {$wpdb->p2p_relationships}");
	}
}
