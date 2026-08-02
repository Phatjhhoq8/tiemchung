<?php

namespace Modules\VaccineRegistration\Support;

class PhoneNormalizer
{
    public static function normalize(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', trim((string) $value));

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = '84' . substr($digits, 1);
        }

        return preg_match('/^84\d{9}$/', $digits) ? '+' . $digits : null;
    }

    public static function display(?string $value): string
    {
        $phone = self::normalize($value);

        return $phone ? '0' . substr($phone, 3) : (string) $value;
    }
}
