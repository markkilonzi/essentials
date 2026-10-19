<?php

namespace Nyumbani\Essentials\Http\Controllers;

use Illuminate\Contracts\View\View;
use Nyumbani\Essentials\Docs\DocsRegistry;

class DocsController
{
    /**
     * Show a page of the docs of a package
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __invoke(DocsRegistry $registry, string $package, ?string $page = null): View
    {
        //  Only the packages that registered docs have them
        $docs = $registry->find($package);
        abort_unless($docs, 404);

        //  Open on the home page, and only show the pages in the sidebar
        $page ??= $docs->home;
        abort_unless($docs->pages()->has($page), 404);

        return view($docs->view($page), [
            'docs' => $docs,
            'packages' => $registry->all(),
            'current' => $page,
            'neighbours' => $docs->neighbours($page),
        ]);
    }
}
