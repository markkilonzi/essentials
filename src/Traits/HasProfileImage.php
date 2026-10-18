<?php

namespace Nyumbani\Essentials\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * HasProfileImage trait giving a model a single, replaceable profile image
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
trait HasProfileImage
{
    /**
     * Get the latest profile image upload
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function profileImage(): MorphOne
    {
        return $this->morphOne(config('essentials.models.upload'), 'owner')
            ->ofMany(['id' => 'max'], fn ($query) => $query->where('type', 'profile-image'));
    }

    /**
     * Determine whether this model has a profile image uploaded
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function hasProfileImage(): bool
    {
        return $this->profileImage()->exists();
    }

    /**
     * Derive the displayable profile image URL, falling back to the ui-avatars service
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function avatar(): Attribute
    {
        return Attribute::get(
            fn () => $this->profileImage?->url
                ?? 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&size=512&rounded=true'
        );
    }
}
