<?php

namespace Nyumbani\Essentials\Traits;

trait HasEnumElements
{
    /**
     * Return a label from the enum
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function label(): string
    {
        return prettify($this->lowerLabel());
    }

    /**
     * Return a lower case label from the enum
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function lowerLabel(): string
    {
        return strtolower($this->name);
    }

    /**
     * Get a random element
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public static function random(): self
    {
        return fake()->randomElement(self::cases());
    }
}
