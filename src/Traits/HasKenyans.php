<?php

namespace Nyumbani\Essentials\Traits;

use Illuminate\Support\Facades\File;

trait HasKenyans
{
    /**
     * Get a user's kenyan name, picked from the package's resources/json/kenyan-names.json
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function generateKenyanName(): string
    {
        //  Get names from json file
        $names = json_decode(File::get(essentials_path('resources/json/kenyan-names.json')), true);

        //  Get the first name
        $firstName = fake()->randomElement($names);

        //  Ensure that the first name and second name will never be the same
        do {
            $secondName = fake()->randomElement($names);
        } while ($secondName === $firstName);

        //  Return the name
        return "$firstName $secondName";
    }

    /**
     * Generate a kenyan looking phone number, deliberately one digit too long so it can never reach a real recipient
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function generateKenyanPhoneNumber(): string
    {
        return '254'
            .($pre = fake()->randomElement([7, 1]))
            .($pre == 7 ? fake()->randomElement(range(0, 9)) : fake()->randomElement(range(0, 1)))
            .fake()->randomElement(range(0, 9))
            .implode(fake()->randomElements(range(0, 9), 7));
    }
}
