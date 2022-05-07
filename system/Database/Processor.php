<?php

namespace AweBooking\System\Database;

use AweBooking\Vendor\Illuminate\Database\Query\Builder;
use AweBooking\Vendor\Illuminate\Database\Query\Processors\MySqlProcessor;

class Processor extends MySqlProcessor
{
    /**
     * {@inheritdoc}
     */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
    {
        $query->getConnection()->insert($sql, $values);

        $id = $query->getConnection()->wpdb()->insert_id;

        return is_numeric($id) ? (int) $id : $id;
    }
}
