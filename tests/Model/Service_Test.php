<?php

use AweBooking\Model\Service;

class Service_Test extends WP_UnitTestCase {
	public function testCreateNew() {
		$service = new Service();
		$service['name'] = 'Wifi';
		$service['description'] = 'AAA';
		$service['operation'] = 'add';
		$service['amount'] = '100';
		$service->save();
		$this->assertService( $service );
	}

	protected function assertService(Service $service) {
		$post = get_post( $service->get_id() );
		$this->assertTrue( $service->exists() );
		$this->assertEquals( $post->post_title, $service->get('name') );
		$this->assertEquals( $post->post_excerpt, $service->get('description') );
		$this->assertEquals( $post->post_date, $service->get('date_created') );
		$this->assertEquals( $post->post_modified, $service->get('date_modified') );
		$this->assertEquals( $post->post_status, $service->get('status') );
		$this->assertEquals( get_post_meta( $post->ID, '_operation', true), $service->get('operation') );
		$this->assertEquals( get_post_meta( $post->ID, '_amount', true), $service->get('amount') );
	}
}
