<?php

namespace Nyumbani\Essentials\Console\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Laravel\Boost\Console\InstallCommand as BoostInstallCommand;
use Nyumbani\Essentials\Contracts\HasHomeRoute;
use Spatie\Permission\Traits\HasPermissions as SpatieHasPermissions;
use Throwable;

/**
 * What an app still has to do by hand after nyumbani:install, found by looking and never by editing application code
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class InstallChecklist
{
    /**
     * The Vite aliases every Nyumbani app has, by alias, for the folder each one points at, relative to the app root.
     *
     * @var array<string, string>
     */
    public const APP_ALIASES = [
        '@node-modules' => 'node_modules',
        '@public' => 'public',
        '@resources' => 'resources',
        '@vendor' => 'vendor',
        '@storage' => 'storage',
        '@enums' => 'app/Enums',
        '@data-tables' => 'app/DataTables',
        '@config' => 'config',
    ];

    /**
     * The app files Tailwind must scan for classes, by path relative to resources/css/app.css, with the Vite alias form of each.
     *
     * @var array<string, string>
     */
    public const APP_SOURCES = [
        '../../app/Enums/*.php' => '@enums/*.php',
        '../../app/DataTables/**/*.php' => '@data-tables/**/*.php',
        '../../config/dt-tables.php' => '@config/dt-tables.php',
    ];

    /**
     * Get every check as a list of label, state (pass, fail or optional) and how to fix it
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function items(): array
    {
        //  Find the user model the app signs people in with
        $user = config('auth.providers.users.model');

        //  The portal steps only matter once the portal has been published
        $portal = $this->hasPortal();

        return [
            $this->item('Theme imported in resources/css/app.css', $this->fileHas(resource_path('css/app.css'), 'nyumbani/essentials/resources/css/theme.css'), "Add @import '../../vendor/nyumbani/essentials/resources/css/theme.css'; after the Tailwind import."),
            $this->item('Enums, data tables and their config scanned in resources/css/app.css', collect(self::APP_SOURCES)->every(fn (string $alias, string $relative) => $this->fileHas(resource_path('css/app.css'), "@source '{$relative}'") || $this->fileHas(resource_path('css/app.css'), "@source '{$alias}'")), 'Add @source lines for app/Enums, app/DataTables and config/dt-tables.php to resources/css/app.css.'),
            $this->item('Vite aliases in vite.config.js', collect(array_keys(self::APP_ALIASES))->every(fn (string $alias) => $this->fileHas(base_path('vite.config.js'), "'{$alias}'")), 'Add the '.implode(', ', array_keys(self::APP_ALIASES)).' aliases to resolve.alias in vite.config.js.'),
            $this->item('Scripts imported in resources/js/app.js', $this->fileHas(resource_path('js/app.js'), 'nyumbani/essentials/resources/dist/essentials.js'), "Add import '../../vendor/nyumbani/essentials/resources/dist/essentials.js'; before Livewire starts."),
            $this->item('Livewire started in resources/js/app.js', $this->fileHas(resource_path('js/app.js'), 'Livewire.start('), "Add import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm'; and Livewire.start(); to resources/js/app.js."),
            $this->item('User model implements HasHomeRoute', is_a($user, HasHomeRoute::class, true), 'Implement Nyumbani\Essentials\Contracts\HasHomeRoute and add homeRoute().'),
            $this->item('User model has roles and default permissions', is_string($user) && method_exists($user, 'assignDefaultPermissions'), $this->permissionsFix($user)),
            $this->item('User model can be impersonated', is_string($user) && method_exists($user, 'canBeImpersonated'), 'Use Lab404\Impersonate\Models\Impersonate on the user model.'),
            $this->item('Migrations have run (permissions with group, activity_log)', $this->hasTables(), 'Run php artisan migrate.'),
            $this->item('Laravel Boost is installed', class_exists(BoostInstallCommand::class), 'Run php artisan nyumbani:install, which requires laravel/boost --dev for you.'),
            $this->item('Boost is set up with the Nyumbani packages', $this->boostLoadsPackages(), $this->boostFix()),
            $this->item('Basic portal (dashboard, layout and sidebar)', $portal, 'Run php artisan nyumbani:install --portal.', optional: true),
            ...($portal ? $this->portalItems($user) : []),
            $this->item('discord log channel', $this->hasDiscordChannel(), "Add 'discord' => ['driver' => 'discord', 'url' => env('DISCORD_WEBHOOK_URL')] to config/logging.php.", optional: true),
            $this->item('Daraja credentials set', filled(config('daraja.docs.short_code')) && filled(config('daraja.docs.consumer_key')), 'Set DARAJA_SHORT_CODE, DARAJA_CONSUMER_KEY, DARAJA_CONSUMER_SECRET and DARAJA_PASS_KEY in .env.', optional: true),
            $this->item('Nyumbani Connect settings set', filled(config('messaging.connect.base_url')) && filled(config('messaging.connect.access_token')), 'Set NYUMBANI_CONNECT_BASE_URL and NYUMBANI_CONNECT_TOKEN in .env.', optional: true),
        ];
    }

    /**
     * Get the checks that only apply to an app with the basic portal: its route, its wiring and its first admin
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function portalItems(mixed $user): array
    {
        return [
            $this->item("Portal route 'home' in routes/admins.php", $this->fileHas(base_path('routes/admins.php'), "->name('home')"), "Add Route::get('/', DashboardController::class)->name('home'); (App\\Http\\Controllers\\Admins\\DashboardController) to routes/admins.php."),
            $this->item('routes/admins.php wired with the role:admin middleware', $this->fileHas(base_path('bootstrap/app.php'), 'admins.php') && $this->fileHas(base_path('bootstrap/app.php'), 'role:admin'), "Load routes/admins.php in bootstrap/app.php with the 'auth' and 'role:admin' middleware, and register the role alias."),
            $this->item('An admin user exists', $this->hasAdminUser($user), "Give your first user the admin role with \$user->assignDefaultPermissions('admin'), after php artisan nyumbani:permissions."),
        ];
    }

    /**
     * Determine whether the app has the discord log channel, read from config/logging.php as well as the loaded config, which still lacks a channel the installer added moments ago
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function hasDiscordChannel(): bool
    {
        return config('logging.channels.discord.driver') === 'discord'
            || (File::exists(config_path('logging.php')) && preg_match('/[\'"]discord[\'"]\s*=>/', File::get(config_path('logging.php'))) === 1);
    }

    /**
     * Determine whether the basic portal's controller, layout, dashboard and sidebar are all in the app
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function hasPortal(): bool
    {
        return File::exists(app_path('Http/Controllers/Admins/DashboardController.php'))
            && File::exists(resource_path('views/admins/pages/dashboard.blade.php'))
            && File::exists(resource_path('views/layouts/app.blade.php'))
            && File::exists(resource_path('views/admins/partials/sidebar.blade.php'));
    }

    /**
     * Determine whether at least one user holds the admin role
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function hasAdminUser(mixed $user): bool
    {
        try {
            return is_string($user) && method_exists($user, 'role') && $user::role('admin')->exists();
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Build one check
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function item(string $label, bool $passes, string $fix, bool $optional = false): array
    {
        return ['label' => $label, 'state' => $passes ? 'pass' : ($optional ? 'optional' : 'fail'), 'fix' => $fix];
    }

    /**
     * Determine whether a file exists and mentions the text
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function fileHas(string $path, string $text): bool
    {
        return File::exists($path) && str_contains(File::get($path), $text);
    }

    /**
     * Say how to give the user model the Nyumbani permissions trait, naming the Spatie trait of the same name when that is what it imports
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function permissionsFix(mixed $user): string
    {
        //  Spatie's HasPermissions is the easy one to import by mistake, and has no roles and no default permissions
        if (is_string($user) && class_exists($user) && in_array(SpatieHasPermissions::class, class_uses_recursive($user), true)) {
            return 'The user model imports Spatie\Permission\Traits\HasPermissions. Import Nyumbani\Essentials\Traits\HasDefaultPermissions instead, which brings roles and assignDefaultPermissions().';
        }

        return 'Use Nyumbani\Essentials\Traits\HasDefaultPermissions on the user model.';
    }

    /**
     * Say how to get Boost to load the Nyumbani packages, whose usual blocker is that Boost reads only the packages an app requires itself
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function boostFix(): string
    {
        //  Boost ignores guidelines and skills of packages that reach the app only through another package
        $missing = (new DirectRequirements)->missing(['nyumbani/daraja', 'nyumbani/messaging']);

        if ($missing !== []) {
            return 'Boost only loads packages the app requires itself. Run composer require '.implode(' ', $missing).', then php artisan nyumbani:install.';
        }

        return 'Run php artisan nyumbani:install to set Boost up and load Essentials, Daraja and Messaging.';
    }

    /**
     * Determine whether Boost has its settings and loads the three Nyumbani packages, so their guidelines and skills reach the agents
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function boostLoadsPackages(): bool
    {
        //  Boost writes its settings the first time it is installed
        $path = base_path('boost.json');

        if (! File::exists($path)) {
            return false;
        }

        //  Read the packages Boost loads from
        $packages = json_decode(File::get($path), true)['packages'] ?? [];

        //  Every Nyumbani package must be among them
        return array_diff(['nyumbani/essentials', 'nyumbani/daraja', 'nyumbani/messaging'], $packages) === [];
    }

    /**
     * Determine whether the permission and activity tables exist, with the permission group
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function hasTables(): bool
    {
        try {
            $permissions = config('permission.table_names.permissions', 'permissions');

            return Schema::hasTable($permissions)
                && Schema::hasColumn($permissions, 'group')
                && Schema::hasTable(config('activitylog.table_name', 'activity_log'));
        } catch (Throwable) {
            return false;
        }
    }
}
