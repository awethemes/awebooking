<?php

namespace AweBooking\System\Inertia;

use AweBooking\System\Container;
use Closure;

class LazyProp
{
	/**
	 * @var Closure
	 */
	protected $callback;

	/**
	 * @param Closure $callback
	 */
	public function __construct(Closure $callback)
	{
		$this->callback = $callback;
	}

	/**
	 * Invoke the prop data.
	 *
	 * @return mixed
	 */
	public function __invoke()
	{
		return Container::getInstance()->call($this->callback);
	}
}
