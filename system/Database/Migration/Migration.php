<?php

namespace AweBooking\System\Database\Migration;

use AweBooking\System\Database\Connection;
use AweBooking\Vendor\Illuminate\Database\Migrations\Migration as IlluminateMigration;

class Migration extends IlluminateMigration
{
    /**
     * @var \AweBooking\System\Database\SchemaBuilder
     */
    protected $schema;

    /**
     * Create new migration.
     */
    public function __construct()
    {
        $this->schema = Connection::getInstance()
            ->getSchemaBuilder();
    }
}
