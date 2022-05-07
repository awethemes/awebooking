<?php

namespace AweBooking\Vendor\Illuminate\Database\Eloquent;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \AweBooking\Vendor\Illuminate\Database\Eloquent\Builder  $builder
     * @param \AweBooking\Vendor\Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
