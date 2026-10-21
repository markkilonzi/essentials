<?php

namespace Nyumbani\Essentials\Traits;

use Spatie\Permission\Traits\HasRoles;

/**
 * Roles and default permissions for a user model, read from permissions.json. Not Spatie's HasPermissions, which has neither
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
trait HasDefaultPermissions
{
    use HasRoles;

    /**
     * Get the default permission names defined for a role in permissions.json
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public static function defaultPermissionsFor(string $role): array
    {
        //  Get the permissions defined by the provided role
        $data = json_decode(
            file_get_contents(database_path('seeders/data/permissions.json')), true
        );

        //  Flatten the grouped permissions
        return collect($data[$role])
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Assign a role and its default permissions to a user
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function assignDefaultPermissions(string $role): void
    {
        //  Give the user the role
        $this->assignRole($role);

        //  Sync the defined permissions
        $this->syncPermissions(static::defaultPermissionsFor($role));
    }
}
