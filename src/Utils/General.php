<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

//  Define the general helper functions of the package, each guarded by function_exists()

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

if (! function_exists('user')) {
    /**
     * Get the authenticated user
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    function user(): ?Authenticatable
    {
        return Auth::user();
    }
}

if (! function_exists('prettify')) {
    /**
     * Prettify text, turning snake and kebab case into Title Case
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    function prettify(?string $text): ?string
    {
        return $text
            ? ucwords(str_replace('-', ' ', str_replace('_', ' ', strtolower($text))))
            : $text;
    }
}

if (! function_exists('numeric')) {
    /**
     * Format numbers, leaving non-numeric values as they are
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    function numeric(mixed $number, int $decimals = 2): mixed
    {
        return ! is_null($number) && is_numeric($number)
            ? number_format($number, $decimals)
            : $number;
    }
}

if (! function_exists('percentage')) {
    /**
     * Get the percentage a value makes of a total, or 0 when the total is not above 0
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    function percentage(mixed $value, float $total): mixed
    {
        //  Ensure that the total will always be above 0
        if ($total > 0) {
            $percentage = $value / $total * 100;
        }

        //  Else return 0
        return numeric($percentage ?? 0);
    }
}

if (! function_exists('currency')) {
    /**
     * Get the currency symbol, taken from essentials.currency
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    function currency(): string
    {
        return (string) config('essentials.currency', 'KES');
    }
}

if (! function_exists('money')) {
    /**
     * Format money for accounting purposes, optionally prefixed with the currency symbol
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    function money(?float $amount = 0, bool $showCurrency = true): mixed
    {
        //  Return null values as they are
        if ($amount === null) {
            return $amount;
        }

        //  Build the formatted amount
        $formattedAmount = numeric($amount);

        //  Prepend the currency symbol if required
        return $showCurrency
            ? currency().' '.$formattedAmount
            : $formattedAmount;
    }
}

if (! function_exists('bag')) {
    /**
     * Return a hidden input that scopes validation errors to an error bag
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    function bag(string $value): HtmlString
    {
        return new HtmlString('<input type="hidden" name="_bag" value="'.e($value).'">');
    }
}
