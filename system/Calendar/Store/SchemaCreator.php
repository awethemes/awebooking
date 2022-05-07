<?php

namespace AweBooking\System\Calendar;

use AweBooking\System\Database\SchemaBuilder;
use AweBooking\Vendor\Illuminate\Database\Schema\Blueprint;

class SchemaCreator
{
    /** @var SchemaBuilder */
    protected $schema;

    public function __construct(SchemaBuilder $schema)
    {
        $this->schema = $schema;
    }

    public function create(string $name): void
    {
        $this->schema->getConnection()->transaction(
            function () use ($name) {
                $this->createDaysTable($name);
                $this->createHoursTable($name);
                $this->createMinutesTable($name);
            }
        );
    }

    public function drop(string $name)
    {
        $this->schema->dropIfExists($name . '_days_events');
        $this->schema->dropIfExists($name . '_days_states');
        $this->schema->dropIfExists($name . '_hours_events');
        $this->schema->dropIfExists($name . '_hours_states');
        $this->schema->dropIfExists($name . '_minutes_events');
        $this->schema->dropIfExists($name . '_minutes_states');
    }

    private function createDaysTable(string $name)
    {
        $name .= '_days';

        $createCallback = function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');

            for ($i = 1; $i <= 31; $i++) {
                $table->bigInteger(sprintf('d%d', $i))->default(0);
            }

            $table->primary(['unit_id', 'year', 'month'], 'primary_keys');
        };

        $this->schema->create($name . '_events', $createCallback);
        $this->schema->create($name . '_states', $createCallback);
    }

    private function createHoursTable(string $name)
    {
        $name .= '_hours';

        $createCallback = function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedTinyInteger('day');

            for ($i = 0; $i <= 23; $i++) {
                $table->bigInteger(sprintf('h%d', $i))->default(0);
            }

            $table->primary(['unit_id', 'year', 'month', 'day'], 'primary_keys');
        };

        $this->schema->create($name . '_events', $createCallback);
        $this->schema->create($name . '_states', $createCallback);
    }

    private function createMinutesTable(string $name)
    {
        $name .= '_minutes';

        $createCallback = function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedTinyInteger('day');
            $table->unsignedTinyInteger('hour');

            for ($i = 0; $i <= 59; $i++) {
                $table->bigInteger(sprintf('m%02d', $i))->default(0);
            }

            $table->primary(['unit_id', 'year', 'month', 'day', 'hour'], 'primary_keys');
        };

        $this->schema->create($name . '_events', $createCallback);
        $this->schema->create($name . '_states', $createCallback);
    }
}
