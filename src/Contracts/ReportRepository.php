<?php

namespace AweBooking\PMS\Contracts;

use ArtisUs\System\Database\Connection as DB;
use ArtisUs\System\DateTime;

interface ReportRepository
{
    /**
     * @param int $artistId
     * @return float
     */
    public function getLifetimeArtistCommissions($artistId): float;

    /**
     * @param int $artistId
     * @return \ArtisUs\Vendor\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function queryArtworkSalesByArtist($artistId);

    /**
     * @param int $artistId
     * @return object|null
     */
    public function getArtistThisMonthRevenue($artistId);
}
