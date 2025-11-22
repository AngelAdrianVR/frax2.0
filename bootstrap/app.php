<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Capturar errores 404 (NotFoundHttpException)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            // Solo renderizamos la vista Vue si no es una petición de API (ej. /api/...)
            // para no romper las respuestas JSON de tu ERP.
            if (!$request->is('api/*')) {
                return Inertia::render('404Error', [
                    'status' => 404,
                    'message' => 'La página que buscas no existe.'
                ])
                ->toResponse($request)
                ->setStatusCode(404);
            }
        });
    })->create();
