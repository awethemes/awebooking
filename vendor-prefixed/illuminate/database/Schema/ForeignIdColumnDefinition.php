<?php

namespace AweBooking\Vendor\Illuminate\Database\Schema;

use AweBooking\Vendor\Illuminate\Support\Str;
class ForeignIdColumnDefinition extends ColumnDefinition
{
    /**
     * The schema builder blueprint instance.
     *
     * @var \AweBooking\Vendor\Illuminate\Database\Schema\Blueprint
     */
    protected $blueprint;
    /**
     * Create a new foreign ID column definition.
     *
     * @param \AweBooking\Vendor\Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  array  $attributes
     * @return void
     */
    public function __construct(Blueprint $blueprint, $attributes = [])
    {
        parent::__construct($attributes);
        $this->blueprint = $blueprint;
    }
    /**
     * Create a foreign key constraint on this column referencing the "id" column of the conventionally related table.
     *
     * @param  string|null  $table
     * @param  string  $column
     * @return \AweBooking\Vendor\Illuminate\Support\Fluent|\AweBooking\Vendor\Illuminate\Database\Schema\ForeignKeyDefinition
     */
    public function constrained($table = null, $column = 'id')
    {
        return $this->references($column)->on($table ?? Str::plural(Str::beforeLast($this->name, '_' . $column)));
    }
    /**
     * Specify which column this foreign ID references on another table.
     *
     * @param  string  $column
     * @return \AweBooking\Vendor\Illuminate\Support\Fluent|\AweBooking\Vendor\Illuminate\Database\Schema\ForeignKeyDefinition
     */
    public function references($column)
    {
        return $this->blueprint->foreign($this->name)->references($column);
    }
}
