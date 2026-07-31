<?php

namespace App\Services\Security;

use Modules\VaccineRegistration\Support\SecurityHelper;

class HtmlSanitizer
{
    /**
     * Clean and sanitize HTML content to prevent stored XSS attacks.
     *
     * @param string|null $html
     * @return string
     */
    public static function clean(?string $html): string
    {
        return SecurityHelper::cleanHtml($html);
    }

    /**
     * Alias method for clean().
     *
     * @param string|null $html
     * @return string
     */
    public static function cleanHtml(?string $html): string
    {
        return SecurityHelper::cleanHtml($html);
    }
}
