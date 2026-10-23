<?php

namespace Nyumbani\Essentials\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Laravel\Boost\Console\InstallCommand as BoostInstallCommand;
use Laravel\Boost\Rules\RuleRepository;
use Nyumbani\Docs\DocsRegistry;
use Nyumbani\Essentials\Console\Support\DirectRequirements;
use Nyumbani\Essentials\Console\Support\InstallChecklist;
use ParseError;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Terminal;
use Symfony\Component\Process\Process as SymfonyProcess;
use Throwable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;

class InstallCommand extends Command
{
    /**
     * The prefix of every rule file the package owns inside the app's .ai/rules folder.
     */
    protected const RULE_PREFIX = 'nyumbani-';

    /**
     * The packages whose guidelines and skills Boost should load, which Essentials requires.
     *
     * @var list<string>
     */
    protected const BOOST_PACKAGES = [
        'nyumbani/essentials',
        'nyumbani/daraja',
        'nyumbani/messaging',
        'nyumbani/dt-tables',
    ];

    /**
     * The config tags to publish, with the file each one creates in the app's config folder.
     *
     * @var array<string, string>
     */
    protected const CONFIG_TAGS = [
        'essentials-config' => 'essentials.php',
        'daraja-config' => 'daraja.php',
        'messaging-config' => 'messaging.php',
        'permission-config' => 'permission.php',
        'activitylog-config' => 'activitylog.php',
        'dt-tables-config' => 'dt-tables.php',
    ];

    /**
     * The classes the Tailwind data tables theme gives its buttons, inputs and selects.
     *
     * @var array<string, string>
     */
    protected const TAILWIND_DATA_TABLE_CLASSES = [
        'buttons' => 'btn btn-sm btn-primary',
        'input' => 'rounded-lg border px-3 py-1.5 text-sm',
        'select' => 'rounded-lg border px-3 py-1.5 text-sm',
    ];

    /**
     * The migration tags to publish, with the name of the migration each one creates.
     *
     * @var array<string, string>
     */
    protected const MIGRATION_TAGS = [
        'activitylog-migrations' => 'create_activity_log_table',
    ];

    /**
     * The name of the migration Essentials ships to create the permission tables, with the group column built in, and the tag that publishes it.
     */
    protected const PERMISSIONS_MIGRATION = 'create_permission_tables';

    protected const PERMISSIONS_MIGRATION_TAG = 'essentials-permissions-migration';

    /**
     * The name of the migration that adds the group column to permission tables an app created earlier without it, and the tag that publishes it.
     */
    protected const GROUP_MIGRATION = 'update_permissions_table_add_group_column';

    protected const GROUP_MIGRATION_TAG = 'essentials-permissions-group-migration';

    /**
     * The environment keys the packages read, which .env.example should list.
     *
     * @var list<string>
     */
    protected const ENV_KEYS = [
        'ESSENTIALS_CURRENCY', 'ESSENTIALS_THEME_COLOR', 'ESSENTIALS_DESCRIPTION',
        'DARAJA_ENVIRONMENT', 'DARAJA_SHORT_CODE', 'DARAJA_CONSUMER_KEY', 'DARAJA_CONSUMER_SECRET', 'DARAJA_PASS_KEY', 'DARAJA_TILL_NUMBER',
        'NYUMBANI_CONNECT_BASE_URL', 'NYUMBANI_CONNECT_TOKEN', 'MESSAGING_SMS_ENABLED', 'MESSAGING_SMS_SENDER_ID',
        'DISCORD_WEBHOOK_URL',
    ];

    /**
     * The discord log channel added to config/logging.php, which leaves the level, throttle and message length to the driver's defaults.
     */
    protected const DISCORD_CHANNEL = <<<'PHP'
        'discord' => [
            'driver' => 'discord',
            'url' => env('DISCORD_WEBHOOK_URL'),
            'username' => env('APP_NAME', 'Nyumbani'),
        ],

PHP;

    /**
     * The packages an app must require itself, because Boost ignores the guidelines and skills of indirect ones.
     *
     * @var list<string>
     */
    protected const DIRECT_PACKAGES = ['nyumbani/daraja', 'nyumbani/messaging', 'nyumbani/dt-tables'];

    /**
     * The environment variable that marks the second pass of the command, so a Boost that still cannot be loaded never loops.
     */
    protected const RESTARTED = 'NYUMBANI_INSTALL_RESTARTED';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nyumbani:install {--portal : Set up the basic portal without asking}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up or refresh the Nyumbani rules, guidelines and skills in this app: safe to run again whenever a package changes';

