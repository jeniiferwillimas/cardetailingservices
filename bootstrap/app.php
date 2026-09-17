<?php

use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway (and most PaaS hosts) terminate TLS at a reverse proxy and
        // forward the original scheme via X-Forwarded-*. Trust it so
        // Request::secure() reflects the real protocol for ForceHttps below.
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('api/*') ? null : route('login'));
        // Baseline rate limit for every API route; sensitive routes
        // (login, checkout, IPN) layer stricter named limiters on top.
        $middleware->throttleApi();
        // append (not prepend): explicit prepends run before Laravel's own
        // global middleware list, which is where TrustProxies lives — if
        // ForceHttps ran first, Request::secure() would still reflect the
        // untrusted scheme and reject every request behind Railway's proxy.
        $middleware->append(ForceHttps::class);
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
