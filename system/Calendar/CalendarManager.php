<?php

namespace AweBooking\System\Calendar;

use AweBooking\System\Calendar\Store\StoreFactory;
use AweBooking\System\Container;

class CalendarManager
{
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @param Container $container The container instance.
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;

		$this->container->singleton(StoreFactory::class, function () {
			return new StoreFactory();
		});
	}

	public function getCalendarFactory(string $code): CalendarFactory
	{
		return new CalendarFactory(
			$this->getEventType($code),
			$this->container->get(StoreFactory::class)
		);
	}

	public function getEventType(string $code)
	{
		return $this->container->get('calendar.event_types.' . $code);
	}

	public function registerEventType(EventTypeInterface $eventType): self
	{
		$wrap = $eventType instanceof EventTypeBuilder
			? static function () use ($eventType) {
				return $eventType->toEventType();
			}
			: $eventType;

		$this->container['calendar.event_types.' . $eventType->getCode()] = $wrap;

		return $this;
	}

	public function registerHourlyEventType(string $code, string $type = null): EventTypeBuilder
	{
		$this->registerEventType($builder = EventTypeBuilder::hourly($code, $type));

		return $builder;
	}

	public function registerDailyEventType(string $code, string $type = null): EventTypeBuilder
	{
		$this->registerEventType($builder = EventTypeBuilder::daily($code, $type));

		return $builder;
	}
}
