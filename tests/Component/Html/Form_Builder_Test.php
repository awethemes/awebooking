<?php

use Awethemes\Http\Request;
use Awethemes\WP_Session\Store;
use AweBooking\Component\Html\Form_Builder;
use Mockery as m;

class Form_Builder_Test extends WP_UnitTestCase {
	/**
	 * @var Form_Builder
	 */
	protected $formBuilder;

	public function setUp() {
		parent::setUp();

		$request = Request::create( '/foo', 'GET', [
			'person'         => [
				'name'    => 'John',
				'surname' => 'Doe',
			],
			'agree'          => 1,
			'checkbox_array' => [ 1, 2, 3 ],
		] );

		$this->formBuilder = new Form_Builder( Request::create_from_base( $request ) );
	}

	public function testRequestValue() {
		$this->formBuilder->consider_request();
		$name    = $this->formBuilder->text( 'person[name]', 'Not John' );
		$surname = $this->formBuilder->text( 'person[surname]', 'Not Doe' );
		$this->assertEquals( '<input name="person[name]" type="text" value="John">', $name );
		$this->assertEquals( '<input name="person[surname]" type="text" value="Doe">', $surname );

		$checked   = $this->formBuilder->checkbox( 'agree', 1 );
		$unchecked = $this->formBuilder->checkbox( 'no_value', 1 );
		$this->assertEquals( '<input checked="checked" name="agree" type="checkbox" value="1">', $checked );
		$this->assertEquals( '<input name="no_value" type="checkbox" value="1">', $unchecked );

		$checked_array   = $this->formBuilder->checkbox( 'checkbox_array[]', 1 );
		$unchecked_array = $this->formBuilder->checkbox( 'checkbox_array[]', 4 );
		$this->assertEquals( '<input checked="checked" name="checkbox_array[]" type="checkbox" value="1">', $checked_array );
		$this->assertEquals( '<input name="checkbox_array[]" type="checkbox" value="4">', $unchecked_array );

		$checked   = $this->formBuilder->radio( 'agree', 1 );
		$unchecked = $this->formBuilder->radio( 'no_value', 1 );
		$this->assertEquals( '<input checked="checked" name="agree" type="radio" value="1">', $checked );
		$this->assertEquals( '<input name="no_value" type="radio" value="1">', $unchecked );

		// now we check that Request is ignored and value take precedence
		$this->formBuilder->consider_request( false );
		$name    = $this->formBuilder->text( 'person[name]', 'Not John' );
		$surname = $this->formBuilder->text( 'person[surname]', 'Not Doe' );
		$this->assertEquals( '<input name="person[name]" type="text" value="Not John">', $name );
		$this->assertEquals( '<input name="person[surname]" type="text" value="Not Doe">', $surname );
	}

	public function testFormLabel() {
		$form1 = $this->formBuilder->label( 'foo', 'Foobar' );
		$form2 = $this->formBuilder->label( 'foo', 'Foobar', [ 'class' => 'control-label' ] );
		$form3 = $this->formBuilder->label( 'foo', 'Foobar <i>bar</i>', null );

		$this->assertEquals( '<label for="foo">Foobar</label>', $form1 );
		$this->assertEquals( '<label for="foo" class="control-label">Foobar</label>', $form2 );
		$this->assertEquals( '<label for="foo">Foobar <i>bar</i></label>', $form3 );
	}

	public function testFormInput() {
		$form1 = $this->formBuilder->input( 'text', 'foo' );
		$form2 = $this->formBuilder->input( 'text', 'foo', 'foobar' );
		$form3 = $this->formBuilder->input( 'date', 'foobar', null, [ 'class' => 'span2' ] );
		$form4 = $this->formBuilder->input( 'hidden', 'foo', true );
		$form6 = $this->formBuilder->input( 'checkbox', 'foo-check', true );

		$this->assertEquals( '<input name="foo" type="text">', $form1 );
		$this->assertEquals( '<input name="foo" type="text" value="foobar">', $form2 );
		$this->assertEquals( '<input class="span2" name="foobar" type="date">', $form3 );
		$this->assertEquals( '<input name="foo" type="hidden" value="1">', $form4 );
		$this->assertEquals( '<input name="foo-check" type="checkbox" value="1">', $form6 );
	}

