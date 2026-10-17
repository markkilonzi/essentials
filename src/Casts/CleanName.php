<?php

namespace Nyumbani\Essentials\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Model;

class CleanName implements CastsInboundAttributes
{
    /**
     * Prepare the given value for storage, collapsing spaces and title casing it.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        //  If the value is not a string or is empty, return it as is
        if (! is_string($value) || trim($value) === '') {
            return $value;
        }

        //  Remove extra spaces between words
        $cleaned = preg_replace('/\s+/', ' ', trim($value));

        //  Convert to title case
        return str($cleaned)->title()->toString();
    }
}
