<?php

namespace Nyumbani\Essentials\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravel\Boost\Console\InstallCommand as BoostInstallCommand;
use Laravel\Boost\Rules\RuleRepository;
use Symfony\Component\Console\Input\ArrayInput;

use function Laravel\Prompts\multiselect;

class InstallCommand extends Command
{
    /**
     * The prefix of every rule file the package owns inside the app's .ai/rules folder.
     */
    protected const RULE_PREFIX = 'nyumbani-';

    /**
     * The package whose guidelines and skills Boost should load.
     */
    protected const BOOST_PACKAGE = 'nyumbani/essentials';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nyumbani:install
        {--sync : Re-publish what is already installed, without asking}
        {--all : Publish everything, without asking}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the Nyumbani rules into this app and set up Laravel Boost with the Nyumbani guidelines and skills';

    /**
     * Execute the console command.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function handle(): int
    {
        //  Work out what to publish
        $selected = $this->selection();

        //  Publish the rules when they were chosen
        if (in_array('rules', $selected, true)) {
            $this->publishRules();
        }

        //  Set up Boost when it was chosen
        if (in_array('boost', $selected, true)) {
            $this->setUpBoost();
        }

        //  Tell the developer when nothing was chosen
        if ($selected === [] && ! $this->option('sync')) {
            $this->components->warn('Nothing was selected, so nothing was published.');
        }

        //  Rebuild the index when rules were published
        return self::SUCCESS;
    }

    /**
     * Decide what to publish.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function selection(): array
    {
        //  Everything is wanted
        if ($this->option('all')) {
            return array_keys($this->choices());
        }

        //  Only what is already installed is wanted
        if ($this->option('sync')) {
            return $this->installed();
        }

        //  Ask the developer, pre-selecting what is already installed
        return multiselect(
            label: 'What do you want to publish?',
            options: $this->choices(),
            default: $this->installed(),
        );
    }

    /**
     * Return the things that can be published, keyed by their identifier.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function choices(): array
    {
        return [
            'rules' => 'AI rules (copied into .ai/rules as nyumbani-*.md)',
            'boost' => 'Laravel Boost (guidelines, skills and MCP, with Essentials enabled)',
        ];
    }

    /**
     * Return the identifiers of what is already installed.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function installed(): array
    {
        return array_values(array_filter([
            $this->installedRules() === [] ? null : 'rules',
            File::exists(base_path('boost.json')) ? 'boost' : null,
        ]));
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
        $this->components->info("Published {$shipped->count()} rule files to .ai/rules.");
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
        $notice = "<!-- Managed by nyumbani/essentials. Do not edit: run php artisan nyumbani:install --sync to refresh. -->\n\n";

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
     * Install Boost, or refresh it when syncing, then load the Essentials package into it.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function setUpBoost(): void
    {
        //  Warn when Boost is not installed, since it owns the guidelines and skills
        if (! class_exists(BoostInstallCommand::class)) {
            $this->components->warn('laravel/boost is not installed, so Boost was not set up. Run composer require laravel/boost --dev first.');

            return;
        }

        //  Install Boost unless this is a sync, which only refreshes what is installed
        if (! $this->option('sync')) {
            $this->option('all')
                ? $this->runWithoutAsking('boost:install', ['--guidelines' => true, '--skills' => true, '--mcp' => true])
                : $this->call('boost:install');
        }

        //  Load Essentials into Boost
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
     * Add Essentials to the packages Boost loads from, which it keeps in boost.json.
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
        $settings['packages'] = array_values(array_unique([...($settings['packages'] ?? []), self::BOOST_PACKAGE]));

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
        //  Warn when Boost is not installed, since it owns the index
        if (! class_exists(RuleRepository::class)) {
            $this->components->warn('laravel/boost is not installed, so .ai/rules/index.md was not rebuilt.');

            return;
        }

        //  Let Boost rewrite the index
        app(RuleRepository::class)->writeIndex();
    }
}
