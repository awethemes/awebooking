<?php

namespace AweBooking\System\Database;

use AweBooking\Vendor\Illuminate\Database\Query\Grammars\MySqlGrammar;

class QueryGrammar extends MySqlGrammar
{
    /**
     * {@inheritdoc}
     */
    public function parameter($value)
    {
        if ($this->isExpression($value)) {
            return $this->getValue($value);
        }

        // Workaround on the null value binding,
        // since $wpdb->prepare doesn't support it.
        if ($value === null) {
            return 'null';
        }

        if (is_numeric($value)) {
            return is_float($value) ? '%f' : '%d';
        }

        return '%s';
    }
}
