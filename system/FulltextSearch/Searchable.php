<?php

namespace AweBooking\System\FulltextSearch;

interface Searchable
{
    /**
     * Get the searchable content.
     *
     * @return array
     */
    public function toSearchableArray(): array;
}
