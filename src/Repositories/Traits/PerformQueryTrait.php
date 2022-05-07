<?php

namespace AweBooking\PMS\Repositories\Traits;

use ArtisUs\Vendor\Illuminate\Database\Eloquent\Builder;

trait PerformQueryTrait
{
    /**
     * Build simple query.
     *
     * @param Builder $query
     * @param string $search
     * @param array $filters
     * @param array $orderings
     * @return Builder
     */
    protected function performSimpleQuery(
        Builder $query,
        string $search = null,
        array $filters = [],
        array $orderings = []
    ) {
        $query = !empty(trim($search))
            ? $this->applySearch($query, $search)
            : $query;

        $query = $this->applyFilters(
            $this->applyOrderings($query, $orderings),
            $filters
        );

        return $query;
    }

    /**
     * Apply the search query to the query.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    protected function applySearch(Builder $query, $search)
    {
        $searchColumns = $this->searchColumns ?? [];

        return $query->where(
            function ($query) use ($search, $searchColumns) {
                global $wpdb;

                $model = $query->getModel();

                $canSearchPrimaryKey = is_numeric($search) && ($search <= PHP_INT_MAX) &&
                    in_array($query->getModel()->getKeyType(), ['int', 'integer']) &&
                    in_array($query->getModel()->getKeyName(), $searchColumns, true);

                if ($canSearchPrimaryKey) {
                    $query->orWhere($query->getModel()->getQualifiedKeyName(), $search);
                }

                foreach ($searchColumns as $column) {
                    $query->orWhere($model->qualifyColumn($column), 'like', '%' . $wpdb->esc_like($search) . '%');
                }
            }
        );
    }

    /**
     * Apply any applicable filters to the query.
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $filters)
    {
        foreach ($filters as $filter) {
            $filter($query);
        }

        return $query;
    }

    /**
     * Apply any applicable orderings to the query.
     *
     * @param Builder $query
     * @param array $orderings
     * @return Builder
     */
    protected function applyOrderings(Builder $query, array $orderings)
    {
        $orderings = array_filter($orderings);

        if (empty($orderings)) {
            return empty($query->getQuery()->orders)
                ? $query->latest($query->getModel()->getQualifiedKeyName())
                : $query;
        }

        foreach ($orderings as $column => $direction) {
            if (!in_array(strtolower($direction), ['asc', 'desc'], true)) {
                continue;
            }

            $query->orderBy($column, $direction);
        }

        return $query;
    }
}
