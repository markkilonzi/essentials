<?php

namespace Nyumbani\Essentials\Models;

use Nyumbani\Essentials\Traits\HasUlids;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Role model extending Spatie's with a ULID public key
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class Role extends SpatieRole
{
    use HasUlids;

    /**
     * Extract all available roles from the app's database/seeders/data/permissions.json, whose top-level keys are the roles.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public static function list(): array
    {
        //  Get the contents of the permissions.json
        $content = file_get_contents(database_path('seeders/data/permissions.json'));

        //  Decode the JSON content into an associative array
        $data = json_decode($content, true);

        //  Return the keys of the array as the available roles
        return array_keys($data);
    }
}
