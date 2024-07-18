<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Dotenv\Exception\ValidationException;
use App\Http\Middleware\TrackLastActiveUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            'last.active' => TrackLastActiveUser::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create();
