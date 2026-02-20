<?php

namespace App\Http\Middleware;

use Closure;

class ThrottleRequests
{
    public function handle($request, Closure $next, $maxAttempts = 5, $decayMinutes = 1)
    {
        $key = 'throttle_' . md5($request->ip() . '|' . $request->path());
        $cacheDir = storage_path('framework/cache');
        $cacheFile = $cacheDir . '/' . $key;

        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $attempts = 0;
        $resetTime = 0;

        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && isset($data['reset']) && $data['reset'] > time()) {
                $attempts = $data['attempts'];
                $resetTime = $data['reset'];
            } else {
                @unlink($cacheFile);
            }
        }

        if ($attempts >= $maxAttempts) {
            $retryAfter = $resetTime - time();
            return response()->json([
                'Success' => false,
                'Message' => 'Too many attempts. Please try again later.',
            ], 429)->header('Retry-After', $retryAfter);
        }

        $response = $next($request);

        file_put_contents($cacheFile, json_encode([
            'attempts' => $attempts + 1,
            'reset' => $resetTime ?: time() + ($decayMinutes * 60),
        ]), LOCK_EX);

        return $response;
    }
}
