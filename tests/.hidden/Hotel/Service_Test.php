<?php

use AweBooking\Model\Service;

class Service_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testInsert() {
		$s = new Service;
		$s['name'] = 'Cleaning';
		$s['description'] = 'Nothing';
		$s['value'] = 15;
		$s['operation'] = Service::OP_ADD_DAILY;
		$s['type'] = Service::MANDATORY;
		$s->save();

		$term = get_term($s->get_id(), 'hotel_extra_service', ARRAY_A);
		$this->assertTrue($s->get_id() > 0);
		$this->assertEquals($term['term_id'], $s->get_id());
		$this->assertEquals($term['name'], $s->get_name());
		$this->assertEquals($term['description'], $s->get_description());
		$this->assertEquals(get_term_meta($s->get_id(), '_service_value', true), $s->get_value());
		$this->assertEquals(get_term_meta($s->get_id(), '_service_type', true), $s->get_type());
		$this->assertEquals(get_term_meta($s->get_id(), '_service_operation', true), $s->get_operation());
	}

	public function testUpdate() {
		$s = new Service;
		$s['name'] = 'Jon Snow';
		$s['description'] = 'Nothing';
		$s->save();

		$term = get_term($s->get_id(), 'hotel_extra_service', ARRAY_A);
		$this->assertEquals($term['term_id'], $s->get_id());
		$this->assertEquals($term['name'], $s->get_name());
		$this->assertEquals($term['description'], $s->get_description());

		$s['name'] = 'Aegon Targaryen';
		$s['description'] = '';
		$s->save();

		$term = get_term($s->get_id(), 'hotel_extra_service', ARRAY_A);
		$this->assertEquals($term['term_id'], $s->get_id());
		$this->assertEquals($term['name'], $s->get_name());
	}

	public function testDelete() {
		$s = new Service;
		$s['name'] = 'Jon Snow';
		$s['description'] = 'Nothing';

		$s->save();
		$s->delete();

		$this->assertNull(get_term($s->get_id(), 'hotel_extra_service'));
	}
}
