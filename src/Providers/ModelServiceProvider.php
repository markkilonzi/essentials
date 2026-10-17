<?php

namespace Nyumbani\Essentials\Providers;

use Illuminate\Support\ServiceProvider;
use Nyumbani\Essentials\Models\Permission;
use Nyumbani\Essentials\Models\Role;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role as SpatieRole;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Point Spatie's permission package at the essentials role and permission models, unless the app already chose its own.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function boot(): void
    {
        //  Use the essentials role model unless the app set one
        if (config('permission.models.role') === SpatieRole::class) {
            config(['permission.models.role' => Role::class]);
        }

        //  Use the essentials permission model unless the app set one
        if (config('permission.models.permission') === SpatiePermission::class) {
            config(['permission.models.permission' => Permission::class]);
        }
    }
}
