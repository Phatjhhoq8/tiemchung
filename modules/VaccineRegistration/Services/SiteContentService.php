<?php

namespace Modules\VaccineRegistration\Services;

use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\Setting;
use Modules\VaccineRegistration\Support\SiteContentRegistry;

class SiteContentService
{
    /**
     * Lấy giá trị của một cài đặt cụ thể.
     */
    public function get(string $key, bool $isDraft = false)
    {
        $fields = SiteContentRegistry::getFields();
        if (!array_key_exists($key, $fields)) {
            return null;
        }

        $default = $fields[$key]['default'] ?? null;

        // Nếu ở chế độ xem thử (isDraft = true), ưu tiên lấy key nháp (key_draft)
        if ($isDraft) {
            $draftKey = $key . '_draft';
            $val = Setting::get($draftKey);
            if ($val !== null) {
                return $this->castValue($val, $fields[$key]['type']);
            }
        }

        // Lấy key chính thức
        $val = Setting::get($key);
        if ($val !== null) {
            return $this->castValue($val, $fields[$key]['type']);
        }

        return $this->castValue($default, $fields[$key]['type']);
    }

    /**
     * Lấy tất cả cài đặt của trang hoặc toàn bộ settings.
     */
    public function getAll(bool $isDraft = false): array
    {
        $fields = SiteContentRegistry::getFields();
        $result = [];

        foreach ($fields as $key => $meta) {
            $result[$key] = $this->get($key, $isDraft);
        }

        return $result;
    }

    /**
     * Lưu bản nháp (Draft) cho các trường nội dung.
     */
    public function saveDraft(array $data): void
    {
        $fields = SiteContentRegistry::getFields();

        DB::transaction(function () use ($data, $fields) {
            foreach ($data as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    // Chuẩn hóa giá trị trước khi lưu
                    $normalizedValue = $this->normalizeValue($value, $fields[$key]['type']);
                    Setting::set($key . '_draft', $normalizedValue);
                }
            }
        });
    }

    /**
     * Xuất bản chính thức (Publish) toàn bộ bản nháp hiện tại.
     */
    public function publish(array $data): void
    {
        $fields = SiteContentRegistry::getFields();

        DB::transaction(function () use ($data, $fields) {
            // 1. Lưu nháp trước
            $this->saveDraft($data);

            // 2. Đồng bộ toàn bộ bản nháp sang chính thức
            foreach ($fields as $key => $meta) {
                $draftVal = Setting::get($key . '_draft');
                if ($draftVal !== null) {
                    Setting::set($key, $draftVal);
                } else {
                    // Nếu chưa có nháp, lấy giá trị chính thức hoặc mặc định để đồng bộ
                    $currentVal = Setting::get($key) ?? $meta['default'];
                    Setting::set($key, $currentVal);
                    Setting::set($key . '_draft', $currentVal);
                }
            }
        });
    }

    /**
     * Khôi phục bản nháp về bản chính thức hiện tại (Reset).
     */
    public function resetDraft(): void
    {
        $fields = SiteContentRegistry::getFields();

        DB::transaction(function () use ($fields) {
            foreach ($fields as $key => $meta) {
                $currentVal = Setting::get($key);
                if ($currentVal !== null) {
                    Setting::set($key . '_draft', $currentVal);
                } else {
                    Setting::set($key . '_draft', $meta['default']);
                }
            }
        });
    }

    /**
     * Chuyển đổi kiểu dữ liệu phù hợp khi lấy ra.
     */
    private function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        if ($type === 'json') {
            if (is_array($value)) {
                return $value;
            }
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return (string)$value;
    }

    /**
     * Chuẩn hóa giá trị trước khi lưu vào database.
     */
    private function normalizeValue($value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($type === 'json') {
            if (is_array($value)) {
                return json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            // Kiểm tra xem chuỗi có là JSON hợp lệ hay không
            $decoded = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return json_encode([], JSON_UNESCAPED_UNICODE);
            }
            return json_encode($decoded, JSON_UNESCAPED_UNICODE);
        }

        return (string)$value;
    }
}
