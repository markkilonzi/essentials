<?php

namespace Nyumbani\Essentials\Enums;

use Nyumbani\Essentials\Traits\HasEnumElements;

/**
 * UploadDisk enum representing the filesystem disks an upload can be stored on
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
enum UploadDisk: string
{
    use HasEnumElements;

    case PUBLIC = 'public';
    case LOCAL = 'local';
}
