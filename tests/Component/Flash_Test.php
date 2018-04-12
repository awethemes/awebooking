<?php

use AweBooking\Component\Flash\Flash_Notifier;
use AweBooking\Component\Flash\WP_Sesstion_Store;

class Support_Flash_Test extends WP_UnitTestCase {

	protected $flash;
	protected $session;

	public function setUp() {
		parent::setUp();

		$this->session = Mockery::spy( WP_Sesstion_Store::class );
		$this->flash = new Flash_Notifier( $this->session );
	}

	public function test_can_interact_with_a_message_as_an_array() {
		$this->flash->add_message( 'Welcome Aboard', 'info' );

		$this->assertEquals( 'Welcome Aboard', $this->flash->messages[0]['message'] );
	}

	public function test_displays_default_flash_notifications() {
		$this->flash->add_message( 'Welcome Aboard' );

		$this->assertCount( 1, $this->flash->messages );

		$message = $this->flash->messages[0];

		$this->assertEquals( '', $message->title );
		$this->assertEquals( 'Welcome Aboard', $message->message );
		$this->assertEquals( 'info', $message->level );
		$this->assertEquals( false, $message->important );
		$this->assertEquals( false, $message->overlay );

		$this->assertSessionIsFlashed();
	}

	public function test_displays_multiple_flash_notifications() {
		$this->flash->add_message( 'Welcome Aboard' );
		$this->flash->add_message( 'Welcome Aboard Again' );

		$this->assertCount( 2, $this->flash->messages );

		$this->assertSessionIsFlashed( 2 );
	}

	public function test_displays_success_flash_notifications() {
		$this->flash->add_message( 'Welcome Aboard', 'success' );

		$message = $this->flash->messages[0];

		$this->assertEquals( '', $message->title );
		$this->assertEquals( 'Welcome Aboard', $message->message );
		$this->assertEquals( 'success', $message->level );
		$this->assertEquals( false, $message->important );
		$this->assertEquals( false, $message->overlay );

		$this->assertSessionIsFlashed();
	}

	public function test_displays_error_flash_notifications() {
		$this->flash->add_message( 'Uh Oh', 'error' );

		$message = $this->flash->messages[0];

		$this->assertEquals( '', $message->title );
		$this->assertEquals( 'Uh Oh', $message->message );
		$this->assertEquals( 'error', $message->level );
		$this->assertEquals( false, $message->important );
		$this->assertEquals( false, $message->overlay );

		$this->assertSessionIsFlashed();
	}

	public function test_displays_warning_flash_notifications() {
		$this->flash->add_message( 'Warning Warning', 'warning' );

		$message = $this->flash->messages[0];

		$this->assertEquals( '', $message->title );
		$this->assertEquals( 'Warning Warning', $message->message );
		$this->assertEquals( 'warning', $message->level );
		$this->assertEquals( false, $message->important );
		$this->assertEquals( false, $message->overlay );

		$this->assertSessionIsFlashed();
	}

	public function test_displays_important_flash_notifications() {
		$this->flash->add_message( 'Welcome Aboard' )->important();

		$message = $this->flash->messages[0];

		$this->assertEquals( '', $message->title );
		$this->assertEquals( 'Welcome Aboard', $message->message );
		$this->assertEquals( 'info', $message->level );
		$this->assertEquals( true, $message->important );
		$this->assertEquals( false, $message->overlay );

		$this->assertSessionIsFlashed();
	}

	public function test_builds_an_overlay_flash_notification() {
		$this->flash->add_message( 'Thank You' )->overlay();

		$message = $this->flash->messages[0];

		$this->assertEquals( '', $message->title );
		$this->assertEquals( 'Thank You', $message->message );
		$this->assertEquals( 'info', $message->level );
		$this->assertEquals( false, $message->important );
		$this->assertEquals( true, $message->overlay );

		$this->flash->clear();

		$this->flash->overlay( 'Overlay message.', 'Overlay Title' );

		$message = $this->flash->messages[0];

		$this->assertEquals( 'Overlay Title', $message->title );
		$this->assertEquals( 'Overlay message.', $message->message );
		$this->assertEquals( 'info', $message->level );
		$this->assertEquals( false, $message->important );
		$this->assertEquals( true, $message->overlay );

		$this->assertSessionIsFlashed( 2 );
	}

	public function test_clears_all_messages() {
		$this->flash->add_message( 'Welcome Aboard' );

		$this->assertCount( 1, $this->flash->messages );

		$this->flash->clear();

		$this->assertCount( 0, $this->flash->messages );
	}

	protected function assertSessionIsFlashed( $times = 1 ) {
		$this->session
			->shouldHaveReceived( 'flash' )
			->with( 'flash_notification', $this->flash->messages )
			->times( $times );
	}
}
