<?php

namespace Nyumbani\Essentials\Traits;

trait HasMorphRouteKey
{
    /**
     * Get the morph route key for use in polymorphic route model binding.
     *
     * Format: {morph_type}:{route_key}
     * Example: product:123 or product:PRD-456 (if using custom route key)
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function getMorphRouteKey(): string
    {
        //  Get the morph type
        $morphType = $this->getMorphClass();

        //  Get the route key
        $routeKey = $this->getRouteKey();

        //  Combine the morph type and route key
        return "{$morphType}:{$routeKey}";
    }
}
