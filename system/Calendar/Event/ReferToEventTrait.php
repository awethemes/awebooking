<?php

namespace AweBooking\System\Calendar\Entry;

use AweBooking\System\Calendar\EventTypeInterface;
use AweBooking\System\Database\Model;
use DateTimeInterface;
use Generator;

use function AweBooking\Vendor\tap;

/**
 * @property-read \AweBooking\Vendor\Illuminate\Database\Eloquent\Collection|EventEntry[] $calendarEvents
 */
trait ReferToEventTrait
{
    /**
     * Boot the trait.
     */
    public static function bootReferToEventTrait()
    {
        static::saved(function (self $model) {
            foreach ($model->generateCalendarEvents() as $event) {
                if ($event instanceof EventEntry) {
                    $event->setUnit($model->getCalendarUnit());
                    $event->setValue($model->getCalendarEventState());

                    $model->calendarEvents()->save($event);
                }
            }
        });

        static::deleting(function (self $model) {
            $model->calendarEvents->each->delete();
        });
    }

    /**
     * @return \AweBooking\Vendor\Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function calendarEvents()
    {
        return $this->morphMany(EventEntry::class, 'reference');
    }

    abstract public function getCalendarUnit();

    abstract public function getCalendarEventState(): int;

    abstract protected function getCalendarEventType(): EventTypeInterface;

    abstract protected function generateCalendarEvents(): Generator;

    protected function newCalendarEvent(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        string $title = null,
        string $description = null
    ): EventEntry {
        $event = $this->getCalendarEventType()->createNewEvent(
            $this->getCalendarUnit(),
            $this->getCalendarEventState(),
            $startDate,
            $endDate,
            $this
        );

        return tap($event, function ($event) use ($title, $description) {
            if ($event instanceof Model) {
                $event->fill(compact('title', 'description'));
            }
        });
    }
}
