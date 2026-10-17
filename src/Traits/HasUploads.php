<?php

namespace Nyumbani\Essentials\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * HasUploads trait giving a model the ability to own multiple uploads
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
trait HasUploads
{
    /**
     * Get all uploads attached to this model, using the model set in essentials.models.upload
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(config('essentials.models.upload'), 'owner');
    }
}
