<?php

namespace Nyumbani\Essentials\Docs;

use Nyumbani\Essentials\Traits\HasEnumElements;

/**
 * An enum made up for the interactive docs, which uses HasEnumElements
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
enum DemoStatus: string
{
    use HasEnumElements;

    case PAID = 'paid';
    case PARTIALLY_PAID = 'partially-paid';
    case ON_HOLD = 'on-hold';
}
