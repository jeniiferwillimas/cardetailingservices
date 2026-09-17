<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Reject plain HTTP in production. A redirect isn't used here because
     * it can silently drop the body of non-GET requests across some HTTP
     * clients — for a JSON API it's safer to fail loudly instead. Relies
     * on TrustProxies being configured correctly so $request->secure()
     * reflects the original scheme when running behind a load balancer.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production') && ! $request->secure()) {
            return response()->json([
                'success' => false,
                'message' => 'HTTPS is required.',
            ], 400);
        }

        return $next($request);
    }
}
