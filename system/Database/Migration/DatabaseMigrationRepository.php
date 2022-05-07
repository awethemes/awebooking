<?php

namespace AweBooking\System\Database\Migration;

use AweBooking\System\Database\Connection;
use AweBooking\Vendor\Illuminate\Database\Migrations\DatabaseMigrationRepository as MigrationRepository;

class DatabaseMigrationRepository extends MigrationRepository
{
    /**
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return Connection::getInstance();
    }
}
