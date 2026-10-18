<?php

namespace Nyumbani\Essentials\Traits;

/**
 * The list of roles in permissions.json, for the app's Role model
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
trait HasRoleList
{
    /**
     * Get the role names from permissions.json
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
