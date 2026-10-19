<?php

namespace Nyumbani\Essentials\Docs;

use Illuminate\Support\Collection;

/**
 * The docs of one Nyumbani package: its pages, grouped by the concept they explain, and where they are served
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class Docs
{
    /**
     * Create the docs of a package
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        protected array $sections,
        protected string $views,
        public readonly string $home = 'introduction',
    ) {}

    /**
     * Get the path the docs are served from
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function path(): string
    {
        return "{$this->slug}/docs";
    }

    /**
     * Get the url of a page, or of the home page when none is given
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function url(?string $page = null): string
    {
        return route('essentials.docs', ['package' => $this->slug, 'page' => $page === $this->home ? null : $page]);
    }

    /**
     * Get the name of the view of a page
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function view(string $page): string
    {
        return "{$this->views}.{$page}";
    }

    /**
     * Get the sections of the sidebar, each with its pages as slug => title
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function sections(): Collection
    {
        return collect($this->sections);
    }

    /**
     * Get every page as slug => title, in reading order
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function pages(): Collection
    {
        return $this->sections()->flatMap(fn (array $section) => $section['pages']);
    }

    /**
     * Get the pages before and after a page, as slug => title
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function neighbours(string $slug): array
    {
        //  Find where the page sits in the reading order
        $slugs = $this->pages()->keys();
        $position = $slugs->search($slug);

        //  Pick the pages either side of it
        $previous = $slugs->get($position - 1);
        $next = $slugs->get($position + 1);

        return [
            'previous' => $position > 0 && $previous ? [$previous => $this->pages()[$previous]] : null,
            'next' => $next ? [$next => $this->pages()[$next]] : null,
        ];
    }

    /**
     * Describe the docs of Essentials
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public static function essentials(): static
    {
        return new static('nyumbani-essentials', 'Essentials', [
            ['title' => 'Getting started', 'pages' => [
                'introduction' => 'Introduction',
                'installation' => 'Installation',
                'configuration' => 'Configuration',
            ]],
            ['title' => 'Foundation', 'pages' => [
                'defaults' => 'App defaults',
                'helpers' => 'Helpers',
                'macros' => 'Macros',
                'validation' => 'Validation',
                'error-bags' => 'Error bags',
                'javascript' => 'JavaScript',
            ]],
            ['title' => 'Models', 'pages' => [
                'traits' => 'Traits',
                'permissions' => 'Roles and permissions',
            ]],
            ['title' => 'Interface', 'pages' => [
                'layouts' => 'Layouts',
                'theme' => 'Theme and buttons',
                'modal' => 'Modal',
                'alerts-and-loader' => 'Alerts and loader',
                'error-pages' => 'Error pages',
            ]],
            ['title' => 'Conventions', 'pages' => [
                'code-style' => 'Code style',
                'app-code' => 'Application code',
                'http-layer' => 'HTTP layer',
                'data-layer' => 'Models and data',
                'view-layer' => 'Views and scripts',
            ]],
            ['title' => 'Tooling', 'pages' => [
                'rules-and-skills' => 'Rules and skills',
                'logging' => 'Logging to Discord',
            ]],
        ], 'essentials::docs.pages');
    }
}
