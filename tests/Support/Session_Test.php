<?php

use Mockery as m;

class SessionStoreTest extends WP_UnitTestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testSessionIsLoadedFromHandler()
    {
        $session = $this->getSession();
        $session->get_handler()->shouldReceive('read')->once()->with($this->getSessionId())->andReturn(['foo' => 'bar', 'bagged' => ['name' => 'taylor']]);
        $session->start();

        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals('baz', $session->get('bar', 'baz'));
        $this->assertTrue($session->has('foo'));
        $this->assertFalse($session->has('bar'));
        $this->assertTrue($session->is_started());

        $session->put('baz', 'boom');
        $this->assertTrue($session->has('baz'));
    }

    public function testSessionMigration()
    {
        $session = $this->getSession();
        $oldId = $session->get_id();
        $session->get_handler()->shouldReceive('destroy')->never();
        $this->assertTrue($session->regenerate());
        $this->assertNotEquals($oldId, $session->get_id());

        $session = $this->getSession();
        $oldId = $session->get_id();
        $session->get_handler()->shouldReceive('destroy')->once()->with($oldId);
        $this->assertTrue($session->regenerate(true));
        $this->assertNotEquals($oldId, $session->get_id());
    }

    public function testCantSetInvalidId()
    {
        $session = $this->getSession();
        $this->assertTrue($session->is_valid_id($session->get_id()));

        $session->set_id(null);
        $this->assertNotNull($session->get_id());
        $this->assertTrue($session->is_valid_id($session->get_id()));

        $session->set_id(['a']);
        $this->assertNotSame(['a'], $session->get_id());

        $session->set_id('wrong');
        $this->assertNotEquals('wrong', $session->get_id());
    }

    public function testSessionInvalidate()
    {
        $session = $this->getSession();
        $oldId = $session->get_id();

        $session->put('foo', 'bar');
        $this->assertGreaterThan(0, count($session->all()));

        $session->flash('name', 'Taylor');
        $this->assertTrue($session->has('name'));

        $session->get_handler()->shouldReceive('destroy')->once()->with($oldId);
        $this->assertTrue($session->invalidate());

        $this->assertFalse($session->has('name'));
        $this->assertNotEquals($oldId, $session->get_id());
        $this->assertCount(0, $session->all());
    }

    public function testSessionIsProperlySaved()
    {
        $session = $this->getSession();
        $session->get_handler()->shouldReceive('read')->once()->andReturn([]);
        $session->start();
        $session->put('foo', 'bar');
        $session->flash('baz', 'boom');
        $session->now('qux', 'norf');
        $session->get_handler()->shouldReceive('write')->once()->with(
            $this->getSessionId(),
            [
                'foo' => 'bar',
                'baz' => 'boom',
                '_flash' => [
                    'new' => [],
                    'old' => ['baz'],
                ],
            ]
        );
        $session->save();

        $this->assertFalse($session->is_started());
    }

    public function testOldInputFlashing()
    {
        $session = $this->getSession();
        $session->put('boom', 'baz');
        $session->flash_input(['foo' => 'bar', 'bar' => 0]);

        $this->assertTrue($session->has_old_input('foo'));
        $this->assertEquals('bar', $session->get_old_input('foo'));
        $this->assertEquals(0, $session->get_old_input('bar'));
        $this->assertFalse($session->has_old_input('boom'));

        $session->age_flash_data();

        $this->assertTrue($session->has_old_input('foo'));
        $this->assertEquals('bar', $session->get_old_input('foo'));
        $this->assertEquals(0, $session->get_old_input('bar'));
        $this->assertFalse($session->has_old_input('boom'));
    }

    public function testDataFlashing()
    {
        $session = $this->getSession();
        $session->flash('foo', 'bar');
        $session->flash('bar', 0);
        $session->flash('baz');

        $this->assertTrue($session->has('foo'));
        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals(0, $session->get('bar'));
        $this->assertTrue($session->get('baz'));

        $session->age_flash_data();

        $this->assertTrue($session->has('foo'));
        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals(0, $session->get('bar'));

        $session->age_flash_data();

        $this->assertFalse($session->has('foo'));
        $this->assertNull($session->get('foo'));
    }

    public function testDataFlashingNow()
    {
        $session = $this->getSession();
        $session->now('foo', 'bar');
        $session->now('bar', 0);

        $this->assertTrue($session->has('foo'));
        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals(0, $session->get('bar'));

        $session->age_flash_data();

        $this->assertFalse($session->has('foo'));
        $this->assertNull($session->get('foo'));
    }

    public function testDataMergeNewFlashes()
    {
        $session = $this->getSession();
        $session->flash('foo', 'bar');
        $session->put('fu', 'baz');
        $session->put('_flash.old', ['qu']);
        $this->assertNotFalse(array_search('foo', $session->get('_flash.new')));
        $this->assertFalse(array_search('fu', $session->get('_flash.new')));
        $session->keep(['fu', 'qu']);
        $this->assertNotFalse(array_search('foo', $session->get('_flash.new')));
        $this->assertNotFalse(array_search('fu', $session->get('_flash.new')));
        $this->assertNotFalse(array_search('qu', $session->get('_flash.new')));
        $this->assertFalse(array_search('qu', $session->get('_flash.old')));
    }

    public function testReflash()
    {
        $session = $this->getSession();
        $session->flash('foo', 'bar');
        $session->put('_flash.old', ['foo']);
        $session->reflash();
        $this->assertNotFalse(array_search('foo', $session->get('_flash.new')));
        $this->assertFalse(array_search('foo', $session->get('_flash.old')));
    }

    public function testReflashWithNow()
    {
        $session = $this->getSession();
        $session->now('foo', 'bar');
        $session->reflash();
        $this->assertNotFalse(array_search('foo', $session->get('_flash.new')));
        $this->assertFalse(array_search('foo', $session->get('_flash.old')));
    }

    public function testReplace()
    {
        $session = $this->getSession();
        $session->put('foo', 'bar');
        $session->put('qu', 'ux');
        $session->replace(['foo' => 'baz']);
        $this->assertEquals('baz', $session->get('foo'));
        $this->assertEquals('ux', $session->get('qu'));
    }

    public function testRemove()
    {
        $session = $this->getSession();
        $session->put('foo', 'bar');
        $pulled = $session->remove('foo');
        $this->assertFalse($session->has('foo'));
        $this->assertEquals('bar', $pulled);
    }

    public function testClear()
    {
        $session = $this->getSession();
        $session->put('foo', 'bar');

        $session->flush();
        $this->assertFalse($session->has('foo'));

        $session->put('foo', 'bar');

        $session->flush();
        $this->assertFalse($session->has('foo'));
    }

    public function testIncrement()
    {
        $session = $this->getSession();

        $session->put('foo', 5);
        $foo = $session->increment('foo');
        $this->assertEquals(6, $foo);
        $this->assertEquals(6, $session->get('foo'));

        $foo = $session->increment('foo', 4);
        $this->assertEquals(10, $foo);
        $this->assertEquals(10, $session->get('foo'));

        $session->increment('bar');
        $this->assertEquals(1, $session->get('bar'));
    }

    public function testDecrement()
    {
        $session = $this->getSession();

        $session->put('foo', 5);
        $foo = $session->decrement('foo');
        $this->assertEquals(4, $foo);
        $this->assertEquals(4, $session->get('foo'));

        $foo = $session->decrement('foo', 4);
        $this->assertEquals(0, $foo);
        $this->assertEquals(0, $session->get('foo'));

        $session->decrement('bar');
        $this->assertEquals(-1, $session->get('bar'));
    }

    public function testHasOldInputWithoutKey()
    {
        $session = $this->getSession();
        $session->flash('boom', 'baz');
        $this->assertFalse($session->has_old_input());

        $session->flash_input(['foo' => 'bar']);
        $this->assertTrue($session->has_old_input());
    }

    public function testName()
    {
        $session = $this->getSession();
        $this->assertEquals($session->get_name(), $this->getSessionName());
        $session->set_name('foo');
        $this->assertEquals($session->get_name(), 'foo');
    }

    public function testKeyExists()
    {
        $session = $this->getSession();
        $session->put('foo', 'bar');
        $this->assertTrue($session->exists('foo'));
        $session->put('baz', null);
        $this->assertFalse($session->has('baz'));
        $this->assertTrue($session->exists('baz'));
        $this->assertFalse($session->exists('bogus'));
        $this->assertTrue($session->exists(['foo', 'baz']));
        $this->assertFalse($session->exists(['foo', 'baz', 'bogus']));
    }

    public function testCountable() {
        $session = $this->getSession();
        $session->put('a', 5);
        $session->put('b', 5);
        $this->assertEquals(2, count($session));
    }

    public function testArrayable() {
        $session = $this->getSession();
        $session->put('a', 5);
        $session['b'] = 100;

        $this->assertTrue(isset($session['b']));
        $this->assertFalse(isset($session['bababa']));

        $this->assertEquals(5, $session['a']);
        $this->assertEquals(100, $session['b']);

        unset($session['b']);
        $this->assertFalse(isset($session['b']));
    }

    public function getSession()
    {
        $reflection = new ReflectionClass('AweBooking\Session\Store');

        return $reflection->newInstanceArgs($this->getMocks());
    }

    public function getMocks()
    {
        return [
            $this->getSessionName(),
            m::mock('SessionHandlerInterface'),
            $this->getSessionId(),
        ];
    }

    public function getSessionId()
    {
        return 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    }

    public function getSessionName()
    {
        return 'name';
    }
}
