<?php

namespace App\Http\Middleware;

use App\Models\SyncActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class LogSyncActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            $statusCode = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            $this->record($request, $statusCode, [
                'message' => $exception->getMessage(),
            ], $startedAt);

            throw $exception;
        }

        $responseData = json_decode((string) $response->getContent(), true);
        $responseData = is_array($responseData) ? $responseData : [];

        $this->record($request, $response->getStatusCode(), $responseData, $startedAt);

        return $response;
    }

    private function record(Request $request, int $statusCode, array $responseData, float $startedAt): void
    {
        $receivedCount = is_array($request->input('items'))
            ? count($request->input('items'))
            : 0;
        $failed = $statusCode < 200 || $statusCode >= 300;
        $errorDetails = $failed
            ? ($responseData['errors'] ?? $responseData['message'] ?? "Request gagal dengan HTTP {$statusCode}.")
            : null;

        try {
            SyncActivityLog::query()->create([
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'status_code' => $statusCode,
                'status' => $failed ? 'failed' : 'success',
                'received_count' => $responseData['received'] ?? $receivedCount,
                'saved_count' => $responseData['saved'] ?? null,
                'updated_count' => $responseData['updated'] ?? null,
                'error_details' => $errorDetails === null
                    ? null
                    : (is_string($errorDetails)
                        ? $errorDetails
                        : (json_encode(
                            $errorDetails,
                            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
                        ) ?: 'Gagal mengubah detail error menjadi JSON.')),
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