    /**
     * Execute the console command.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function handle(): int
    {
        //  Greet the developer
        $this->banner('Nyumbani install');
        $this->line(' <fg=gray>Rules, guidelines, skills and the files your app needs, set up safely and re-runnable.</>');
        $this->newLine();

        //  Install what is missing first, and let a fresh process finish, since the new classes are not loaded here
        if (! $this->isBoostInstalled() || $this->missingPackages() !== []) {
            return $this->installBoostAndContinue();
        }

        //  Copy the rules, replacing the ones the package manages
        $this->publishRules();

        //  Publish the config files and migrations of the packages, once
        $this->publishPackageFiles();

        //  Prepare the app's own files, adding only what is missing
        $this->addEnvKeys();

        //  Add the discord log channel
        $this->addLogChannel();

        //  Add the Vite aliases every Nyumbani app shares
        $this->addViteAliases();

        //  Add the package's imports
        $this->addImports();

        //  Create the starter permissions file
        $this->createPermissionsFile();

        //  Set up the basic portal, when the developer wants it
        $this->setUpPortal();

        //  Build the assets, so the theme, scripts and any new views are compiled
        $this->buildAssets();

        //  Offer to run the migrations, so the roles and permissions can be synced in this run
        $this->offerMigrations();

        //  Sync the roles and permissions from the file, once the tables exist
        $this->syncPermissions();

        //  Link the public storage folder
        $this->linkStorage();

        //  Format the PHP files the installer published
        $this->formatPublishedFiles();

        //  Set up Boost the first time, then refresh its guidelines and skills
        $this->setUpBoost();

        //  Say what is still left to do by hand
        $this->showChecklist();

        //  Invite the developer to the docs
        $this->showDocsInvitation();

        //  Close with the next step
        $this->newLine();
        $this->banner('All set. Run php artisan migrate if the checklist asks for it, and run this command again whenever a package changes.');

        //  Success
        return self::SUCCESS;
    }

    /**
     * Publish the config files and the migrations of the packages Essentials builds on, each only when the app has none yet.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function publishPackageFiles(): void
    {
        //  Go through the config files, an existing one is never overwritten
        foreach (self::CONFIG_TAGS as $tag => $file) {
            //  Keep the config the app already has
            if (File::exists(config_path($file))) {
                continue;
            }

            //  Publish the config file
            $this->callSilent('vendor:publish', ['--tag' => [$tag]]);

            //  Say what was published
            $this->done("Published config/{$file}.");

            //  Give the data tables the Nyumbani look, in the copy just published only, so an app's own edits stay
            if ($tag === 'dt-tables-config') {
                $this->useTailwindDataTables();
            }
        }

        //  Go through the migrations, skipping any the app already has under that name, since each publish adds a new timestamped copy
        foreach (self::MIGRATION_TAGS as $tag => $name) {
            //  Keep the migration the app already has
            if (File::glob(database_path("migrations/*_{$name}.php")) !== []) {
                continue;
            }

            //  Publish the migration
            $this->callSilent('vendor:publish', ['--tag' => [$tag]]);

            //  Say what was published
            $this->done("Published the {$name} migration.");
        }

        //  The permission tables come from Essentials' own migration, which already has the group column, so one file does it all
        $this->publishPermissionsMigration();
    }

    /**
     * Set the freshly published data tables config to the Tailwind theme and the Nyumbani button, input and select classes.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function useTailwindDataTables(): void
    {
        //  Read the config just published
        $path = config_path('dt-tables.php');

        if (! File::exists($path)) {
            return;
        }

        //  Use the Tailwind theme by default
        $config = preg_replace("/('theme'\s*=>\s*)'[^']*'/", "$1'tailwind'", File::get($path), 1);

        //  Give the Tailwind theme the Nyumbani classes
        $config = preg_replace_callback("/('tailwind'\s*=>\s*\[)(.*?)(\n\s*\],)/s", function (array $block) {
            $classes = collect(self::TAILWIND_DATA_TABLE_CLASSES)
                ->reduce(fn (string $content, string $value, string $key) => preg_replace("/('{$key}'\s*=>\s*)'[^']*'/", "$1'{$value}'", $content, 1), $block[2]);

            return $block[1].$classes.$block[3];
        }, $config, 1);

        //  Keep the result
        File::put($path, $config);

        //  Say what was set
        $this->done('Set the data tables to the Tailwind theme.');
    }

    /**
     * Give the app the permission tables with the group column: its own full migration for a new app, or the small one that adds the column to tables created without it.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function publishPermissionsMigration(): void
    {
        //  Find any permission tables migration the app already has
        $existing = File::glob(database_path('migrations/*_'.self::PERMISSIONS_MIGRATION.'.php'));

        //  A new app gets Essentials' migration, which creates the tables with the group column
        if ($existing === []) {
            $this->publishMigration(self::PERMISSIONS_MIGRATION_TAG, self::PERMISSIONS_MIGRATION);

            return;
        }

        //  A migration that already has the group column, or an upgrade for it, needs nothing more
        $hasGroup = collect($existing)->contains(fn (string $path) => str_contains(File::get($path), "'group'"));

        if ($hasGroup || File::glob(database_path('migrations/*_'.self::GROUP_MIGRATION.'.php')) !== []) {
            return;
        }

        //  Otherwise add the column to the tables the app created earlier
        $this->publishMigration(self::GROUP_MIGRATION_TAG, self::GROUP_MIGRATION);
    }

    /**
     * Publish one of Essentials' migrations by its tag, which gives it a timestamped name.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function publishMigration(string $tag, string $name): void
    {
        //  Publish the migration
        $this->callSilent('vendor:publish', ['--tag' => [$tag]]);

        //  Say what was published
        $this->done("Published the {$name} migration.");
    }

    /**
     * Append the package keys that .env.example lacks, with blank values. The real .env is never touched.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function addEnvKeys(): void
    {
        //  Find the example file
        $path = base_path('.env.example');

        //  Nothing to add to when the app has no .env.example
        if (! File::exists($path)) {
            return;
        }

        //  Read it and find the keys that are missing
        $content = File::get($path);
        $missing = collect(self::ENV_KEYS)->reject(fn (string $key) => preg_match('/^'.preg_quote($key, '/').'=/m', $content));

        //  Stop when every key is already listed
        if ($missing->isEmpty()) {
            return;
        }

        //  Add them as one block
        File::put($path, rtrim($content)."\n\n# Nyumbani packages\n".$missing->map(fn (string $key) => "{$key}=")->implode("\n")."\n");

        //  Say what was added
        $this->done("Added {$missing->count()} keys to .env.example.");
    }

    /**
     * Add the discord log channel to config/logging.php after the slack channel, unless the app has one already, and only when the file still parses as PHP afterwards.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function addLogChannel(): void
    {
        //  Nothing to add to when the app has no logging config
        $path = config_path('logging.php');

        if (! File::exists($path)) {
            return;
        }

        //  Keep the discord channel the app already has
        $content = File::get($path);

        if (preg_match('/[\'"]discord[\'"]\s*=>/', $content)) {
            return;
        }

        //  Put it after the slack channel, the other webhook channel, or before the stack channel when there is no slack, or first in the list when there is neither
        $updated = match (true) {
            (bool) preg_match("/^        'slack' => \[.*?^        \],\R/ms", $content) => preg_replace("/(^        'slack' => \[.*?^        \],\R)/ms", '$1'.PHP_EOL.rtrim(self::DISCORD_CHANNEL).PHP_EOL, $content, 1),
            str_contains($content, "        'stack' => [") => preg_replace("/^(        'stack' => \[)/m", self::DISCORD_CHANNEL.'$1', $content, 1),
            default => preg_replace("/('channels' => \[\R)/", '$1'.self::DISCORD_CHANNEL, $content, 1),
        };

        //  Leave the file alone when the channels list was not found
        if ($updated === null || $updated === $content) {
            return;
        }

        //  Never write a file that no longer parses
        try {
            token_get_all($updated, TOKEN_PARSE);
        } catch (ParseError) {
            return;
        }

        //  Save it
        File::put($path, $updated);

        //  Say what was added
        $this->done('Added the discord channel to config/logging.php. Add discord to LOG_STACK to use it.');
    }

    /**
     * Import the theme and the scripts in the app's css and js, when they are not imported yet.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function addImports(): void
    {
        //  Find the stylesheet
        $css = resource_path('css/app.css');

        //  Import by the @vendor alias when the Vite config has it, and by the relative path when it does not
        $vendor = $this->hasViteAlias('@vendor') ? '@vendor' : '../../vendor';

        //  The theme goes after the Tailwind import, unless it is already imported
        if (File::exists($css) && ! str_contains(File::get($css), 'nyumbani/essentials/resources/css/theme.css')) {
            //  Add the import
            File::append($css, "\n@import '{$vendor}/nyumbani/essentials/resources/css/theme.css';\n");

            //  Say what was imported
            $this->done('Imported the theme in resources/css/app.css.');
        }

        //  Tell Tailwind to scan the app's enums, data tables and their config, where classes are written in PHP
        $this->addSources($css);

        //  Find the script
        $js = resource_path('js/app.js');

        //  The scripts go first, so they load before Livewire starts, unless they are already imported
        if (File::exists($js) && ! str_contains(File::get($js), 'nyumbani/essentials/resources/dist/essentials.js')) {
            //  Add the import
            File::prepend($js, "import '{$vendor}/nyumbani/essentials/resources/dist/essentials.js';\n");

            //  Say what was imported
            $this->done('Imported the scripts in resources/js/app.js.');
        }

        //  Livewire starts the Alpine every Nyumbani script needs, so the script must start it unless it already does
        if (File::exists($js) && ! str_contains(File::get($js), 'Livewire.start(')) {
            //  Add the import and the start after the existing code
            File::append($js, "\nimport { Livewire } from '{$vendor}/livewire/livewire/dist/livewire.esm';\n\n//  Start Livewire, which starts the Alpine it bundles\nLivewire.start();\n");

            //  Say what was started
            $this->done('Started Livewire in resources/js/app.js.');
        }

        //  Move the relative paths an earlier run wrote to the aliases, once the config has them
        $this->useAliasesInImports($css, $js);
    }

    /**
     * Tell whether vite.config.js defines an alias.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function hasViteAlias(string $alias): bool
    {
        return File::exists(base_path('vite.config.js')) && str_contains(File::get(base_path('vite.config.js')), "'{$alias}'");
    }

    /**
     * Replace the relative paths of the vendor files and the app sources an earlier run wrote with their aliases, for each alias the Vite config has.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function useAliasesInImports(string $css, string $js): void
    {
        //  The relative paths to swap, by file, for the alias that replaces each
        $swaps = [
            $css => [
                "'../../vendor/nyumbani/essentials/" => ['@vendor', "'@vendor/nyumbani/essentials/"],
                ...collect(InstallChecklist::APP_SOURCES)->mapWithKeys(fn (string $alias, string $relative) => ["@source '{$relative}'" => [strstr($alias, '/', true), "@source '{$alias}'"]])->all(),
            ],
            $js => [
                "'../../vendor/nyumbani/essentials/" => ['@vendor', "'@vendor/nyumbani/essentials/"],
                "'../../vendor/livewire/livewire/" => ['@vendor', "'@vendor/livewire/livewire/"],
            ],
        ];

        foreach ($swaps as $file => $replacements) {
            //  Skip a file the app does not have
            if (! File::exists($file)) {
                continue;
            }

            //  Swap each path whose alias the config has
            $content = File::get($file);

            foreach ($replacements as $from => [$alias, $to]) {
                if ($this->hasViteAlias($alias)) {
                    $content = str_replace($from, $to, $content);
                }
            }

            //  Keep the file as it was when nothing changed
            if ($content !== File::get($file)) {
                File::put($file, $content);

                $this->done('Moved the imports in '.str_replace(base_path().'/', '', $file).' to the Vite aliases.');
            }
        }
    }

    /**
     * Add the shared Vite aliases to vite.config.js, each only when it is missing, and keep the file only if Node accepts it.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function addViteAliases(): void
    {
        //  Find the config
        $path = base_path('vite.config.js');

        //  Nothing to add to without one
        if (! File::exists($path)) {
            return;
        }

        //  Keep the aliases the config does not have yet
        $original = File::get($path);
        $missing = collect(InstallChecklist::APP_ALIASES)
            ->reject(fn (string $folder, string $alias) => str_contains($original, "'{$alias}'") || str_contains($original, "\"{$alias}\""));

        //  Leave the config alone when it has them all
        if ($missing->isEmpty()) {
            return;
        }

        //  Write each one the way the config already writes its paths
        $usesPath = str_contains($original, 'path.resolve(dirname');
        $lines = $missing
            ->map(fn (string $folder, string $alias) => $usesPath ? "'{$alias}': path.resolve(dirname, '{$folder}')," : "'{$alias}': fileURLToPath(new URL('./{$folder}', import.meta.url)),")
            ->values()
            ->all();

        //  Add them, or say why this config has to be edited by hand
        $updated = $this->withAliases($original, $lines, ! $usesPath);

        if ($updated === null || ! $this->isValidJavaScript($updated)) {
            note('vite.config.js was left as it is. Add the '.implode(', ', $missing->keys()->all()).' aliases to resolve.alias by hand.');

            return;
        }

        //  Keep the edited config
        File::put($path, $updated);

        //  Say what was added
        $this->done('Added the '.implode(', ', $missing->keys()->all()).' aliases to vite.config.js.');
    }

    /**
     * Put alias lines into a Vite config: inside the alias object it has, or in a new resolve setting at the top of defineConfig, and null when the config has neither a place for them nor room for one.
     *
     * @param  array<int, string>  $lines
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function withAliases(string $config, array $lines, bool $needsUrlImport): ?string
    {
        //  Indent the lines to sit in an alias object
        $indented = collect($lines)->map(fn (string $line) => '            '.$line)->implode("\n");

        //  Add to the alias object the config already has
        if (preg_match('/alias:\s*\{/', $config)) {
            $updated = preg_replace('/(alias:\s*\{)/', "$1\n{$indented}", $config, 1);
        } elseif (str_contains($config, 'resolve') || ! preg_match('/defineConfig\(\s*\{/', $config)) {
            //  A resolve setting of its own, or nothing to add to: leave it for the developer
            return null;
        } else {
            //  Start the config with a resolve setting
            $updated = preg_replace('/(defineConfig\(\s*\{)/', "$1\n    resolve: {\n        alias: {\n{$indented}\n        },\n    },", $config, 1);
        }

        //  The url helper the lines use must be imported
        if ($needsUrlImport && ! str_contains($updated, 'fileURLToPath }')) {
            $updated = "import { fileURLToPath } from 'node:url';\n".$updated;
        }

        return $updated;
    }

    /**
     * Tell whether Node accepts the code as a module, which is false when Node is not installed.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function isValidJavaScript(string $code): bool
    {
        //  Write the code where Node can read it as a module
        $file = sys_get_temp_dir().'/nyumbani-'.uniqid().'.mjs';
        File::put($file, $code);

        //  Ask Node to parse it without running it
        $valid = rescue(fn () => Process::run(['node', '--check', $file])->successful(), false, false);

        //  Clean up
        File::delete($file);

        return $valid;
    }

    /**
     * Add the @source lines for the app's PHP files that hold classes, each only when it is missing.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function addSources(string $css): void
    {
        //  Nothing to add to without a stylesheet
        if (! File::exists($css)) {
            return;
        }

        //  Keep the lines the stylesheet does not have yet, in either form
        $missing = collect(InstallChecklist::APP_SOURCES)
            ->reject(fn (string $alias, string $relative) => str_contains(File::get($css), "@source '{$relative}'") || str_contains(File::get($css), "@source '{$alias}'"))
            ->map(fn (string $alias, string $relative) => "@source '".($this->hasViteAlias(strstr($alias, '/', true)) ? $alias : $relative)."';");

        //  Leave the stylesheet alone when it has them all
        if ($missing->isEmpty()) {
            return;
        }

        //  Add them after what is already there
        File::append($css, "\n".$missing->implode("\n")."\n");

        //  Say what was added
        $this->done('Added the enum, data table and config sources in resources/css/app.css.');
    }

    /**
     * Create a starter permissions.json, when the app has none.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function createPermissionsFile(): void
    {
        //  Find where the file belongs
        $path = database_path('seeders/data/permissions.json');

        //  Keep the app's own file
        if (File::exists($path)) {
            return;
        }

        //  Publish the starter, which has one role and its permissions
        $this->callSilent('vendor:publish', ['--tag' => ['essentials-permissions']]);

        //  Say what was created
        $this->done('Created database/seeders/data/permissions.json.');
    }

    /**
     * Set up the basic portal when the developer asks for it with --portal or answers yes, which a non-interactive run never asks.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function setUpPortal(): void
    {
        //  Skip it unless it was asked for, by the flag or by the answer
        if (! $this->option('portal') && ! ($this->input->isInteractive() && confirm('Set up a basic portal (dashboard, layout and sidebar)?', default: false))) {
            return;
        }

        //  Publish the controller and views, each only when the app has none of that name
        $this->callSilent('vendor:publish', ['--tag' => ['essentials-portal']]);

        //  Say what was published
        $this->done('Published the portal views and layout.');

        //  Application code is never edited, so say what is left to do by hand, unless the app has it already
        if ((new InstallChecklist)->hasHomeRoute()) {
            return;
        }

        $this->line(" <fg=#d97706;options=bold>The portal needs a route.</> Add <fg=#d97706>Route::view('dashboard', 'admins.pages.dashboard')->name('dashboard')</> inside your auth group in <fg=#d97706>routes/web.php</>, and return its URL from <fg=#d97706>homeRoute()</> on the user model. The checklist below tracks it.");
        $this->newLine();
    }

    /**
     * Run npm run build when the app has a package.json and its dependencies installed, and say how to do it by hand when it cannot.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function buildAssets(): void
    {
        //  Nothing to build without a package.json
        if (! File::exists(base_path('package.json'))) {
            return;
        }

        //  The build needs the npm dependencies
        if (! File::isDirectory(base_path('node_modules'))) {
            note('Assets were not built: run npm install, then npm run build.');

            return;
        }

        //  Build, and report a failure instead of stopping the install
        $result = spin(fn () => Process::path(base_path())->timeout(600)->run(['npm', 'run', 'build']), 'Building assets with npm run build...');

        if ($result->failed()) {
            error('npm run build failed. Run it by hand to see why.');

            return;
        }

        //  Say what was built
        $this->done('Built the assets with npm run build.');
    }

    /**
     * Ask whether to run the migrations when the permission and activity tables are missing, which defaults to no and is never asked in a non-interactive run.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function offerMigrations(): void
    {
        //  Nothing to offer when the tables exist, or when nobody is there to answer
        if ((new InstallChecklist)->hasTables() || ! $this->input->isInteractive()) {
            return;
        }

        //  Respect a no
        if (! confirm('Run the migrations now?', default: false)) {
            return;
        }

        //  Run them, and report a failure instead of stopping the install
        try {
            $this->call('migrate');
        } catch (Throwable $exception) {
            error("The migrations failed: {$exception->getMessage()}");
        }
    }

    /**
     * Sync the roles and permissions from permissions.json, which needs the permission tables the migrations create.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function syncPermissions(): void
    {
        //  Wait for the migrations, which this command never runs, and say how to carry on
        if (! (new InstallChecklist)->hasTables()) {
            note('Roles and permissions are not synced yet: run php artisan migrate, then run this command again.');

            return;
        }

        //  Sync them, and report a file that cannot be read instead of stopping the install
        try {
            $this->call('nyumbani:permissions');
        } catch (Throwable $exception) {
            error("Roles and permissions were not synced: {$exception->getMessage()}");
        }
    }

    /**
     * Link the public storage folder, when a configured link is missing.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function linkStorage(): void
    {
        //  Find out whether any configured link is missing
        $missing = collect(config('filesystems.links', []))->keys()->contains(fn (string $link) => ! file_exists($link));

        //  Leave the links alone when they all exist
        if ($missing) {
            //  Create the links
            $this->callSilent('storage:link');

            //  Say what was linked
            $this->done('Linked the public storage folder.');
        }
    }

    /**
     * Print a message on a block in the Nyumbani primary colour, the way a prompt's intro and outro are drawn, which Laravel Prompts only draws in cyan.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function banner(string $message): void
    {
        $this->line("<bg=#d97706;fg=black;options=bold> {$message} </>");
    }

    /**
     * Print one finished step as a green tick and its message.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function done(string $message): void
    {
        $this->line("  <fg=green>✓</> {$message}");
    }

    /**
     * Run Pint on the PHP files the installer published or edited, and only those, so the app's own code is never reformatted. Skipped when the app has no Pint.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function formatPublishedFiles(): void
    {
        //  Nothing to format with when the app has no Pint
        if (! File::exists(base_path('vendor/bin/pint'))) {
            return;
        }

        //  Gather the files and folders the installer writes to, that exist
        $paths = collect([
            ...collect(self::CONFIG_TAGS)->map(fn (string $file) => config_path($file)),
            config_path('logging.php'),
            ...File::glob(database_path('migrations/*_'.self::PERMISSIONS_MIGRATION.'.php')),
            ...File::glob(database_path('migrations/*_'.self::GROUP_MIGRATION.'.php')),
            ...collect(self::MIGRATION_TAGS)->flatMap(fn (string $name) => File::glob(database_path("migrations/*_{$name}.php"))),
        ])->filter(fn (string $path) => File::exists($path))->values();

        if ($paths->isEmpty()) {
            return;
        }

        //  Format them, and report a failure instead of stopping the install
        $result = spin(fn () => Process::path(base_path())->run([PHP_BINARY, 'vendor/bin/pint', ...$paths->all()]), 'Formatting the published files with Pint...');

        $result->successful()
            ? $this->done('Formatted the published files with Pint.')
            : note('Pint could not format the published files: run vendor/bin/pint by hand.');
    }

    /**
     * Print what the app still has to do by hand, found by looking, never by editing application code.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function showChecklist(): void
    {
        //  Find the checks
        $items = collect((new InstallChecklist)->items());

        //  Count what is done
        $done = $items->where('state', 'pass')->count();
        $total = $items->count();

        //  Draw a bar for the progress, sized to a fixed width so it never wraps
        $filled = (int) round(($done / max($total, 1)) * 20);
        $bar = '<fg=green>'.str_repeat('━', $filled).'</><fg=gray>'.str_repeat('━', 20 - $filled).'</>';

        //  Print the heading
        $this->newLine();
        $this->line(" <options=bold>Setup checklist</>  {$bar}  <fg=gray>{$done} of {$total} done</>");
        $this->newLine();

        //  One line per check: a mark, then its label, in red while it is to do
        $width = max(40, (new Terminal)->getWidth() - 8);

        foreach ($items as $item) {
            [$mark, $color] = match ($item['state']) {
                'pass' => ['✓', 'green'],
                'optional' => ['○', 'yellow'],
                default => ['✗', 'red'],
            };

            //  A done check stays quiet
            if ($item['state'] === 'pass') {
                $this->line("  <fg=green>{$mark}</> <fg=gray>{$item['label']}</>");

                continue;
            }

            //  What is left shows its label and, beneath it, how to fix it
            $label = $item['state'] === 'optional' ? "<options=bold>{$item['label']}</> <fg=gray>(optional)</>" : "<fg=red;options=bold>{$item['label']}</>";
            $this->line("  <fg={$color};options=bold>{$mark}</> {$label}");
            $this->line('    <fg=#d97706>→</> <fg=gray>'.wordwrap($item['fix'], $width, "</>\n      <fg=gray>", false).'</>');
        }
    }

    /**
     * Invite the developer to the docs, which every Nyumbani package serves with examples that run.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function showDocsInvitation(): void
    {
        //  Find the docs the packages registered
        $registry = app(DocsRegistry::class);
        $essentials = $registry->find('nyumbani-essentials');

        //  Leave the invitation out when Essentials registered none
        if (! $essentials) {
            return;
        }

        //  Line up a link for the first page to read, then one for each package
        $links = collect([
            'Start here' => $essentials->url('quick-start'),
            'Essentials' => $essentials->url(),
            'Payments' => $registry->find('nyumbani-daraja')?->url(),
            'SMS' => $registry->find('nyumbani-messaging')?->url(),
            'Tables' => $registry->find('nyumbani-dt-tables')?->url(),
        ])->filter();

        //  Print the invitation
        $this->newLine();
        $this->line(' <fg=#d97706;options=bold>✦ Nyumbani Essentials is ready</>');
        $this->newLine();
        $this->line(' <fg=gray>Every feature has a page with examples you can run, not only read.</>');
        $this->newLine();

        //  One link per line, with the labels and addresses on opposite sides
        $links->each(fn (string $url, string $label) => $this->components->twoColumnDetail($label, $url));

        //  Say where the docs are served
        $this->newLine();
        $this->line(' <fg=gray>The docs are served only in the local environment.</>');
    }

    /**
     * Determine whether Laravel Boost is installed, which owns the guidelines, skills and the rules index.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function isBoostInstalled(): bool
    {
        return class_exists(BoostInstallCommand::class);
    }

    /**
     * Require what is missing, Boost and the Nyumbani packages Boost would otherwise ignore, then run the command again in a fresh process, which can load them.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function installBoostAndContinue(): int
    {
        //  A second pass that still finds something missing stops here instead of looping
        if (getenv(self::RESTARTED)) {
            //  Say how to do it by hand
            error('Something is still missing after installing it. Run composer require laravel/boost --dev and composer require '.implode(' ', self::DIRECT_PACKAGES).', then run this command again.');

            //  Stop
            return self::FAILURE;
        }

        //  Install Boost when it is missing
        if (! $this->isBoostInstalled()) {
            //  Ask first when someone is there to answer
            if ($this->input->isInteractive() && ! confirm('Laravel Boost is missing. Install it now with composer require laravel/boost --dev?', default: true)) {
                //  Respect the answer
                error('Laravel Boost is needed for the rules, guidelines and skills. Run composer require laravel/boost --dev, then run this command again.');

                return self::FAILURE;
            }

            //  Stop when Composer cannot install it
            if (! $this->requireBoost()) {
                //  Say how to do it by hand
                error('Could not install laravel/boost. Run composer require laravel/boost --dev, then run this command again.');

                //  Stop
                return self::FAILURE;
            }
        }

        //  Require the Nyumbani packages the app only gets through Essentials, since Boost loads only the packages an app requires itself
        $missing = $this->missingPackages();

        if ($missing !== []) {
            //  Say why
            $this->done('Boost only loads packages the app requires itself, so '.implode(' and ', $missing).' must be required directly.');

            //  Stop when Composer cannot require them
            if (! $this->requirePackages($missing)) {
                //  Say how to do it by hand
                error('Could not require '.implode(' and ', $missing).'. Run composer require '.implode(' ', $missing).', then run this command again.');

                //  Stop
                return self::FAILURE;
            }
        }

        //  Finish the setup where the new packages are loaded
        return $this->restart();
    }

    /**
     * Get the list of Nyumbani packages the app does not require itself, which Boost would ignore.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function missingPackages(): array
    {
        return (new DirectRequirements)->missing(self::DIRECT_PACKAGES);
    }

    /**
     * Run composer require for a list of packages behind a spinner, and show what Composer said if it fails.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function requirePackages(array $packages): bool
    {
        //  Run composer while a spinner shows
        $result = spin(
            fn () => Process::path(base_path())->forever()->run(['composer', 'require', ...$packages, '--no-interaction']),
            'Requiring '.implode(' and ', $packages).'...',
        );

        //  Show what Composer said when it failed
        if ($result->failed()) {
            note(trim($result->errorOutput() ?: $result->output()));
        }

        return $result->successful();
    }

    /**
     * Run composer require laravel/boost --dev behind a spinner, and show what Composer said if it fails.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function requireBoost(): bool
    {
        //  Run composer while a spinner shows
        $result = spin(
            fn () => Process::path(base_path())->forever()->run(['composer', 'require', 'laravel/boost', '--dev', '--no-interaction']),
            'Installing laravel/boost as a dev dependency...',
        );

        //  Show what Composer said when it failed
        if ($result->failed()) {
            note(trim($result->errorOutput() ?: $result->output()));
        }

        return $result->successful();
    }

    /**
     * Run this command again in a new process, interactive when this one is, and return its exit code.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function restart(): int
    {
        //  Keep asking questions only when this run does
        $interactive = $this->input->isInteractive();

        //  Run it in a new process, marked as the second pass, and show its output
        return Process::path(base_path())
            ->forever()
            ->env([self::RESTARTED => '1'])
            ->tty($interactive && SymfonyProcess::isTtySupported())
            ->run([PHP_BINARY, 'artisan', 'nyumbani:install', ...($interactive ? [] : ['--no-interaction']), ...($this->option('portal') ? ['--portal'] : [])], fn (string $type, string $output) => $this->output->write($output))
            ->exitCode();
    }

    /**
     * Copy the package rules into .ai/rules and rebuild the index.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function publishRules(): void
    {
        //  Get the destination folder
        $destination = base_path('.ai/rules');

        //  Make sure the folder exists
        File::ensureDirectoryExists($destination);

        //  Collect the file names the package ships
        $shipped = collect(File::glob(essentials_path('resources/boost/rules/*.md')))
            ->map(fn (string $path) => self::RULE_PREFIX.basename($path));

        //  Remove the owned files the package no longer ships
        collect($this->installedRules())
            ->reject(fn (string $path) => $shipped->contains(basename($path)))
            ->each(fn (string $path) => File::delete($path));

        //  Copy every shipped rule, marking it as managed
        collect(File::glob(essentials_path('resources/boost/rules/*.md')))->each(
            fn (string $path) => File::put(
                $destination.'/'.self::RULE_PREFIX.basename($path),
                $this->managedContent($path),
            )
        );

        //  Rebuild the rules index
        $this->rebuildIndex();

        //  Report the result
        $this->done("Published {$shipped->count()} rule files to .ai/rules.");
    }

    /**
     * Mark a rule as managed.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function managedContent(string $path): string
    {
        //  Read the rule
        $content = File::get($path);

        //  Define the notice
        $notice = "<!-- Managed by nyumbani/essentials. Do not edit: run php artisan nyumbani:install to refresh. -->\n\n";

        //  Place the notice after the frontmatter
        return (string) preg_replace('/^(---\R.*?\R---\R\R?)/s', '$1'.$notice, $content, 1);
    }

    /**
     * Return the paths of the rule files this package already put in the app.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function installedRules(): array
    {
        return File::glob(base_path('.ai/rules/'.self::RULE_PREFIX.'*.md')) ?: [];
    }

    /**
     * Install Boost the first time, when it has no settings yet, then load the Nyumbani packages and refresh its guidelines and skills.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function setUpBoost(): void
    {
        //  Install Boost only the first time, asking unless this run is non-interactive
        if (! File::exists(base_path('boost.json'))) {
            $this->input->isInteractive()
                ? $this->call('boost:install')
                : $this->runWithoutAsking('boost:install', ['--guidelines' => true, '--skills' => true, '--mcp' => true]);
        }

        //  Load the Nyumbani packages into Boost
        $this->enableBoostPackages();

        //  Refresh the guidelines and skills without asking, so it is safe in a composer script
        $this->runWithoutAsking('boost:update');
    }

    /**
     * Run another command with prompts switched off, which a nested call cannot do on its own.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function runWithoutAsking(string $command, array $options = []): int
    {
        //  Build the input and answer every prompt with its default
        $input = new ArrayInput($options);
        $input->setInteractive(false);

        return $this->getApplication()->find($command)->run($input, $this->output);
    }

    /**
     * Add Essentials, Daraja, Messaging and DtTables to the packages Boost loads from, which it keeps in boost.json.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function enableBoostPackages(): void
    {
        //  Nothing to enable until Boost has written its settings
        $path = base_path('boost.json');

        if (! File::exists($path)) {
            return;
        }

        //  Merge it into the packages Boost already loads
        $settings = json_decode(File::get($path), true) ?? [];
        $settings['packages'] = array_values(array_unique([...($settings['packages'] ?? []), ...self::BOOST_PACKAGES]));

        //  List the packages' skills too, since boost:update installs only the skills named here and finds new ones only by asking
        $settings['skills'] = array_values(array_unique([...($settings['skills'] ?? []), ...$this->packageSkills()]));

        //  A non-interactive first install picks no agent, and boost:update refuses to run without one, so default to Claude Code
        if (empty($settings['agents'])) {
            $settings['agents'] = ['claude_code'];
        }

        //  Save the settings
        File::put($path, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
    }

    /**
     * Get the names of the skills the Nyumbani packages ship, read from their folders in vendor.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function packageSkills(): array
    {
        return collect(self::BOOST_PACKAGES)
            ->map(fn (string $package) => base_path("vendor/{$package}/resources/boost/skills"))
            ->filter(fn (string $folder) => File::isDirectory($folder))
            ->flatMap(fn (string $folder) => File::directories($folder))
            ->map(fn (string $skill) => basename($skill))
            ->values()
            ->all();
    }

    /**
     * Rebuild .ai/rules/index.md through Boost.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function rebuildIndex(): void
    {
        //  Let Boost rewrite the index
        app(RuleRepository::class)->writeIndex();
    }
}
