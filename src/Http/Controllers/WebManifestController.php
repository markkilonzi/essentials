<?php

namespace Nyumbani\Essentials\Http\Controllers;

use Illuminate\Http\JsonResponse;

class WebManifestController
{
    /**
     * Describe the installable app, named after the app that uses the package.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __invoke(): JsonResponse
    {
        //  Get the app name
        $name = config('app.name');

        //  Get the theme colour
        $color = config('essentials.branding.theme_color');

        return response()->json([
            'name' => $name,
            'short_name' => $name,
            'icons' => [
                ['src' => route('essentials.favicon', ['image' => 'android-chrome-192x192.png']), 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => route('essentials.favicon', ['image' => 'android-chrome-512x512.png']), 'sizes' => '512x512', 'type' => 'image/png'],
            ],
            'theme_color' => $color,
            'background_color' => '#ffffff',
            'display' => 'standalone',
        ], 200, ['Content-Type' => 'application/manifest+json']);
    }
}
