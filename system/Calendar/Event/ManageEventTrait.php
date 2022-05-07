<?php

namespace AweBooking\System\Calendar\Entry;

use AweBooking\System\Calendar\EventTypeInterface;
use AweBooking\System\Calendar\EventTypeRegistryInterface;
use AweBooking\System\Container;
use AweBooking\System\Database\Model;
use DateTimeInterface;

trait ManageEventTrait
{
    /**
     * Bootstrap the trait.
     */
    public static function bootManageEventTrait()
    {
        static::saved(function (self $model) {
            $model->getEventType()->storeBatEvent($model);
        });

        static::deleting(function (self $model) {
            $model->getEventType()->storeBatEvent($model, true);
        });
    }

    /**
     * @return \AweBooking\Vendor\Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function unit()
    {
        return $this->morphTo('unit');
    }

    /**
     * @return \AweBooking\Vendor\Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reference()
    {
        return $this->morphTo('reference');
    }

    public function getEventType(): EventTypeInterface
    {
        return $this->getEventTypeRegistry()->get($this->event_type);
    }

    public function setEventType(EventTypeInterface $eventType)
    {
        $this->setAttribute('event_type', $eventType->getName());
    }

    public function getId()
    {
        return $this->getKey();
    }

    public function getUnit()
    {
        return $this->getRelationValue('unit');
    }

    public function getUnitId()
    {
        return $this->getAttribute('unit_id');
    }

    public function setUnit(object $unit)
    {
        if ($unit instanceof Model) {
            $this->unit()->associate($unit);
        }
    }

    public function getValue(): ?int
    {
        return $this->getAttribute('value');
    }

    public function setValue(int $value)
    {
        $this->setAttribute('value', $value);
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->getAttribute('start_date');
    }

    public function setStartDate(DateTimeInterface $date)
    {
        $this->setAttribute('start_date', $date);
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->getAttribute('end_date');
    }

    public function setEndDate(DateTimeInterface $date)
    {
        $this->setAttribute('end_date', $date);
    }

    public function getReference()
    {
        return $this->getRelationValue('reference');
    }

    public function setReference(object $model)
    {
        if ($model instanceof Model) {
            $this->reference()->associate($model);
        }
    }

    protected function getEventTypeRegistry(): EventTypeRegistryInterface
    {
        return Container::getInstance()->get(EventTypeRegistryInterface::class);
    }
}
