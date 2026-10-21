<?php

namespace Nyumbani\Essentials\Console\Support;

use Illuminate\Support\Facades\File;

/**
 * The packages an app lists in its own composer.json, which is what Laravel Boost counts as direct dependencies
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class DirectRequirements
{
    /**
     * Get the packages among the given list that the app does not require itself, however many it gets through other packages
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function missing(array $packages): array
    {
        //  Without a composer.json there is nothing to compare with
        $path = base_path('composer.json');

        if (! File::exists($path)) {
            return [];
        }

        //  Read the packages the app requires itself, in production and in development
        $composer = json_decode(File::get($path), true) ?? [];
        $required = array_keys(array_merge($composer['require'] ?? [], $composer['require-dev'] ?? []));

        //  Keep the ones that are missing
        return array_values(array_diff($packages, $required));
    }
}
