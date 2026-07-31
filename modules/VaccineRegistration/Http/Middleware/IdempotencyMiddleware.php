<?php

namespace Modules\VaccineRegistration\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header('Idempotency-Key')
            ?? $request->header('X-Idempotency-Key')
            ?? $request->header('idempotency_key')
            ?? $request->input('idempotency_key');

        if (!$idempotencyKey) {
            return $next($request);
        }

        $cacheKey = 'idempotency:' . md5((string)$idempotencyKey);

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && isset($cached['content'], $cached['status'])) {
                $response = response($cached['content'], $cached['status']);
                $contentType = $cached['content_type'] ?? 'application/json';
                $response->headers->set('Content-Type', $contentType);
                $response->headers->set('X-Idempotency-Hit', 'true');
                return $response;
            }
            if ($cached instanceof Response) {
                return $cached;
            }
        }

        $response = $next($request);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            Cache::put($cacheKey, [
                'status' => $response->getStatusCode(),
                'content' => $response->getContent(),
                'content_type' => $response->headers->get('Content-Type', 'application/json'),
            ], now()->addHours(24));
        }

        return $response;
    }
}
