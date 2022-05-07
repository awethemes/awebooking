<?php

namespace AweBooking\System\Calendar\Store;

use AweBooking\Vendor\Roomify\Bat\Store\StoreInterface;

interface StoreFactoryInterface
{
	public function create(string $name, string $source, array $options = []): StoreInterface;
}
