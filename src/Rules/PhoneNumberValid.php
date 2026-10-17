<?php

namespace Nyumbani\Essentials\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberValid implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //  Fail when the value is not an international phone number
        if (! preg_match('/^\+?[0-9]{7,15}$/', (string) $value)) {
            $fail('The :attribute must be a valid phone number.');
        }
    }
}
