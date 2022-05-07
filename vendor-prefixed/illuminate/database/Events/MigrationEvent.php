<?php

namespace AweBooking\Vendor\Illuminate\Database\Events;

use AweBooking\Vendor\Illuminate\Contracts\Database\Events\MigrationEvent as MigrationEventContract;
use AweBooking\Vendor\Illuminate\Database\Migrations\Migration;
abstract class MigrationEvent implements MigrationEventContract
{
    /**
     * An migration instance.
     *
     * @var \AweBooking\Vendor\Illuminate\Database\Migrations\Migration
     */
    public $migration;
    /**
     * The migration method that was called.
     *
     * @var string
     */
    public $method;
    /**
     * Create a new event instance.
     *
     * @param \AweBooking\Vendor\Illuminate\Database\Migrations\Migration  $migration
     * @param  string  $method
     * @return void
     */
    public function __construct(Migration $migration, $method)
    {
        $this->method = $method;
        $this->migration = $migration;
    }
}
