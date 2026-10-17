<?php

namespace Nyumbani\Essentials\Traits;

trait HasUlids
{
    /**
     * Create a hook in the boot method to generate a ULID for the model.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected static function bootHasUlids(): void
    {
        static::creating(function ($model) {
            //  Get the route key column
            $key = $model->getRouteKeyName();

            //  Generate a lower case ULID unless one is already set
            $model->{$key} = strtolower($model->{$key} ?: (string) str()->ulid());
        });
    }

    /**
     * Get the route key for the model. This is used to generate the ULID.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function getRouteKeyName(): string
    {
        return 'ulid';
    }
}
