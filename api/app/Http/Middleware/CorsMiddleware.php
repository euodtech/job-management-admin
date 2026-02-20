<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware {

    public function handle($request, Closure $next){

        $allowedOrigins = array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:8080')));
        $origin = $request->header('Origin');

        $headers = [
            'Access-Control-Allow-Methods'     => 'HEAD,POST,GET,OPTIONS,PUT,DELETE',
            'Access-Control-Allow-Headers'     => 'Content-Type, X-API-Key, Authorization',
            'Access-Control-Max-Age'           => '86400',
            'X-Frame-Options'                  => 'SAMEORIGIN',
            'X-Content-Type-Options'           => 'nosniff',
            'X-XSS-Protection'                 => '1; mode=block',
            'Referrer-Policy'                  => 'strict-origin-when-cross-origin',
        ];

        if (in_array($origin, $allowedOrigins)) {
            $headers['Access-Control-Allow-Origin'] = $origin;
            $headers['Vary'] = 'Origin';
        }

        if ($request->isMethod('OPTIONS')){
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }

        $response = $next($request);

        foreach($headers as $key => $value){
            $response->header($key, $value);
        }

        return $response;
    }
}
