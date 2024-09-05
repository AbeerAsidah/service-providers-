<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Middleware\CheckAbilities;
use Dotenv\Exception\ValidationException;
use App\Http\Middleware\TrackLastActiveUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/v1/app/api.php'));
            Route::middleware('api')
                ->prefix('api/v1/admin')
                ->group(base_path('routes/v1/admin/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SetLocale::class);
        $middleware->alias([
            'last.active' => TrackLastActiveUser::class,
            'ability' => CheckAbilities::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable|Exception $e) {

            if($e instanceof ThrottleRequestsException){
                return error('Too many requests. Please try again later.' , [
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
                ] , 429) ;
            }

            if ($e instanceof AuthenticationException) {
                return error('Unauthenticated', [$e->getMessage()], 401);
            }

            if ($e instanceof AuthorizationException) {
                return error('Forbidden', [$e->getMessage()], 403);
            }

            if ($e instanceof ModelNotFoundException) {
                return error('Resource not found', [$e->getMessage()], 404);
            }

            if (($e instanceof ValidationException) or ($e instanceof HttpResponseException)) {
                return error('Validation error', [$e->getMessage()], 422);
            }


            // Default error response for all other exceptions
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        });
    })->create();
