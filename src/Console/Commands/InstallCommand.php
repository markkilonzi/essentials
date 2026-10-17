<?php

namespace Nyumbani\Essentials\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravel\Boost\Rules\RuleRepository;

use function Laravel\Prompts\multiselect;

class InstallCommand extends Command
{
    /**
     * The prefix of every rule file the package owns inside the app's .ai/rules folder.
     */
    protected const RULE_PREFIX = 'nyumbani-';

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
    protected $description = 'Publish the Nyumbani Essentials rules (and more, as they arrive) into this app';

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

        //  Tell the developer when nothing was chosen
        if ($selected === [] && ! $this->option('sync')) {
            $this->components->warn('Nothing was selected, so nothing was published.');
        }

        return self::SUCCESS;
    }

    /**
     * Decide what to publish: everything with --all, what is already installed with --sync, otherwise whatever the developer picks.
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
            return $this->installedRules() === [] ? [] : ['rules'];
        }

        //  Ask the developer, pre-selecting what is already installed
        return multiselect(
            label: 'What do you want to publish?',
            options: $this->choices(),
            default: $this->installedRules() === [] ? [] : ['rules'],
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
        ];
    }

    /**
     * Copy the package rules into the app's .ai/rules folder, drop the ones the package no longer has, and rebuild the index.
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
     * Return the package rule with a notice under its frontmatter that it is managed and will be overwritten.
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
     * Rebuild .ai/rules/index.md through Boost, so the rows come from each rule's paths frontmatter.
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
