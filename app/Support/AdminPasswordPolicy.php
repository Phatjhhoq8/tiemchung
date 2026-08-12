<?php

namespace App\Support;

use Illuminate\Validation\Rules\Password;

class AdminPasswordPolicy
{
    public static function rule(): Password
    {
        return Password::min(8);
    }
}
