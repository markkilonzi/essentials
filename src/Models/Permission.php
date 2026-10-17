<?php

namespace Nyumbani\Essentials\Models;

use Nyumbani\Essentials\Traits\HasUlids;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Permission model extending Spatie's with a ULID public key
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class Permission extends SpatiePermission
{
    use HasUlids;
}
