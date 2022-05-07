<?php

namespace AweBooking\Vendor\Illuminate\Database\Events;

class StatementPrepared
{
    /**
     * The database connection instance.
     *
     * @var \AweBooking\Vendor\Illuminate\Database\Connection
     */
    public $connection;
    /**
     * The PDO statement.
     *
     * @var \PDOStatement
     */
    public $statement;
    /**
     * Create a new event instance.
     *
     * @param \AweBooking\Vendor\Illuminate\Database\Connection  $connection
     * @param  \PDOStatement  $statement
     * @return void
     */
    public function __construct($connection, $statement)
    {
        $this->statement = $statement;
        $this->connection = $connection;
    }
}
