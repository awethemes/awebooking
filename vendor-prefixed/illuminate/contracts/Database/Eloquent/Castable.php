<?php

namespace AweBooking\Vendor\Illuminate\Contracts\Database\Eloquent;

interface Castable
{
    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @return string|\AweBooking\Vendor\Illuminate\Contracts\Database\Eloquent\CastsAttributes|\AweBooking\Vendor\Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes
     */
    public static function castUsing();
}
