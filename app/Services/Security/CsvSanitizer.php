<?php

namespace App\Services\Security;

class CsvSanitizer
{
    /**
     * Sanitize a cell value to prevent CSV Formula Injection (Excel / LibreOffice / Google Sheets).
     * If value starts with =, +, -, @, or whitespace followed by those characters, prefix with single quote.
     *
     * @param string|null $value
     * @return string
     */
    public static function sanitizeCell(?string $value): string
    {
        $value = (string) $value;
        if (preg_match('/^\s*[=\-+@]/', $value)) {
            return "'" . ltrim($value);
        }
        return $value;
    }
}
