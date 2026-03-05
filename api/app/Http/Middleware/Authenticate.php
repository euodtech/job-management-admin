<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Models\UserLogin;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $apiKey = $request->header('X-API-Key');

        // Temporary backward compatibility (remove after mobile app update)
        if (empty($apiKey)) {
            $apiKey = $request->input('x-key');
            if ($apiKey) {
                \Log::warning('API key passed via query parameter - deprecated. Use X-API-Key header.', [
                    'ip' => $request->ip(),
                    'path' => $request->path()
                ]);
            }
        }

        if (empty($apiKey)) {
            return response([
                'Status' => 401,
                'Message' => 'Unauthorized'
            ], 401);
        }

        $data = UserLogin::where('ApiKey', $apiKey)->first();

        if (!$data) {
            return response([
                'Status' => 401,
                'Message' => 'Unauthorized Token'
            ], 401);
        }

        // Store authenticated user on request for downstream use
        $request->attributes->set('authenticated_user', $data);

        // Throttle LastActivity writes: only update if >1 minute since last update
        // This eliminates a DB WRITE on every single request
        $now = time();
        $lastActivity = $data->LastActivity ? strtotime($data->LastActivity) : 0;
        if (($now - $lastActivity) > 60) {
            $data->LastActivity = date('Y-m-d H:i:s', $now);
            $data->save();
        }

        return $next($request);
    }
}