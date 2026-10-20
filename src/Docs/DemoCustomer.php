<?php

namespace Nyumbani\Essentials\Docs;

use Illuminate\Database\Eloquent\Model;
use Nyumbani\Essentials\Traits\HasMoney;
use Nyumbani\Essentials\Traits\HasMorphRouteKey;
use Nyumbani\Essentials\Traits\HasUlids;

/**
 * A model made up for the interactive docs, never saved
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class DemoCustomer extends Model
{
    use HasMoney, HasMorphRouteKey, HasUlids;

    /**
     * Get the morph name the route key shows
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function getMorphClass(): string
    {
        return 'customer';
    }
}
