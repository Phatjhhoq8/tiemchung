<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\VaccineRegistration\Support\SecurityHelper;

class SafeImageFile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value && ! SecurityHelper::isSafeImageFile($value)) {
            $fail('Tệp ảnh không hợp lệ hoặc chứa nội dung SVG không được phép.');
        }
    }
}
