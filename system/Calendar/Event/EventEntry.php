<?php

namespace AweBooking\System\Calendar\Entry;

use AweBooking\System\Database\Model;

/**
 * @property int $id
 * @property string $event_type
 * @property int $unit_id
 * @property int $value
 * @property string $title
 * @property string $description
 * @property \AweBooking\System\DateTime $start_date
 * @property \AweBooking\System\DateTime $end_date
 * @property \AweBooking\System\DateTime $created_at
 * @property \AweBooking\System\DateTime $updated_at
 * @property-read \AweBooking\System\Database\Model|mixed $entryReference
 * @method static \AweBooking\Vendor\Illuminate\Database\Eloquent\Builder|EventEntry query()
 * @method static \AweBooking\Vendor\Illuminate\Database\Eloquent\Builder|EventEntry newQuery()
 * @method static \AweBooking\Vendor\Illuminate\Database\Eloquent\Builder|EventEntry newModelQuery()
 */
class EventEntry extends Model implements EventEntryInterface
{
	use ManageEventTrait;

    /**
     * @var string
     */
    protected $table = 'awepointment_events';

    /**
     * @var array
     */
    protected $casts = [
        'unit_id' => 'int',
        'value' => 'int',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'title',
        'description',
    ];
}
