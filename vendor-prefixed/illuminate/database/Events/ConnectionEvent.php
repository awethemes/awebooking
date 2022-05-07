<?php

namespace AweBooking\Vendor\Illuminate\Database\Events;

abstract class ConnectionEvent
{
    /**
     * The name of the connection.
     *
     * @var string
     */
    public $connectionName;
    /**
     * The database connection instance.
     *
     * @var \AweBooking\Vendor\Illuminate\Database\Connection
     */
    public $connection;
    /**
     * Create a new event instance.
     *
     * @param \AweBooking\Vendor\Illuminate\Database\Connection  $connection
     * @return void
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
    }
}
