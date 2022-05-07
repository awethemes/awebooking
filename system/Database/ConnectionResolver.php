<?php

namespace AweBooking\System\Database;

use AweBooking\Vendor\Illuminate\Database\ConnectionResolverInterface;

class ConnectionResolver implements ConnectionResolverInterface
{
    public function connection($name = null)
    {
        return Connection::getInstance();
    }

    public function getDefaultConnection()
    {
        return 'wpdb';
    }

    public function setDefaultConnection($name)
    {
    }
}