	public function testPasswordsNotFilled() {
		$this->formBuilder->set_session_store( $session = m::mock( Store::class ) );
		$session->shouldReceive( 'get_old_input' )->never();

		$form1 = $this->formBuilder->password( 'password' );
		$this->assertEquals( '<input name="password" type="password" value="">', $form1 );
	}

	public function testFilesNotFilled() {
		$this->formBuilder->set_session_store( $session = m::mock( Store::class ) );
		$session->shouldReceive( 'get_old_input' )->never();

		$form = $this->formBuilder->file( 'img' );
		$this->assertEquals( '<input name="img" type="file">', $form );
	}

	public function testFormText() {
		$form1 = $this->formBuilder->input( 'text', 'foo' );
		$form2 = $this->formBuilder->text( 'foo' );
		$form3 = $this->formBuilder->text( 'foo', 'foobar' );
		$form4 = $this->formBuilder->text( 'foo', null, [ 'class' => 'span2' ] );

		$this->assertEquals( '<input name="foo" type="text">', $form1 );
		$this->assertEquals( $form1, $form2 );
		$this->assertEquals( '<input name="foo" type="text" value="foobar">', $form3 );
		$this->assertEquals( '<input class="span2" name="foo" type="text">', $form4 );
	}

	public function testFormTextArray()
	{
		$form1 = $this->formBuilder->input('text', 'foo[]', 'testing');
		$form2 = $this->formBuilder->text('foo[]');

		$this->assertEquals('<input name="foo[]" type="text" value="testing">', $form1);
		$this->assertEquals('<input name="foo[]" type="text">', $form2);
	}

	public function testFormTextRepopulation() {
		$this->formBuilder->set_session_store( $session = m::mock( Store::class ) );
		$this->setModel( $model = [ 'relation' => [ 'key' => 'attribute' ], 'other' => 'val' ] );

		$session->shouldReceive( 'get_old_input' )->once()->with( 'name_with_dots' )->andReturn( 'some value' );
		$input = $this->formBuilder->text( 'name.with.dots', 'default value' );
		$this->assertEquals( '<input name="name.with.dots" type="text" value="some value">', $input );

		$session->shouldReceive( 'get_old_input' )->once()->with( 'text.key.sub' )->andReturn( null );
		$input = $this->formBuilder->text( 'text[key][sub]', 'default value' );
		$this->assertEquals( '<input name="text[key][sub]" type="text" value="default value">', $input );

		$session->shouldReceive( 'get_old_input' )->with( 'relation.key' )->andReturn( null );
		$input1 = $this->formBuilder->text( 'relation[key]' );

		$this->setModel( $model, false );
		$input2 = $this->formBuilder->text( 'relation[key]' );

		$this->assertEquals( '<input name="relation[key]" type="text" value="attribute">', $input1 );
		$this->assertEquals( $input1, $input2 );
	}

	public function testFormRepopulationWithMixOfArraysAndObjects() {
		$this->formBuilder->set_model( [ 'user' => (object) [ 'password' => 'apple' ] ] );
		$input = $this->formBuilder->text( 'user[password]' );
		$this->assertEquals( '<input name="user[password]" type="text" value="apple">', $input );

		$this->formBuilder->set_model( (object) [ 'letters' => [ 'a', 'b', 'c' ] ] );
		$input = $this->formBuilder->text( 'letters[1]' );
		$this->assertEquals( '<input name="letters[1]" type="text" value="b">', $input );
	}

	protected function setModel( array $data, $object = true ) {
		if ( $object ) {
			$data = new FormBuilderModelStub( $data );
		}

		$this->formBuilder->set_model( $data );
	}
}

class FormBuilderModelStub {
	protected $data;

	public function __construct( array $data = [] ) {
		foreach ( $data as $key => $val ) {
			if ( is_array( $val ) ) {
				$val = new self( $val );
			}

			$this->data[ $key ] = $val;
		}
	}

	public function __get( $key ) {
		return $this->data[ $key ];
	}

	public function __isset( $key ) {
		return isset( $this->data[ $key ] );
	}
}
