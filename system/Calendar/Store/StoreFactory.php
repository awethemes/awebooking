<?php

namespace AweBooking\System\Calendar\Store;

use AweBooking\Vendor\Roomify\Bat\Store\StoreInterface;

class StoreFactory implements StoreFactoryInterface
{
	/**
	 * @var class-string<StoreInterface>
	 */
	private $storeClassName;

	/**
	 * @param string $storeClassName
	 */
	public function __construct(string $storeClassName = DatabaseStore::class)
	{
		$this->storeClassName = $storeClassName;
	}

	/**
	 * {@inheritdoc}
	 */
	public function create(string $name, string $source, array $options = []): StoreInterface
	{
		$className = $this->storeClassName;

		return new $className($name, $source, $options);
	}
}
