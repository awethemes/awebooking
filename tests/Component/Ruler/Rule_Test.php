<?php

use Ruler\Operator\LogicalAnd;
use AweBooking\Component\Ruler\Rule;
use AweBooking\Component\Ruler\Context;
use AweBooking\Component\Ruler\Variable;

class Rule_Test extends \WP_UnitTestCase {
	public function testBasic() {
		$vars = new Variable;
		$vars['days']->in(1, 2, 3);
		$vars['time']->equal('12:00');
	}
 }
