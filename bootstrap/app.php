<?php

use App\Http\Messages\FlashMessage;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (AuthenticationException $e) {
            return response()->json(
                FlashMessage::error(trans('auth.unauthenticated')),
                Response::HTTP_UNAUTHORIZED
            );
        });

        $exceptions->renderable(function (HttpException $e) {
            if ($e->getStatusCode() === 403) {
                return response()->json(
                    FlashMessage::error(trans('auth.unauthorized')),
                    $e->getStatusCode()
                );
            }
        });
    })->create();
