<?php

namespace Nyumbani\Essentials\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Generate a year-scoped sequential number, e.g. WOR-2026-0001
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
trait HasNumberIdentifiers
{
    /**
     * Create a hook in the boot method
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected static function bootHasNumberIdentifiers(): void
    {
        //  Assign a prefix + year scoped sequential number
        static::creating(function (Model $model) {
            //  Don't assign a number if one is already set
            if (filled($model->number)) {
                return;
            }

            //  Determine the year to scope the sequence to
            $dateColumn = $model->resolveNumberDateColumn();

            //  Get the current year
            $year = ($model->{$dateColumn} ?? now())->year;

            //  Get the most recently created matching row's number, locking the row set
            $lastNumber = $model->resolveNumberQuery()
                ->whereYear($dateColumn, $year)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('number');

            //  Extract and increment the numeric sequence
            $sequence = $lastNumber ? ((int) str($lastNumber)->afterLast('-')->toString()) + 1 : 1;

            //  Set the formatted number, e.g. WOR-2026-0001
            $model->number = sprintf('%s-%d-%04d', $model->resolveNumberPrefix(), $year, $sequence);
        });
    }

    /**
     * Resolve the number prefix, by default the first three letters of the class name
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function resolveNumberPrefix(): string
    {
        return $this->numberPrefix ?? strtoupper(substr(class_basename($this), 0, 3));
    }

    /**
     * Resolve the column that sets the number year
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function resolveNumberDateColumn(): string
    {
        return $this->numberDateColumn ?? 'created_at';
    }

    /**
     * Resolve the query for the last used number, scoped to the tenant
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function resolveNumberQuery(): Builder
    {
        return $this->applyNumberTenantScope(static::query());
    }

    /**
     * Scope the query to the tenant column, when present
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function applyNumberTenantScope(Builder $query): Builder
    {
        //  Loop through the tenant columns
        foreach (['project_id', 'organization_id'] as $tenantColumn) {
            //  Scope the query when the model carries the column
            if (array_key_exists($tenantColumn, $this->getAttributes())) {
                return $query->where($tenantColumn, $this->{$tenantColumn});
            }
        }

        //  Return the query
        return $query;
    }
}
