<?php

//  Define the global helper functions of the package, each guarded by function_exists()

if (! function_exists('essentials_path')) {
    /**
     * Get the path to a file or folder inside the package.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    function essentials_path(string $path = ''): string
    {
        return dirname(__DIR__, 2).($path ? DIRECTORY_SEPARATOR.ltrim($path, '/\\') : '');
    }
}
