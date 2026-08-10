<?php

namespace App\Support;

use Illuminate\Validation\Rules\Password;

class AdminPasswordPolicy
{
    public static function rule(): Password
    {
        $rule = Password::min(12)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        if (app()->environment('production')) {
            $rule->uncompromised();
        }

        return $rule;
    }
}
