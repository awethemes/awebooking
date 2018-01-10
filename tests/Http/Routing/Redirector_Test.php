<?php

use Awethemes\Http\Request;
use Awethemes\Http\Redirect_Response;
use AweBooking\Http\Routing\Redirector;
use AweBooking\Http\Routing\Url_Generator;

class Http_Routing_Redirector_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->headers = Mockery::mock('Symfony\Component\HttpFoundation\HeaderBag');

		$this->request = Mockery::mock(Request::class);
		$this->request->headers = $this->headers;

		$this->url = $this->create_url_generator( $this->request );
		$this->session = Mockery::mock('Awethemes\WP_Session\Session');

		$this->redirect = new Redirector($this->url);
		$this->redirect->set_wp_session($this->session);
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();
	}

	protected function create_url_generator( $request ) {
		$awebooking = AweBooking::get_instance();
		$awebooking->instance('request', $request );

		return new Url_Generator( $awebooking );
	}

	public function testBasicRedirectTo() {
		$response = $this->redirect->to('bar');
		$this->assertInstanceOf(Redirect_Response::class, $response);
		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/bar', $response->getTargetUrl());
		$this->assertEquals(302, $response->getStatusCode());
		$this->assertEquals($this->session, $response->get_session());
	}

	public function testBasicRedirectToWithFullUrl() {
		$response = $this->redirect->to('http://' . WP_TESTS_DOMAIN);
		$this->assertEquals('http://' . WP_TESTS_DOMAIN, $response->getTargetUrl());
	}

	public function testComplexRedirectTo() {
		$response = $this->redirect->to('bar', 303, ['X-RateLimit-Limit' => 60, 'X-RateLimit-Remaining' => 59]);

		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/bar', $response->getTargetUrl());
		$this->assertEquals(303, $response->getStatusCode());
		$this->assertEquals(60, $response->headers->get('X-RateLimit-Limit'));
		$this->assertEquals(59, $response->headers->get('X-RateLimit-Remaining'));
	}

	public function testRedirectHome() {
		$response = $this->redirect->home( 301 );
		$this->assertEquals('http://' . WP_TESTS_DOMAIN, $response->getTargetUrl());
		$this->assertEquals(301, $response->getStatusCode());
	}

	public function testRedirectAdmin() {
		$response = $this->redirect->admin();
		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/wp-admin/', $response->getTargetUrl());

		$response = $this->redirect->admin( 'tool.php', 301 );
		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/wp-admin/tool.php', $response->getTargetUrl());
		$this->assertEquals(301, $response->getStatusCode());
	}

	public function testBackRedirectToHttpReferer() {
		$_SERVER['HTTP_REFERER'] = 'http://'.WP_TESTS_DOMAIN.'/bar/test/112';
		$response = $this->redirect->back();
		$this->assertEquals('http://'.WP_TESTS_DOMAIN.'/bar/test/112', $response->getTargetUrl());
	}

	public function testRedirectSiteRoute() {
		$this->set_permalink_structure( '' );
		$response = $this->redirect->route();
		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/index.php?awebooking_route=/', $response->getTargetUrl());

		$response = $this->redirect->route( '/', [ 'foo' => 'bar' ]);
		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/index.php?awebooking_route=%2F&foo=bar', $response->getTargetUrl());

		// ===========
		$this->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );
		$response = $this->redirect->route();
		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/awebooking-route/', $response->getTargetUrl());

		$response = $this->redirect->route( '/', [ 'foo' => 'bar' ]);
		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/awebooking-route/?foo=bar', $response->getTargetUrl());
	}

	public function testRedirectAdminRoute() {
		$response = $this->redirect->admin_route();
		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/wp-admin/admin.php?awebooking=/', $response->getTargetUrl());

		$response = $this->redirect->admin_route( '/path/to' );
		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/wp-admin/admin.php?awebooking=/path/to', $response->getTargetUrl());

		$response = $this->redirect->admin_route( '/path/to', [ 'with' => 'args' ] );
		$this->assertEquals('http://' . WP_TESTS_DOMAIN . '/wp-admin/admin.php?awebooking=%2Fpath%2Fto&with=args', $response->getTargetUrl());
	}
}
