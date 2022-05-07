<?php

namespace AweBooking\PMS\Models;

use Isolated\Illuminate\Support\Arr;
use AweBooking\System\Database\Model;
use AweBooking\System\FulltextSearch\Searchable;

class Post extends Model implements Searchable
{
    protected $table = 'posts';

    protected $primaryKey = 'ID';

    public function toSearchableArray(): array
    {
        return Arr::only(
            $this->toArray(),
            ['ID', 'post_content', 'post_title']
        );
    }
}
