<?php

use AweBooking\Admin\Forms\Form_Abstract;

class Test_Admin_Form extends Form_Abstract {
	protected $form_id = 'asdfghjkl';
	public function register_fields() {
		$this->add_field([
			'id' => 'id_1',
			'type' => 'text',
			'default' => 1,
		]);

		$this->add_field([
			'id' => 'id_2',
			'type' => 'select',
		]);
	}
}

class Form_Abstract_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
		$this->form = new Test_Admin_Form;
	}

	public function testArrayAccess() {
		$this->assertNotNull($this->form['id_1']);
		$this->assertNotNull($this->form['id_2']);
		$this->assertNull($this->form['noexists']);

		$this->assertTrue(isset($this->form['id_1']));
		$this->assertTrue(isset($this->form['id_2']));
		$this->assertFalse(isset($this->form['noexists']));

		$this->form['id_3'] = [
			'type' => 'checkbox',
			'name' => 'Are you sure?'
		];

		$this->assertTrue(isset($this->form['id_3']));
		$this->assertNotNull($this->form['id_3']);

		unset($this->form['id_3']);
		$this->assertNull($this->form['id_3']);
		$this->assertFalse(isset($this->form['id_3']));
	}

	public function testProxyField() {
		$field1 = $this->form['id_1'];

		$this->assertInstanceOf('AweBooking\\Admin\\Forms\\Field_Proxy', $field1);
		$this->assertEquals($field1, $this->form->get_field('id_1'));
		$this->assertEquals($field1->value(), null);
		$this->assertEquals($field1->get_value(), 1);

		$field1->value = 100;
		$this->assertEquals($field1->value, 100);
		$this->assertEquals($field1->value(), 100);
		$this->assertEquals($field1->get_value(), 100);
	}
}
