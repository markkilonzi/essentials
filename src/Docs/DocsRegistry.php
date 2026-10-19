<?php

namespace Nyumbani\Essentials\Docs;

use Illuminate\Support\Collection;

/**
 * The docs of every Nyumbani package installed in the app, so each package can add its own
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class DocsRegistry
{
    /**
     * The registered docs by slug
     *
     * @var array<string, Docs>
     */
    protected array $docs = [];

    /**
     * Add the docs of a package
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function register(Docs $docs): void
    {
        $this->docs[$docs->slug] = $docs;
    }

    /**
     * Find the docs of a package by the slug in their url
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function find(string $slug): ?Docs
    {
        return $this->docs[$slug] ?? null;
    }

    /**
     * Get the docs of every package, ordered by slug
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function all(): Collection
    {
        return collect($this->docs)->sortKeys();
    }
}
