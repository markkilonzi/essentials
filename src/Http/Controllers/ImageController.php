<?php

namespace Nyumbani\Essentials\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController
{
    /**
     * Serve one of the package images, cached for a year.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __invoke(string $image): BinaryFileResponse
    {
        //  Resolve the image inside the package images folder only
        $path = essentials_path('resources/images/'.basename($image));

        //  Refuse anything that is not a shipped image
        abort_unless(is_file($path), 404);

        //  Return the file with long cache headers
        return response()->file($path, ['Cache-Control' => 'public, max-age=31536000, immutable']);
    }
}
