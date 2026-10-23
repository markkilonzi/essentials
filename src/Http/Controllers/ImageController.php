<?php

namespace Nyumbani\Essentials\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController
{
    /**
     * Serve one of the package images, or one of its favicons when the route names that folder, cached for a year.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __invoke(string $image, string $folder = ''): BinaryFileResponse
    {
        //  Resolve the image inside the package images folder, or the folder the route names, only
        $path = essentials_path('resources/images/'.($folder !== '' ? $folder.'/' : '').basename($image));

        //  Refuse anything that is not a shipped image
        abort_unless(is_file($path), 404);

        //  Return the file with long cache headers
        return response()->file($path, ['Cache-Control' => 'public, max-age=31536000, immutable']);
    }
}
