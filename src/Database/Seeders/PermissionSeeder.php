<?php

namespace Nyumbani\Essentials\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * The roles from permissions.json.
     */
    private array|object $roles;

    /**
     * Sync the roles and permissions defined in permissions.json into the database.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function run(): void
    {
        //  Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        //  Launch a database transaction
        DB::transaction(function () {
            //  Get the role model
            $role = config('permission.models.role');

            //  Get the permission model
            $permission = config('permission.models.permission');

            //  Get the predefined roles and permissions from the included .json file
            $this->roles = json_decode(file_get_contents(database_path('seeders/data/permissions.json')));

            //  Track permissions created during this run
            $newPermissions = [];

            //  For the first level, the key is the role name and the value is an object of groups
            foreach ($this->roles as $name => $groups) {
                //  Create or update the role
                $role::updateOrCreate(['name' => $name]);

                //  Loop through all the groups
                foreach ($groups as $group => $permissions) {
                    //  Loop through all the permissions
                    foreach ($permissions as $permissionName) {
                        //  Create or update the permission
                        $record = $permission::updateOrCreate(
                            ['name' => $permissionName],
                            ['group' => $group]
                        );

                        //  Track newly created permissions
                        if ($record->wasRecentlyCreated) {
                            $newPermissions[] = $record->name;
                        }
                    }
                }
            }

            //  Handle deleted permissions
            $this->handleDeletedPermissions();

            //  Handle assignment of new permissions
            if (count($newPermissions) > 0) {
                $this->handleAssignmentOfNewPermissions($newPermissions);
            }
        });
    }

    /**
     * Ensure that the permissions deleted from the json are also deleted from the database
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function handleDeletedPermissions(): void
    {
        //  Get the list of all permissions in the json file
        $jsonPermissions = collect($this->roles)
            ->map(fn ($permissions) => collect($permissions)->flatten())
            ->flatten()
            ->unique()
            ->toArray();

        //  Delete the permissions missing from the json
        config('permission.models.permission')::query()
            ->whereNotIn('name', $jsonPermissions)
            ->delete();
    }

    /**
     * Give the new permissions to the existing users of every role they belong to
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function handleAssignmentOfNewPermissions(array $newPermissions): void
    {
        //  Get the user model
        $user = config('auth.providers.users.model');

        //  Query the roles that the new permissions apply to
        foreach ($this->roles as $role => $permissions) {
            //  Get the permissions from the roles in json
            $permissions = collect($permissions)->flatten()->toArray();

            //  Get the relevant permissions in a role
            $relevantPermissions = array_intersect($permissions, $newPermissions);

            //  If they exist, assign them to the users involved
            if (count($relevantPermissions) > 0) {
                $user::query()
                    ->with('permissions')
                    ->whereHas('roles', fn ($query) => $query->whereIn('name', [$role]))
                    ->each(fn ($user) => $user->givePermissionTo($relevantPermissions));
            }
        }
    }
}
