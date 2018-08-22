<?php

use AweBooking\Plugin;
use Awethemes\Http\Request;
use AweBooking\Component\Routing\Url_Generator;

class Component_Routing_Uri_Generator_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		$this->set_permalink_structure( '' );
	}

	public function testToUrl() {
		$url = $this->create_url_generator( Request::create(WP_TESTS_DOMAIN, 'GET' ) );

		$this->assertEquals( 'http://anhskohbo.example', $url->to('http://anhskohbo.example') );
		$this->assertEquals( 'ftp://anhskohbo.example', $url->to('ftp://anhskohbo.example') );

		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN, $url->to() );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/', $url->to('/') );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/path/to/this', $url->to('/path/to/this') );

		// With the parameters.
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/?name=1&do=2', $url->to('/', ['name' => 1, 'do' => 2]) );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/path/to/this?name=1&do=2', $url->to('/path/to/this', ['name' => 1, 'do' => 2]) );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/?aaaa=hello&do=2&name=1', $url->to('/?aaaa=hello&do=1', ['name' => 1, 'do' => 2]) );

		// With the scheme.
		$this->assertEquals( 'https://' . WP_TESTS_DOMAIN, $url->to('', [], 'https' ) );
	}

	public function testBasicRouteUrl() {
		$url = $this->create_url_generator( Request::create(WP_TESTS_DOMAIN, 'GET' ) );

		$this->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/awebooking-route/', $url->route() );

		$this->set_permalink_structure( '/index.php/%year%/%monthnum%/%day%/%postname%/' );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/index.php/awebooking-route/', $url->route() );

		$this->set_permalink_structure( '' );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/index.php?awebooking_route=/', $url->route() );
	}

	public function testRouteUrlWithSlash() {
		$url = $this->create_url_generator( Request::create(WP_TESTS_DOMAIN, 'GET' ) );

		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/index.php?awebooking_route=/', $url->route( '' ) );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/index.php?awebooking_route=/admin/123', $url->route( 'admin/123' ) );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/index.php?awebooking_route=/admin/123', $url->route( '/admin/123' ) );
	}

	/*public function testRouterUrlWithScheme() {
		$url = $this->create_url_generator( Request::create(WP_TESTS_DOMAIN, 'GET' ) );
		$this->assertSame( 'http', parse_url( $url->route('/', [] ), PHP_URL_SCHEME ) );
		$this->assertSame( 'http', parse_url( $url->route('/', [], false ), PHP_URL_SCHEME ) );
		$this->assertSame( 'https', parse_url( $url->route('/', [], true ), PHP_URL_SCHEME ) );
	}*/

	public function testAdminRouteUrl() {
		// Just for test.
		$this->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );

		$url = $this->create_url_generator( Request::create(WP_TESTS_DOMAIN, 'GET' ) );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/wp-admin/admin.php?awebooking=/', $url->admin_route() );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/wp-admin/admin.php?awebooking=/', $url->admin_route( '' ) );

		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/wp-admin/admin.php?awebooking=/abc/123', $url->admin_route( '/abc/123' ) );
		$this->assertEquals( 'http://' . WP_TESTS_DOMAIN . '/wp-admin/admin.php?awebooking=/abc/123', $url->admin_route( 'abc/123' ) );
	}

	protected function create_url_generator( $request ) {
		$awebooking = Plugin::get_instance();

		$awebooking->instance('request', $request );

		return new Url_Generator( $awebooking );
	}
}
