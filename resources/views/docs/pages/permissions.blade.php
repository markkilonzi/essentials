@extends('essentials::docs.layout')

@section('page', 'Roles and permissions')
@section('lead', 'Roles and their permissions are written once in a JSON file and synced with one command.')

@section('content')
    <x-essentials::docs.section id="file" title="permissions.json">
        <p>Define each role with its permissions grouped by area. The file lives at <code>database/seeders/data/permissions.json</code>.</p>
        <x-essentials::docs.code lang="json" title="database/seeders/data/permissions.json">
            {
                "admin": {
                    "invoices": ["invoices.view", "invoices.create"],
                    "users": ["users.view", "users.manage"]
                },
                "clerk": {
                    "invoices": ["invoices.view"]
                }
            }
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="sync" title="Sync it">
        <x-essentials::docs.code lang="bash" title="Terminal">
            php artisan nyumbani:permissions
        </x-essentials::docs.code>
        <p>The command creates the roles and permissions, gives new permissions to users who already hold the role, and deletes permissions that were removed from the file. Run it on every deploy.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="models" title="On your models">
        <x-essentials::docs.code>
            use Illuminate\Foundation\Auth\User as Authenticatable;
            use Nyumbani\Essentials\Traits\HasPermissions;

            class User extends Authenticatable
            {
                use HasPermissions;
            }

            $user->assignDefaultPermissions('clerk');
            User::defaultPermissionsFor('clerk');
        </x-essentials::docs.code>
        <p>Give the app's <code>Role</code> model <code>HasRoleList</code> to list the role names from the same file, for a select box.</p>
    </x-essentials::docs.section>
@endsection
