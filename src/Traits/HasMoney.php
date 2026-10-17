<?php

namespace Nyumbani\Essentials\Traits;

trait HasMoney
{
    /**
     * Return the currency symbol, taken from essentials.currency
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function currencySymbol(): string
    {
        return currency();
    }

    /**
     * Format the amount of money to be displayed, as an absolute value unless told otherwise
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function money(?float $amount = 0, bool $currency = true, bool $absolute = true): string
    {
        //  Drop the sign when an absolute value is wanted
        $amount = $absolute ? abs($amount ?? 0) : ($amount ?? 0);

        //  Format the amount, with or without the currency symbol
        return (string) money($amount, $currency);
    }

    /**
     * Return the relative amount of money without considering the absolute value
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function relativeMoney(float $amount, bool $currency = true): string
    {
        return $this->money($amount, $currency, false);
    }
}
