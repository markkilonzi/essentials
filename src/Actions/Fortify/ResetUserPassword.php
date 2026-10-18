<?php

namespace Nyumbani\Essentials\Actions\Fortify;

use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the password.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function reset($user, array $input): void
    {
        //  Validate the input
        Validator::make($input, ['password' => $this->passwordRules()])->validate();

        //  Update the user's password
        $user->update(['password' => $input['password']]);
    }
}
