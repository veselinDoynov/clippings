<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin' => 'null',
            'Access-Control-Allow-Methods' => 'POST, GET, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Credentials' => 'false',
            'Access-Control-Max-Age' => '0',
            'Access-Control-Allow-Headers' => 'Accept, Authorization, Cache-Control, Content-Type, Pragma, X-Requested-With, Content-Type, Accept, X-FrontEnd-Version, X-Twilio-Signature',
            'Access-Control-Expose-Headers' => 'Content-Disposition'
        ];

        $allowedOrigins = explode(',', env('CORS_ALLOW_ORIGINS', ''));
        $origin = $request->server('HTTP_ORIGIN') ?? $request->server('HTTPORIGIN');

        foreach ($allowedOrigins as $allowedOrigin) {
            if ($allowedOrigin[0] == '*') {
                $allowedOrigin = substr($allowedOrigin, 1);
                if (strpos($origin, $allowedOrigin) !== false) {
                    $headers['Access-Control-Allow-Origin'] = $origin;
                }
            } else {
                $originHost = parse_url($request->server('HTTP_ORIGIN') ?? $request->server('HTTPORIGIN'));
                $originHost = $originHost['host'] ?? $originHost['path'];
                if ($originHost == $allowedOrigin) {
                    $headers['Access-Control-Allow-Origin'] = $origin;
                }
            }
        }

        if ($request->isMethod('OPTIONS')) {
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}
