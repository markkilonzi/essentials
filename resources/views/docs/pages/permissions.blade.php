@extends('essentials::docs.layout')

@section('page', 'Roles and permissions')
@section('lead', 'Roles and their permissions are written once in a JSON file and synced with one command.')

@section('content')
    <x-essentials::docs.section id="file" title="permissions.json">
        <p>Define each role with its permissions grouped by area. The file lives at <code>database/seeders/data/permissions.json</code>.</p>
        <x-essentials::docs.code lang="json" title="database/seeders/data/permissions.json">
            {
                "admin": {
                    "invoices": ["view-invoices", "create-invoice"],
                    "users": ["view-users", "create-user"]
                },
                "clerk": {
                    "invoices": ["view-invoices"]
                }
            }
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="how" title="How it fits together">
        <x-essentials::docs.steps :steps="[
            ['title' => 'Write permissions.json', 'actor' => 'You, once per change', 'detail' => 'The one source of truth: each role, its groups and the permissions in each group. It lives in the repository, so every environment gets the same roles.', 'code' => 'clerk > invoices > view-invoices'],
            ['title' => 'Sync it', 'actor' => 'php artisan nyumbani:permissions, on every deploy', 'detail' => 'Creates or updates the roles and permissions, gives new permissions to users who already hold the role, and deletes permissions you removed from the file.'],
            ['title' => 'Assign a role to a user', 'actor' => 'Your app, when a user is created', 'detail' => 'assignDefaultPermissions gives the user the role and syncs exactly that role\'s permissions from the file.', 'code' => '$user->assignDefaultPermissions(\'clerk\');'],
            ['title' => 'Check it in a policy', 'actor' => 'Your policies', 'detail' => 'Ask for the permission, never the role. Allowed or denied follows from the file alone.', 'code' => 'return $user->hasPermissionTo(\'view-invoices\');'],
        ]" />
        <x-essentials::docs.callout type="note" title="The group column">
            <code>nyumbani:install</code> publishes a migration that creates the permission tables with a nullable <code>group</code> column, which <code>nyumbani:permissions</code> fills from the file. An app that published Spatie's own migration earlier gets a small one that adds the column instead. Run <code>php artisan migrate</code> after the installer.
        </x-essentials::docs.callout>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="sync" title="Sync it">
        <x-essentials::docs.code lang="bash" title="Terminal">
            php artisan nyumbani:permissions
        </x-essentials::docs.code>
        <p>The command creates the roles and permissions, gives new permissions to users who already hold the role, and deletes permissions that were removed from the file. Run it on every deploy.</p>
        <x-essentials::docs.table :columns="['You change the file by', 'The sync']" :rows="[
            ['Adding a permission to a role', 'Creates it, and gives it to every existing user who holds that role.'],
            ['Removing a permission', 'Deletes it from the database, and so from every user and role.'],
            ['Adding a role', 'Creates the role. Users are not moved into it.'],
            ['Moving a permission to another group', 'Updates its group.'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="models" title="On your models">
        <x-essentials::docs.callout type="warning" title="Not Spatie's HasPermissions">
            Spatie ships <code>Spatie\Permission\Traits\HasPermissions</code>, which has no roles and no default permissions. Use <code>Nyumbani\Essentials\Traits\HasDefaultPermissions</code>, which adds both. The installer's checklist tells you when the Spatie one is imported.
        </x-essentials::docs.callout>
        <x-essentials::docs.code>
            use Illuminate\Foundation\Auth\User as Authenticatable;
            use Nyumbani\Essentials\Traits\HasDefaultPermissions;

            class User extends Authenticatable
            {
                use HasDefaultPermissions;
            }

            $user->assignDefaultPermissions('clerk');
            User::defaultPermissionsFor('clerk');
        </x-essentials::docs.code>
        <x-essentials::docs.code title="In a policy">
            use App\Models\User;

            public function viewAny(User $user): bool
            {
                return $user->hasPermissionTo('view-invoices');
            }
        </x-essentials::docs.code>
        <p>Name permissions as kebab-case verb-noun: plural for a list (<code>view-invoices</code>), singular for one record (<code>create-invoice</code>). Check permissions in policies, never roles.</p>
        <p>Give the app's <code>Role</code> model <code>HasRoleList</code> to list the role names from the same file, for a select box: <code>Role::list()</code> returns <code>['admin', 'clerk']</code>.</p>
    </x-essentials::docs.section>
@endsection
