<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception, Request $request) {
            if (app()->environment('testing')) {
                return null;
            }

            if ($exception instanceof ValidationException) {
                if (! $request->expectsJson()) {
                    return null;
                }

                return response()->json([
                    'message' => 'Dữ liệu gửi lên không hợp lệ.',
                    'errors' => $exception->errors(),
                ], 422);
            }

            $status = match (true) {
                $exception instanceof AuthenticationException => 401,
                $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
                default => 500,
            };

            $messages = [
                401 => 'Bạn chưa đăng nhập.',
                403 => 'Bạn không có quyền truy cập tài nguyên này.',
                404 => 'Không tìm thấy trang hoặc tài nguyên yêu cầu.',
                405 => 'Phương thức yêu cầu không được hỗ trợ.',
                419 => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.',
                422 => 'Dữ liệu gửi lên không hợp lệ.',
                429 => 'Quá nhiều yêu cầu. Vui lòng thử lại sau.',
                500 => 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.',
                503 => 'Dịch vụ hiện không khả dụng. Vui lòng thử lại sau.',
            ];

            if (! isset($messages[$status])) {
                return null;
            }

            $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];

            if ($request->expectsJson()) {
                return response()->json(['message' => $messages[$status]], $status, $headers);
            }

            return response()->view("errors.{$status}", [], $status, $headers);
        });
    })->create();
