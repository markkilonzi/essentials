<?php

namespace Nyumbani\Essentials\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Laravel\Boost\Console\InstallCommand as BoostInstallCommand;
use Laravel\Boost\Rules\RuleRepository;
use Nyumbani\Essentials\Console\Support\InstallChecklist;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process as SymfonyProcess;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

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
    ];

    /**
     * The migration tags to publish, with the name of the migration each one creates.
     *
     * @var array<string, string>
     */
    protected const MIGRATION_TAGS = [
        'permission-migrations' => 'create_permission_tables',
        'activitylog-migrations' => 'create_activity_log_table',
    ];

    /**
     * The migration Essentials adds to the permissions table, which has to run after the Spatie one that creates it.
     */
    protected const GROUP_MIGRATION = 'update_permissions_table_add_group_column';

    /**
     * The environment keys the packages read, which .env.example should list.
     *
     * @var list<string>
     */
    protected const ENV_KEYS = [
        'ESSENTIALS_CURRENCY', 'ESSENTIALS_THEME_COLOR', 'ESSENTIALS_DESCRIPTION',
        'DARAJA_ENVIRONMENT', 'DARAJA_SHORT_CODE', 'DARAJA_CONSUMER_KEY', 'DARAJA_CONSUMER_SECRET', 'DARAJA_PASS_KEY', 'DARAJA_TILL_NUMBER',
        'NYUMBANI_CONNECT_BASE_URL', 'NYUMBANI_CONNECT_TOKEN', 'MESSAGING_SMS_ENABLED', 'MESSAGING_SMS_SENDER_ID',
        'DISCORD_WEBHOOK_URL', 'DISCORD_LOG_LEVEL',
    ];

    /**
     * The environment variable that marks the second pass of the command, so a Boost that still cannot be loaded never loops.
     */
    protected const RESTARTED = 'NYUMBANI_INSTALL_RESTARTED';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nyumbani:install';

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
        intro('Nyumbani install');

        //  Install Boost first when it is missing, and let a fresh process finish, since its classes are not loaded here
        if (! $this->isBoostInstalled()) {
            return $this->installBoostAndContinue();
        }

        //  Copy the rules, replacing the ones the package manages
        $this->publishRules();

        //  Publish the config files and migrations of the packages, once
        $this->publishPackageFiles();

        //  Prepare the app's own files, adding only what is missing
        $this->addEnvKeys();

        //  Add the package's imports
        $this->addImports();

        //  Create the starter permissions file
        $this->createPermissionsFile();

        //  Link the public storage folder
        $this->linkStorage();

        //  Set up Boost the first time, then refresh its guidelines and skills
        $this->setUpBoost();

        //  Say what is still left to do by hand
        $this->showChecklist();

        //  Close with the next step
        outro('Done. Run php artisan migrate if the checklist asks for it, and run this command again whenever a package changes.');

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
            info("Published config/{$file}.");
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
            info("Published the {$name} migration.");
        }

        //  The group column is copied last, so its timestamp is never earlier than the table's, which the Spatie publish stamps
        if (File::glob(database_path('migrations/*_'.self::GROUP_MIGRATION.'.php')) === []) {
            //  Make sure the migrations folder exists
            File::ensureDirectoryExists(database_path('migrations'));

            //  Copy the stub under a timestamped name
            File::copy(essentials_path('database/migrations/'.self::GROUP_MIGRATION.'.php.stub'), database_path('migrations/'.now()->format('Y_m_d_His').'_'.self::GROUP_MIGRATION.'.php'));

            //  Say what was published
            info('Published the '.self::GROUP_MIGRATION.' migration.');
        }
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
        info("Added {$missing->count()} keys to .env.example.");
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

        //  The theme goes after the Tailwind import, unless it is already imported
        if (File::exists($css) && ! str_contains(File::get($css), 'nyumbani/essentials/resources/css/theme.css')) {
            //  Add the import
            File::append($css, "\n@import '../../vendor/nyumbani/essentials/resources/css/theme.css';\n");

            //  Say what was imported
            info('Imported the theme in resources/css/app.css.');
        }

        //  Find the script
        $js = resource_path('js/app.js');

        //  The scripts go first, so they load before Livewire starts, unless they are already imported
        if (File::exists($js) && ! str_contains(File::get($js), 'nyumbani/essentials/resources/dist/essentials.js')) {
            //  Add the import
            File::prepend($js, "import '../../vendor/nyumbani/essentials/resources/dist/essentials.js';\n");

            //  Say what was imported
            info('Imported the scripts in resources/js/app.js.');
        }
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

        //  Make sure the folder exists
        File::ensureDirectoryExists(dirname($path));

        //  Write a starter with one role and its permissions
        File::put($path, json_encode(['admin' => ['users' => ['view-users', 'create-user', 'update-user', 'delete-user']]], JSON_PRETTY_PRINT)."\n");

        //  Say what was created
        info('Created database/seeders/data/permissions.json.');
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
            info('Linked the public storage folder.');
        }
    }

    /**
     * Print what the app still has to do by hand, found by looking, never by editing application code.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function showChecklist(): void
    {
        //  Turn every check into a row, with the fix only for what is not done
        $rows = collect((new InstallChecklist)->items())->map(fn (array $item) => [
            $item['label'],
            match ($item['state']) {
                'pass' => '✓ done',
                'optional' => '○ optional',
                default => '✗ to do',
            },
            $item['state'] === 'pass' ? '' : $item['fix'],
        ])->all();

        //  Show them as a table
        table(['Setup checklist', 'State', 'How to fix'], $rows);
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
     * Require Boost as a dev dependency, then run the command again in a fresh process, which can load it.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function installBoostAndContinue(): int
    {
        //  A second pass that still finds no Boost stops here instead of looping
        if (getenv(self::RESTARTED)) {
            //  Say how to do it by hand
            error('laravel/boost is still not available after installing it. Run composer require laravel/boost --dev, then run this command again.');

            //  Stop
            return self::FAILURE;
        }

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

        //  Finish the setup where Boost is loaded
        return $this->restart();
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
            ->run([PHP_BINARY, 'artisan', 'nyumbani:install', ...($interactive ? [] : ['--no-interaction'])], fn (string $type, string $output) => $this->output->write($output))
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
        info("Published {$shipped->count()} rule files to .ai/rules.");
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
     * Add Essentials, Daraja and Messaging to the packages Boost loads from, which it keeps in boost.json.
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

        //  Save the settings
        File::put($path, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
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
