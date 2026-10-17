<?php

namespace Nyumbani\Essentials\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KenyanNumberValid implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //  Fail when the value is not a Kenyan mobile number
        if (! preg_match('/^(0|\+254|254)?(7[0-9]|1[0-1])[0-9]{1}[0-9]{6}$/', (string) $value)) {
            $fail('The :attribute must be a valid Kenyan number.');
        }
    }
}
