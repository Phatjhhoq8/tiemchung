<?php

namespace Tests\Feature;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class VietnameseLocalizationTest extends TestCase
{
    public function test_vietnamese_is_the_default_locale_and_catalog_is_complete(): void
    {
        $this->assertSame('vi', config('app.locale'));
        $this->assertSame('vi', config('app.fallback_locale'));
        $this->assertSame('vi_VN', config('app.faker_locale'));

        $english = require base_path('vendor/laravel/framework/src/Illuminate/Translation/lang/en/validation.php');
        $vietnamese = require lang_path('vi/validation.php');

        foreach (array_keys($english) as $key) {
            $this->assertArrayHasKey($key, $vietnamese);
        }

        foreach (['between', 'gt', 'gte', 'lt', 'lte', 'max', 'min', 'password', 'size'] as $key) {
            $this->assertSame(array_keys($english[$key]), array_keys($vietnamese[$key]));
        }

        $this->assertSame('họ và tên là bắt buộc.', Validator::make([], ['name' => 'required'])->errors()->first('name'));
        $this->assertSame('Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau 30 giây.', __('auth.throttle', ['seconds' => 30]));
        $this->assertSame('Trang sau &raquo;', __('pagination.next'));
    }

    #[DataProvider('htmlExceptionProvider')]
    public function test_production_html_exceptions_use_standalone_vietnamese_pages(int $status, string $text): void
    {
        $this->useProductionEnvironment();

        $exception = match ($status) {
            401 => new AuthenticationException('INTERNAL SECRET'),
            500 => new RuntimeException('INTERNAL SECRET'),
            default => new HttpException($status, 'INTERNAL SECRET'),
        };

        $response = $this->renderException($exception, Request::create('/khong-ton-tai'));

        $response->assertStatus($status)
            ->assertViewIs("errors.{$status}")
            ->assertSee('<html lang="vi">', false)
            ->assertSeeText($text)
            ->assertDontSee('INTERNAL SECRET');
    }

    public static function htmlExceptionProvider(): array
    {
        return [
            '401' => [401, 'Bạn chưa đăng nhập'],
            '403' => [403, 'Truy cập bị từ chối'],
            '404' => [404, 'Không tìm thấy trang'],
            '405' => [405, 'Phương thức không được hỗ trợ'],
            '419' => [419, 'Phiên làm việc đã hết hạn'],
            '422' => [422, 'Dữ liệu không hợp lệ'],
            '429' => [429, 'Quá nhiều yêu cầu'],
            '500' => [500, 'Đã xảy ra lỗi hệ thống'],
            '503' => [503, 'Dịch vụ tạm thời không khả dụng'],
        ];
    }

    #[DataProvider('jsonExceptionProvider')]
    public function test_production_json_exceptions_have_safe_vietnamese_messages(int $status, string $message): void
    {
        $this->useProductionEnvironment();

        $headers = $status === 405 ? ['Allow' => 'GET'] : [];
        $exception = match ($status) {
            401 => new AuthenticationException('INTERNAL SECRET'),
            500 => new RuntimeException('INTERNAL SECRET'),
            default => new HttpException($status, 'INTERNAL SECRET', null, $headers),
        };
        $request = Request::create('/api/thuc-nghiem', 'GET', server: ['HTTP_ACCEPT' => 'application/json']);

        $response = $this->renderException($exception, $request);

        $response->assertStatus($status)
            ->assertExactJson(['message' => $message])
            ->assertDontSee('INTERNAL SECRET');

        if ($status === 405) {
            $response->assertHeader('Allow', 'GET');
        }
    }

    public static function jsonExceptionProvider(): array
    {
        return [
            '401' => [401, 'Bạn chưa đăng nhập.'],
            '403' => [403, 'Bạn không có quyền truy cập tài nguyên này.'],
            '404' => [404, 'Không tìm thấy trang hoặc tài nguyên yêu cầu.'],
            '405' => [405, 'Phương thức yêu cầu không được hỗ trợ.'],
            '419' => [419, 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.'],
            '422' => [422, 'Dữ liệu gửi lên không hợp lệ.'],
            '429' => [429, 'Quá nhiều yêu cầu. Vui lòng thử lại sau.'],
            '500' => [500, 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.'],
            '503' => [503, 'Dịch vụ hiện không khả dụng. Vui lòng thử lại sau.'],
        ];
    }

    public function test_validation_json_keeps_field_errors(): void
    {
        $this->useProductionEnvironment();

        $validator = Validator::make([], ['email' => ['required', 'email']]);
        $request = Request::create('/api/thuc-nghiem', 'POST', server: ['HTTP_ACCEPT' => 'application/json']);
        $response = $this->renderException(new ValidationException($validator), $request);

        $response->assertUnprocessable()
            ->assertJsonPath('message', 'Dữ liệu gửi lên không hợp lệ.')
            ->assertJsonPath('errors.email.0', 'địa chỉ email là bắt buộc.');
    }

    private function renderException(\Throwable $exception, Request $request): TestResponse
    {
        $response = app(ExceptionHandler::class)->render($request, $exception);

        return TestResponse::fromBaseResponse($response);
    }

    private function useProductionEnvironment(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        config()->set('app.debug', false);
    }
}
